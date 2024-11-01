<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$nonce = wp_create_nonce( 'gift-nonce' );

// get gift list
$get_all_gifts = get_posts(array(
    'post_type' => 'gift_product',
    'post_status' => 'publish',
    'posts_per_page' => -1,
        ));
?>

<h2 class="wc-shipping-zones-heading">
	<?php _e( 'Gifts', 'woocommerce' ); ?>
	<a href="<?php echo admin_url( 'admin.php?page=wc-settings&tab=gift_tab&gift_id=new&&_wpnonce='.$nonce ); ?>" class="page-title-action"><?php esc_html_e( 'Add Gift', 'woocommerce' ); ?></a>
</h2>
<table class="wc-shipping-zones widefat">
	<thead>
		<tr>
			<th class="wc-shipping-zone-name"><?php esc_html_e( 'Name', 'woocommerce' ); ?></th>
			<th class="wc-shipping-zone-region"><?php esc_html_e( 'Min', 'woocommerce' ); ?></th>
			<th class="wc-shipping-zone-methods"><?php esc_html_e( 'Max', 'woocommerce' ); ?></th>
			<th class="wc-shipping-zone-methods"><?php esc_html_e( 'Status', 'woocommerce' ); ?></th>
		</tr>
	</thead>
	<tbody class="wc-shipping-zone-rows">
		<!-- Show gift list -->
		<?php
            if (!empty($get_all_gifts)) {
                $i = 1;
                foreach ($get_all_gifts as $gift) {
                	$title = get_the_title($gift->ID) ? get_the_title($gift->ID) : 'Gift';
                    $get_gift_min = get_post_meta($gift->ID, 'min_total', true);
                    $get_gift_max = get_post_meta($gift->ID, 'max_total', true);
                    $get_gift_status = get_post_meta($gift->ID, 'gift_status', true);
        ?>
		<tr data-id="{{ data.zone_id }}">
			<td class="wc-shipping-zone-name">
				<a href="admin.php?page=wc-settings&tab=gift_tab&gift_id=<?php echo $gift->ID ?>&_wpnonce=<?php echo $nonce; ?>"><?php echo $title ?></a>
				<div class="row-actions">
					<a href="admin.php?page=wc-settings&tab=gift_tab&gift_id=<?php echo $gift->ID ?>&_wpnonce=<?php echo $nonce; ?>"><?php _e( 'Edit', 'woocommerce' ); ?></a> | <a href="admin.php?page=wc-settings&tab=gift_tab&gift_delete=<?php echo $gift->ID ?>&_wpnonce=<?php echo $nonce; ?>" class="wc-shipping-zone-delete"><?php _e( 'Delete', 'woocommerce' ); ?></a>
				</div>
			</td>
			<td class="wc-shipping-zone-region">
				<?php echo $get_gift_min ?>
			</td>
			<td class="wc-shipping-zone-methods">
				<?php echo $get_gift_max ?>
			</td>
			<td class="wc-shipping-zone-methods">
				<?php echo $get_gift_status ?>
			</td>
		</tr>
		<?php } } ?>
	</tbody>
</table>
