<?php
/*
	Plugin Name: Post Avatar
	Plugin URI: http://www.garinungkadol.com/plugins/post-avatar/
	Description: Attach a picture to posts easily by selecting from a list of uploaded images. Similar to Livejournal Userpics. 
	Version: 1.4.3
	Author: Vicky Arulsingam
	Author URI: http://garinungkadol.com
	License: GPL2
*/

/*  Copyright 2006 - 2011 Vicky Arulsingam  (email : vix@garinungkadol.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Avoid calling page directly
if ( ! function_exists( 'add_action' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );

	exit( 'Uh oh! Accessing this file outside of WordPress is not allowed' );	
}

/* PLUGIN and WP-CONTENT directory constants if not already defined */
if ( ! defined( 'WP_PLUGIN_URL' ) )
	define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
if ( ! defined( 'WP_PLUGIN_DIR' ) )
	define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );
if ( ! defined( 'WP_CONTENT_URL' ) )
	define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
if ( ! defined( 'WP_CONTENT_DIR' ) )
	define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
	
	
/**
 * Load Text-Domain
 */
load_plugin_textdomain( 'gklpa', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );


/**
 * OPTIONS
 */
$gklpa_siteurl = get_option('siteurl');
$gkl_myAvatarDir = str_replace('/', DIRECTORY_SEPARATOR, ABSPATH . get_option('gklpa_mydir')); // Updated absolute path to images folder (takes into account Win servers)
$gkl_AvatarURL = trailingslashit($gklpa_siteurl) . get_option('gklpa_mydir'); // URL to images folder
$gkl_ShowAvatarInPost = get_option('gklpa_showinwritepage'); // Show image in Write Page?
$gkl_ScanRecursive = get_option('gklpa_scanrecursive'); // Recursive scan of the images?
$gkl_ShowInContent = get_option('gklpa_showincontent'); // Show avatar automatically in content?
$gkl_getsize = get_option('gklpa_getsize'); // Use getimagesize?
$gkl_dev_override = false;
$gkl_pa_version = '1.4.2';

/**
 * Display post avatar within The Loop
 *
 * @param string $class
 * @param string $before
 * @param string $after
 */
function gkl_postavatar($class='', $before='', $after='', $do_what= 'echo') {
	global $post, $allowedposttags;
	
	if (empty($class)) $class  = get_option('gklpa_class');
	if( empty( $before ) ) $before =  get_option( 'gklpa_before' );
	if( empty( $after ) ) $after = get_option( 'gklpa_after' ) ;

	# Validation & Sanitization
	$possible_values = array( 'echo', 'return' );
	if ( !in_array( $do_what, $possible_values ) )
		wp_die( 'Invalid value in gkl_postavatar template tag', 'gklpa');
	
	$class = sanitize_html_class( $class );
	$before = wp_kses( $before, $allowedposttags );
	$after = wp_kses( $after, $allowedposttags );
	if (!empty($class)) $class = ' class="' . $class . '"';
	
	$post_avatar = gkl_get_postavatar($post);
	$avatar_dim = '';

	if (!is_null($post_avatar)) {
		if ($post_avatar['show_image_dim']) {
			$avatar_dim = 'width="' . intval( $post_avatar['image_width'] ) .'" height="'. intval( $post_avatar['image_height'] ) .'"';
		}
		
		$post_avatar_text = $before .'<img' .$class . ' src="'.  esc_url( str_replace( ' ', '%20', $post_avatar['avatar_url'] ) ) .'" '. $avatar_dim . ' alt="'. esc_attr( $post_avatar['post_title'] ). '" />'. $after ."\n";
		// Show post avatar	
		if( $do_what == 'echo' ) echo $post_avatar_text;
		elseif( $do_what == 'return' ) return $post_avatar_text;
	}
	
}


/**
 * Get post avatar data
 *
 */
