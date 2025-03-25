<?php

class CheckoutFileUpload {

    function __construct() {
        // Add file upload field to the checkout form
        add_action('woocommerce_after_checkout_billing_form', array($this, 'custom_checkout_file_upload'),10);
        
        // Enqueue JavaScript
        add_action('wp_enqueue_scripts', array($this, 'enqueue_js'));
        
        // Handle AJAX upload
        add_action('wp_ajax_rwsupload', array($this, 'rws_file_upload'));
        add_action('wp_ajax_nopriv_rwsupload', array($this, 'rws_file_upload'));

        add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'rws_save_what_we_added' ) );
        add_action( 'woocommerce_admin_order_data_after_order_details', array( $this, 'rws_order_meta_general' ) );
    }

    function custom_checkout_file_upload($checkout) {
        ?>
        <div class="form-row form-row-wide custom_cheque_file_upload" >
            <input type="file" id="rws_file" name="rws_file" />
            <input type="hidden" name="rws_file_field" />
            <label for="rws_file"><a>Select a cool image</a></label>
            <div id="rws_filelist"></div>
        </div>
        <?php
    }

    function enqueue_js() {
        wp_enqueue_script('checkout-woo', get_template_directory_uri() . '/assets/js/woo.js', array('jquery'), '1.0.0', true);
    }

    function rws_file_upload() {
        $upload_dir = wp_upload_dir();      
        if (isset($_FILES['rws_file'])) {
            $path = $upload_dir['path'] . '/' . basename($_FILES['rws_file']['name']);
            
            if (move_uploaded_file($_FILES['rws_file']['tmp_name'], $path)) {
                echo $upload_dir['url'] . '/' . basename($_FILES['rws_file']['name']);
            }
        }
        die;
    }
    
    function rws_save_what_we_added( $order_id ){

        if( ! empty( $_POST[ 'rws_file_field' ] ) && ( $order = wc_get_order( $order_id ) ) ) {
            $order->update_meta_data( 'rws_file_field', sanitize_text_field( $_POST[ 'rws_file_field' ] ) );
            $order->save();
        }

    }    

    function rws_order_meta_general( $order ){

        $file = $order->get_meta( 'rws_file_field' );
        if( $file ) {
            echo printf( "<h3> Uploaded Image </h3>" );
            echo '<img src="' . esc_url( $file ) . '" style=" width: 300px; height: 300px; "  />';
        }

    }

}

new CheckoutFileUpload();



