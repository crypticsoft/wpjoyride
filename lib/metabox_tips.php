<?php

/**
 * Calls the class on the post edit screen.
 */
function call_tipMetaBox() {

    return new tipMetaBox();
}

if ( is_admin() ) {
    add_action( 'load-post.php', 'call_tipMetaBox' );
	add_action( 'load-post-new.php', 'call_tipMetaBox' );
}

/** 
 * The Class.
 */
class tipMetaBox {

	/**
	 * Hook into the appropriate actions when the class is constructed.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		add_action( 'save_post', array( $this, 'save' ) );
	}

	/**
	 * Adds the meta box container.
	 */
	public function add_meta_box() {
		add_meta_box(
			 'wpjoyride_tips_metabox'
			,__( 'Tips Meta Data', 'wpjoyride_textdomain' )
			,array( $this, 'render_meta_box_content' )
			,'joyride_tip'
			,'advanced'
			,'high'
		);
	}

	/**
	 * Save the meta when the post is saved.
	 *
	 * @param int $post_id The ID of the post being saved.
	 */
	public function save( $post_id ) {
	
		/*
		 * We need to verify this came from the our screen and with proper authorization,
		 * because save_post can be triggered at other times.
		 */
		// Check if our nonce is set.
		if ( ! isset( $_POST['myplugin_inner_custom_box_nonce'] ) )
			return $post_id;

		$nonce = $_POST['myplugin_inner_custom_box_nonce'];

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $nonce, 'myplugin_inner_custom_box' ) )
			return $post_id;

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
			return $post_id;
