<?php 

/**
 * Plugin Name: Woocommerce Gift Product
 * Plugin URI: https://www.webewox.com/wp-plugins
 * Description: Woocommerce Gift Product
 * Version: 1.0.0
 * Author: Usama Farooq
 * Author URI: https://www.webewox.com
 * Copyright: © 2018 WooCommerce / Gift Product.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: woocommerce-gift-product
 * WC requires at least: 2.6
 */

require_once __DIR__ . '/Gift.php';
define('GIFT_BASEPATH',plugin_basename( __FILE__ ));
$app = new Gift_product();
$app->init();