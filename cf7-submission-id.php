<?php
/*
Plugin Name: Contact Form 7 - Submission id
Description: Adds a field for a unique submission id.
Version: 2.1.0
Author: Ewald Harmsen
Text Domain: cf7-submission-id
*/

require __DIR__ . '/includes/submission_id.php';

/**
 * Function init plugin
**/
function cf7_submission_id_init(){
    wpcf7_add_form_tag('submission_id','cf7_submission_id_uid_form_tag_handler', true );
    wpcf7_add_form_tag('submission_id_hidden','cf7_submission_id_uid_form_tag_handler', true );
    add_action( 'admin_notices', 'cf7_submission_id_admin_notice' );
}
add_action( 'plugins_loaded', 'cf7_submission_id_init' , 20 );

//Enqueue javascript
add_action('wp_enqueue_scripts', 'cf7_submission_id_js');
function cf7_submission_id_js() {
	wp_enqueue_script('cf7_submission_id_script',plugins_url('/includes/submission_id.js', __FILE__),"",'1.0', true);
	wp_localize_script( 'cf7_submission_id_script', 'cf7_submission_id_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
}

/* Tag generator */
add_action( 'wpcf7_admin_init',
    'cf7_submission_id_add_tag_generator_id_field', 30 );

function cf7_submission_id_add_tag_generator_id_field() {
    $tag_generator = WPCF7_TagGenerator::get_instance();
    $tag_generator->add( 'submission_id', __( 'submission id', 'contact-form-7' ),
        'cf7_submission_id_tag_uid_field' );
}

//Update the counter when a form is submitted, and send the value back to the form
add_action('wp_ajax_update_cf7_submission_id', 'cf7_submission_id_submit');
add_action('wp_ajax_nopriv_update_cf7_submission_id', 'cf7_submission_id_submit');
function cf7_submission_id_submit() { 
	if( isset($_POST['formid']) ){
		//Retrieve current value from database, and add 1
		$val = get_post_meta( $_POST['formid'], "cf7_submission_id_COUNTER",true);
		if ($val == ""){
			$val = 1;
		}else{
			$val += 1;
		}
		//Save the value
		update_post_meta($_POST['formid'], "cf7_submission_id_COUNTER", $val );
		//Send value back to js, via AJAX
		wp_send_json($val+1);
	}
};

//Add filter to update the submission id, so that it is stored correctly in the database
add_filter( 'wpcf7_posted_data', 'cf7_submission_update_id', 1);
function cf7_submission_update_id( $array ) {
	//Find the field in the posted data
	$array_key = "";
	foreach ($array as $key => $val) {
		//submission_id-716
		if(substr($key, 0, strlen("submission_id")) === "submission_id"){
			$array_key = $key;
		}
	}
	
	$val = get_post_meta( $_POST['_wpcf7'], "cf7_submission_id_COUNTER",true);
	if ($val == ""){
		$val = 1;
	}else{
		$val += 1;
	}
	$array[$array_key] = $val;

    return $array;
};  

/**
 * Verify Contact Form 7 dependencies.
 */
function cf7_submission_id_admin_notice() {
    if ( is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ) {
        $wpcf7_path = WP_PLUGIN_DIR . '/contact-form-7/wp-contact-form-7.php';
        $wpcf7_data = get_plugin_data( $wpcf7_path, false, false );

        $version = $wpcf7_data['Version'];

        // If Contact Form 7 version is < 4.2.0.
        if ( $version < 4.2 ) {
            ?>

            <div class="error notice">
                <p>
                    <?php esc_html_e( "Error: Please update Contact Form 7.", 'cf7-mollie' );?>
                </p>
            </div>

            <?php
        }
    } else {
        // If Contact Form 7 isn't installed and activated, throw an error.
        $wpcf7_path = WP_PLUGIN_DIR . '/contact-form-7/wp-contact-form-7.php';
        $wpcf7_data = get_plugin_data( $wpcf7_path, false, false );
        ?>

        <div class="error notice">
            <p>
                <?php esc_html_e( 'Error: Please install and activate Contact Form 7.', 'cf7-mollie' );?>
            </p>
        </div>

        <?php
    }
}