/*
		// Check the user's permissions.
		if ( 'joyride_tip' == $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) )
				return $post_id;
	
		} else {			
			if ( ! current_user_can( 'edit_post', $post_id ) )
				return $post_id;
		}
*/
		/* OK, its safe for us to save the data now. */

		// Sanitize the user input.
		$parent_id = sanitize_text_field( $_POST['wpjoyride_parent_id'] );
		$tip_text = sanitize_text_field( $_POST['wpjoyride_tip_text'] );
		$tip_location = sanitize_text_field( $_POST['wpjoyride_tip_location'] );
		$tip_animation = sanitize_text_field( $_POST['wpjoyride_tip_animation'] );
		$button_text = sanitize_text_field( $_POST['wpjoyride_button_text'] );
		$tour_id = sanitize_text_field( $_POST['wpjoyride_tour'] );
		$tour_order = sanitize_text_field( $_POST['wpjoyride_tour_order'] );

		// Update the meta field.
		update_post_meta( $post_id, 'wpjoyride_parent_id', $parent_id );
		update_post_meta( $post_id, 'wpjoyride_tip_text', $tip_text );
		update_post_meta( $post_id, 'wpjoyride_tip_location', $tip_location );
		update_post_meta( $post_id, 'wpjoyride_tip_animation', $tip_animation );
		update_post_meta( $post_id, 'wpjoyride_button_text', $button_text );
		update_post_meta( $post_id, 'wpjoyride_tour', $tour_id );
		update_post_meta( $post_id, 'wpjoyride_tour_order', $tour_order );

	}

	private function get_tours(){

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
	/**
	 * Render Meta Box content.
	 *
	 * @param WP_Post $post The post object.
	 */
	public function render_meta_box_content( $post ) {
	
		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'myplugin_inner_custom_box', 'myplugin_inner_custom_box_nonce' );
		// get all tours
		$tours = $this->get_tours();

		// Use get_post_meta to retrieve an existing value from the database.
		$parent_id = get_post_meta( $post->ID, 'wpjoyride_parent_id', true );
		$tip_text = get_post_meta( $post->ID, 'wpjoyride_tip_text', true );
		$tip_location = get_post_meta( $post->ID, 'wpjoyride_tip_location', true );
		$tip_animation = get_post_meta( $post->ID, 'wpjoyride_tip_animation', true );
		$button_text = get_post_meta( $post->ID, 'wpjoyride_button_text', true );
		$tour_id = get_post_meta( $post->ID, 'wpjoyride_tour', true );
		$tour_order = get_post_meta( $post->ID, 'wpjoyride_tour_order', true );

		// Display the form, using the current value.
		echo '<div class="input-wrap">';
		echo '<p class="label"><label for="wpjoyride_parent_id">';
		_e( 'Parent ID / Selector <em>( Be sure to use # for ID or . for class names )</em>', 'wpjoyride_textdomain' );
		echo '</label></p>';
		echo '<input type="text" id="wpjoyride_parent_id" name="wpjoyride_parent_id" class="text" value="' . esc_attr( $parent_id ) . '" />';
		echo '</div>';

		echo '<div class="input-wrap">';
		echo '<p class="label"><label for="wpjoyride_tip_text">';
		_e( 'Tip Text', 'wpjoyride_textdomain' );
		echo '</label></p>';
		echo '<input type="text" id="wpjoyride_tip_text" name="wpjoyride_tip_text" class="text" value="' . esc_attr( $tip_text ) . '" />';
		echo '</div>';

		echo '<div class="input-wrap">';
		echo '<p class="label"><label for="wpjoyride_tip_location">';
		_e( 'Tip Location', 'wpjoyride_textdomain' );
		echo '</label></p>';
		echo '<ul class="checkbox-list">';
		echo '<li><label><input ' . ( $tip_location == 'Top' ? 'checked="yes"' : '' ) . 'type="checkbox" id="wpjoyride_tip_location" name="wpjoyride_tip_location" class="checkbox" value="Top" /> Top</label></li>';
		echo '<li><label><input ' . ( $tip_location == 'Bottom' ? 'checked="yes"' : '' ) . 'type="checkbox" id="wpjoyride_tip_location" name="wpjoyride_tip_location" class="checkbox" value="Bottom" /> Bottom</label></li>';
		echo '<li><label><input ' . ( $tip_location == 'Left' ? 'checked="yes"' : '' ) . 'type="checkbox" id="wpjoyride_tip_location" name="wpjoyride_tip_location" class="checkbox" value="Left" /> Left</label></li>';
		echo '<li><label><input ' . ( $tip_location == 'Right' ? 'checked="yes"' : '' ) . 'type="checkbox" id="wpjoyride_tip_location" name="wpjoyride_tip_location" class="checkbox" value="Right" /> Right</label></li>';				
		echo '</ul>';
		echo '</div>';

		echo '<div class="input-wrap">';
		echo '<p class="label"><label for="wpjoyride_tip_animation">';
		_e( 'Tip Animation', 'wpjoyride_textdomain' );
		echo '</label></p>';
		echo '<ul class="checkbox-list">';
		echo '<li><label><input ' . ( $tip_animation == 'Pop' ? 'checked="yes"' : '' ) . 'type="checkbox" id="wpjoyride_tip_animation" name="wpjoyride_tip_animation" class="checkbox" value="Pop" /> Pop</label></li>';
		echo '<li><label><input ' . ( $tip_animation == 'Fade' ? 'checked="yes"' : '' ) . 'type="checkbox" id="wpjoyride_tip_animation" name="wpjoyride_tip_animation" class="checkbox" value="Fade" /> Fade</label></li>';		
		echo '</ul>';
		echo '</div>';

		echo '<div class="input-wrap">';
		echo '<p class="label"><label for="wpjoyride_button_text">';
		_e( 'Button Text', 'wpjoyride_textdomain' );
		echo '</label></p>';
		echo '<input type="text" id="wpjoyride_button_text" name="wpjoyride_button_text" class="text" value="' . esc_attr( $button_text ) . '" />';
		echo '</div>';

		echo '<div class="input-wrap">';
		echo '<p class="label"><label for="wpjoyride_tour">';
		_e( 'Tour', 'wpjoyride_textdomain' );
		echo '</label></p>';
		echo '<select name="wpjoyride_tour" id="wpjoyride_tour">';
		echo '<option>Select One</option>';
		foreach($tours as $tour){
			$checked = $tour['id'] == (int) $tour_id ? ' selected="selected"' : '';
			echo '<option value="' . $tour['id'] . '"' . $checked . '>' . $tour['title'] . '</option>';
		}
		echo '</select>';
		echo '</div>';

		echo '<div class="input-wrap">';
		echo '<p class="label"><label for="wpjoyride_tour_order">';
		_e( 'Tour Order', 'wpjoyride_textdomain' );
		echo '</label></p>';
		echo '<input type="text" id="wpjoyride_tour_order" name="wpjoyride_tour_order" class="text" value="' . esc_attr( $tour_order ) . '" />';
		echo '</div>';
	}
}