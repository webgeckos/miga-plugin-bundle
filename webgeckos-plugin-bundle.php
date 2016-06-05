<?php
/*
Plugin Name:  Miga Plugin Bundle
Plugin URI:   http://webgeckos.com
Description:  Theme specific plugins selection for WebGeckos WordPress Themes
Version:      1.0.0
Author:       Danijel Rose
Author URI:   http://webgeckos.com
Text Domain:  miga
License:      GPL-2.0+
License URI:  http://www.gnu.org/licenses/gpl-2.0.txt
*/

/*
    1. ENQUEUE SCRIPTS
    2. ACTION HOOKS
    3. FILTER HOOKS
    4. SHORTCODES
    5. CUSTOM POST TYPES
    6. CUSTOM TAXONOMIES
    7. OTHER FUNCTIONS
*/

// Exit if accessed directly
if ( ! defined('ABSPATH') ) {
  exit;
}

/* 1. ENQUEUE SCRIPTS */

// Enqueue scripts for countdown timecircles
function scripts_for_countdownclock() {
    wp_register_style('timecirclecss', plugins_url() . '/miga-plugin-bundle/css/TimeCircles.css');
    wp_register_script('timecirclejquery', plugins_url() . '/miga-plugin-bundle/js/TimeCircles.js', array('jquery'), '', true);
    wp_register_script('timecirclescript', plugins_url() . '/miga-plugin-bundle/js/timecirclescript.js', array('timecirclejquery'), '', true);
}

/* 2. ACTION HOOKS */

// hook for enqueuing scripts for countdown timecircles
add_action('wp_enqueue_scripts', 'scripts_for_countdownclock');

// hook for registering the shortcodes
add_action('init', 'miga_register_shortcodes');

// hook for registering custom post types and taxonomies
add_action( 'init', 'miga_init' );

// hook for setting default term for taxonomy display-locations
add_action( 'save_post', 'miga_set_default_object_terms', 100, 2 );

/* 3. FILTER HOOKS */

/* 4. SHORTCODES */

// register shortcodes
function miga_register_shortcodes() {
	add_shortcode('time-circle', 'miga_countdown_shortcode');
  add_shortcode('miga_btn', 'miga_button_shortcode');
}

// adding shortcode for countdown timecircles
function miga_countdown_shortcode($atts, $content = null){
    $timecircledata = shortcode_atts(array(
        'data_date' => '0',
        'data_timer' => '0',
        'time_animation' => 'ticks',
        'timestop' => '0',
        'message' => 'time has ended',
        'circle_bg_color' => '#000000'
    ),$atts);
    wp_enqueue_style('timecirclecss');
    wp_enqueue_script('timecirclejquery');
    wp_enqueue_script('timecirclescript');
    wp_localize_script('timecirclescript','timecircledata2',$timecircledata);
    $d_date = $timecircledata['data_date'];
    $d_timer = $timecircledata['data_timer'];
    if ($d_timer !=='0') {
        $countdownoutput = '<div class="timecircle" data-timer="'.$d_timer.'"></div>';
    }
    elseif ($d_date !=='0') {
        $countdownoutput = '<div class="timecircle" data-date="'.$d_date.'"></div>';
    }
    else {
        $countdownoutput = '<div class="timecircle" data-timer="3600"></div>';
    }
    return $countdownoutput;
}

// adding shortcode for custom button -> doesn't work with excerpt -> use the_content!!!
function miga_button_shortcode($atts) {
    extract(shortcode_atts(array(
        'text' => 'more',
        'url' => 'http://yourdomain.com',
        ),$atts));
    return sprintf('<br><a href="%1$s" class="btn btn-lg">%2$s</a><br><br>',
        esc_url( $url ),
        esc_html( $text )
    );
}

/* 5. CUSTOM POST TYPES */