function gkl_get_postavatar($post) {
	global $gkl_AvatarURL, $gkl_myAvatarDir, $gkl_getsize;

	// Defaults
	$post_avatar = array();
	$post_id = 0;
	$CurrAvatar = '';
	
	$post_id = $post->ID;
	$CurrAvatar = get_post_meta($post_id,'postuserpic', true);
	$CheckAvatar = $gkl_myAvatarDir . $CurrAvatar;

	// Return nothing if value is empty or file does not exist
	if ( !empty($CurrAvatar) && file_exists($CheckAvatar) ) {
		$post_title = sanitize_title($post->post_title);
		$CurrAvatarLoc = $gkl_AvatarURL . $CurrAvatar;

		if ( $CurrAvatarLoc != $gkl_AvatarURL ) {
			$CurrAvatarLoc = str_replace('/', DIRECTORY_SEPARATOR, $gkl_myAvatarDir . ltrim($CurrAvatar[0],'/'));
			if($gkl_getsize) {
				$dim = @getimagesize($CheckAvatar);
			 } else {
			 	$dim[0] = null;
			 	$dim[1] = null;
			 }
			$CurrAvatarLoc = $gkl_AvatarURL . ltrim($CurrAvatar,'/');

			// create array of post avatar values			
			$post_avatar = array("avatar_url"=>$CurrAvatarLoc, "show_image_dim"=>$gkl_getsize, "image_height"=>$dim[1], "image_width"=>$dim[0], "post_id"=>$post_id, "post_title"=>$post_title, "image_name"=>ltrim($CurrAvatar,'/'));

		}
	} else {
			$post_avatar = null;
	}
	
	return $post_avatar;
}


/**
 * Get list of directory
 *
 * @param string $dir
 * @param boolean $recursive
 * @return array
 */
function gkl_readdir($dir, $recursive = true) {
	global $gkl_myAvatarDir;
	
	// Cut of the myAvatarDir from the output
	$dir2 = $gkl_myAvatarDir .'/';

	// Init
	$array_items = array();

	$handle = @opendir($dir);

	while (false !== ($file = @readdir($handle))) {
		// Bad for recursive to scan the current folder again and again and again...
		// ...also bad to scan the parent folder
		if ( $file != '.' && $file != ".." ) {
			// if is_file
			if (!is_dir($dir .'/'. $file)) {
				$file = $dir .'/'. $file;
				// Cut of the myAvatarDir from the output
				$array_items[] = str_replace($dir2, '', $file);
			} else {
				// if (is_dir && recusive scan) scan dir
				if ($recursive) {
					$array_items = array_merge($array_items, gkl_readdir($dir .'/'. $file, $recursive));
				}
				$file = $dir .'/'. $file;
				// Cut of the myAvatarDir from the output
				$array_items[] = str_replace($dir2, '', $file);
			}
		}
	}
	@closedir($handle);

	// Limit list to only images
	$array_items = preg_grep('/.jpg$|.jpeg$|.gif$|.png$/', $array_items);
	asort($array_items);
	return $array_items;
}

/*
 * Filter to include post avatar in the_content() or the_excerpt()
 *
 * @param text $content
 * @return text $content
 */
function gkl_postavatar_filter( $content ) {
	global $post, $gkl_AvatarURL, $gkl_myAvatarDir, $wp_query, $gkl_dev_override;
	
	// Using this to determine if we're in wp-admin or in the site
	// For compatibility with search unleashed plugin
	// or plugins that call add_filter after saving a post
	if (is_null($wp_query->posts)){ 
		return $content; 
	} else {
		if (!$wp_query->is_feed && $gkl_dev_override == 0){
				$post_avatar = gkl_postavatar('', '', '', 'return');
				// Show post avatar		
		}
		$new_content = $post_avatar . $content;
		return $new_content;
	}
}


/*
 * Filter to include post avatar in feeds
 *
 * @param text $content
 * @return text $content
 */
function gkl_postavatar_feed_filter($content) {
	global $post, $wp_query;
	$post_avatar = '';
	$showinfeeds = get_option('gklpa_showinfeeds');
	if($showinfeeds == 1 && $wp_query->is_feed) 
		$post_avatar = gkl_postavatar('', '', '', 'return');

	return $post_avatar . $content;
}

