<?php
/*
Plugin Name: WPJoyride
Plugin URI: 
Description: A plugin to create dashboard visual tours using jquery joyride
Version: 1.0
Author: Todd Wilson
Author URI: http://toddwilson.icreativepro.com
License: GPLv2 only
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

function joyride_admin_init(){

		$plugin_uri = plugin_dir_url( __FILE__ );

	/* load css */
		wp_enqueue_style( 'joyride-css', $plugin_uri . 'joyride/joyride-2.1.css' );

	/* load js */
		wp_enqueue_script( 'jquery-cookie', $plugin_uri . 'joyride/jquery.cookie.js', array('jquery') );
		wp_enqueue_script( 'jquery-modernizr', $plugin_uri . 'joyride/modernizr.mq.js', array('jquery') );
		wp_enqueue_script( 'jquery-joyride', $plugin_uri . 'joyride/jquery.joyride-2.1.js', array('jquery') );
		wp_enqueue_script( 'jquery-joyride-init', $plugin_uri . 'joyride/plugin.js', array('jquery', 'jquery-joyride') );
		require( $plugin_url . 'lib/metabox_tips.php');
}

	function get_tours(){
		$args = array(
			'post_type' => 'joyride_tour',
		);
		// The Query
		$the_query = new WP_Query( $args );

		// The Loop
		while ( $the_query->have_posts() ) {
			$the_query->the_post();
			$post = get_post(get_the_ID());
			$tours[] = array(
				'title' => get_the_title(),
				'slug' => $post->post_name,
				'id' => get_the_ID()
			);
		}
		return $tours;
	}

	function get_tips($tour_id){
		$args = array(
			'post_type' => 'joyride_tip',
			'meta_query' => array(
				array(
					'key' => 'wpjoyride_tour', // name of custom field
					'value' => $tour_id, // matches exaclty "123", not just 123. This prevents a match for "1234"
					'compare' => 'LIKE'
				)
			),
			'orderby' => 'meta_value', 
			'meta_key' => 'wpjoyride_tour_order', 
			'order' => 'ASC'
		);		
		// The Query
		$the_query = new WP_Query( $args );		
		// The Loop
		while ( $the_query->have_posts() ) {
			$the_query->the_post();
			$post_id = get_the_ID();
			$data[] = array(
				'title' => get_the_title(),
				'parent_id' => get_post_meta( $post_id, 'wpjoyride_parent_id', true),
				'tip_text' => get_post_meta( $post_id, 'wpjoyride_tip_text', true),
				'tip_location' => get_post_meta( $post_id, 'wpjoyride_tip_location', true),
				'tip_animation' => get_post_meta( $post_id, 'wpjoyride_tip_animation', true),
				'button_text' => get_post_meta( $post_id, 'wpjoyride_button_text', true),
				'tour' => get_post_meta( $post_id, 'wpjoyride_tour', true),
				'tour_order' => get_post_meta( $post_id, 'wpjoyride_tour_order', true),
			);
		}
		return $data;
	}
	function build_tour_json() {
		//build up json object with tour + tips
		$str = "<script id=\"tour_data\" type=\"application/json\">\n";
		$data = array();		
		foreach(get_tours() as $tour){
			// get tips related to this tour
			$tips = get_tips( $tour['id'] );
			foreach($tips as $tip){
				$data[ $tour['slug'] ]['steps'][] = array(
					'id' => ( substr($tip['parent_id'], 0, 1) == '#' ? str_replace('#','',$tip['parent_id']) : NULL ),
					'class' => ( substr($tip['parent_id'], 0, 1) == '.' ? str_replace('.','',$tip['parent_id']) : NULL ),
					'text' => $tip['button_text'],
					'content' => htmlentities( $tip['tip_text'] ),
					'title' => $tip['title'],
					'options' => 'tipLocation:' . $tip['tip_location'] . ';tipAnimation:' . $tip['tip_animation']
				);
			}
		}		
		$str .= json_encode($data);
		$str .= "</script>";
		print $str;

		// embedded styles
		print '<style type="text/css">';
		print 'div.input-wrap { padding: 0px 0 10px; border-bottom: 1px solid #e8e8e8; }';
		print 'div.input-wrap input[type=text] { width: 100%; padding: 5px; }';
		print 'div.input-wrap label { font-weight: bold; }';
		print 'div.input-wrap ul.checkbox-list li { display: inline-block; padding-right: 10px; }';
		print 'div.input-wrap ul.checkbox-list li label {  font-weight: normal !important; }';
		print '</style>';
	}


function joyride_init(){
		// create CPT
		tips_custom_post_type();
}

add_action( 'admin_init', 'joyride_admin_init' );
add_action( 'init', 'joyride_init');
add_action( 'admin_footer', 'build_tour_json');
/* support widget */

