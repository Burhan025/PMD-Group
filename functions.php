<?php
//* Start the engine
include_once( get_template_directory() . '/lib/init.php' );

//* Setup Theme
include_once( get_stylesheet_directory() . '/lib/theme-defaults.php' );

//* Subpage Header Code
require_once('subpage-header.php');

//* Portfolio Post type
require_once('portfolio-post-type.php');

//* Set Localization (do not remove)
load_child_theme_textdomain( 'parallax', apply_filters( 'child_theme_textdomain', get_stylesheet_directory() . '/languages', 'parallax' ) );

//* Add Image upload to WordPress Theme Customizer
add_action( 'customize_register', 'parallax_customizer' );
function parallax_customizer(){
	require_once( get_stylesheet_directory() . '/lib/customize.php' );
}

//* Include Section Image CSS
include_once( get_stylesheet_directory() . '/lib/output.php' );

global $blogurl;
$blogurl = get_stylesheet_directory_uri();

//* Enqueue scripts and styles
add_action( 'wp_enqueue_scripts', 'parallax_enqueue_scripts_styles' );
function parallax_enqueue_scripts_styles() {
	// Styles
	wp_enqueue_style( 'dashicons' );
	wp_enqueue_style( 'custom', get_stylesheet_directory_uri() . '/custom.css', array() );
	wp_enqueue_style( 'gravityforms', get_stylesheet_directory_uri() . '/css/formsmain.css', array('custom') );
	wp_enqueue_style( 'mediaqueries', get_stylesheet_directory_uri() . '/css/mediaqueries.css', array() );
	wp_enqueue_style( 'fontawesome', get_stylesheet_directory_uri() . '/fonts/css/font-awesome.min.css', array() );
	wp_enqueue_style( 'googlefonts', '//fonts.googleapis.com/css?family=Fjalla+One|Montserrat:200,300,400,500,600,700', array() );	

	// Scripts
	wp_enqueue_script( 'responsive-menu-js', get_stylesheet_directory_uri() . '/js/responsive-menu/responsive-menu.js', array( 'jquery' ), '1.0.0' );
	wp_enqueue_script( 'scripts', get_stylesheet_directory_uri() . '/js/scripts.js', array() );
	wp_enqueue_script( 'fancyBox-js', get_bloginfo( 'stylesheet_directory' ) . '/js/fancy/jquery.fancybox.js', array( 'jquery' ), '1.0.0', true );

	// Examples & Optionals:
	wp_enqueue_style( 'fancyBoxcss', get_stylesheet_directory_uri() . '/js/fancy/jquery.fancybox.css', array() );
	
}

// Removes Query Strings from scripts and styles
function remove_script_version( $src ){
  if ( strpos( $src, 'uploads/bb-plugin' ) !== false || strpos( $src, 'uploads/bb-theme' ) !== false ) {
    return $src;
  }
  else {
    $parts = explode( '?ver', $src );
    return $parts[0];
  }
}
add_filter( 'script_loader_src', 'remove_script_version', 15, 1 );
add_filter( 'style_loader_src', 'remove_script_version', 15, 1 );


//* Add HTML5 markup structure
add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );

//* Add viewport meta tag for mobile browsers
add_theme_support( 'genesis-responsive-viewport' );

//* Reposition the primary navigation menu
remove_action( 'genesis_after_header', 'genesis_do_nav' );
add_action( 'genesis_header', 'genesis_do_nav', 12 );

//* Reposition the secondary navigation menu
remove_action( 'genesis_after_header', 'genesis_do_subnav' );
remove_action( 'genesis_footer', 'genesis_do_subnav', 7 );

// Add Search to Primary Nav
//add_filter( 'genesis_header', 'genesis_search_primary_nav_menu', 10 );
function genesis_search_primary_nav_menu( $menu ){
    locate_template( array( 'searchform-header.php' ), true );
}

//* Add support for structural wraps
add_theme_support( 'genesis-structural-wraps', array(
	'header',
	'nav',
	'subnav',
	'breadcrumb',
	'footer-widgets',
	'footer',
) );

// Add Read More Link to Excerpts
add_filter('excerpt_more', 'get_read_more_link');
add_filter( 'the_content_more_link', 'get_read_more_link' );
function get_read_more_link() {
   return '...&nbsp;<a class="readmore" href="' . get_permalink() . '">Read&nbsp;More &raquo;</a>';
}