# ----------------------------------------------------------------------------------- #
#                                 POST META BOX                                       #
# ----------------------------------------------------------------------------------- #

/** 
 * Write Post display for WP 2.5+
 *
 */
function gkl_postavatar_metabox_admin() {
	 if ( current_user_can('post_avatars') ) {
		global $gkl_myAvatarDir, $gkl_AvatarURL, $gkl_ShowAvatarInPost, $gkl_ScanRecursive;
		$post_id = 0;	
		// Get current post's avatar
		if( isset( $_GET['post'] ) ) $post_id = intval($_GET['post'] );
		$CurrAvatar = esc_attr( get_post_meta( $post_id, 'postuserpic', true ) );
		$selected = ltrim( $CurrAvatar, '/' );
	
		//! Get AvatarList
		if ($gkl_ScanRecursive == 1)
			$recursive = true;
		else
			$recursive = false;
		$AvatarList = gkl_readdir($gkl_myAvatarDir, $recursive);
	?>
		<fieldset id="postavatarfield">
			<?php  gkl_avatar_html($AvatarList, $CurrAvatar, $selected); ?>		
		</fieldset>
	<?php
	}
}

/** 
 * Generate html for post avatar display
 *
 */
function gkl_avatar_html($AvatarList, $CurrAvatar, $selected) {
	global $gkl_ShowAvatarInPost, $gklpa_siteurl, $gkl_myAvatarDir, $gkl_AvatarURL
?>
	<table cellspacing="3" cellpadding="3" width="100%" align="left">
		<tr valign="top">
			<th width="20%"><?php _e('Select an avatar', 'gklpa'); ?></th>
			<td align="center">
			<?php if ($gkl_ShowAvatarInPost) { ?>
				<a href="#prev" onclick="prevPostAvatar();return false" class="pa"><img src="<?php echo plugin_dir_url( __FILE__ ) . 'images/prev.png'; ?>" alt="prev" title="" /></a>
	<?php } ?>
				<select name="postuserpic" id="postuserpic" onchange="chPostAvatar(this)">
					<option value="no_avatar.png" onclick="chPostAvatar(this)"><?php _e('No Avatar selected', 'gklpa'); ?></option>
			<?php
				foreach ($AvatarList as $file) {
					if ($file == 'no_avatar.png')
						continue;
	
					$oncklick = ( $gkl_ShowAvatarInPost == 1 ) ? ' onclick="chPostAvatar(this)"' : '';
					echo '<option value="/'. esc_attr( $file ) .'"'. selected( $selected, $file, false ) . $oncklick .'>'. esc_attr( $file ) .'</option>'."\n";
				}
	?>
				</select>
	<?php if ($gkl_ShowAvatarInPost) { ?>
				<a href="#next" onclick="nextPostAvatar();return false" class="pa"><img src="<?php echo plugin_dir_url( __FILE__ ) . 'images/next.png'; ?>" alt="next" title="" /></a>
	<?php } ?>
			</td>
		</tr>
	<?php
		// Display current avatar in Write Post page
		if ( $gkl_ShowAvatarInPost == 1 ) {
	?>
			<tr>
				<th width="20%" align="center"><?php _e('Preview', 'gklpa'); ?></th>
				<td align="center">
				<?php
					if ( !empty($CurrAvatar) ) {
						if ( file_exists($gkl_myAvatarDir . $CurrAvatar) ) {
							$CurrAvatarLoc = $gkl_AvatarURL . $CurrAvatar;
							echo '<img id="postavatar" src="'. esc_url( str_replace( ' ', '%20', $CurrAvatarLoc ) ) .'" alt="Avatar" />';
						} else {
							echo '<img id="postavatar" src="'. plugin_dir_url( __FILE__ ) . 'images/missing_avatar.png" alt="'. __('Avatar Does Not Exist', 'gklpa') .'" />';
						}
					} else {
						echo '<img id="postavatar" src="'. plugin_dir_url( __FILE__ ) . 'images/no_avatar.png" alt="'. __('No Avatar selected', 'gklpa') .'" />';
					}
				?></td>
			</tr><?php
		}
	?>
	</table>
	<?php wp_nonce_field( plugin_basename( __FILE__ ), 'postuserpic-key' ); 
}

