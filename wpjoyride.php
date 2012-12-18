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

function joyride_init(){

		$plugin_uri = plugin_dir_url( __FILE__ );

	/* load css */
		wp_enqueue_style( 'joyride-css', $plugin_uri . 'joyride/joyride-2.0.2.css' );

	/* load js */
		wp_enqueue_script( 'jquery-cookie', $plugin_uri . 'joyride/jquery.cookie.js', array('jquery') );
		wp_enqueue_script( 'jquery-modernizr', $plugin_uri . 'joyride/modernizr.mq.js', array('jquery') );
		wp_enqueue_script( 'jquery-joyride', $plugin_uri . 'joyride/jquery.joyride-2.0.2.js', array('jquery') );
		wp_enqueue_script( 'jquery-joyride-init', $plugin_uri . 'joyride/plugin.js', array('jquery', 'jquery-joyride') );		

}

add_action( 'admin_init', 'joyride_init' );

/* support widget */

// Create the function to output the contents of our Dashboard Widget

function tips_dashboard_widget_function() {

    echo '<p>Below are interactive tours of the WordPress admin which detail how to manage content, step by step:</p>';

    //define tour links : title => URL
    $tour_links = array(
    	'How to edit the site title'	=>	'/wp-admin/options-general.php#tour-title'
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

// Hook into the 'wp_dashboard_setup' action to register our other functions

add_action('wp_dashboard_setup', 'tips_add_dashboard_widgets' ); // Hint: For Multisite Network Admin Dashboard use wp_network_dashboard_setup instead of wp_dashboard_setup.