// Add Beaver Builder Editable Footers to the Genesis Footer hook
add_action( 'genesis_before_footer', 'global_footer', 2 );
function global_footer(){
echo do_shortcode('[fl_builder_insert_layout slug="global-footer"]');
}

//* Add support for 4-column footer widgets
add_theme_support( 'genesis-footer-widgets', 1 );

//* Customize the entry meta in the entry header (requires HTML5 theme support)
add_filter( 'genesis_post_info', 'sp_post_info_filter' );
function sp_post_info_filter($post_info) {
	$post_info = '[post_date] [post_comments] [post_edit]';
	return $post_info;
}

//* Custom Breadcrumb Hook 
function breadcrumb_hook() {
	do_action('breadcrumb_hook');
}

//* Remove breadcrumbs and reposition them
remove_action( 'genesis_before_loop', 'genesis_do_breadcrumbs' );
add_action( 'breadcrumb_hook', 'genesis_do_breadcrumbs', 12 );

// Modify Breadcrumbs Args
add_filter( 'genesis_breadcrumb_args', 'malcolm_breadcrumb_args' );
function malcolm_breadcrumb_args( $args ) {
	$args['prefix'] = '<div class="breadcrumbs"><div class="wrap">';
	$args['suffix'] = '</div></div>';
	$args['sep'] = ' <span class="bread-sep">&raquo;</span> ';
	$args['heirarchial_attachments'] = true;
	$args['heirarchial_categories'] = true;
	$args['display'] = true;
	$args['labels']['prefix'] = '';
    return $args;
}
// Blog Widgets
genesis_register_sidebar( array(
	'id'			=> 'blog-sidebar',
	'name'			=> __( 'Blog Widgets', 'thrive' ),
	'description'	=> __( 'This is latest news widget', 'thrive' ),
) );

// Add Header Links Widget to Header
//add_action( 'genesis_before', 'header_widget', 1 );
	function header_widget() {
	if (is_active_sidebar( 'header-links' ) ) {
 	genesis_widget_area( 'header-links', array(
		'before' => '<div class="header-links">',
		'after'  => '</div>',
	) );
}}

// Unregister unused sidebar
//unregister_sidebar( 'header-right' );

// Previous / Next Post Navigation Filter For Genesis Pagination
add_filter( 'genesis_prev_link_text', 'gt_review_prev_link_text' );
function gt_review_prev_link_text() {
        $prevlink = '&laquo;';
        return $prevlink;
}
add_filter( 'genesis_next_link_text', 'gt_review_next_link_text' );
function gt_review_next_link_text() {
        $nextlink = '&raquo;';
        return $nextlink;
}

/* Subpage Header Backgrounds - Utilizes: Featured Images & Advanced Custom Fields Repeater Fields */

// AFC Repeater Setup - NOTE: Set Image Return Value to ID
// Row Field Name:
$rows = '';
$rows = get_field('subpage_header_backgrounds', 5);
// Counts the rows and selects a random row
$row_count = count($rows);
$i = rand(0, $row_count - 1);
// Set Image size to be returned
$image_size = 'subpage-header';
// Get Image ID from the random row
$image_id = $rows[ $i ]['background_image'];
// Use Image ID to get Image Array
$image_array = wp_get_attachment_image_src($image_id, $image_size);
// Set "Default BG" to first value of the Image Array. $image_array[0] = URL;
$default_bg = $image_array[0]; 


