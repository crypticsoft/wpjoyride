<?php

class WpTour
{

  // Names of Custom Post Type
  public $postTypeNameSingle = 'joyride_tour';
  public $postTypeNamePlural = 'Tours';

  // Meta Box Stuff
  public $metaBoxTitle = 'Tips';
  public $metaBoxTempl = 'templates/metabox.templ.php';

  // Question Id's
  public $tipIds = array('tip-1', 'tip-2', 'tip-3', 'tip-4');

  // Javascript
  public $jsAdmin = '/js/main.js';

  public function __construct($type)
  {
    switch ($type) {
      case 'admin' :
/*
        // Register the Post Type
        $this->registerPostType(
          $this->postTypeNameSingle,
          $this->postTypeNamePlural);
*/
        // Add the Meta Box
        //add_action('add_meta_boxes', array($this, 'addMetaBox'));
        add_action('admin_footer', array($this, 'addMetaBox'));

        // Accept an Ajax Request
        //add_action('wp_ajax_save_answer', array($this, 'saveAnswers'));
        add_action('wp_ajax_save_tour', array($this, 'saveTour'));

        // Watch for Post being saved
        add_action('save_post', array($this, 'savePost'));
    }
  }
  /**
   *
   * ----------------------------------------------
   * Add Meta Box.
   * ----------------------------------------------
   *
   * Add a meta box for this plugin.
   * Also Enqueue the JS needed for the Admin.
   *
   */
  public function addMetaBox()
  {

    // Load the Javascript needed on this admin page.
    //$this->addScripts();

    // Create an id based on Post-type name
    $id = $this->postTypeNameSingle . '_metabox';
/*
    // Add the meta box
    add_meta_box(
      $id,
      $this->metaBoxTitle,
      array($this, 'getMetaBox'),
      $this->postTypeNameSingle
    );
*/    
    return $this->getTourBox();
    //return $this->getMetaBox;
  }

/* 

*/
public function getTourBox() {


    // Set data needed in the template
    $viewData = array(
      'tour_id' => 'tour_title',
      'index' => 'New Tour',
      'tour' => null,
      'tips' => json_encode( $this->tipIds )
    );

    // Output the rendered template
    echo $this->getTemplatePart($this->metaBoxTempl, $viewData);

}
  /**
   * ----------------------------------------------
   * Get the Meta box Template File.
   * ----------------------------------------------
   *
   * Send anything variables needed via $viewData Array.
   *
   * @param $post - The current Post ID
   * @return string
   */
  public function getMetaBox($post)
  {
    // Get the current values for the questions
    $json = array();
    foreach ($this->answerIds as $id) {
      $json[] = $this->getOneAnswer($post->ID, $id);
    }

    // Set data needed in the template
    $viewData = array(
      'post' => $post,
      'tips' => json_encode($json),
      'correct' => json_encode(get_post_meta($post->ID, 'correct_answer', true))
    );

    // Output the rendered template
    echo $this->getTemplatePart($this->metaBoxTempl, $viewData);

  }

  /**
   * 
   * ----------------------------------------------
   * Get a single answer in the correct format
   * ----------------------------------------------
   *
   * @param $post_id
   * @param $tip_id
   * @return array
   *
   */
  public function getOneTip($post_id, $tip_id){
    return array(
      'tip_id' => $tip_id,
      'tip' => get_post_meta($post_id, $tip_id, true)
    );
  }

  public function getOneTour($post_id){
    $tour = get_post( $post_id );
    return array(
      'title' => $tour->post_title,
      'hashtag' => get_post_meta( $post_id, 'tour_hashtag', true),
      'url' => get_post_meta( $post_id, 'tour_url', true),
      'id' => $tour->ID
    );
  }
  /**
   *
   * ----------------------------------------------
   * Save Post
   * ----------------------------------------------
   *
   * Catch info from meta boxes and save it to the Database
   *
   */
  public function savePost($post_id)
  {

    // Check we are saving our Custom Post Type
    if ($_POST['post_type'] !== strtolower($this->postTypeNameSingle)){
      return;
    }

    // Check that the user has relevant permissions
    if (!$this->canSaveData($post_id)){
      return;
    }

    $fields = array();

    // Grab the info posted from Save
    foreach ($this->answerIds as $id) {
      $fields[$id] = $_POST[$id];
    }

    // Loop through them and save each one
    foreach ($fields as $id => $field) {
      add_post_meta($post_id, $id, $field, true)
        or
        update_post_meta($post_id, $id, $field);
    }

    // Now add the Correct answer
    add_post_meta($post_id, 'correct_answer', $_POST['correct_answer'], true)
      or
      update_post_meta($post_id, 'correct_answer', $_POST['correct_answer']);

  }

