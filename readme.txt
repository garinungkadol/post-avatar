==== Post Avatar ====
Contributors: garinungkadol
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=2N7SF4KU37D6Y
Tags: post, avatars, images, image, thumbnail
Requires at least: 2.8
Tested up to: 3.2.1
Stable tag: 1.4.2

Choose an avatar from a pre-defined list to include in a post. 

== Description == 
This plugin simplifies including a picture when writing posts by allowing the user to choose from a predefined list of images. The image can be automatically shown on the page or output customized with the use of a template tag in themes. This plugin is similar to Livejournal userpics. Developed with [Dominik Menke](http://WordPress.gaw2006.de).

Translations:

* German (de_DE) [Dominik Menke](http://WordPress.gaw2006.de)
* Italian (it_IT) [Gianni Diurno](http://gidibao.net)
* Belorussian (ru_RU) [Fat Cower](http://www.fatcow.com)
* Dutch (nl_NL) [Jay August](http://www.jayaugust.com)
* Spanish (es_ES) [gogollack](http://queestapasando.co.cc/)
* Czech (cz_CZ) [Lelkoun](http://lelkoun.cz)
* French (fr_FR) [Mathieu Haratyk](http://www.eduens.com)
* Polish (pl_PL) [Meloniq](http://blog.meloniq.net)
* Irish (ga_IR) [Ray S.](http://letsbefamous.com)


= Features =
* Easy selection of images in the Write Post screen.
* Scans images in sub-directories of the image option folder.
* Allows the following file types: .jpg, .jpeg, .gif and .png.
* Settings display avatars automatically or through the use of template tags.
* Customize html output of avatars.
* Does not display missing images.

Please visit the [Post Avatar home page](http://garinungkadol.com/plugins/post-avatar/) for more information.

== Installation ==
1. Download the plugin.
2. Unzip.
3. Upload "post-avatar" directory to your plugin folder (/wp-content/plugins).
4. Activate the plugin from the Plugin Management screen.
5. Set plugin options in Settings - Post Avatar. 
	* **Path to Images Folder** - location of your images folder in relation to your WordPress installation.

	* **Show image in Write Post Page** - Place a tick mark if you want to see a thumbnail of the post avatar in the Write Post screen.

	* **Scan the images directory and its sub-directories** - Place a tick mark if you want to list all images including those in sub-directories of the image folder.

	* **Show avatar in post** - Place a tick mark to show avatar automatically on your blog post. Disable to use the template tag.

	These options help you further customize the display of your post avatar

	* **Customize HTML/CSS** - These options help you customize the look of your post avatar
			
	1. **Before and After HTML** - enter the HTML you want to display before and after the post avatar. 
		Example: Before: `<div class="myimage">` / After: `</div>`
		Output: `<div class="myimage"><img src="http://mydomain.com/images/image.jpg" style="border:0" alt="post-title" /></div>`

	2. **CSS Class** - enter the name of the css class that you would like to associate with the post avatar image. Can be left blank. 
		Example: The class name is: postimage
		Output: `<img class="postimage" src="http://mydomain.com/images/image.jpg" style="border:0" alt="post-title" />`

	If you use both the css class and the before and after html you will get the following output:
		`<div class="myimage"><img class="postimage" src="http://mydomain.com/images/image.jpg" style="border:0" alt="post-title" /></div>`

	* **Others**
	
	1. **Get image size?** - Turned on by default to determine the image's width and height. If you encounter any getimagesize errors, turn this feature off.

	2. **Show in feeds?** - Turned off by default. Check this option to display post avatars in your RSS feeds.


= Usage =
**A. UPLOAD IMAGES**
	
Upload the images that you intend to use to the folder defined in the Post Avatar options.


**B. ADDING AN AVATAR TO A POST**

1. To add an image to a post, go to the Post Avatar section.
   The image selector is below the Excerpt but you can move it to a different location.

2. Select the image name from the list. 

3. Save your entry.


= For Theme Developers =

For improved integration with third-party WordPress themes, Post Avatar has two additional tags to help producing custom output in additional functions. 

**OVERRIDE AUTOMATIC DISPLAY OF POST AVATARS

In case users automatic display of avatars is set to, use the [remove_filter()](http://codex.wordpress.org/Function_Reference/remove_filter)


**CUSTOM OUTPUT

To produce your own output with post avatar data, use the function:
	`<?php gkl_get_postavatar(); ?>`
This lets you create a array containing the url to the avatar, image height and width, post title, post id and boolean value to let you know if the getimagesize option has been turned on or not.


Please visit the [Post Avatar Page](http://www.garinungkadol.com/plugins/post-avatar/) for details on customizing the avatar display.

== Upgrade Notice ==
= 1.4.2 =
Added Irish translation. Fixed issue with post avatars being saved twice when post revisions are on.

= 1.4.1 =
If your image filenames have spaces, you will need to upgrade.

= 1.4 =
Improved security. Please save Post Avatar Settings after upgrade.

= 1.3 =
This is a version update. Please only upgrade if you are using WordPress 2.8 or greater.

= 1.2.3 =
If you are upgrading from a previous version of Post Avatar, deactivate and activate the plugin to enable role capabilities.


== Changelog ==
= 1.4.2 =
(07/13/2011)
* Added: Gaellic translation. Thanks to Ray.
* Fixed: Exclude revisions from post avatar saving routine

= 1.4.1 =
(06/27/2011)
* Fixed: Spaces in image filenames were being removed by `esc_url`.

= 1.4 =
(06/20/2011)
* Added: Improved security checks when saving post meta data and options as well as displaying data.
* Added: Activation hook to process capabilities and default options
* Added: Improved compatibility with WordPress 3.0 and above.
* Deprecated functions: `gkl_unescape_html` and `gkl_dev_override`
* Notice: This will be the last version to support PHP 4

= 1.3.2 =
(04/13/2011)
* Added: Polish translation. Thanks to [Meloniq](http://blog.meloniq.net).
* Fixed: Duplication of post avatar when "apply_filters" tag is used in other plugins.
* Fixed: Improved data validation. Now using wp_kses when validating HTML.

= 1.3.1 = 
(08/23/2010)
* Added: French translation. Thanks to [Mathieu Haratyk](http://www.eduens.com).


= 1.3 = 
(05/14/2010)
* Version Upgrade: Removed usage of deprecated WordPress functions. This version supports WordPress 2.8 and greater.

= 1.2.7.1 =
(03/11/2010)
* Added: Spanish translation. Thanks to [gogollack](http://queestapasando.co.cc/).
* Added: Czech translation. Thanks to [Lelkoun](http://lelkoun.cz).


= 1.2.7 =
(02/12/2010)
* Fixed: IE preview problems when reselecting an image. Thanks [spedney](http://wordpress.org/support/topic/305900)
* Fixed: removed border="0" in image display for XHTML compliance. Thanks [Jay August](http://wordpress.org/support/topic/352564)

= 1.2.5.5 =
(08/21/2009)
* Added: Dutch translation. Thanks to [Jay August](http://www.jayaugust.com).


= 1.2.5.4 =
(08/21/2009)
* Added: Dutch translation. Thanks to [Jay August](http://www.jayaugust.com).

= 1.2.5.3 =
(08/06/2009)
* Added: Belorussian translation. Thanks to [Fat Cower](http://www.fatcow.com).


= 1.2.5.2 =
(06/02/2009)
* Added: Italian translation. Thanks to [Gianni Diurno](http://gidibao.net).


= 1.2.5 =
(12/15/2008)
* Fixed: Incorrect display of css class
* Fixed: Bugs in image display (height/width switched up)
* Fixed: "Cannot modify header information" errors when saving posts when plugin is used in conjunction with search unleashed plugin
* Added: Theme developer override option for automatic avatar display
* Added: template tag `gkl_get_postavatar`, to return post avatar data in an array. 

= 1.2.4 =
(03/31/2008)
* Added: Slideshow effect to navigate for next and previous images
* Fixed: Display of avatar in Write Post page and navigation effects work in IE6+
* Added: HTML for meta boxes in WordPress 2.5+
* Added: Option to include avatars in RSS feeds

= 1.2.3 = 
(10/06/2007)
* Added: Role capabilities that allow admins, editors and authors to post avatars
* Added: HTML and CSS classes inside the options page
* Added: Include automatic avatar display in post excerpts
* Added: Option to display image dimensions
* Fixed: Stop avatars from displaying in feeds

= 1.2.2 =
(02/12/2007)
* Fixed: Additional checks in updating posts to make sure that comment posting don't delete post avatars

= 1.2.1 = 
(01/11/2007)
* Added: Compatibility for Wordpress 2.1
* Added: Option to display image automatically without have to use template tag

= 1.2 = 
(12/09/2006)
* Added: Scan subdirectories for images
* Added: Created external scriptfile to make extending script easier
* Added: Check if PHP_SELF contains substring (for subdomain installations)
* Fixed: Improved image display in Write Post screen
* Added: Check image existence using absolute path instead of url (for those without `Allow_url_fopen`)

= 1.1 =
(09/06/2006)
* Added: Live preview of avatar in Write Post screen (tested in Mozilla)
* Fixed: `gkl_postavatar` template tag produces correct (X)HTML
* Speed optimization
* Improved parameters ($before, $after, $class)
* Added: Translation support

= 1.0 = 
(08/26/2006)
* Initial release


