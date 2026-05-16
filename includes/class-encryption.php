<?php
/**
 * Encryption Class
 *
 * Handles data encryption and decryption
 *
 * @since 1.0.0
 */

class Premium_Academy_Encryption {

    /**
     * Get encryption key
     *
     * @since 1.0.0
     * @return string
     */
    private static function get_encryption_key() {
        // Use WordPress security keys for encryption
        if ( defined( 'ABSPATH' ) ) {
            return wp_salt( 'nonce' );
        }
        return 'default-key';
    }

    /**
     * Encrypt data
     *
     * @since 1.0.0
     * @param string $data The data to encrypt
     * @return string
     */
    public static function encrypt( $data ) {
        if ( ! function_exists( 'openssl_encrypt' ) ) {
            return base64_encode( $data );
        }

        $key = self::get_encryption_key();
        $iv = openssl_random_pseudo_bytes( openssl_cipher_iv_length( 'AES-256-CBC' ) );
        $encrypted = openssl_encrypt( $data, 'AES-256-CBC', hash( 'sha256', $key, true ), false, $iv );

        return base64_encode( $iv . $encrypted );
    }

    /**
     * Decrypt data
     *
     * @since 1.0.0
     * @param string $encrypted_data The encrypted data
     * @return string
     */
    public static function decrypt( $encrypted_data ) {
        if ( ! function_exists( 'openssl_decrypt' ) ) {
            return base64_decode( $encrypted_data, true );
        }

        $key = self::get_encryption_key();
        $data = base64_decode( $encrypted_data, true );
        $iv_length = openssl_cipher_iv_length( 'AES-256-CBC' );
        $iv = substr( $data, 0, $iv_length );
        $encrypted = substr( $data, $iv_length );

        return openssl_decrypt( $encrypted, 'AES-256-CBC', hash( 'sha256', $key, true ), false, $iv );
    }

    /**
     * Hash data securely
     *
     * @since 1.0.0
     * @param string $data The data to hash
     * @return string
     */
    public static function hash( $data ) {
        return hash( 'sha256', $data );
    }
}
