<?php
/*
The MIT License

Dayspring Technologies, Inc.  (http://www.dayspring-tech.com)
Copyright 2009, Dayspring Technologies, Inc.

Permission is hereby granted, free of charge, to any person obtaining a
copy of this software and associated documentation files (the "Software"),
to deal in the Software without restriction, including without limitation
the rights to use, copy, modify, merge, publish, distribute, sublicense,
and/or sell copies of the Software, and to permit persons to whom the
Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
DEALINGS IN THE SOFTWARE.
*/

	echo '<div class="wrap">';
	echo '<h2>Copy Post</h2>';
	$remote_host = get_option('remote_url');
	$host = get_option('remote_db_host');
	$user = get_option('remote_db_user');
	$pass = get_option('remote_db_pass');
	$db = get_option('remote_db_name');

	if (empty($remote_host) || empty($host) || empty($user) || empty($pass) || empty($db)){
		echo "Please configure this plugin before using it.";
		exit();
	}
	$myo = mysql_connect($host, $user, $pass, true);
	mysql_select_db($db, $myo);
	
	mysql_set_charset('utf8', $myo);
	echo "Target blog: <b>".$remote_host."</b><br>";
	
	if ( isset($_POST['submit']) ) {
		global $wpdb;

		$post_id = $_POST['postId'];


		$post = get_post($post_id, ARRAY_A);
		echo "<b>Copying post: ".$post['post_title']."</b><br>";
		
		
		// setup some common functions
		function findCorrespondingUser($user_id){
			global $myo;
			$user1 = get_userdata($user_id);
			
			$sql = "select * from wp_users where user_login='".$user1->user_login."'";
			$res = mysql_query($sql, $myo);
			if ($res && mysql_num_rows($res) > 0){
				$user2 = mysql_fetch_assoc($res);
				return $user2['ID'];
			} else {
				return null;
			}
		}		
		
		// check that post hasn't been copied already
		$sql = "select * from wp_posts where guid = '".$post['guid']."'";
		$res = mysql_query($sql, $myo);
		if ($res && mysql_num_rows($res) > 0){
			echo "ERROR: Post already exists (by guid check)!";
			exit();
		}
		
		// check that author exists on target blog
		$user1 = get_userdata($post['post_author']);
		$post['post_author'] = findCorrespondingUser($post['post_author']);
		if ($post['post_author'] == null){			
			echo "ERROR: Author (".$user1->user_login.") does not exist on target system!";
			exit();
		}
		$post['post_status'] = 'private';
		
		// ********************
		// create new post
		// ********************		
		$vals = sprintf("%s, '%s', '%s', '%s', '%s', '%s',
				'%s', '%s', '%s', '%s','%s', '%s', '%s',
				'%s', '%s', '%s',
				%s, '%s', %s, '%s', '%s', %s",
			mysql_real_escape_string($post['post_author']), mysql_real_escape_string($post['post_date']), mysql_real_escape_string($post['post_date_gmt']), mysql_real_escape_string($post['post_content']), mysql_real_escape_string($post['post_title']), mysql_real_escape_string($post['post_excerpt']),
			mysql_real_escape_string($post['post_status']), mysql_real_escape_string($post['comment_status']), mysql_real_escape_string($post['ping_status']), mysql_real_escape_string($post['post_password']), mysql_real_escape_string($post['post_name']), mysql_real_escape_string($post['to_ping']), mysql_real_escape_string($post['pinged']), 
			mysql_real_escape_string($post['post_modified']), mysql_real_escape_string($post['post_modified_gmt']), mysql_real_escape_string($post['post_content_filtered']), 
			mysql_real_escape_string($post['post_parent']), mysql_real_escape_string($post['guid']), mysql_real_escape_string($post['menu_order']), mysql_real_escape_string($post['post_type']), mysql_real_escape_string($post['post_mime_type']), mysql_real_escape_string($post['comment_count'])
		);
		$cols = "post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt,
				post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged,
				post_modified, post_modified_gmt, post_content_filtered,
				post_parent, guid, menu_order, post_type, post_mime_type, comment_count";
		$sql = "insert into wp_posts (".$cols.") values(".$vals.")";
		mysql_query($sql, $myo) or die("error running insert".mysql_error());
		$new_post_id = mysql_insert_id($myo);

		
		// ********************
		// copy tags to new post
		// ********************	
		$sql = "select t.name, t.slug from wp_term_relationships w natural join wp_term_taxonomy tt natural join wp_terms t where taxonomy = 'post_tag' and object_id = ".$post['ID'];
		$tags = $wpdb->get_results($sql, ARRAY_A);		
		if (is_array($tags)){
		foreach ($tags as $tag){
			$sql = "select * from wp_terms where name='".mysql_real_escape_string($tag['name'])."'";
			$res = mysql_query($sql, $myo);
			if (!$res || mysql_num_rows($res) <= 0){
				// create term
				$sql = "insert into wp_terms (name, slug) values('".mysql_real_escape_string($tag['name'])."', '".mysql_real_escape_string($tag['slug'])."')";
				mysql_query($sql, $myo);
				$term_id = mysql_insert_id($myo);
			} else {
				$t = mysql_fetch_assoc($res);
				$term_id = $t['term_id'];
			}
			
			$sql = "select * from wp_term_taxonomy where term_id=".$term_id;
			$res = mysql_query($sql, $myo);
			if (!$res || mysql_num_rows($res) <= 0){
				// create term_taxonomy
				$sql = "insert into wp_term_taxonomy (term_id, taxonomy, description) values(".$term_id.", 'post_tag', '')";
				mysql_query($sql, $myo);
				$term_taxonomy_id = mysql_insert_id($myo);
			} else {
				$t = mysql_fetch_assoc($res);
				$term_taxonomy_id = $t['term_taxonomy_id'];
			}
			
			$sql = "insert into wp_term_relationships (object_id, term_taxonomy_id) values(".$new_post_id.", ".$term_taxonomy_id.")";
			mysql_query($sql, $myo);
			$sql = "update wp_term_taxonomy set count = count+1 where term_taxonomy_id = ".$term_taxonomy_id;
			mysql_query($sql, $myo);
		}
		}
		
		
		// ********************
		// copy comments to new post
		// ********************	
		$comment_id_map = array();
		$sql = "SELECT * FROM wp_comments where comment_post_ID = ".$post['ID']." order by comment_ID";
		$comments = $wpdb->get_results($sql, ARRAY_A);		
		if (is_array($comments)){
		foreach ($comments as $comment){
			$user_id = findCorrespondingUser($comment['user_id']);
			if ($user_id == null){
				$user_id = 0;
			}
			
			if ($comment['comment_parent'] != 0){
				if (!array_key_exists($comment['comment_parent'], $comment_id_map)){
					echo "ERROR: could not rebuild comment tree!";
					exit();
				}				
				$comment['comment_parent'] = $comment_id_map[$comment['comment_parent']];
			}
		
			$vals = sprintf("%s, '%s', '%s', '%s', '%s', '%s', '%s', 
							'%s', %s, '%s', '%s', 
							'%s', %s, %s", 
				$new_post_id, mysql_real_escape_string($comment['comment_author']), mysql_real_escape_string($comment['comment_author_email']), mysql_real_escape_string($comment['comment_author_url']), mysql_real_escape_string($comment['comment_author_IP']), mysql_real_escape_string($comment['comment_date']), mysql_real_escape_string($comment['comment_date_gmt']), 
				mysql_real_escape_string($comment['comment_content']), mysql_real_escape_string($comment['comment_karma']), mysql_real_escape_string($comment['comment_approved']), mysql_real_escape_string($comment['comment_agent']), 
				mysql_real_escape_string($comment['comment_type']), mysql_real_escape_string($comment['comment_parent']), mysql_real_escape_string($user_id)
			);
			$cols = "comment_post_ID, comment_author, comment_author_email, comment_author_url, comment_author_IP, comment_date, comment_date_gmt, 
			comment_content, comment_karma, comment_approved, comment_agent, 
			comment_type, comment_parent, user_id";
			
			$sql = "insert into wp_comments (".$cols.") values(".$vals.")";
			mysql_query($sql, $myo);
			$comment_id = mysql_insert_id($myo);
			$comment_id_map[$comment['comment_ID']] = $comment_id;
		}
		}
		
		echo "Copy complete!<br>";
		echo '<a href="'.$remote_host.'wp-admin/post.php?action=edit&post='.$new_post_id.'">Edit new post (in new window)</a>';
	} else {
	
	
?>
<form name="form1" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
	Post ID: <input type="text" name="postId" /><br>
	<input type="submit" name="submit" value="<?php _e('Copy Post') ?>" />
</form>
<?php  
	}
	echo '</div>';
?>