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

require( 'lib/settings_page.php');

function joyride_admin_init(){

		$plugin_uri = plugin_dir_url( __FILE__ );
	/* load css */
		wp_enqueue_style( 'font-awesome', $plugin_uri . 'lib/font-awesome/css/font-awesome.min.css' );
		wp_enqueue_style( 'joyride', $plugin_uri . 'joyride/joyride-2.1.css' );
		wp_enqueue_style('jquery-ui',
                'http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/smoothness/jquery-ui.css',
                false,
                '1.10.3',
                false);
		wp_enqueue_style( 'wptuts-theme-settings', $plugin_uri . 'lib/settings/css/wptuts_theme_settings.css' );

	/* load js */
		wp_enqueue_script( 'jquery-cookie', $plugin_uri . 'joyride/jquery.cookie.js', array('jquery') );
		wp_enqueue_script( 'jquery-modernizr', $plugin_uri . 'joyride/modernizr.mq.js', array('jquery') );
		wp_enqueue_script( 'jquery-joyride', $plugin_uri . 'joyride/jquery.joyride-2.1.js', array('jquery') );
		wp_enqueue_script( 'jquery-joyride-init', $plugin_uri . 'joyride/plugin.js', array('jquery','jquery-ui-core','jquery-ui-sortable','jquery-ui-accordion','jquery-ui-draggable','jquery-ui-droppable','jquery-joyride') );

		require( 'lib/metabox_tips.php');
}

	function get_tours(){
		$args = array(
			'post_type' => 'joyride_tour',
			'order' => 'ASC',
			'orderby' => 'date',
			'posts_per_page' => -1
		);
		// The Query
		$the_query = new WP_Query( $args );

		// The Loop
		while ( $the_query->have_posts() ) {
			$the_query->the_post();
			$post = get_post(get_the_ID());
			$meta_url = get_post_meta( $post->ID, 'tour_url', true );
			$meta_hashtag = get_post_meta( $post->ID, 'tour_hashtag', true );

			$tours[] = array(
				'title' => get_the_title(),
				'slug' => $post->post_name,
				'id' => $post->ID,
				'url' => $meta_url,
				'hashtag' => $meta_hashtag,
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
				'id' => $post_id,
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

	function get_tours_tips() {
		foreach(get_tours() as $tour){
			// get tips from tour, returns array with json encoded strings
			$tips = tip_meta( $tour['id'], 'get' );
			$hashtag = get_post_meta( $tour['id'], 'tour_hashtag', true );
			$url = get_post_meta( $tour['id'], 'tour_url', true );
			$data = array(
				'title' => $tour['title'],
				'hashtag' => $hashtag,
				'url'	=> $url,
				'id' => $tour['id'],
				'steps' => array()
			);

			foreach($tips as $tip){
				$tip = json_decode($tip);
				if( $tip->parent_id && $tip->tip_title ) {
					$data['steps'][] = array(
						'id' => ( substr($tip->parent_id, 0, 1) == '#' ? str_replace('#','',$tip->parent_id) : NULL ),
						'class' => ( substr($tip->parent_id, 0, 1) == '.' ? str_replace('.','',$tip->parent_id) : NULL ),
						'text' => $tip->button_text,
						'content' => htmlentities( stripslashes( $tip->tip_text ) ),
						'title' => $tip->tip_title,
						'options' => 'tipLocation:' . $tip->tip_location . ';tipAnimation:' . $tip->tip_animation
					);
				}
			}
			$tours[] = $data;
		}
		return $tours;
	}
	function build_tour_json() {
		$data = array();		
		$tours = array();
		$tours_groups = array();
		$terms = get_terms('tour_groups');
		foreach($terms as $term){
			$tours_groups[] = array(
				'id' => $term->term_id,
				'title' => $term->name
			);
		}
		$tours = get_tours_tips();

		//build up json object with tour + tips
		$str .= "<script id=\"tours_all\" type=\"application/json\">\n";
		$str .= json_encode($tours);
		$str .= "</script><script id=\"tours_groups\" type=\"application/json\">\n";
		$str .= json_encode($tours_groups);
		$str .= "</script>";
		print $str;
		print getTourBox();
	}


function joyride_init(){
		// create CPT
		tips_custom_post_type();
}

add_action( 'admin_init', 'joyride_admin_init' );
add_action( 'init', 'joyride_init');
add_action( 'admin_footer', 'build_tour_json');

if ( is_admin() )
{
	add_action('wp_ajax_create_tour', 'create_tour_callback');
    add_action('wp_ajax_save_tour', 'save_tour_callback');	
	add_action('wp_ajax_save_tip', 'save_tip_callback');
	add_action('wp_ajax_delete_tip', 'delete_tip_callback');
	add_action('wp_ajax_delete_tour', 'delete_tour_callback');
}


/**
   * ----------------------------------------------
   * Render a Template File
   * ----------------------------------------------
   *
   * @param $filePath
   * @param null $viewData
   * @return string
   */
  function getTemplatePart($filePath, $viewData = null) {

    ($viewData) ? extract($viewData) : null;

    ob_start();
    include ("$filePath");
    $template = ob_get_contents();
    ob_end_clean();

    return $template;
  }

function getTourBox() {
	
	$metaBoxTempl = 'joyride/src/templates/metabox.templ.php';

    // Set data needed in the template
    $viewData = array(
      'tour_id' => 'tour_title',
      'index' => 'New Tour',
      'tour' => null,
      //'tips' => json_encode( $this->tipIds )
    );

    // Output the rendered template
    echo getTemplatePart($metaBoxTempl, $viewData);

}

function getOneTour($post_id){
    $tour = get_post( $post_id );
    return array(
      'title' => $tour->post_title,
      'hashtag' => get_post_meta( $post_id, 'tour_hashtag', true),
      'url' => get_post_meta( $post_id, 'tour_url', true),
      'id' => $tour->ID
    );
}

function create_tour_callback() {
		global $wpdb; // this is how you get access to the database

		$tour = array(
			'title' => $_POST['tour_title'],
			'hashtag' => $_POST['tour_hashtag'],
			'url' => $_POST['tour_url'],
			'group_id' => array_key_exists('tour_group', $_POST) ? $_POST['tour_group'] : null,
		);
		//return json_encode($tour);
		$args = array(
			'post_title' => $tour['title'],
			'post_type' => 'joyride_tour',
			'post_status' => 'publish',
			'post_content' => 'tour description can go here or nothing at all'
		);
		$tour_id = wp_insert_post( $args, true );

		if ( $tour_id ) {
			add_post_meta( $tour_id, 'tour_hashtag', $tour['hashtag'], true);
			add_post_meta( $tour_id, 'tour_url', $tour['url'], true);
			echo $tour_id;
		}
		die();
}

function save_tip_callback() {

		$tip = array(
			'parent_id' => $_POST['parent_id'],
			'tip_title' => $_POST['tip_title'],
			'tip_text' => addslashes( $_POST['tip_text'] ),
			'tip_location' => $_POST['tip_location'],
			'tip_animation' => $_POST['tip_animation'],
			'button_text' => $_POST['button_text'],
		);
		$tour_id = $_POST['tour_id'];		
		if( tip_meta( $tour_id, 'update', json_encode($tip) ) ) {
			echo json_encode($tip);
		}
		die();

}

function delete_tip_callback() {
	$post_id = $_POST['post_id'];
	$title = $_POST['tip_title'];

	$tips = tip_meta( $post_id, 'get' );
	foreach( $tips as $tip ){
		//$utip = json_decode($tip);
		if( $title == $tip['tip_title'] ) {
			$del = tip_meta( $post_id, 'delete', $tip );
			if( $del ) return;
		}
	}
}

function delete_tour_callback() {
	$post_id = $_POST['post_id'];
	if ( wp_delete_post( $post_id ) ) {
		echo json_encode( get_tours_tips() );
		die();
	}
}

function save_tour_callback()
  {

    // Get POST data
    $model = array(
      'tour_title' => $_POST['tour_title'],
      'tour_hashtag' => $_POST['tour_hashtag'],
      'tour_url' => $_POST['tour_url'],
    );

    $tour = array(
      'title' => $model['tour_title'],
      'hashtag' => $model['tour_hashtag'],
      'url' => $model['tour_url'],
    );
    //return json_encode($tour);
    $args = array(
      'post_title' => $tour['title'],
      'post_type' => 'joyride_tour',
      'post_status' => 'publish',
      'post_content' => 'tour description can go here or nothing at all'
    );
    $tour_id = wp_insert_post( $args, true );

    if ( $tour_id ) {
      add_post_meta($tour_id, 'tour_hashtag', $tour['hashtag'], true);
      add_post_meta($tour_id, 'tour_url', $tour['url'], true);
      echo json_encode(getOneTour($tour_id));
    }
    die();
  }
/* tip meta */
function tip_meta( $post_id, $action = 'get', $tip = null ) {
  
  //Let's make a switch to handle the three cases of 'Action'
  switch ($action) {
    case 'update' :
      if( ! $tip )
        //If nothing is given to update, end here
        return false;
      
      if( $tip ) { 
        add_post_meta( $post_id, 'tour_tip', $tip );
        return true;
      }

    break;
    case 'delete' :

	    // This will delete all instances of the following keys from the given post
    	// By passing in a meta_value, we can remove a specific meta
		// delete_post_meta( $post_id, $meta_key, $meta_value )
	    delete_post_meta( $post_id, 'tour_tip', $tip );
      
    break;
    case 'get' :
  
      $stored_tips = get_post_meta( $post_id, 'tour_tip' );

      if ( ! empty( $stored_tips ) )
      	return $stored_tips;
    break;
    default :
      return false;
    break;
  } //end switch
} //end function

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

// create custom post type to house the tours
function tips_custom_post_type() {


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
	    'supports' => array( 'title', 'author', 'excerpt', 'custom-fields' ),
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

	// Add new taxonomy to classify tours into 'tour groups'
	$labels = array(
		'name'                       => _x( 'Tour Groups', 'taxonomy general name' ),
		'singular_name'              => _x( 'Tour Group', 'taxonomy singular name' ),
		'search_items'               => __( 'Search Tour Groups' ),
		'popular_items'              => __( 'Popular Tour Groups' ),
		'all_items'                  => __( 'All Tour Groups' ),
		'parent_item'                => null,
		'parent_item_colon'          => null,
		'edit_item'                  => __( 'Edit Tour Group' ),
		'update_item'                => __( 'Update Tour Group' ),
		'add_new_item'               => __( 'Add New Tour Group' ),
		'new_item_name'              => __( 'New Tour Group Name' ),
		'separate_items_with_commas' => __( 'Separate Tour Groups with commas' ),
		'add_or_remove_items'        => __( 'Add or remove Tour Groups' ),
		'choose_from_most_used'      => __( 'Choose from the most used Tour Groups' ),
		'not_found'                  => __( 'No Tour Groups found.' ),
		'menu_name'                  => __( 'Tour Groups' ),
	);

	$args = array(
		'hierarchical'          => true,
		'labels'                => $labels,
		'show_ui'               => true,
		'show_admin_column'     => true,
		'update_count_callback' => '_update_post_term_count',
		'query_var'             => true,
		//'rewrite'               => array( 'slug' => 'writer' ),
	);

	register_taxonomy( 'tour_groups', 'joyride_tour', $args );

}
// Hook into the 'wp_dashboard_setup' action to register our other functions

add_action('wp_dashboard_setup', 'tips_add_dashboard_widgets' ); // Hint: For Multisite Network Admin Dashboard use wp_network_dashboard_setup instead of wp_dashboard_setup.