/**
 * Update post avatar
 *
 * @param integer $postid
 */
function gkl_avatar_edit($postid) {
	global $gkl_myAvatarDir;

	if( !isset($postid) )
		$postid = ((int) $_POST['post_ID']);
		
	// verify if this is an auto save routine. 
	// Don't do anything if post has not been submitted
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
		return;		
		
	// verify if this is the post revision routine
	// Don't do anything if this is a revision
	if ( is_int( wp_is_post_revision( $postid ) ) )
		return;
	
	$key = '';	
	if( isset( $_POST['postuserpic-key'] ) ) $key =  $_POST['postuserpic-key'];	
	// origination and intention: Are we in Write Post?
	if ( !wp_verify_nonce( $key, plugin_basename( __FILE__ ) ) )
			return;	
	
	

	// Check permissions. Will probably have to do something about allowing custom post types
	if ( 'page' == $_POST['post_type'] ) {
		if ( !current_user_can( 'edit_page', $postid ) )
			return;
	}  else   {
		if ( !current_user_can( 'edit_post', $postid ) )
			return;
	}	
			
	$meta_value =  esc_attr($_POST['postuserpic']) ;
	$CheckAvatar = $gkl_myAvatarDir . $meta_value;

	// Verify avatar exists
	if ( !empty($meta_value) && !file_exists($CheckAvatar) ) unset($meta_value);

	if( isset($meta_value) && !empty($meta_value) && $meta_value != 'no_avatar.png' ) {
		update_post_meta($postid, 'postuserpic', $meta_value);
	} else {
		delete_post_meta($postid, 'postuserpic');
	}
}

# ----------------------------------------------------------------------------------- #
#                            SETTINGS PAGE AND BACKEND                                #
# ----------------------------------------------------------------------------------- #

/**
 * Create Options Page
 *
 */
function gkl_settings_menu() {
	add_options_page(__('Post Avatar Options', 'gklpa'), 'Post Avatar', 'manage_options', basename(__FILE__), 'gkl_settings_form');
}

/**
 * Options Form
 *
 * Displays the options form and handles validation/sanitization before update
 */
