<?php
// Display the image upload field when "Check Payment" is selected
add_action('woocommerce_review_order_before_submit', 'add_image_upload_field_check_payment');
function add_image_upload_field_check_payment() {
// Get the currently selected payment method
$chosen_payment_method = WC()->session->get('chosen_payment_method');

// Only display the field if "Check Payment" is selected
$display_style = ($chosen_payment_method === 'cheque') ? 'block' : 'none';
?>
<div id="image_upload_wrapper" class="form-row form-row-wide" style="display: <?php echo $display_style; ?>;">
  <label for="checkout_image"><?php esc_html_e('Upload Image for Check Payment', 'woocommerce'); ?></label>
  <input type="file" id="checkout_image" name="checkout_image" accept="image/*" />
  <input type="hidden" name="checkout_image_field" id="checkout_image_field" />
  <div id="image_upload_status"></div>
</div>
<?php
}

// Enqueue necessary scripts
add_action('wp_enqueue_scripts', 'enqueue_checkout_image_upload_scripts');
function enqueue_checkout_image_upload_scripts() {
    if (is_checkout()) {
        wp_enqueue_script('checkout-image-upload', get_template_directory_uri() . '/js/checkout-image-upload.js', array('jquery'), '1.0', true);
        wp_localize_script('checkout-image-upload', 'checkout_image_upload_params', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('upload_checkout_image'),
        ));

        // Add some basic styling
        wp_add_inline_style('woocommerce-layout', '
            #image_upload_wrapper { margin: 1em 0; }
            #image_upload_status { margin-top: 0.5em; font-size: 0.9em; }
        ');
    }
    
}

// Handle the image upload via AJAX
add_action('wp_ajax_upload_checkout_image', 'handle_checkout_image_upload');
add_action('wp_ajax_nopriv_upload_checkout_image', 'handle_checkout_image_upload');
function handle_checkout_image_upload() {
    check_ajax_referer('upload_checkout_image', 'nonce');

    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        wp_send_json_error(array('message' => __('No file uploaded or upload error.', 'woocommerce')));
    }

    // Verify the file is an image
    $file_info = wp_check_filetype_and_ext($_FILES['file']['tmp_name'], $_FILES['file']['name']);
    if (!in_array($file_info['ext'], array('jpg', 'jpeg', 'png', 'gif'))) {
        wp_send_json_error(array('message' => __('Only JPG, PNG, and GIF images are allowed.', 'woocommerce')));
    }

    // Handle the upload
    $upload = wp_handle_upload($_FILES['file'], array('test_form' => false));

    if (isset($upload['error'])) {
        wp_send_json_error(array('message' => $upload['error']));
    }

    // Store the file URL in the session temporarily
    WC()->session->set('checkout_image_url', $upload['url']);
    wp_send_json_success(array('url' => $upload['url']));
}

// Save the image URL to order meta
add_action('woocommerce_checkout_update_order_meta', 'save_checkout_image_to_order');
function save_checkout_image_to_order($order_id) {
    if (isset($_POST['payment_method']) && $_POST['payment_method'] === 'cheque') {
        $image_url = WC()->session->get('checkout_image_url');
        if (!empty($image_url)) {
            $order = wc_get_order($order_id);
            $order->update_meta_data('_checkout_image_url', esc_url_raw($image_url));
            $order->save();

            // Clear the session after saving
            WC()->session->set('checkout_image_url', null);
        }
    }
}

// Display the image in admin order details
add_action('woocommerce_admin_order_data_after_billing_address', 'display_checkout_image_admin');
function display_checkout_image_admin($order) {
    $image_url = $order->get_meta('_checkout_image_url');
    if (!empty($image_url)) {
        echo '<p><strong>' . esc_html__('Uploaded Image for Check Payment', 'woocommerce') . ':</strong><br>';
        echo '<a href="' . esc_url($image_url) . '" target="_blank">';
        echo '<img src="' . esc_url($image_url) . '" style="max-width: 200px; height: auto;" />';
        echo '</a></p>';
    }
}

// Display the image in frontend order details
add_action('woocommerce_order_details_after_order_table', 'display_checkout_image_in_order_details', 10, 1);
function display_checkout_image_in_order_details($order) {
    $image_url = $order->get_meta('_checkout_image_url');
		echo $image_url;

    if (!empty($image_url)) {
        echo '<div class="checkout-image-details">';
        echo '<h3>' . esc_html__('Uploaded Image for Check Payment', 'woocommerce') . '</h3>';
        echo '<p><a href="' . esc_url($image_url) . '" target="_blank">';
        echo '<img src="' . esc_url($image_url) . '" alt="' . esc_attr__('Check Payment Image', 'woocommerce') . '" style="max-width: 200px; height: auto;" />';
        echo '</a></p>';
        echo '</div>';
    }
}

add_action('woocommerce_admin_order_data_after_order_details', function( $order ) {
		$image_url = $order->get_meta('_checkout_image_url');
		if (!empty($image_url)) {
				echo '<p><strong>' . esc_html__('Uploaded Image for Check Payment', 'woocommerce') . ':</strong><br>';
				echo '<a href="' . esc_url($image_url) . '" target="_blank">';
				echo '<img src="' . esc_url($image_url) . '" style="max-width: 200px; height: auto;" />';
				echo '</a></p>';
		}
});