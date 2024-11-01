<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$post_id = '';
$type = 'Add';
// check post edit or add
if (isset($_GET['gift_id']) && $_GET['gift_id'] != 'new') {
    $type = 'Edit';
	$post_id = (int)$_GET['gift_id'];
	$gift_title = __(get_the_title($post_id), 'woocommerce');
	$get_gift_min = get_post_meta($post_id, 'min_total', true);
    $get_gift_max = get_post_meta($post_id, 'max_total', true);
    $get_gift_product = get_post_meta($post_id, 'gift_product', true);
    $get_gift_status = get_post_meta($post_id, 'gift_status', true);
}


?>

<h2>
	<?php _e( $type.' Gift', 'woocommerce' ); ?>
</h2>

<?php $nonce = wp_create_nonce( 'gift-action' ); ?>
<input type="hidden" name="gift_nonce" value="<?php echo $nonce ?>" />
<input type="hidden" name="post_type" value="gift">
<input type="hidden" name="gift_post_id" value="<?php echo esc_attr($post_id); ?>">
<table class="form-table table-outer product-fee-table">
    <tbody>
        <!-- Gift Title Field -->
        <tr valign="top">
            <th class="titledesc" scope="row"><label for="fee_settings_product_fee_title"><?php _e('Gift Title', 'woocommerce'); ?><span class="required-star">*</span></label></th>
            <td class="forminp mdtooltip">
                <input type="text" name="gift_title" class="text-class" id="fee_settings_product_fee_title" value="<?php echo isset($gift_title) ? esc_attr($gift_title) : ''; ?>" required="1" placeholder="<?php _e('Enter product gift title', 'woocommerce'); ?>">
            </td>

        </tr>
        <!-- Gift Min totla Field -->
        <tr valign="top">
            <th class="titledesc" scope="row"><label for="fee_settings_product_fee_title"><?php _e('Minimum Cart Total', 'woocommerce'); ?><span class="required-star">*</span></label></th>
            <td class="forminp mdtooltip">
                <input type="text" name="min_total" class="text-class" id="fee_settings_product_fee_title" value="<?php echo isset($get_gift_min) ? esc_attr($get_gift_min) : ''; ?>" required="1" placeholder="<?php _e('Minimum Cart Total', 'woocommerce'); ?>">
            </td>

        </tr>
        <!-- Gift Max Total Field -->
        <tr valign="top">
            <th class="titledesc" scope="row"><label for="fee_settings_product_fee_title"><?php _e('Maximum Cart Total', 'woocommerce'); ?><span class="required-star">*</span></label></th>
            <td class="forminp mdtooltip">
                <input type="text" name="max_total" class="text-class" id="fee_settings_product_fee_title" value="<?php echo isset($get_gift_max) ? esc_attr($get_gift_max) : ''; ?>"  placeholder="<?php _e('Maximum Cart Total', 'woocommerce'); ?>">
            </td>

        </tr>
        <!-- Gift Product Field -->
        <tr valign="top">
            <th class="titledesc" scope="row">	
                <label for="fee_settings_select_fee_type"><?php _e('Product', 'woocommerce'); ?></label>
            </th>
            <td class="forminp mdtooltip">
                <select name="gift_product" id="fee_settings_select_fee_type" class="">
                	<?php 
                		global $woocommerce;
                        $get_all_product = get_posts(array(
                            'post_type' => 'product',
                            'post_status' => 'publish',
                            'posts_per_page' => -1,
                        ));
						//$wc_currencies = $this->settings->get_list_currencies();
						foreach ( $get_all_product as $post ) {
                            global $product;
                            $detail = get_post($post->ID); 
                            $slug = $detail->post_name;
                	?>
                	<option value="<?php echo $slug; ?>" <?php echo isset($get_gift_product) && $get_gift_product == $slug ? 'selected="selected"' : '' ?>><?php echo get_the_title($post->ID); ?></option>
                	<?php } ?>
                </select>
            </td>
        </tr>
        <!-- Gift Status Field -->
        <tr valign="top">
            <th class="titledesc" scope="row"><label for="onoffswitch"><?php _e('Status', 'woocommerce'); ?></label></th>
            <td class="forminp mdtooltip">
                <label class="switch">
                    <input type="checkbox" name="gift_status" value="on" <?php echo (isset($get_gift_status) && $get_gift_status == 'off') ? '' : 'checked'; ?>>
                    <div class="slider round"></div>
                </label>
            </td>
        </tr>   
        
    </tbody>
</table>
<script type="text/javascript">
    jQuery(document).ready(function() {
        // add checkbox switch
        jQuery('input[name="gift_status"]').lc_switch();
        jQuery('.lcs_switch').click(function() {
            jQuery(this).toggleClass('lcs_off')
            jQuery(this).toggleClass('lcs_on')
        })
    })
</script>