function gkl_settings_form() {
	global $allowedposttags;
	
	$gklpa_mydir = get_option('gklpa_mydir');
	$gklpa_showinwritepage = get_option('gklpa_showinwritepage');
	$gklpa_scanrecursive = get_option('gklpa_scanrecursive');
	$gklpa_showincontent = get_option('gklpa_showincontent');
	$gklpa_class = get_option('gklpa_class');
	$gklpa_before = get_option('gklpa_before');
	$gklpa_after = get_option('gklpa_after');
	$gklpa_getsize = get_option('gklpa_getsize');
	$gklpa_showinfeeds = get_option('gklpa_showinfeeds');

	// Update Post Avatar settings
	if ( isset($_POST['submit']) ) {
	
	   if ( function_exists('current_user_can') && !current_user_can('manage_options') )
	      wp_die(__('Uh oh! You lack permissions to perform this action.', 'gklpa'));
		  
		// origination and intention: Are we in the Post Avatar settings page?
		if ( !wp_verify_nonce( $_POST['gkl_postavatar_form'], plugin_basename( __FILE__ ) ) )
			return;	   
	      
		check_admin_referer( plugin_basename( __FILE__ ),  'gkl_postavatar_form' );

		// Sanitize everything.
		$gklpa_mydir = esc_attr(trailingslashit(rtrim($_POST['gklpa_mydir'], '/')));
		if( isset( $_POST['gklpa_showinwritepage']) )  $gklpa_showinwritepage = gkl_validate_checked($_POST['gklpa_showinwritepage']);
		else $gklpa_showinwritepage = 0;
		
		if( isset( $_POST['gklpa_scanrecursive'] ) ) $gklpa_scanrecursive = gkl_validate_checked($_POST['gklpa_scanrecursive']);
		else $gklpa_scanrecursive = 0;
		
		if( isset( $_POST['gklpa_showincontent'] ) ) $gklpa_showincontent = gkl_validate_checked($_POST['gklpa_showincontent']);		
		else $gklpa_showincontent = 0;
		
		if( isset( $_POST['gklpa_getsize'] ) ) $gklpa_getsize = gkl_validate_checked($_POST['gklpa_getsize']);
		else $gklpa_getsize = 0;
		
		if( isset( $_POST['gklpa_showinfeeds']) ) $gklpa_showinfeeds = gkl_validate_checked($_POST['gklpa_showinfeeds']);
		else $gklpa_showinfeeds = 0;
		
		$gklpa_class = sanitize_html_class($_POST['gklpa_class']); // allow alphanumeric characters only
		$gklpa_before = wp_kses( $_POST['gklpa_before'], $allowedposttags ); 
		$gklpa_after = wp_kses( $_POST['gklpa_after'], $allowedposttags );
		
		// Save the options
		update_option('gklpa_mydir', $gklpa_mydir);
		update_option('gklpa_showinwritepage', $gklpa_showinwritepage);
		update_option('gklpa_scanrecursive', $gklpa_scanrecursive);
		update_option('gklpa_showincontent', $gklpa_showincontent);
		update_option('gklpa_class', $gklpa_class);
		update_option('gklpa_before', $gklpa_before);
		update_option('gklpa_after', $gklpa_after);
		update_option('gklpa_getsize', $gklpa_getsize);
		update_option('gklpa_showinfeeds', $gklpa_showinfeeds);		
	}
?>
<div class=wrap>
	<h2><?php _e('Post Avatar Settings', 'gklpa'); ?></h2>
	<form name="gkl_postavatar" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?page=gkl-postavatar.php&amp;updated=true">
		<input type="hidden" name="gkl_postavatar_options" value="1" /> <?php wp_nonce_field( plugin_basename( __FILE__ ),  'gkl_postavatar_form'); ?>

		<h3><?php _e('Default options', 'gklpa'); ?></h3>
		<table class="form-table">
		<tr valign="top">
		<th scope="row"><?php _e('Path to Images Folder:', 'gklpa'); ?></th>
		<td><input name="gklpa_mydir" type="text" id="gklpa_mydir" value="<?php echo $gklpa_mydir; ?>" size="45" /><br />
		<?php _e('You must not leave this field blank. The directory also must exist.', 'gklpa'); ?>
		</td>
		</tr>
		<tr valign="top">
		<th scope="row"><?php _e('Display', 'gklpa'); ?></th>
		<td><input name="gklpa_showinwritepage" type="checkbox" value="1" <?php checked('1', $gklpa_showinwritepage); ?> /> <?php _e('Show image in Write Post Page?', 'gklpa'); ?><br />
		
		<input name="gklpa_showincontent" type="checkbox" value="1" <?php checked('1', $gklpa_showincontent); ?> /> <?php _e('Show avatar in post? Disable to use template tag', 'gklpa'); ?>
		
		</td>
		</tr>
		

		<tr valign="top">
		<th scope="row"><?php _e('Others', 'gklpa'); ?></th>
		<td><input name="gklpa_scanrecursive" type="checkbox" value="1" <?php checked('1', $gklpa_scanrecursive); ?> /> <?php _e('Scan the images directory and its sub-directories?', 'gklpa'); ?><br />
		
		<input name="gklpa_getsize" type="checkbox" value="1" <?php checked('1', $gklpa_getsize); ?> /> <?php _e('Get image dimensions. Disable this feature if you encounter getimagesize errors', 'gklpa'); ?><br />
		
		<input name="gklpa_showinfeeds" type="checkbox" value="1" <?php checked('1', $gklpa_showinfeeds); ?> /> <?php _e('Check here to display post avatars in your rss feeds', 'gklpa'); ?>
		
		</td>
		</tr>
		</table>
		
		<h3><?php _e('Customize HTML/CSS', 'gklpa'); ?></h3>

		<table class="form-table">
		<tr valign="top">
		<th scope="row"><?php _e('HTML', 'gklpa'); ?></th>
		<td><?php _e('Use this HTML before/after the post avatar image', 'gklpa'); ?><br />
		<input name="gklpa_before" type="text" value="<?php echo esc_html( stripslashes($gklpa_before ) ); ?>" /> / <input name="gklpa_after" type="text" value="<?php echo esc_html( stripslashes($gklpa_after ) ); ?>" /><br />
		<?php _e('You can leave this field blank.', 'gklpa'); ?>
		</td>
		</tr>
		
		<tr valign="top">
		<th scope="row"><?php _e('CSS', 'gklpa'); ?></th>
		<td><?php _e('Use this CSS class for the post avatar image', 'gklpa'); ?><br />
		<input name="gklpa_class" type="text" value="<?php echo esc_attr( $gklpa_class ); ?>" /><br />
		<?php _e('You can leave this field blank.', 'gklpa'); ?>
		</td>
		</tr>
		</table>
		
		<p class="submit"><input type="submit" name="submit" value="<?php _e('Save Changes', 'gklpa') ?> &raquo;" /></p>

		</form>
</div><?php
}

