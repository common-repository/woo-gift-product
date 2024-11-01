<?php

/**
 * 
 */
class Gift_product 
{
	
	function __construct()
	{
        // add gift tab action 
		add_action( 'woocommerce_settings_tabs_gift_tab', array($this,'gift_tab' ));
        // add post type action
		add_action( 'init', array($this,'gift_post_type') );
        // add gift product action
		add_action( 'woocommerce_before_cart_contents', array($this,'add_gift_product'), 10, 0 ); 
        // add custom price action
		add_action( 'woocommerce_before_calculate_totals', array($this,'woocommerce_custom_price_to_cart_item'), 99 );
        // remove gift product delete button
        add_filter( 'woocommerce_cart_item_remove_link', array($this,'gift_product_remove_link'), 10, 2 );
        // remove gift product quantity field
        add_filter( 'woocommerce_cart_item_quantity', array($this,'gift_cart_item_quantity'), 10, 2 );

	}
    // fiter gift tab in woocommerce setting 
	public static function init() {
        add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50 );
    }
    // add gift tab in woocommerce setting
    public static function add_settings_tab( $settings_tabs ) {
        $settings_tabs['gift_tab'] = __( 'Gift', 'woocommerce-gift-tab' );
        return $settings_tabs;
    }
    // add css and js resource
    public function add_resource()
    {
        wp_register_style('wc-switch-css', plugin_dir_url(__FILE__).'assets/css/lc_switch.css');
        wp_enqueue_style('wc-switch-css');
        wp_register_script( 'wc-switch-js', plugin_dir_url(__FILE__).'assets/js/lc_switch.min.js', array(), '1.0', true);
        wp_enqueue_script('wc-switch-js');
    }
    // gift tab view
    public function gift_tab()
    {
        if (isset($_REQUEST['_wpnonce'])) {
            $nonce = $_REQUEST['_wpnonce'];
            if ( ! wp_verify_nonce( $nonce, 'gift-nonce' ) ) {
                wp_redirect(admin_url('admin.php?page=wc-settings&tab=gift_tab'));
                exit(); 
            }
        }
    	if (isset($_GET['gift_id'])) {
            $this->add_resource();
    		include 'templates/add.php';
    	}
    	elseif (isset($_GET['gift_delete'])) {
    		wp_delete_post($_GET['gift_delete']);
    		wp_redirect(admin_url('admin.php?page=wc-settings&tab=gift_tab'));
        	exit();
    	}
    	else{
    		include 'templates/main.php';
    	}
    }
    // add custom gift post type and check woocommerce is active
    public function gift_post_type() {
        if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
            $class = 'notice notice-error';
            $message = __("Error! <a href='https://wordpress.org/plugins/woocommerce/' target='_blank'>WooCommerce</a> Plugin is required to activate Woocommerce Gift Product", 'optinspin');
            printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), $message);
            if (!function_exists('deactivate_plugins')) {
                require_once(ABSPATH . 'wp-admin/includes/plugin.php');
            }
            deactivate_plugins(GIFT_BASEPATH);
        }
		if (isset($_POST['gift_title'])) {
		    $post = $_POST;
		    $this->gift_save($post);
		}
		register_post_type( 'Gifts', array(
			'labels' => array(
			    'name'               => __('Gifts', 'bonestheme'),
			    'singular_name'      => __('Gift', 'bonestheme'),
			    'add_new_item'       => __('Add New Gift', 'bonestheme'),
			    'edit_item'          => __('Edit Gift', 'bonestheme'),
			    'new_item'           => __('New Gift', 'bonestheme'),
			    'view_item'          => __('View Gift', 'bonestheme'),
			    'search_items'       => __('Search Gift', 'bonestheme'),
			    'not_found'          => __('No Gift Found', 'bonestheme'),
			    'not_found_in_trash' => __('No Gift found in Trash', 'bonestheme')
			),
			'description'          => 'Represents a single slide in the header Gift.',
			'hierarchical'         => false,
			'menu_icon'            => 'dashicons-images-alt2',
			'menu_position'        => 5,
			'public'               => true,
			'register_meta_box_cb' => array('SlideJS', 'createCPTMetaboxes'),
			'rewrite'              => array( 'slug' => 'gift_product', 'with_front' => false ),
			'show_in_admin_bar'    => false,
			'show_in_nav_menus'    => false,
			'show_ui'              => false,
			'supports'             => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'custom-fields', 'revisions', 'page-attributes')
			)
		);
	}
    // save gift form data
	public function gift_save($post)
	{
        if (!wp_verify_nonce($post['gift_nonce'], 'gift-action')) {
            wp_redirect(admin_url('admin.php?page=wc-settings&tab=gift_tab'));
            exit();
        }
		if (isset($post['post_type']) && $post['post_type'] == 'gift') {
			if ($post['gift_post_id'] == '') {
                $gift_post = array(
                    'post_title' => $post['gift_title'],
                    'post_status' => 'publish',
                    'post_type' => 'gift_product'
                );
                $post_id = wp_insert_post($gift_post);
            } else {
                $gift_post = array(
                    'ID' => $post['gift_post_id'],
                    'post_title' => $post['gift_title'],
                    'post_status' => 'publish'
                );
                $post_id = wp_update_post($gift_post);
            }
            if (isset($post['min_total']))
                update_post_meta($post_id, 'min_total', sanitize_text_field($post['min_total']));
            if (isset($post['max_total']))
                update_post_meta($post_id, 'max_total', sanitize_text_field($post['max_total']));
            if (isset($post['gift_product']))
                update_post_meta($post_id, 'gift_product', sanitize_text_field($post['gift_product']));
            if (isset($post['gift_status'])) {
                update_post_meta($post_id, 'gift_status', 'on');
            } else {
                update_post_meta($post_id, 'gift_status', 'off');
            }
		}
		wp_redirect(admin_url('admin.php?page=wc-settings&tab=gift_tab'));
        exit();
	}
    // add gift product in woocommerce action
	public function add_gift_product()
	{
		global $woocommerce;
        // get gift list
		$get_all_gifts = get_posts(array(
		    'post_type' => 'gift_product',
		    'post_status' => 'publish',
		    'posts_per_page' => -1,
        ));

        if (!empty($get_all_gifts)) {
            // gift list loop
            foreach ($get_all_gifts as $gift) {
            	$set = 0;
            	$post_id = $gift->ID;
            	$getGiftMin = get_post_meta($post_id, 'min_total', true);
    			$getGiftMax = get_post_meta($post_id, 'max_total', true);
            	$getGiftStatus = get_post_meta($post_id, 'gift_status', true);
            	$getGiftProduct = get_post_meta($post_id, 'gift_product', true);
                // check gift active
            	if ($getGiftStatus == 'on') {

            		$total = $woocommerce->cart->cart_contents_total+$woocommerce->cart->tax_total;
            		$product_obj = get_page_by_path( $getGiftProduct, OBJECT, 'product' );

        			$items = $woocommerce->cart->get_cart();
                    $key = [];
                    // set gift product price
        			foreach($items as $item => $values) { 
			            $_product =  wc_get_product( $values['data']->get_id() );
			            $product_id = $values['product_id'];
                        $price = 1;
                        if (isset($values["custom_price"])) {
                            $price = $values["custom_price"];
                        }
			            if ($product_id == $product_obj->ID && $price == 0) {
			            	$set = 1;
			            	$key[] = $item;
			            }
			        }
                    // check order total
                    if ($total > $getGiftMin) {

                        if (!empty($getGiftMax) && $total > $getGiftMax) {
                            $set = 1;
                        }
				        if ($set == 0) {

				        	$cart_item_data = array('custom_price' => 0);
    						$woocommerce->cart->add_to_cart( $product_obj->ID, 1, '', array(), $cart_item_data);
				        }
            		}
            		else{
            			if (isset($key) && !empty($key)) {
                            foreach ($key as $k) {
                                WC()->cart->remove_cart_item($k);
                            }
            			}
            		}
            	}
            }
        }
	}
    // set gift product price in action
	public function woocommerce_custom_price_to_cart_item( $cart_object ) {  
	    if( !WC()->session->__isset( "reload_checkout" )) {
	        foreach ( $cart_object->cart_contents as $key => $value ) {
	            if( isset( $value["custom_price"] ) ) {
	                $value['data']->set_price($value["custom_price"]);
	            }
	        }  
	    }  
	}
    // remove gift product delete link
    public function gift_product_remove_link( $link, $cart_item_key ){
        if( WC()->cart->find_product_in_cart( $cart_item_key ) ){
            $cart_item = WC()->cart->cart_contents[ $cart_item_key ];
            $product_id = $cart_item['product_id'];
            if( isset( $cart_item["custom_price"] ) ) {
                $link = '';
            }
        }
        return $link;
    }
    // remove gift product quantity field
    public function gift_cart_item_quantity( $product_quantity, $cart_item_key ){
        $cart_item = WC()->cart->cart_contents[ $cart_item_key ];
        if( isset($cart_item["custom_price"])){
            $product_quantity = '1';
        }
        return $product_quantity;
    }
}