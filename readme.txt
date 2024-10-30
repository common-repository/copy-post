=== Copy Post ===
Contributors: jwong.dayspring, Dayspring Technologies
Tags: copy, post, migrate, deploy
Requires at least: 2.7
Tested up to: 2.8.4
Stable tag: 0.2.0

Copy Post allows you to copy posts from one instance of WordPress to another.  

== Description ==

Copy Post removes the tedium of having to copy content, comments, and tags manually from one instance to another.  It also preserves the original post author and date.

Copy Post was originally written when we were preparing to launch a public blog and wanted to pre-populate it with content from our in-office blog that we had been using for the past two years.  If our marketing team thinks that something originally posted to our in-office blog would be helpful or meaningful to others outside of our company, they can easily copy it to the public blog.  
Copy Post could also be used in a situation where multiple WordPress instances are used in a development/staging/production setup.

First, a few assumptions and caveats.

* You must have access to the MySQL database of the destination blog.
* The author of the post you are copying must have an account on the destination blog with the same login name, though the IDs don't have to be the same.
* Categories assignments are not copied over, mainly because in our case the categories on the two blogs are different and were going to be edited anyway.
* Comments attributed to registered users retain this attribution only if the commenter has an account on the destination blog (just like the author).  If the commenter doesn't have an account, the comment is still copied, but it's treated as if it were made by an unregistered visitor.

Use the copy post feature to send a post to a destination blog.  You'll need the post ID, which is available by looking at the edit link for the post.  The plugin will copy the post content, tags, and comments.  The original author and post date will be retained.  All category information will not be copied over.  The resulting post will be set as a "private" post to allow you to set the categories and make edits before it goes live.  Also, you'll need to be an admin on the source blog to use the plugin.

The settings for the destination blog are configured directly in the WordPress Admin interface, making it possible for us to distribute the plugin to anyone who would need it.


== Installation ==

1. Upload the `copy-post` folder to the `/wp-content/plugins/` directory.  
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure the plugin with the destination blog's URL and MySQL database information.  The source blog's server must have access to the MySQL database of the destination blog.


== Changelog ==

= 0.2.0 =
* Fixed directory structure and paths so the plugin works "out of the box" from the WordPress plugin installer.

= 0.1.4 =
* Removed 'post_category' from set of columns being copied to posts table.  This should allow Copy Post to work with a clean WordPress 2.8 install.

= 0.1.3 =
* Initial release of Copy Post plugin.
* Basic post content, comments, and tags are copied.  Categories are not copied.

