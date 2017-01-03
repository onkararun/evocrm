<?php
/*
Plugin Name: Vehicle Images
Plugin URI: https://itzkapilsblog.wordpress.com
Description: Allows users to submit vehicle images from Frontend.
Version: 1.0
License: GPLv2
Author: Kapil Sharma
Author URI: https://itzkapilsblog.wordpress.com
*/

define('MAX_UPLOAD_SIZE', 2000000);
define('TYPE_WHITELIST', serialize(array(
  'image/jpeg',
  'image/png',
  'image/gif'
  )));


add_shortcode('wp_form', 'wp_form_shortcode');


function wp_form_shortcode(){

  if(!is_user_logged_in()){
  
    return '<p>You need to be logged in to submit an image.</p>';    

  }

  global $current_user;
    
  if(isset( $_POST['wp_upload_image_form_submitted'] ) && wp_verify_nonce($_POST['wp_upload_image_form_submitted'], 'wp_upload_image_form') ){  

    $result = wp_parse_file_errors($_FILES['wp_image_file'], $_POST['wp_image_caption']);
    
    if($result['error']){
    
      echo '<p>ERROR: ' . $result['error'] . '</p>';
    
    }else{

      $post_id = $_GET['pid'];

      if($post_id){
      
        wp_process_image('wp_image_file', $post_id, $result['caption']);
      
        wp_set_object_terms($post_id, (int)$_POST['wp_image_category'], 'wp_image_category');
      
      }
    }
  }  

  if (isset( $_POST['wp_form_delete_submitted'] ) && wp_verify_nonce($_POST['wp_form_delete_submitted'], 'wp_form_delete')){

    if(isset($_POST['wp_image_delete_id'])){
    
      if($user_images_deleted = wp_delete_user_images($_POST['wp_image_delete_id'])){        
      
        echo '<p>' . $user_images_deleted . ' images(s) deleted!</p>';
        
      }
    }
  }
  

  echo wp_get_upload_image_form($wp_image_caption = $_POST['wp_image_caption'], $wp_image_category = $_POST['wp_image_category']);
  
  if($user_images_table = wp_get_user_images_table($current_user->ID)){
  
    echo $user_images_table;
    
  }

}


function wp_delete_user_images($images_to_delete){

  $images_deleted = 0;
  $post_id =$_GET['pid'];
  foreach($images_to_delete as $user_image){
    if (isset($_POST['wp_image_delete_id'])){
      $attachment_id = $_POST['wp_image_delete_id'];
      foreach ($attachment_id as $attachment) {
        wp_delete_attachment($attachment);
      }
      $data = get_post_meta($post_id, '_thumbnail_id', true);
      if($data) {
        $result = array_diff($data, $attachment_id); // remove duplicates
        $data = array_unique($result);
        $images = get_post_meta($post_id, '_thumbnail_id', true);
        update_post_meta($post_id, '_thumbnail_id', $data, $images);
      }
    }
  }

  return $images_deleted;

}


function wp_get_user_images_table($user_id){
  $post_id = $_GET['pid'];
  if(!$post_id) return 0;
  
  $out = '';
  $out .= '<p>Your Vehicle images - Click to see full size</p>';
  
  $out .= '<form method="post" action="">';
  
  $out .= wp_nonce_field('wp_form_delete', 'wp_form_delete_submitted');  
  
  $out .= '<table id="user_images">';
  $images = get_post_meta($post_id, '_thumbnail_id', true);  
  if(!empty($images)) {
    $out .= '<thead><th>Image</th><th>Delete</th></thead>';
    foreach($images as $image_id){
      $post_thumbnail_id = get_post_thumbnail_id($image_id);   

      $out .= wp_nonce_field('wp_image_delete_' . $post_id, 'wp_image_delete_id_' . $post_id, false); 
         
      $out .= '<tr>';
      $out .= '<td>' . wp_get_attachment_link($image_id, 'thumbnail') . '</td>'; 
      $out .= '<td><input type="checkbox" name="wp_image_delete_id[]" value="' . $image_id . '" /></td>';          
      $out .= '</tr>';
      
    }
  $out .= '</table>';
  $out .= '<input type="submit" name="wp_delete" value="Delete Selected Images" />';
  $out .= '</form>';  
  return $out;
  }
}


function wp_process_image($file, $post_id, $caption){
 
  require_once(ABSPATH . "wp-admin" . '/includes/image.php');
  require_once(ABSPATH . "wp-admin" . '/includes/file.php');
  require_once(ABSPATH . "wp-admin" . '/includes/media.php');
 
  $attachment_id = media_handle_upload($file, $post_id);
  $data = get_post_meta($post_id, '_thumbnail_id', true);
  if( count($data) != 0 ) {
    $data[] = $attachment_id;
    $data = array_unique($data); // remove duplicates
    update_post_meta($post_id, '_thumbnail_id', $data);
  } else {
    $data = array();
    $data[0] = $attachment_id;
    update_post_meta($post_id, '_thumbnail_id', $data);  
  }
  return $data;

}


function wp_parse_file_errors($file = '', $image_caption){

  $result = array();
  $result['error'] = 0;
  
  if($file['error']){
  
    $result['error'] = "No file uploaded or there was an upload error!";
    
    return $result;
  
  }

  $image_caption = trim(preg_replace('/[^a-zA-Z0-9\s]+/', ' ', $image_caption));
  
  if($image_caption == ''){

    $result['error'] = "Your caption may only contain letters, numbers and spaces!";
    
    return $result;
  
  }
  
  $result['caption'] = $image_caption;  

  $image_data = getimagesize($file['tmp_name']);
  
  if(!in_array($image_data['mime'], unserialize(TYPE_WHITELIST))){
  
    $result['error'] = 'Your image must be a jpeg, png or gif!';
    
  }elseif(($file['size'] > MAX_UPLOAD_SIZE)){
  
    $result['error'] = 'Your image was ' . $file['size'] . ' bytes! It must not exceed ' . MAX_UPLOAD_SIZE . ' bytes.';
    
  }
    
  return $result;

}



function wp_get_upload_image_form($wp_image_caption = '', $wp_image_category = 0){

  $out = '';
  $out .= '<form id="wp_upload_image_form" method="post" action="" enctype="multipart/form-data">';

  $out .= wp_nonce_field('wp_upload_image_form', 'wp_upload_image_form_submitted');
  
  $out .= '<label for="wp_image_caption">Image Caption - Letters, Numbers and Spaces</label><br/>';
  $out .= '<input type="text" id="wp_image_caption" name="wp_image_caption" value="' . $wp_image_caption . '"/><br/>';
  $out .= '<label for="wp_image_file">Select Your Image - ' . MAX_UPLOAD_SIZE . ' bytes maximum</label><br/>';  
  $out .= '<input type="file" size="60" name="wp_image_file" id="wp_image_file"><br/>';
    
  $out .= '<input type="submit" id="wp_submit" name="wp_submit" value="Upload Image">';

  $out .= '</form>';

  return $out;
  
}


function wp_get_image_categories_dropdown($taxonomy, $selected){

  return wp_dropdown_categories(array('taxonomy' => $taxonomy, 'name' => 'wp_image_category', 'selected' => $selected, 'hide_empty' => 0, 'echo' => 0));

}