// Create the function to output the contents of our Dashboard Widget

function tips_dashboard_widget_function() {

    echo '<p>Below are interactive tours of the WordPress admin which detail how to manage content, step by step:</p>';

    //define tour links : title => URL
    $tour_links = array(
    	'How to edit the site title'	=>	'/wp-admin/options-general.php#website-title'
    );
    
    echo '<div class="tips-widget"><ul>';
    foreach( $tour_links as $key => $value ):
    	printf('<li><a href="%s">%s &raquo;</a></li>', get_site_url() . $value, $key);
	endforeach;
    echo '</ul></div>';
} 

// Create the function use in the action hook

function tips_add_dashboard_widgets() {
	// Global the $wp_meta_boxes variable (this will allow us to alter the array)
	global $wp_meta_boxes;

	// add in our widget function and output with a title
	wp_add_dashboard_widget('tips_dashboard_widget', 'WordPress Support &amp; Tips', 'tips_dashboard_widget_function');	

	// Since there is no way to specific 'side', we have to unset then reset
	// First, we make a backup of your widget
	$my_widget = $wp_meta_boxes['dashboard']['normal']['core']['tips_dashboard_widget'];

	// Second, we unset that part of the array
	unset($wp_meta_boxes['dashboard']['normal']['core']['tips_dashboard_widget']);

	// Lastly, we just add your widget back in
	$wp_meta_boxes['dashboard']['side']['high']['tips_dashboard_widget'] = $my_widget;


} 

// create custom post type to house the tips
function tips_custom_post_type() {

	$labels = array(
	    'name' => 'Tips',
	    'singular_name' => 'Tip',
	    'add_new' => 'Add New',
	    'add_new_item' => 'Add New Tip',
	    'edit_item' => 'Edit Tip',
	    'new_item' => 'New Tip',
	    'all_items' => 'All Tips',
	    'view_item' => 'View Tip',
	    'search_items' => 'Search Tips',
	    'not_found' =>  'No Tips found',
	    'not_found_in_trash' => 'No Tips found in Trash', 
	    'parent_item_colon' => '',
	    'menu_name' => 'Tips'
	  );

	  $args = array(
	    'labels' => $labels,
	    'public' => true,
	    'publicly_queryable' => false,
	    'show_ui' => true, 
	    'show_in_menu' => true, 
	    'query_var' => true,
	    'rewrite' => array( 'slug' => 'tip' ),
	    'capability_type' => 'post',
	    'has_archive' => true, 
	    'hierarchical' => true,
	    'menu_position' => null,
	    'supports' => array( 'title', 'author', 'excerpt' ),
		'capabilities' => array(
		    'edit_post'          => 'update_core',
		    'read_post'          => 'update_core',
		    'delete_post'        => 'update_core',
		    'edit_posts'         => 'update_core',
		    'edit_others_posts'  => 'update_core',
		    'publish_posts'      => 'update_core',
		    'read_private_posts' => 'update_core'
		),
	  ); 

	  register_post_type( 'joyride_tip', $args );

	$labels_tour = array(
	    'name' => 'Tours',
	    'singular_name' => 'Tour',
	    'add_new' => 'Add New',
	    'add_new_item' => 'Add New Tour',
	    'edit_item' => 'Edit Tour',
	    'new_item' => 'New Tour',
	    'all_items' => 'All Tours',
	    'view_item' => 'View Tour',
	    'search_items' => 'Search Tours',
	    'not_found' =>  'No Tours found',
	    'not_found_in_trash' => 'No Tours found in Trash', 
	    'parent_item_colon' => '',
	    'menu_name' => 'Tours'
	  );

	  $args_tour = array(
	    'labels' => $labels_tour,
	    'public' => true,
	    'publicly_queryable' => false,
	    'show_ui' => true, 
	    'show_in_menu' => true, 
	    'query_var' => true,
	    'rewrite' => array( 'slug' => 'tour' ),
	    'capability_type' => 'post',
	    'has_archive' => true, 
	    'hierarchical' => true,
	    'menu_position' => null,
	    'supports' => array( 'title', 'author', 'excerpt' ),
		'capabilities' => array(
		    'edit_post'          => 'update_core',
		    'read_post'          => 'update_core',
		    'delete_post'        => 'update_core',
		    'edit_posts'         => 'update_core',
		    'edit_others_posts'  => 'update_core',
		    'publish_posts'      => 'update_core',
		    'read_private_posts' => 'update_core'
		),
	  ); 

	  register_post_type( 'joyride_tour', $args_tour );

}
// Hook into the 'wp_dashboard_setup' action to register our other functions

add_action('wp_dashboard_setup', 'tips_add_dashboard_widgets' ); // Hint: For Multisite Network Admin Dashboard use wp_network_dashboard_setup instead of wp_dashboard_setup.