  /**
   * ----------------------------------------------
   * Save data received as JSON from Backbone
   * ----------------------------------------------
   *
   * AJAX
   *
   */

    public function saveTour()
  {

    // Get PUT data & decode it
    //$model = json_decode(file_get_contents("php://input"));
    $model = array(
      'tour_title' => $_POST['tour_title'],
      'tour_hashtag' => $_POST['tour_hashtag'],
      'tour_url' => $_POST['tour_url'],
    );

    global $wpdb; // this is how you get access to the database
  //if( array_key_exists( 'tour_title', $_POST ) ) {
    $tour = array(
      'title' => $model['tour_title'],
      'hashtag' => $model['tour_hashtag'],
      'url' => $model['tour_url'],
    );
    //return json_encode($tour);
    $args = array(
      'post_title' => $tour['title'],
      'post_type' => 'joyride_tour',
    //  'tax_input' => [ array( 'taxonomy_id' => array( $tour['group_id'] ) ) ],
      'post_status' => 'publish',
      'post_content' => 'tour description can go here or nothing at all'
    );
    //print_r($args);die();
    $tour_id = wp_insert_post( $args, true );
    //print_r($args);
    //if ( $wp_error ) return $wp_error;
    if ( $tour_id ) {
      add_post_meta($tour_id, 'tour_hashtag', $tour['hashtag'], true);
      add_post_meta($tour_id, 'tour_url', $tour['url'], true);
      echo json_encode($this->getOneTour($tour_id));
    }
/*
    // If a save or update was successful, return the model in JSON format
    if ($update) {
      echo json_encode($this->getOneTip($model->post_id, $model->answer_id));
    } else {
      echo 0;
    }
*/    
    die();
  }


  public function saveAnswers()
  {

    // Get PUT data & decode it
    $model = json_decode(file_get_contents("php://input"));

    // Ensure that this user has the correct permissions
    if (!$this->canSaveData($model->post_id)) {
      return;
    }

    // Attempt an insert/update
    $update = add_post_meta($model->post_id, $model->answer_id, $model->answer, true)
      or
      $update = update_post_meta($model->post_id, $model->answer_id, $model->answer);

    // If a save or update was successful, return the model in JSON format
    if ($update) {
      echo json_encode($this->getOneAnswer($model->post_id, $model->answer_id));
    } else {
      echo 0;
    }
    die();
  }

  /**
   * ----------------------------------------------
   * Determine if the current user has the relevant permissions
   * ----------------------------------------------
   *
   * @param $post_id
   * @return bool
   */
  private function canSaveData($post_id)
  {

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
      return false;

    if ('page' == $_POST['post_type']) {
      if (!current_user_can('edit_page', $post_id))
        return false;
    } else {
      if (!current_user_can('edit_post', $post_id))
        return false;
    }

    return true;
  }

  /**
   * ----------------------------------------------
   * Add Scripts
   * ----------------------------------------------
   */
  private function addScripts()
  {
    wp_register_script('wp_quiz_main_js', pp() . $this->jsAdmin , array('backbone'), null, true);
    wp_enqueue_script('wp_quiz_main_js');
  }

  /**
   * ----------------------------------------------
   * Register a Custom Post Type
   * ----------------------------------------------
   *
   * @param $single
   * @param $plural
   * @param null $supports
   */
  private function RegisterPostType($single, $plural, $supports = null)
  {

    $labels = array(
      'name' => _x($plural, 'post type general name'),
      'singular_name' => _x("$single", 'post type singular name'),
      'add_new' => _x("Add New $single", "$single"),
      'add_new_item' => __("Add New $single"),
      'edit_item' => __("Edit $single"),
      'new_item' => __("New $single"),
      'all_items' => __("All $plural"),
      'view_item' => __("View $single"),
      'search_items' => __("Search $plural"),
      'not_found' => __("No $plural found"),
      'not_found_in_trash' => __("No $single found in Trash"),
      'parent_item_colon' => '',
      'menu_name' => $plural
    );
    $args = array(
      'labels' => $labels,
      'public' => true,
      'publicly_queryable' => true,
      'show_ui' => true,
      'show_in_menu' => true,
      'query_var' => true,
      'rewrite' => true,
      'capability_type' => 'post',
      'has_archive' => true,
      'hierarchical' => false,
      'menu_position' => null,
      'supports' => ($supports) ? $supports : array('title', 'editor', 'page-attributes')
    );
    register_post_type($single, $args);
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
  public function getTemplatePart($filePath, $viewData = null) {

    ($viewData) ? extract($viewData) : null;

    ob_start();
    include ("$filePath");
    $template = ob_get_contents();
    ob_end_clean();

    return $template;
  }
}