/**
 * Validate checked options
 *
 * @param string $option
 * @return $value
 */
function gkl_validate_checked($option) {

	$value = intval($option);
	if (!empty($value)) 
		$value = 1;
	
	return $value;
}

/**
 * Installation function
 *
 * Performs a version check to make sure WordPress is 3.0 or greater,
 * creates `post_avatar` capability
 * and creates the default options
 */
function gkl_install(){
	global $wp_version; 
	if ( version_compare( $wp_version , "3.0", "<" ) ) { 
		deactivate_plugins( basename( __FILE__ ) ); // Deactivate our plugin
		wp_die('This plugin requires WordPress version 2.9 or higher.', 'gklpa' );
	}
	
	// Set default options
	add_option('gklpa_mydir', 'wp-content/uploads/icons/', '', 'yes');
	add_option('gklpa_showinwritepage', 1, '', 'yes');
	add_option('gklpa_scanrecursive', 1, '', 'yes');
	add_option('gklpa_showincontent', 1, '', 'yes');
	add_option('gklpa_class', '', '', 'yes');
	add_option('gklpa_before', '<div class="postavatar">', '', 'yes');
	add_option('gklpa_after', '</div>', '', 'yes');
	add_option('gklpa_getsize', 1 , '', 'yes');
	add_option('gklpa_showinfeeds', 0, '', 'yes');
	
	// Create capability
	$role = get_role('administrator');
	if(!$role->has_cap('post_avatars'))
		$role->add_cap('post_avatars');

	$role = get_role('editor');
	if(!$role->has_cap('post_avatars'))
		$role->add_cap('post_avatars');
			
	$role = get_role('author');
	if(!$role->has_cap('post_avatars'))
		$role->add_cap('post_avatars');
	
}

/**
 * Actions to run inside admin
 *
 */
function gkl_admin_init(){
	// Add meta box and 
	add_meta_box('postavatardiv', __('Post Avatar', 'gklpa'), 'gkl_postavatar_metabox_admin', 'post');
	add_action('admin_head', 'gkl_admin_head');
	// Save image data		
	add_action('save_post', 'gkl_avatar_edit');
}

/** 
 * Actions to run everywhere 
 *
 */
function gkl_init(){
	wp_register_script( 'gkl_postavatar_js', plugins_url('head/gkl-postavatar.js',  __FILE__ ), array(), NULL );
	wp_register_style('gkl_postavatar_css', plugins_url('head/gkl-postavatar.css', __FILE__), array(), NULL );
}

/** 
 * Enqueues the javascript file
 *
 */
function gkl_display_script(){
	wp_enqueue_script( 'gkl_postavatar_js');
}

