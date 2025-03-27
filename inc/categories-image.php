<?php
class categoryImage{
    function __construct( ){
        add_action( 'category_add_form_fields',array( $this, 'add_term_image_field' ) );
        add_action('admin_enqueue_scripts',array( $this,'term_script' ));

        add_action('created_category', array( $this, 'save_term_image' ) ); // Replace 'category' with your taxonomy
        add_action('edited_category', array( $this, 'save_term_image' ) );  // Replace 'category' with your taxonomy

        add_action('category_edit_form_fields', array( $this, 'edit_term_image_field' ));
    }

    function add_term_image_field($taxonomy){
        ?>
        <div class="form-field term-image-wrap">
            <label for="term_image">Term Image</label>
            <input type="hidden" name="term_image" id="term_image" value="">
            <div id="term-image-preview" style="margin-bottom: 10px;"></div>
            <button class="upload-term-image button">Upload Image</button>
            <button class="remove-term-image button" style="display:none;">Remove Image</button>
            <p class="description">Upload an image for this term.</p>
        </div>
        <?php
    }

    function term_script(){
        wp_enqueue_media();
        wp_enqueue_script( 'term-script', get_template_directory_uri().'/assets/js/term-script.js' );
    }

    function save_term_image( $term_id ){
        if( isset( $_POST['term_image'] ) && current_user_can('manage_categories') ){
            $image_id = sanitize_text_field($_POST['term_image']);
            if( $image_id ){
                update_term_meta($term_id, 'zci_taxonomy_image', $image_id );
            }else{
                delete_term_meta($term_id, 'zci_taxonomy_image');
            }
        }
    }

    function edit_term_image_field( $term ){
        $image_id = get_term_meta($term->term_id, 'zci_taxonomy_image', true);
        $image_url = $image_id ? wp_get_attachment_url($image_id) : '';
        ?>
        <tr class="form-field term-image-wrap">
            <th scope="row"><label for="term_image">Term Image</label></th>
            <td>
                <input type="hidden" name="term_image" id="term_image" value="<?php echo esc_attr($image_id); ?>">
                <div id="term-image-preview" style="margin-bottom: 10px;">
                    <?php if ($image_url) : ?>
                        <img src="<?php echo esc_url($image_url); ?>" style="max-width: 200px; height: auto;">
                    <?php endif; ?>
                </div>
                <button class="upload-term-image button">Upload Image</button>
                <button class="remove-term-image button" style="<?php echo $image_id ? '' : 'display:none;'; ?>">Remove Image</button>
                <p class="description">Upload an image for this term.</p>
            </td>
        </tr>
        <?php
    }
}

new categoryImage();