<?php
/**
 * Helper Functions and AJAX Handlers
 *
 * @since 1.0.0
 */

/**
 * Generate application ID
 *
 * @since 1.0.0
 */
function premium_academy_generate_app_id() {
    return 'APP-' . strtoupper( bin2hex( random_bytes( 8 ) ) ) . '-' . date( 'Y' );
}

/**
 * Get application link
 *
 * @since 1.0.0
 */
function premium_academy_get_app_link( $app_id ) {
    return admin_url( 'admin.php?page=premium-academy-app&id=' . urlencode( $app_id ) );
}

/**
 * Send email notification
 *
 * @since 1.0.0
 */
function premium_academy_send_email( $to, $subject, $message, $headers = array() ) {
    $default_headers = array( 'Content-Type: text/html; charset=UTF-8' );
    $headers = array_merge( $default_headers, $headers );
    
    return wp_mail( $to, $subject, $message, $headers );
}

/**
 * Format date
 *
 * @since 1.0.0
 */
function premium_academy_format_date( $date ) {
    return date_i18n( 'F j, Y', strtotime( $date ) );
}

/**
 * Format currency
 *
 * @since 1.0.0
 */
function premium_academy_format_currency( $amount ) {
    return 'GHS ' . number_format( $amount, 2 );
}

/**
 * Log error
 *
 * @since 1.0.0
 */
function premium_academy_log_error( $error_message ) {
    if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
        error_log( '[Premium Academy] ' . $error_message );
    }
}

/**
 * AJAX: Submit admission form
 *
 * @since 1.0.0
 */
add_action( 'wp_ajax_premium_academy_submit_admission', 'premium_academy_submit_admission' );
add_action( 'wp_ajax_nopriv_premium_academy_submit_admission', 'premium_academy_submit_admission' );

function premium_academy_submit_admission() {
    check_ajax_referer( 'premium_academy_nonce', 'nonce' );

    require_once PREMIUM_ACADEMY_PLUGIN_DIR . 'includes/class-validation.php';
    require_once PREMIUM_ACADEMY_PLUGIN_DIR . 'includes/class-sanitization.php';
    require_once PREMIUM_ACADEMY_PLUGIN_DIR . 'includes/class-database.php';
    require_once PREMIUM_ACADEMY_PLUGIN_DIR . 'includes/class-security-manager.php';

    $data = Premium_Academy_Sanitization::sanitize_application_data( $_POST );
    
    $validation = Premium_Academy_Validation::validate_admission_form( $data );
    if ( true !== $validation ) {
        wp_send_json_error( array( 'message' => implode( ' ', $validation ) ) );
    }

    $app_id = premium_academy_generate_app_id();
    $data['application_id'] = $app_id;

    $inserted = Premium_Academy_Database::insert_application( $data );
    if ( $inserted ) {
        Premium_Academy_Security_Manager::log_security_event( 'Application Submitted', 'App ID: ' . $app_id );
        
        // Send confirmation email
        premium_academy_send_email(
            $data['email'],
            __( 'Application Received', 'premium-academy' ),
            sprintf(
                __( 'Thank you for applying to Premium Academy. Your application ID is: %s', 'premium-academy' ),
                $app_id
            )
        );

        wp_send_json_success( array(
            'app_id' => $app_id,
            'message' => __( 'Application submitted successfully!', 'premium-academy' )
        ) );
    } else {
        wp_send_json_error( array( 'message' => __( 'Failed to submit application.', 'premium-academy' ) ) );
    }
}

/**
 * AJAX: Submit contact form
 *
 * @since 1.0.0
 */
add_action( 'wp_ajax_premium_academy_submit_contact', 'premium_academy_submit_contact' );
add_action( 'wp_ajax_nopriv_premium_academy_submit_contact', 'premium_academy_submit_contact' );

function premium_academy_submit_contact() {
    check_ajax_referer( 'premium_academy_nonce', 'nonce' );

    require_once PREMIUM_ACADEMY_PLUGIN_DIR . 'includes/class-validation.php';
    require_once PREMIUM_ACADEMY_PLUGIN_DIR . 'includes/class-sanitization.php';

    $name = Premium_Academy_Sanitization::sanitize_text( $_POST['name'] ?? '' );
    $email = Premium_Academy_Sanitization::sanitize_email( $_POST['email'] ?? '' );
    $message = Premium_Academy_Sanitization::sanitize_text( $_POST['message'] ?? '' );

    if ( ! $name || ! $email || ! $message ) {
        wp_send_json_error( array( 'message' => __( 'All fields are required.', 'premium-academy' ) ) );
    }

    if ( ! Premium_Academy_Validation::validate_email( $email ) ) {
        wp_send_json_error( array( 'message' => __( 'Invalid email address.', 'premium-academy' ) ) );
    }

    // Send email to admin
    premium_academy_send_email(
        get_option( 'admin_email' ),
        sprintf( __( 'New Contact Form Submission from %s', 'premium-academy' ), $name ),
        sprintf(
            __( 'Name: %s\nEmail: %s\nMessage: %s', 'premium-academy' ),
            $name, $email, $message
        )
    );

    wp_send_json_success( array( 'message' => __( 'Message sent successfully!', 'premium-academy' ) ) );
}

/**
 * Display frontend
 *
 * @since 1.0.0
 */
function premium_academy_display_frontend( $atts ) {
    ob_start();
    include PREMIUM_ACADEMY_PLUGIN_DIR . 'frontend/main.php';
    return ob_get_clean();
}