// Custom function for getting background images
function custom_background_image($postID = "") {
	// Variables
	global $default_bg;
	global $postID;
	global $blog_slug;
	
	$currentID = get_the_ID();
	$blogID = get_option( 'page_for_posts');
	$parentID = wp_get_post_parent_id( $currentID );

	// is_home detects if you're on the blog page- must be set in admin area
	if( is_home() ) {
		$currentID = $blogID;
	} 
	// Else if post page, set ID to BlogID.
	elseif( is_home() || is_single() || is_archive() || is_search() ) {
		$currentID = $blogID;
	}

	// Try to get custom background based on current page/post
	$currentBackground = wp_get_attachment_image_src(get_post_thumbnail_id($currentID), 'subpage-header');
	//Current page/post has no custom background loaded
	if(!$currentBackground) {
		// Find blog ID
		$blog_page = get_page_by_path($blog_slug, OBJECT, 'page');
		if ($blog_page) {
			$blogID = $blogID;
			$currentID = $blogID;
		}
		// Else if post page, set ID to BlogID.
		elseif(is_single() || is_archive()) {
			$currentID = $blogID; 
		}

		// Current page has a parent
		if($parentID) {
			// Try to get parents custom background
			$parent_background = wp_get_attachment_image_src(get_post_thumbnail_id($parentID), 'subpage-header');
			// Set parent background if it exists
			if($parent_background) {
				$background_image = $parent_background[0];
			}
			// Set default background
			else {
				$background_image = $default_bg;
			}
		}
		// NO parent or no parent background: set default bg.
		else {
			$background_image = $default_bg;
		}
	}
	// Current Page has a custom background: use that
	else {
		$background_image = $currentBackground[0];
	}
	return $background_image;
}

/* Changing the Copyright text */
function genesischild_footer_creds_text () {
	global $blogurl;
 	echo '<div class="clearboth copy-line">
 			<div class="copyright first">
 				<p><span id="copy">&copy; '. date("Y") .' PMD GROUP</span> <span class="format-pipe">&#124;</span>  
	 			<a href="/sitemap/">Sitemap</a> 
	 			</p>
 			</div>
 			<div class="credits">
 				<span>Site by</span>
 				<a target="_blank" href="http://www.pmdgrp.com/">
 					<img class="svg" src="'.  $blogurl . '/images/siteby-logo.jpg" alt="PMD Group - Credit Union Marketing and Advertising">
 				</a>
 			</div>
 		  </div>';
}
add_filter( 'genesis_footer_creds_text', 'genesischild_footer_creds_text' );


//* Reposition the primary navigation menu
remove_action( 'genesis_after_header', 'genesis_do_nav' );
add_action( 'genesis_after_header', 'genesis_do_nav', 12 );

// Add Additional Image Sizes
add_image_size( 'genesis-post-thumbnail', 469, 216, true );
add_image_size( 'subpage-header', 1920, 388, true );
add_image_size( 'news-thumb', 260, 150, false );
add_image_size( 'news-full', 800, 300, false );
add_image_size( 'sidebar-thumb', 200, 150, false );
add_image_size( 'portfolio-thumb-home', 372, 270, false );
add_image_size( 'masanory-portfolio', 382, 9999, false );
add_image_size( 'single-portfolio', 274, 9999, false );
add_image_size( 'mailchimp', 564, 9999, false );


// Gravity Forms confirmation anchor on all forms
add_filter( 'gform_confirmation_anchor', '__return_true' );


// Button Shortcode
// Usage: [button url="https://www.google.com"] Button Shortcode [/button]
function button_shortcode($atts, $content = null) {
  extract( shortcode_atts( array(
	  'url' => '#',
	  'target' => '_self',
	  'onclick' => '',

  ), $atts ) 
);
return '<a target="' . $target . '" href="' . $url . '" class="button" onClick="' . $onclick . '"><span>' . do_shortcode($content) . '</span></a>';
}
add_shortcode('button', 'button_shortcode');

// Link Shortcode
// Usage: [link url=”tel:1-817-447-9194″ onClick=”onClick=”ga(‘send’, ‘event’, { eventCategory: ‘Click to Call’, eventAction: ‘Clicked Phone Number’, eventLabel: ‘Header Number’});”]
function link_shortcode($atts, $content = null) {
  extract( shortcode_atts( array(
	  'url' => '#',
	  'target' => '_self',
	  'onclick' => '',
  ), $atts ) 
);
return '<a class="calltoaction" target="' . $target . '" href="' . $url . '" onClick="' . $onclick . '">' . do_shortcode($content) . '</a>';
}
add_shortcode('link', 'link_shortcode');

//* Declare WooCommerce support
add_action( 'after_setup_theme', 'woocommerce_support' );
function woocommerce_support() {
    add_theme_support( 'woocommerce' );
}

// Advance Custom field for Scheme Markups will be output under wphead tag
add_action('wp_head', 'add_scripts_to_wphead');
function add_scripts_to_wphead() {
	if( get_field('custom_javascript') ):	
		echo get_field('custom_javascript', 5);
	endif;
}

// Run shortcodes in Text Widgets
add_filter('widget_text', 'do_shortcode');


