(function($){
    var fire = {
        term_image : function(){

            var frame;
            $('.upload-term-image').on('click',function(e){
                e.preventDefault();

                if( frame ){
                    frame.open();
                    return;
                }
                frame = wp.media({
                    title: 'Select Image',
                    button: {text: 'Upload this image'},
                    multiple : false
                });
                frame.on('select',function(){
                    var attachment = frame.state().get('selection').first().toJSON();
                    console.log( attachment );
                    $('#term_image').val( attachment.id );
                    $('#term-image-preview').html('<img src="' + attachment.url + '" style="max-width:200px; height:auto " >');
                    $('.upload-term-image').siblings('.remove-term-image').show();
                });

                frame.open();
            });

            //remove

            $('.remove-term-image').on('click',function(e){
                e.preventDefault();
                $('#term_image').val('');
                $('#term-image-preview').empty();
                $('upload-term-image').hide();

            });
        }
    };

    $(function(){
        fire.term_image();
    });

})(jQuery);