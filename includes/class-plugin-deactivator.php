<?php
/**
 * Fired during plugin deactivation
 *
 * @since 1.0.0
 */

class Premium_Academy_Deactivator {

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since 1.0.0
     */
    public static function deactivate() {
        // Clear scheduled hooks
        wp_clear_scheduled_hook( 'premium_academy_daily_backup' );
        wp_clear_scheduled_hook( 'premium_academy_cleanup_logs' );
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
}
