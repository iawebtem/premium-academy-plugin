<?php
/**
 * Database Operations Class
 *
 * @since 1.0.0
 */

class Premium_Academy_Database {

    /**
     * Insert application
     *
     * @since 1.0.0
     */
    public static function insert_application( $data ) {
        global $wpdb;
        $table = $wpdb->prefix . 'pa_applications';

        $result = $wpdb->insert(
            $table,
            array(
                'application_id' => $data['application_id'],
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'date_of_birth' => $data['dob'],
                'gender' => $data['gender'],
                'grade' => $data['grade'],
                'parent_name' => $data['parent_name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'address' => $data['address'] ?? '',
                'photo' => $data['photo'] ?? null,
                'status' => 'pending'
            ),
            array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
        );

        return $result ? $wpdb->insert_id : false;
    }

    /**
     * Get application
     *
     * @since 1.0.0
     */
    public static function get_application( $app_id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'pa_applications';
        return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE application_id = %s", $app_id ) );
    }

    /**
     * Get all applications
     *
     * @since 1.0.0
     */
    public static function get_all_applications( $status = null, $limit = 100, $offset = 0 ) {
        global $wpdb;
        $table = $wpdb->prefix . 'pa_applications';

        if ( $status ) {
            $query = $wpdb->prepare( "SELECT * FROM $table WHERE status = %s ORDER BY created_at DESC LIMIT %d OFFSET %d", $status, $limit, $offset );
        } else {
            $query = $wpdb->prepare( "SELECT * FROM $table ORDER BY created_at DESC LIMIT %d OFFSET %d", $limit, $offset );
        }

        return $wpdb->get_results( $query );
    }

    /**
     * Update application status
     *
     * @since 1.0.0
     */
    public static function update_application_status( $app_id, $status ) {
        global $wpdb;
        $table = $wpdb->prefix . 'pa_applications';

        return $wpdb->update(
            $table,
            array( 'status' => $status ),
            array( 'application_id' => $app_id ),
            array( '%s' ),
            array( '%s' )
        );
    }

    /**
     * Delete application
     *
     * @since 1.0.0
     */
    public static function delete_application( $app_id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'pa_applications';
        return $wpdb->delete( $table, array( 'application_id' => $app_id ) );
    }

    /**
     * Get approved students
     *
     * @since 1.0.0
     */
    public static function get_approved_students() {
        return self::get_all_applications( 'approved', 1000, 0 );
    }

    /**
     * Count applications
     *
     * @since 1.0.0
     */
    public static function count_applications( $status = null ) {
        global $wpdb;
        $table = $wpdb->prefix . 'pa_applications';

        if ( $status ) {
            return $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $table WHERE status = %s", $status ) );
        }

        return $wpdb->get_var( "SELECT COUNT(*) FROM $table" );
    }

    /**
     * Search applications
     *
     * @since 1.0.0
     */
    public static function search_applications( $search_term, $limit = 100 ) {
        global $wpdb;
        $table = $wpdb->prefix . 'pa_applications';
        $search = '%' . $wpdb->esc_like( $search_term ) . '%';

        return $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM $table WHERE first_name LIKE %s OR last_name LIKE %s OR email LIKE %s OR phone LIKE %s ORDER BY created_at DESC LIMIT %d",
            $search, $search, $search, $search, $limit
        ) );
    }

    /**
     * Save setting
     *
     * @since 1.0.0
     */
    public static function save_setting( $key, $value ) {
        global $wpdb;
        $table = $wpdb->prefix . 'pa_settings';

        $existing = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM $table WHERE setting_key = %s", $key ) );

        if ( $existing ) {
            return $wpdb->update(
                $table,
                array( 'setting_value' => maybe_serialize( $value ) ),
                array( 'setting_key' => $key ),
                array( '%s' ),
                array( '%s' )
            );
        } else {
            return $wpdb->insert(
                $table,
                array( 'setting_key' => $key, 'setting_value' => maybe_serialize( $value ) ),
                array( '%s', '%s' )
            );
        }
    }

    /**
     * Get setting
     *
     * @since 1.0.0
     */
    public static function get_setting( $key, $default = null ) {
        global $wpdb;
        $table = $wpdb->prefix . 'pa_settings';
        $value = $wpdb->get_var( $wpdb->prepare( "SELECT setting_value FROM $table WHERE setting_key = %s", $key ) );

        return $value ? maybe_unserialize( $value ) : $default;
    }
}