/** 
 * Enqueues the css file
 *
 */
function gkl_display_css(){
	global $gkl_ShowInContent;
	if( is_admin() ) wp_enqueue_style('gkl_postavatar_css');  
		
	// Display the stylesheet in the front end it automatic display is turned on.
	if ( $gkl_ShowInContent == 1 ) 	wp_enqueue_style('gkl_postavatar_css');  
}

/**
 * Prints js- and css-code in the admin-head-area
 *
 */
function gkl_admin_head() {
	global $gkl_AvatarURL, $gkl_ShowAvatarInPost, $gklpa_siteurl, $pagenow;
	
	if( $pagenow == 'post-new.php' || $pagenow == 'post.php' ):
	?>	<script type="text/javascript">
		//<![CDATA[
			var gkl_site = "<?php echo $gklpa_siteurl; ?>";
			var gkl_avatar = "<?php echo $gkl_AvatarURL ; ?>";
			var gkl_avatar_img = "<?php echo plugin_dir_url( __FILE__ ) . 'images'; ?>";
		//]]>
		</script>
	<?php
	endif; 
}

# ----------------------------------------------------------------------------------- #
#                               DEPRECATED FUNCTIONS                                  #
# ----------------------------------------------------------------------------------- #
function gkl_dev_override($deprecated_override = false) {
	global $gkl_dev_override;
	_deprecated_argument( __FUNCTION__, 'Post Avatar 1.4', __('Use <code>remove_filter</code> to override the automatic theme display', 'gklpa') );
	$gkl_dev_override = $deprecated_override;
}

/**
 * Display html characters
 *
 * @deprecated Post Avatar 1.4
 * @deprecated Use esc_html() instead
 *
 * @param string $value
 * @return $value
 */
function gkl_unescape_html($value) {
	_deprecated_argument( __FUNCTION__, 'Post Avatar 1.4', __('Using <code>esc_html()</code> to convert html for display in input boxes/text area', 'gklpa') );
	return str_replace(
		array("&lt;", "&gt;", "&quot;", "&amp;"),
		array("<", ">", "\"", "&"),
		$value);
}

/**
 * Checks, whether one of two strings are substrings of PHP_SELF
 *
 * @return boolean
 */
function gkl_check_phpself() {
	if (substr_count($_SERVER['PHP_SELF'], '/wp-admin/post.php') == 1 
		|| substr_count($_SERVER['PHP_SELF'], '/wp-admin/page.php') == 1 
		|| substr_count($_SERVER['PHP_SELF'], '/wp-admin/page-new.php') == 1 || substr_count($_SERVER['PHP_SELF'], '/wp-admin/post-new.php') == 1 
		|| substr_count($_SERVER['PHP_SELF'], '/wp-admin/edit.php') == 1)
		return true;
	else
		return false;
}

# ----------------------------------------------------------------------------------- #
#                               HOOKS AND FILTERS                                     #
# ----------------------------------------------------------------------------------- #

register_activation_hook( __FILE__, 'gkl_install' );	// Installation
add_action( 'admin_init', 'gkl_admin_init' );			// Post Meta Box
add_action( 'init', 'gkl_init' );						// JavaScript and CSS files are registered
add_action( 'admin_menu', 'gkl_settings_menu');			// Settings

// Displays the CSS and JavaScript files in the appropriate places
add_action('admin_print_scripts-post.php', 'gkl_display_script' );
add_action('admin_print_scripts-post-new.php', 'gkl_display_script' );
add_action('admin_print_styles-post.php', 'gkl_display_css' );
add_action('admin_print_styles-post-new.php','gkl_display_css' );
add_action('wp_print_styles', 'gkl_display_css' );

// Content Filters
add_filter('the_content', 'gkl_postavatar_feed_filter');			// Feed Filter
// Automatic Display	
if ($gkl_ShowInContent == 1){
	add_filter('the_content', 'gkl_postavatar_filter', 99);			
	add_filter('the_excerpt', 'gkl_postavatar_filter', 99);
}
?>