if ( ! function_exists( 'miga_init' ) ) :

  function miga_init() {

  /*
  * Register custom post types
  */

  register_post_type('sections', array(
      'labels'        => array(
              'name' => __( 'Sections', 'miga' ),
              'singular_name' => __( 'Section', 'miga' )
          ),
      'public'        => true,
      'supports'      => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
      'menu_icon'     => 'dashicons-admin-generic',
      'has_archive'   => true,
      'show_in_menu'  => true,
      'taxonomies'    => array( 'display-locations', 'display-options' )
  ));

  register_post_type('portfolio', array(
      'labels'        => array(
              'name' => __( 'Portfolio', 'miga' ),
              'singular_name' => __( 'Portfolio', 'miga' )
          ),
      'public'        => true,
      'supports'      => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
      'menu_icon'     => 'dashicons-images-alt2',
      'has_archive'   => true,
      'show_in_menu'  => true,
      'taxonomies'    => array( 'portfolio-categories' )
  ));

  register_post_type('testimonials', array(
      'labels'        => array(
              'name' => __( 'Testimonials', 'miga' ),
              'singular_name' => __( 'Testimonial', 'miga' )
          ),
      'public'        => true,
      'supports'      => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
      'menu_icon'     => 'dashicons-admin-comments',
      'has_archive'   => true,
      'show_in_menu'  => true,
  ));

  register_post_type('pricing-tables', array(
      'labels'        => array(
              'name' => __( 'Pricing Tables', 'miga' ),
              'singular_name' => __( 'Pricing Table', 'miga' )
          ),
      'public'        => true,
      'supports'      => array( 'title', 'editor' ),
      'menu_icon'     => 'dashicons-list-view',
      'has_archive'   => true,
      'show_in_menu'  => true,
  ));

/* 6. CUSTOM TAXONOMIES */

  // Use categories and tags with attachments
  register_taxonomy_for_object_type( 'category', 'attachment' );
  register_taxonomy_for_object_type( 'post_tag', 'attachment' );

/*
* Register custom taxonomies
*/

  register_taxonomy('display-locations', 'sections', array(
      'labels' =>
          array(
              'name' => __( 'Display Locations', 'miga' ),
              'singular_name' => __( 'Display Location', 'miga' ),
              'add_new_item' => __( 'Add New Display Location', 'miga' ),
              'edit_item' => __( 'Edit Display Location', 'miga' ),
      'update_item' => __( 'Update Display Location', 'miga' )
          ),
      'hierarchical' => true,
      'show_admin_column' => true
  ));

  register_taxonomy('portfolio-categories', 'portfolio', array(
      'labels' =>
          array(
              'name' => __( 'Portfolio Categories', 'miga' ),
              'singular_name' => __( 'Portfolio Category', 'miga' ),
              'add_new_item' => __( 'Add New Portfolio Category', 'miga' ),
              'edit_item' => __( 'Edit Portfolio Category', 'miga' ),
              'update_item' => __( 'Update Portfolio Category', 'miga' )
          ),
      'hierarchical' => true,
      'show_admin_column' => true
  ));

  }
endif;

/* 7. OTHER FUNCTIONS */

/**
 * Define default terms for custom taxonomies in WordPress 3.0.1
 *
 * @author    Michael Fields     http://wordpress.mfields.org/
 * @props     John P. Bloch      http://www.johnpbloch.com/
 *
 * @since     2010-09-13
 * @alter     2010-09-14
 *
 * @license   GPLv2
 */
function miga_set_default_object_terms( $post_id, $post ) {
    if ( $post->post_type === 'sections' ) {
        $defaults = array(
            'display-locations' => array( 'home' ), // more default display locations can be defined here
            );
        $taxonomies = get_object_taxonomies( $post->post_type );
        foreach ( (array) $taxonomies as $taxonomy ) {
            $terms = wp_get_post_terms( $post_id, $taxonomy );
            if ( empty( $terms ) && array_key_exists( $taxonomy, $defaults ) ) {
                wp_set_object_terms( $post_id, $defaults[$taxonomy], $taxonomy );
            }
        }
    }
}
