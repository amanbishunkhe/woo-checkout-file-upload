(function($){
    fire = {
        checkout_form_event : function(){
            $('#rws_file').change(function(){

                if( ! this.files.length ){
                    console.log( "empty ")
                }else{
                    const file = this.files[0];
                    console.log( file );
                    $('#rws_filelist').html('<img src="' + URL.createObjectURL(file) + '"> <span>' + file.name + '</span>');
                    const formdata = new FormData();
                    formdata.append( 'rws_file',file );

                    $.ajax({
                        url: wc_checkout_params.ajax_url + '?action=rwsupload',
                        type: 'POST',
                        data: formdata,
                        contentType: false,
                        enctype: 'multipart/form-data',
                        processData: false,
                        success: function ( response ) {
                           $( 'input[name="rws_file_field"]' ).val( response );
                           console.log( response ,'success');
                        }
                    });

                }
            });
        },

        checkout_cheque_payment : function(){
            function toggleFileUpload(){
                const upload_file_field = $('#custom_cheque_file_upload');
                if( $('#payment_method_cheque' ).is(':checked')){
                    console.log( upload_file_field );
                    $('#custom_cheque_file_upload').insertAfter('#payment_method_cheque').show();
                }else{
                    console.log(' no test');
                    $('#custom_cheque_file_upload').hide();
                }
            }

            // run on page load
            toggleFileUpload();

            // Run when payment method changes
            $(document).on('change', 'input[name="payment_method"]', function() {
                toggleFileUpload();
                console.log( ' run ');
            });
        }
    }
    $(function(){
        fire.checkout_form_event();
        fire.checkout_cheque_payment();
    })
})(jQuery);