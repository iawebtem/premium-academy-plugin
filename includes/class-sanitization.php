<?php
/**
 * Sanitization Class
 *
 * Handles output sanitization for the plugin
 *
 * @since 1.0.0
 */

class Premium_Academy_Sanitization {

    /**
     * Sanitize for HTML output
     *
     * @since 1.0.0
     * @param string $data The data to sanitize
     * @return string
     */
    public static function sanitize_html_output( $data ) {
        return wp_kses_post( $data );
    }

    /**
     * Sanitize for HTML attributes
     *
     * @since 1.0.0
     * @param string $data The data to sanitize
     * @return string
     */
    public static function sanitize_attribute( $data ) {
        return wp_kses_post( $data );
    }

    /**
     * Sanitize for JavaScript output
     *
     * @since 1.0.0
     * @param mixed $data The data to sanitize
     * @return string
     */
    public static function sanitize_js_output( $data ) {
        return wp_json_encode( $data );
    }

    /**
     * Sanitize for URL output
     *
     * @since 1.0.0
     * @param string $url The URL to sanitize
     * @return string
     */
    public static function sanitize_url_output( $url ) {
        return esc_url( $url );
    }

    /**
     * Sanitize textarea output
     *
     * @since 1.0.0
     * @param string $data The data to sanitize
     * @return string
     */
    public static function sanitize_textarea_output( $data ) {
        return wp_kses_post( $data );
    }

    /**
     * Sanitize form data array
     *
     * @since 1.0.0
     * @param array $data The data to sanitize
     * @return array
     */
    public static function sanitize_form_data( $data ) {
        $sanitized = array();

        foreach ( $data as $key => $value ) {
            $sanitized[ sanitize_key( $key ) ] = sanitize_text_field( $value );
        }

        return $sanitized;
    }
}
