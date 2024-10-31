<?php
/*
Plugin Name: Random Product
Plugin URI: http://www.89designs.net/2009/09/random-product/
Description: Displays a random product from the WP e-Commerce shop in a widget in the side bar. Requires WP e-Commerce.
Version: 1.2
Author: Adam Tootle
Author URI: http://www.89designs.net
*/

add_action("widgets_init", array('random_product', 'register'));
register_activation_hook( __FILE__, array('random_product', 'activate'));
register_deactivation_hook( __FILE__, array('random_product', 'deactivate'));
class random_product {
  function activate(){
    //$data = array( 'widget_title' => 'Widget Title');
    if ( ! get_option('random_product')){
      add_option('random_product_title' , 'title here');
    } else {
      update_option('random_product_title' , 'title here');
    }
    if ( ! get_option('products_page_url')){
      add_option('products_page_url' , 'products-page');
    } else {
      update_option('products_page_url' , 'products-page');
    }
  }
  function deactivate(){
    delete_option('random_product_title');
    delete_option('products_page_url');
  }
  function control(){
  $widget_title = get_option('random_product_title');
  $page_url = get_option('products_page_url')
  ?>
  <p><label>Widget Title<input name="random_product_widget_title"
type="text" value="<?php echo get_option('random_product_title'); ?>" /></label></p>

<p><label>Products Page URL<input name="products_page_url"
type="text" value="<?php echo get_option('products_page_url'); ?>" /></label><br>
<i>Requires permalinks to be enabled. This is 'products-page' by default. Enter whatever your permalink is <b>without</b> the slashes.</i>
</p>

  <?php
   if (isset($_POST['random_product_widget_title'])){
    $widget_title = attribute_escape($_POST['random_product_widget_title']);
    update_option('random_product_title', $widget_title);
  	}
  	if (isset($_POST['products_page_url'])){
    $widget_title = attribute_escape($_POST['products_page_url']);
    update_option('products_page_url', $widget_title);
  	}
  }
  function widget($args){
  	global $wpdb;
  	$wpsc_product_list = $wpsc_product_order = $wpsc_product_categories = $wpsc_product_image = $wpsc_product_url = '';
  	$random_product_id = $random_product = $random_product_category = $random_product_category_nice_name = $random_product_image = $my_product_url = $image_src = '';
  	$wpsc_product_list = $wpdb->prefix . "wpsc_product_list";
  	$wpsc_product_order = $wpdb->prefix . "wpsc_product_order";
  	$wpsc_product_categories = $wpdb->prefix. "wpsc_product_categories";
  	$wpsc_product_image = $wpdb->prefix. "wpsc_product_images";
  	$wpsc_product_url = $wpdb->prefix . "wpsc_productmeta";
    echo $args['before_widget'] . $args['before_title']. get_option( 'random_product_title' )  . $args['after_title'];
    $product_count = $wpdb->get_col("SELECT id FROM $wpsc_product_list" );
    $random_product_id = rand(1, end($product_count));
    
    while(!$random_product){
    	$random_product = $wpdb->get_row("SELECT * FROM $wpsc_product_list WHERE id = $random_product_id");
    }
    
    $random_product_category = $wpdb->get_var("SELECT category_id FROM $wpsc_product_order WHERE product_id = $random_product_id");
    $random_product_category_nice_name = $wpdb->get_var("SELECT `nice-name` FROM $wpsc_product_categories WHERE `id` = $random_product_id");
    $random_product_image = $wpdb->get_var("SELECT image FROM $wpsc_product_image WHERE product_id = $random_product_id");
    $my_product_url = $wpdb->get_var("SELECT meta_value FROM $wpsc_product_url WHERE product_id = $random_product_id AND meta_key = 'url_name'");
    $image_src = get_option('siteurl') . "/wp-content/uploads/wpsc/product_images/thumbnails/";
    echo ("<a href=\"" . get_option('siteurl') . "/" . get_option('products_page_url') . "/" . $random_product_category_nice_name . "/" . $my_product_url . "\" class=\"random_product_link\">");

    if($random_product_image){
    	echo "<image src=\"";
    	echo $image_src;
    	echo $random_product_image;
    	echo "\"><br>";
    }
    echo $random_product->name;
    echo "</a>";
    echo "<br>";
    echo $random_product->description . "<br>";
    
    //echo $random_product_category_nice_name;
    //echo "cat id is" . $random_product_category;
    //echo $random_product_id;
    //echo end($productCount);
    echo $args['after_widget'];
  }
  function register(){
    register_sidebar_widget('Random Product', array('random_product', 'widget'));
    register_widget_control('Random Product', array('random_product', 'control'));
  }
}
?>
