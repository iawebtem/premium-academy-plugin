<?php
/**
 * Two Factor Authentication Class
 *
 * Handles TOTP-based 2FA
 *
 * @since 1.0.0
 */

class Premium_Academy_Two_Factor_Auth {

    /**
     * Generate secret key
     *
     * @since 1.0.0
     * @return string
     */
    public static function generate_secret() {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = '';
        for ( $i = 0; $i < 16; $i++ ) {
            $secret .= $chars[ rand( 0, strlen( $chars ) - 1 ) ];
        }
        return $secret;
    }

    /**
     * Get TOTP code
     *
     * @since 1.0.0
     * @param string $secret The secret key
     * @return string
     */
    public static function get_totp_code( $secret ) {
        $time = floor( time() / 30 );
        return self::hmac_totp( $secret, $time );
    }

    /**
     * Verify TOTP code
     *
     * @since 1.0.0
     * @param string $secret The secret key
     * @param string $code The code to verify
     * @param int $window Verification window (default 1)
     * @return bool
     */
    public static function verify_totp_code( $secret, $code, $window = 1 ) {
        $time = floor( time() / 30 );

        // Check current and previous codes
        for ( $i = -$window; $i <= $window; $i++ ) {
            if ( self::hmac_totp( $secret, $time + $i ) === $code ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate HMAC TOTP
     *
     * @since 1.0.0
     * @param string $secret The secret key
     * @param int $counter The time counter
     * @return string
     */
    private static function hmac_totp( $secret, $counter ) {
        $key = self::base32_decode( $secret );

        // Pack counter as big-endian
        $counter_bytes = pack( 'N*', 0 ) . pack( 'N*', $counter );

        // Calculate HMAC-SHA1
        $hmac = hash_hmac( 'sha1', $counter_bytes, $key, true );

        // Dynamic truncation
        $offset = ord( $hmac[19] ) & 0xf;
        $p = substr( $hmac, $offset, 4 );
        $value = unpack( 'N', $p )[1];
        $value = ( $value & 0x7fffffff ) % 1000000;

        return str_pad( $value, 6, '0', STR_PAD_LEFT );
    }

    /**
     * Decode base32
     *
     * @since 1.0.0
     * @param string $input The base32 string to decode
     * @return string
     */
    private static function base32_decode( $input ) {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $output = '';
        $v = 0;
        $vbits = 0;

        for ( $i = 0; $i < strlen( $input ); $i++ ) {
            $char = $input[$i];
            if ( '=' === $char ) {
                break;
            }

            $pos = strpos( $alphabet, $char );
            if ( false === $pos ) {
                continue;
            }

            $v = ( $v << 5 ) | $pos;
            $vbits += 5;

            if ( $vbits >= 8 ) {
                $vbits -= 8;
                $output .= chr( ( $v >> $vbits ) & 255 );
            }
        }

        return $output;
    }
}