//Removing unused Default Wordpress Emoji Script - Performance Enhancer
function disable_emoji_dequeue_script() {
    wp_dequeue_script( 'emoji' );
}
add_action( 'wp_print_scripts', 'disable_emoji_dequeue_script', 100 );
remove_action( 'wp_head', 'print_emoji_detection_script', 7 ); 
remove_action( 'wp_print_styles', 'print_emoji_styles' );

// Removes Emoji Scripts 
add_action('init', 'remheadlink');
function remheadlink() {
	remove_action('wp_head', 'rsd_link');
	remove_action('wp_head', 'wp_generator');
	remove_action('wp_head', 'index_rel_link');
	remove_action('wp_head', 'wlwmanifest_link');
	remove_action('wp_head', 'feed_links', 2);
	remove_action('wp_head', 'feed_links_extra', 3);
	remove_action('wp_head', 'parent_post_rel_link', 10, 0);
	remove_action('wp_head', 'start_post_rel_link', 10, 0);
	remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
	remove_action('wp_head', 'wp_shortlink_header', 10, 0);
	remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
}



//* Add custom Viewport meta tag for mobile browsers
add_action( 'genesis_meta', 'thrive_dns_prefetching' );
function thrive_dns_prefetching() {
	// Set Homepage Domains to Prefetch/Preconnect
	if( class_exists('acf') && is_front_page() ) {
		// check for domains
		if( have_rows('homepage_dns_prefetching_preconnect', 5) ):
		 	// List all domains
		 	echo '<meta http-equiv="x-dns-prefetch-control" content="on">';
		    while ( have_rows('homepage_dns_prefetching_preconnect', 5) ) : the_row();
				echo '<link rel="dns-prefetch" href="//' . get_sub_field('add_domain') . '">';
				echo '<link rel="preconnect" href="/' . get_sub_field('add_domain') . '">';
		    endwhile;
		endif;
	}
	elseif( class_exists('acf') && (is_single() || is_home() || is_page()) ) {
		// check for domains
		if( have_rows('subpage_dns_prefetching_preconnect', 5) ):
		 	// List all domains
		 	echo '<meta http-equiv="x-dns-prefetch-control" content="on">';
		    while ( have_rows('subpage_dns_prefetching_preconnect', 5) ) : the_row();
				echo '<link rel="dns-prefetch" href="//' . get_sub_field('add_domain') . '">';
				echo '<link rel="preconnect" href="/' . get_sub_field('add_domain') . '">';
		    endwhile;
		endif;
	}
	else {
		// If all else fails do nothing
	}	
}

// Simply remove anything that looks like an archive title prefix ("Archive:", "Foo:", "Bar:").
add_filter('get_the_archive_title', function ($title) {
    return preg_replace('/^\w+: /', '', $title);
});

// Enable Featured Images in RSS Feed
function featuredtoRSS($content) {
global $post;
if ( has_post_thumbnail( $post->ID ) ){
$content = '<div>' . get_the_post_thumbnail( $post->ID, 'mailchimp', array( 'style' => 'margin-bottom: 15px;' ) ) . '</div>' . $content;
}
return $content;
}
 
add_filter('the_excerpt_rss', 'featuredtoRSS');
add_filter('the_content_feed', 'featuredtoRSS');

function hide_jetpack_icon() {
    wp_register_style( 'custom_wp_admin_jetpack_css', get_stylesheet_directory_uri() . '/jetpack_custom.css', false, '1.0.0' );
    wp_enqueue_style( 'custom_wp_admin_jetpack_css' );
}
add_action( 'admin_enqueue_scripts', 'hide_jetpack_icon' );


 
 //* Custom RSS:

/**
 * Initialise RSS feed
 * @see https://developer.wordpress.org/reference/functions/add_feed/
 */
function init_rss_feed_custom( ) {
    add_feed('html', 'add_rss_feed_custom');
}
add_action('init', 'init_rss_feed_custom');




/** 
 * Generates Events Master List Feed RSS
 * URL: /feed/?feed=alg_events_master_list
 * 
 * @see https://developer.wordpress.org/reference/functions/add_feed/
 */
