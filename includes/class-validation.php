<?php
/**
 * Validation Class
 *
 * Handles input validation for the plugin
 *
 * @since 1.0.0
 */

class Premium_Academy_Validation {

    /**
     * Validate email
     *
     * @since 1.0.0
     * @param string $email The email to validate
     * @return bool
     */
    public static function validate_email( $email ) {
        return is_email( $email );
    }

    /**
     * Validate phone number
     *
     * @since 1.0.0
     * @param string $phone The phone number to validate
     * @return bool
     */
    public static function validate_phone( $phone ) {
        $phone = preg_replace( '/[^0-9+]/', '', $phone );
        return preg_match( '/^\+?[1-9]\d{1,14}$/', $phone );
    }

    /**
     * Validate date
     *
     * @since 1.0.0
     * @param string $date The date to validate (YYYY-MM-DD)
     * @return bool
     */
    public static function validate_date( $date ) {
        $pattern = '/^\d{4}-\d{2}-\d{2}$/';
        if ( ! preg_match( $pattern, $date ) ) {
            return false;
        }

        $d = DateTime::createFromFormat( 'Y-m-d', $date );
        return $d && $d->format( 'Y-m-d' ) === $date;
    }

    /**
     * Validate age
     *
     * @since 1.0.0
     * @param string $dob Date of birth (YYYY-MM-DD)
     * @param int $min_age Minimum age
     * @param int $max_age Maximum age
     * @return bool
     */
    public static function validate_age( $dob, $min_age = 2, $max_age = 30 ) {
        $birth_date = new DateTime( $dob );
        $today = new DateTime();
        $age = $today->diff( $birth_date )->y;

        return $age >= $min_age && $age <= $max_age;
    }

    /**
     * Validate URL
     *
     * @since 1.0.0
     * @param string $url The URL to validate
     * @return bool
     */
    public static function validate_url( $url ) {
        return filter_var( $url, FILTER_VALIDATE_URL ) !== false;
    }

    /**
     * Validate admission form
     *
     * @since 1.0.0
     * @param array $data The form data
     * @return array
     */
    public static function validate_admission_form( $data ) {
        $errors = array();

        // Validate first name
        if ( empty( $data['first_name'] ) ) {
            $errors['first_name'] = 'First name is required';
        } elseif ( strlen( $data['first_name'] ) < 2 ) {
            $errors['first_name'] = 'First name must be at least 2 characters';
        }

        // Validate last name
        if ( empty( $data['last_name'] ) ) {
            $errors['last_name'] = 'Last name is required';
        } elseif ( strlen( $data['last_name'] ) < 2 ) {
            $errors['last_name'] = 'Last name must be at least 2 characters';
        }

        // Validate date of birth
        if ( empty( $data['date_of_birth'] ) ) {
            $errors['date_of_birth'] = 'Date of birth is required';
        } elseif ( ! self::validate_date( $data['date_of_birth'] ) ) {
            $errors['date_of_birth'] = 'Invalid date format';
        } elseif ( ! self::validate_age( $data['date_of_birth'] ) ) {
            $errors['date_of_birth'] = 'Student age must be between 2 and 30 years';
        }

        // Validate gender
        if ( empty( $data['gender'] ) || ! in_array( $data['gender'], array( 'Male', 'Female' ), true ) ) {
            $errors['gender'] = 'Valid gender is required';
        }

        // Validate grade
        if ( empty( $data['grade'] ) ) {
            $errors['grade'] = 'Grade is required';
        }

        // Validate parent name
        if ( empty( $data['parent_name'] ) ) {
            $errors['parent_name'] = 'Parent/Guardian name is required';
        }

        // Validate email
        if ( empty( $data['email'] ) ) {
            $errors['email'] = 'Email is required';
        } elseif ( ! self::validate_email( $data['email'] ) ) {
            $errors['email'] = 'Invalid email address';
        }

        // Validate phone
        if ( empty( $data['phone'] ) ) {
            $errors['phone'] = 'Phone number is required';
        } elseif ( ! self::validate_phone( $data['phone'] ) ) {
            $errors['phone'] = 'Invalid phone number';
        }

        return $errors;
    }

    /**
     * Validate contact form
     *
     * @since 1.0.0
     * @param array $data The form data
     * @return array
     */
    public static function validate_contact_form( $data ) {
        $errors = array();

        if ( empty( $data['name'] ) ) {
            $errors['name'] = 'Name is required';
        }

        if ( empty( $data['email'] ) || ! self::validate_email( $data['email'] ) ) {
            $errors['email'] = 'Valid email is required';
        }

        if ( empty( $data['message'] ) || strlen( $data['message'] ) < 10 ) {
            $errors['message'] = 'Message must be at least 10 characters';
        }

        return $errors;
    }
}
