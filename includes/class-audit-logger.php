<?php
/**
 * Audit Logger Class
 *
 * @since 1.0.0
 */

class Premium_Academy_Audit_Logger {

    /**
     * Log action
     *
     * @since 1.0.0
     */
    public static function log( $action, $details = '', $user_id = 0, $ip_address = '', $user_agent = '' ) {
        global $wpdb;
        $table = $wpdb->prefix . 'pa_audit_logs';

        if ( 0 === $user_id ) {
            $user_id = get_current_user_id();
        }

        if ( empty( $ip_address ) ) {
            require_once PREMIUM_ACADEMY_PLUGIN_DIR . 'includes/class-security-manager.php';
            $ip_address = Premium_Academy_Security_Manager::get_client_ip();
        }

        if ( empty( $user_agent ) ) {
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        }

        return $wpdb->insert(
            $table,
            array(
                'user_id' => $user_id,
                'action' => $action,
                'details' => $details,
                'ip_address' => $ip_address,
                'user_agent' => $user_agent
            ),
            array( '%d', '%s', '%s', '%s', '%s' )
        );
    }

    /**
     * Get audit logs
     *
     * @since 1.0.0
     */
    public static function get_logs( $limit = 100 ) {
        global $wpdb;
        $table = $wpdb->prefix . 'pa_audit_logs';
        return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table ORDER BY created_at DESC LIMIT %d", $limit ) );
    }

    /**
     * Get audit logs by user
     *
     * @since 1.0.0
     */
    public static function get_logs_by_user( $user_id, $limit = 50 ) {
        global $wpdb;
        $table = $wpdb->prefix . 'pa_audit_logs';
        return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table WHERE user_id = %d ORDER BY created_at DESC LIMIT %d", $user_id, $limit ) );
    }

    /**
     * Get audit logs by action
     *
     * @since 1.0.0
     */
    public static function get_logs_by_action( $action, $limit = 50 ) {
        global $wpdb;
        $table = $wpdb->prefix . 'pa_audit_logs';
        return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table WHERE action = %s ORDER BY created_at DESC LIMIT %d", $action, $limit ) );
    }

    /**
     * Delete old logs
     *
     * @since 1.0.0
     */
    public static function cleanup_old_logs( $days = 90 ) {
        global $wpdb;
        $table = $wpdb->prefix . 'pa_audit_logs';
        $date = date( 'Y-m-d H:i:s', strtotime( "-{$days} days" ) );
        return $wpdb->query( $wpdb->prepare( "DELETE FROM $table WHERE created_at < %s", $date ) );
    }
}