function add_rss_feed_custom ( ) {
header( 'Content-Type: ' . feed_content_type( 'rss2' ) . '; charset=' . get_option( 'blog_charset' ), true );
$more = 1;

echo '<?xml version="1.0" encoding="' . get_option( 'blog_charset' ) . '"?' . '>';
?>
<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
	<?php
	/**
	 * Fires at the end of the RSS root to add namespaces.
	 *
	 * @since 2.0.0
	 */
	do_action( 'rss2_ns' );
	?>
>

<channel>
	<title><?php wp_title_rss(); ?></title>
	<atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
	<link><?php bloginfo_rss( 'url' ); ?></link>
	<description><?php bloginfo_rss( 'description' ); ?></description>
	<lastBuildDate><?php echo get_feed_build_date( 'r' ); ?></lastBuildDate>
	<language><?php bloginfo_rss( 'language' ); ?></language>
	<sy:updatePeriod>
	<?php
		$duration = 'hourly';

		/**
		 * Filters how often to update the RSS feed.
		 *
		 * @since 2.1.0
		 *
		 * @param string $duration The update period. Accepts 'hourly', 'daily', 'weekly', 'monthly',
		 *                         'yearly'. Default 'hourly'.
		 */
		echo apply_filters( 'rss_update_period', $duration );
	?>
	</sy:updatePeriod>
	<sy:updateFrequency>
	<?php
		$frequency = '1';

		/**
		 * Filters the RSS update frequency.
		 *
		 * @since 2.1.0
		 *
		 * @param string $frequency An integer passed as a string representing the frequency
		 *                          of RSS updates within the update period. Default '1'.
		 */
		echo apply_filters( 'rss_update_frequency', $frequency );
	?>
	</sy:updateFrequency>
	<?php
	/**
	 * Fires at the end of the RSS2 Feed Header.
	 *
	 * @since 2.0.0
	 */
	do_action( 'rss2_head' );

	while ( have_posts() ) :
		the_post();


		$content = get_the_content();
$content = preg_replace("/<img[^>]+\>/i", " ", $content);          
$content = apply_filters('the_content', $content);
$content = str_replace(']]>', ']]>', $content);


		?>
	<item>
		<title><?php the_title_rss(); ?></title>
		<link><?php the_permalink_rss(); ?></link>
		<?php if ( get_comments_number() || comments_open() ) : ?>
		<comments><?php comments_link_feed(); ?></comments>
		<?php endif; ?>
		<pubDate><?php echo mysql2date( 'D, d M Y H:i:s +0000', get_post_time( 'Y-m-d H:i:s', true ), false ); ?></pubDate>
		<dc:creator><![CDATA[<?php the_author(); ?>]]></dc:creator>
		<?php the_category_rss( 'rss2' ); ?>
		<guid isPermaLink="false"><?php the_guid(); ?></guid>
		<?php if ( get_option( 'rss_use_excerpt' ) ) : ?>
		
		<description>
	    <![CDATA[<?php if ( has_post_thumbnail( $_post->ID ) ) {
		        echo get_the_post_thumbnail( $_post->ID, 'mailchimp' );
	    	}?><?php echo $content; ?>]]></description>
		<?php else : ?>
		<description>
			
			<![CDATA[<?php if ( has_post_thumbnail( $_post->ID ) ) {
		        echo get_the_post_thumbnail( $_post->ID, 'mailchimp' );
	    	}?> <?php echo $content; ?>]]></description>
			<?php $content = get_the_content_feed( 'rss2' ); ?>
			<?php if ( strlen( $content ) > 0 ) : ?>
		<content:encoded><![CDATA[ <?php echo $content; ?>]]></content:encoded>
	<?php else : ?>
		<content:encoded><![CDATA[<?php the_excerpt_rss(); ?>]]></content:encoded>
	<?php endif; ?>
		<?php endif; ?>
		<?php if ( get_comments_number() || comments_open() ) : ?>
		<wfw:commentRss><?php echo esc_url( get_post_comments_feed_link( null, 'rss2' ) ); ?></wfw:commentRss>
		<slash:comments><?php echo get_comments_number(); ?></slash:comments>
		<?php endif; ?>
		<?php rss_enclosure(); ?>
		<?php
		/**
		 * Fires at the end of each RSS2 feed item.
		 *
		 * @since 2.0.0
		 */
		do_action( 'rss2_item' );
		?>
	</item>
	<?php endwhile; ?>
</channel>
</rss>


<?php

}