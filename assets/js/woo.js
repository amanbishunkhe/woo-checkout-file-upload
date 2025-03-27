(function($) {
    var fire = {
        checkout_form_event: function() {
            // Function to bind the file upload event
            function bindFileUploadEvent() {
                // Use a more specific selector and ensure it’s rebindable
                $('.payment_method_cheque #rws_file').off('change'); // Remove any existing listeners to avoid duplicates
                $('.payment_method_cheque #rws_file').on('change', function(e) {
                    console.log('File input changed');
                    var $this = $(this);
                    var files = this.files;

                    if (!files || !files.length) {
                        console.log('No files selected');
                        $('#rws_filelist').html('<p>No file selected.</p>');
                        return;
                    }

                    var file = files[0];
                    console.log('Selected file:', file);

                    // Display the file preview
                    $('#rws_filelist').html('<img src="' + URL.createObjectURL(file) + '"> <span>' + file.name + '</span>');

                    // Prepare FormData for AJAX upload
                    var formData = new FormData();
                    formData.append('rws_file', file);

                    // AJAX request to upload the file
                    $.ajax({
                        url: wc_checkout_params.ajax_url + '?action=rwsupload',
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        enctype: 'multipart/form-data',
                        processData: false,
                        success: function(response) {
                            console.log('AJAX success:', response);
                            $('input[name="rws_file_field"]').val(response);
                        },
                        error: function(xhr, status, error) {
                            console.log('AJAX error:', status, error);
                        }
                    });
                });
            }

            // Initial binding
            bindFileUploadEvent();

            // Rebind after WooCommerce AJAX updates
            $(document.body).on('updated_checkout', function() {
                console.log('Checkout updated, rebinding file upload event');
                bindFileUploadEvent();
            });
        },
       
    };
    // Run on DOM ready
    $(function() {
        fire.checkout_form_event();       
    });
})(jQuery);

