jQuery(document).ready(function($) {
    // Handle payment method change to show/hide our upload field
    $('form.checkout').on('change', 'input[name="payment_method"]', function() {
        if ($(this).val() === 'cheque') {
            $('#image_upload_wrapper').show();
        } else {
            $('#image_upload_wrapper').hide();
        }
    });

    // Handle file upload
    $('#checkout_image').on('change', function(e) {
        e.preventDefault();

        var file = this.files[0];
        var formData = new FormData();
        formData.append('file', file);
        formData.append('action', 'upload_checkout_image');
        formData.append('nonce', checkout_image_upload_params.nonce);

        $('#image_upload_status').html('<p><?php esc_html_e("Uploading...", "woocommerce"); ?></p>');

        $.ajax({
            url: checkout_image_upload_params.ajax_url,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                if (response.success) {
                    $('#image_upload_status').html('<p style="color:green;"><?php esc_html_e("Image uploaded successfully!", "woocommerce"); ?></p>');
                    $('#checkout_image_field').val(response.data.url);
                } else {
                    $('#image_upload_status').html('<p style="color:red;">' + response.data.message + '</p>');
                }
            },
            error: function() {
                $('#image_upload_status').html('<p style="color:red;"><?php esc_html_e("Upload failed. Please try again.", "woocommerce"); ?></p>');
            }
        });
    });
});