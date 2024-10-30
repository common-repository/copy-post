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
?>
<div class="wrap">
<h2>Copy Post Options</h2>

<form method="post" action="options.php">
<table class="form-table">

<tr valign="top">
<th scope="row">Remote Blog URL (ex. http://blog.dayspring-tech.com/, including trailing slash)</th>
<td><input type="text" name="remote_url" value="<?php echo get_option('remote_url'); ?>" /></td>
</tr>
 
<tr valign="top">
<th scope="row">Remote Database Host</th>
<td><input type="text" name="remote_db_host" value="<?php echo get_option('remote_db_host'); ?>" /></td>
</tr>

<tr valign="top">
<th scope="row">Remote Database User</th>
<td><input type="text" name="remote_db_user" value="<?php echo get_option('remote_db_user'); ?>" /></td>
</tr>

<tr valign="top">
<th scope="row">Remote Database Password</th>
<td><input type="password" name="remote_db_pass" value="<?php echo get_option('remote_db_pass'); ?>" /></td>
</tr>

<tr valign="top">
<th scope="row">Remote Database Name</th>
<td><input type="text" name="remote_db_name" value="<?php echo get_option('remote_db_name'); ?>" /></td>
</tr>

</table>
<?php
settings_fields( 'myoption-group' );
?>

<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>

</form>
</div>
