<?php
/**
 * Function add submission id field in wpcf7
 * @version 1.0
**/

function cf7_submission_id_uid_form_tag_handler ($tag){
    if ( empty( $tag->name ) ) {
        return '';
    }

    $validation_error = wpcf7_get_validation_error( $tag->name );

    $class = wpcf7_form_controls_class( $tag->type );

    $class .= ' wpcf7-validates-as-number';

    if ( $validation_error ) {
        $class .= ' wpcf7-not-valid';
    }

    $atts = array();

    $atts['class'] = $tag->get_class_option( $class );
    $atts['id'] = $tag->get_id_option();
    $atts['tabindex'] = $tag->get_option( 'tabindex', 'signed_int', true );
    $atts['readonly'] = 'readonly';

    if ( $tag->is_required() ) {
        $atts['aria-required'] = 'true';
    }

    $atts['aria-invalid'] = $validation_error ? 'true' : 'false';

    $value = (string) reset( $tag->values );
    $default_value = $tag->get_default_option( $value );

    $wpcf7 = WPCF7_ContactForm::get_current();
    $formid = $wpcf7->id();
	//Get current value from database
	$value = get_post_meta( $formid, "cf7_submission_id_COUNTER",true);
	if ($value == ""){
		$value =1;
	}else{
		$value +=1;
	}

    if ($value < $default_value){
        $value = $default_value;
    }

    $atts['value'] = $value;

    $atts['name'] = $tag->name;

    if (strpos($atts['class'], 'hidden') !== false) {
        $atts['type'] = 'hidden';
    }else{
        $atts['type'] = 'text';
    }

    $atts = wpcf7_format_atts( $atts );

    $html = sprintf(
        '<span class="wpcf7-form-control-wrap %1$s"><input %2$s />%3$s</span>',
        sanitize_html_class( $tag->name ), $atts, $validation_error );

    return $html;
}

/* Validation filter */
add_filter( 'wpcf7_validate_submission_uid', 'wpcf7_number_validation_filter', 10, 2 );
add_filter( 'wpcf7_validate_submission_uid*', 'wpcf7_number_validation_filter', 10, 2 );
add_filter( 'wpcf7_validate_range', 'wpcf7_number_validation_filter', 10, 2 );
add_filter( 'wpcf7_validate_range*', 'wpcf7_number_validation_filter', 10, 2 );

function cf7_submission_id_tag_uid_field( $contact_form, $args = '' ) {
    $args = wp_parse_args( $args, array() );
    $type = 'submission_id';

    $description = __( "Generate a form-tag for a hidden field where the submisison id will be stored.", 'cf7-mollie' );
?>
<div class="control-box">
<fieldset>
<legend><?php echo esc_html( $description ); ?></legend>

<table class="form-table">
<tbody>
        
    <tr>
    <th scope="row"><?php echo esc_html( __( 'Field type', 'contact-form-7' ) ); ?></th>
    <td>
        <fieldset>
            <legend class="screen-reader-text"><?php echo esc_html( __( 'Field type', 'contact-form-7' ) ); ?></legend>
            <select name="tagtype">
                <option value="submission_id" selected="selected"><?php echo esc_html( __( 'Tekst', 'contact-form-7' ) ); ?></option>
                <option value="submission_id_hidden"><?php echo esc_html( __( 'Hidden', 'contact-form-7' ) ); ?></option>
            </select>
        </fieldset>
    </td>
    </tr>

    <tr>
    <th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-name' ); ?>"><?php echo esc_html( __( 'Name', 'contact-form-7' ) ); ?></label></th>
    <td><input type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr( $args['content'] . '-name' ); ?>" /></td>
    </tr>

    <tr>
    <th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-values' ); ?>"><?php echo esc_html( __( 'Starting number', 'contact-form-7' ) ); ?></label></th>
    <td><input type="text" name="values" class="oneline" id="<?php echo esc_attr( $args['content'] . '-values' ); ?>" /><br />
    </td>
    </tr>

    <tr>
    <th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-id' ); ?>"><?php echo esc_html( __( 'Id attribute', 'contact-form-7' ) ); ?></label></th>
    <td><input type="text" name="id" class="idvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-id' ); ?>" /></td>
    </tr>

    <tr>
    <th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-class' ); ?>"><?php echo esc_html( __( 'Class attribute', 'contact-form-7' ) ); ?></label></th>
    <td><input type="text" name="class" class="classvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-class' ); ?>" /></td>
    </tr>
</tbody>
</table>
</fieldset>
</div>

<div class="insert-box">
    <input type="text" name="<?php echo $type; ?>" class="tag code" readonly="readonly" onfocus="this.select()" />

    <div class="submitbox">
    <input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr( __( 'Insert Tag', 'contact-form-7' ) ); ?>" />
    </div>

    <br class="clear" />

    <p class="description mail-tag"><label for="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>"><?php echo sprintf( esc_html( __( "To use the value input through this field in a mail field, you need to insert the corresponding mail-tag (%s) into the field on the Mail tab.", 'contact-form-7' ) ), '<strong><span class="mail-tag"></span></strong>' ); ?><input type="text" class="mail-tag code hidden" readonly="readonly" id="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>" /></label></p>
</div>
<?php
}