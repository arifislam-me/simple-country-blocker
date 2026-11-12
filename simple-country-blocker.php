<?php
/**
 * Plugin Name: Simple Country Blocker
 * Description: Restricts access to your website based on selected countries.
 * Version: 1.0.3
 * Requires at least: 5.2
 * Requires PHP: 7.2
 * Author: MD Arif Islam
 * Author URI: https://arifislam.me
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: simple-country-blocker
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Add the top-level menu and submenus
function simple_country_blocker_add_admin_menu() {
    // Create a top-level menu item before the "Settings" menu
    $position = 79; // Position before "Settings" which is 80
    add_menu_page(
        'Simple Country Blocker', // Page title
        'Simple Country Blocker', // Menu title
        'manage_options', // Capability
        'simple-country-blocker-settings', // Menu slug
        'simple_country_blocker_render_settings', // Callback function
        'dashicons-shield', // Icon URL (using a WordPress dashicon)
        $position // Position
    );

    // Add the "Settings" submenu
    add_submenu_page(
        'simple-country-blocker-settings', // Parent slug
        'Settings', // Page title
        'Settings', // Menu title
        'manage_options', // Capability
        'simple-country-blocker-settings', // Menu slug
        'simple_country_blocker_render_settings' // Callback function
    );

    // Add the "Test IP Address" submenu
    add_submenu_page(
        'simple-country-blocker-settings', // Parent slug
        'Test IP Address', // Page title
        'Test IP Address', // Menu title
        'manage_options', // Capability
        'simple-country-blocker-test-ip', // Menu slug
        'simple_country_blocker_render_test_ip' // Callback function
    );
}
add_action( 'admin_menu', 'simple_country_blocker_add_admin_menu' );

// Register settings
function simple_country_blocker_register_settings() {
    register_setting( 'simple_country_blocker_settings_group', 'simple_country_blocker_blocked_countries', array(
        'sanitize_callback' => 'simple_country_blocker_sanitize_countries',
    ) );
}
add_action( 'admin_init', 'simple_country_blocker_register_settings' );

/**
 * Add suggested privacy policy content for site administrators.
 */
function simple_country_blocker_add_privacy_policy_content() {
    if ( ! function_exists( 'wp_add_privacy_policy_content' ) ) {
        return;
    }
    $content = sprintf(
        /* translators: %s: URL to IPinfo privacy policy */
        __( 'This site uses the Simple Country Blocker plugin to restrict access from specified countries. When a visitor requests a page on this website, their IP address is sent to IPinfo (%s) to determine their geographic country. Successful lookups are cached locally in temporary transients for up to 12 hours. IP lookups are performed solely for geographic access control and site security.', 'simple-country-blocker' ),
        'https://ipinfo.io/privacy-policy'
    );
    wp_add_privacy_policy_content( 'Simple Country Blocker', wp_kses_post( wpautop( $content ) ) );
}
add_action( 'admin_init', 'simple_country_blocker_add_privacy_policy_content' );

// Render the settings page
function simple_country_blocker_render_settings() {
    include_once plugin_dir_path( __FILE__ ) . 'admin/settings.php';
}

// Render the IP test page
function simple_country_blocker_render_test_ip() {
    include_once plugin_dir_path( __FILE__ ) . 'admin/ip-test.php';
}

/** Normalize stored country codes, including the empty selection. */
function simple_country_blocker_sanitize_countries( $countries ) {
    if ( ! is_array( $countries ) ) {
        return array();
    }
    $valid = array();
    foreach ( $countries as $country ) {
        if ( is_string( $country ) && preg_match( '/^[A-Za-z]{2}$/D', $country ) ) {
            $valid[] = strtoupper( $country );
        }
    }
    return array_values( array_unique( $valid ) );
}

/** Return the connection's IP, never untrusted client-supplied proxy headers. */
function simple_country_blocker_get_client_ip() {
    $ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
    return filter_var( $ip, FILTER_VALIDATE_IP ) ? $ip : '';
}

/**
 * Look up a public IP using IPinfo. Only successful lookups are cached.
 *
 * @return array|WP_Error IPinfo details, or a descriptive error.
 */
function simple_country_blocker_is_public_ip( $ip ) {
    if ( ! is_string( $ip ) || ! filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) ) {
        return false;
    }
    $packed = inet_pton( $ip );
    if ( strlen( $packed ) === 16 && substr( $packed, 0, 12 ) === str_repeat( "\0", 10 ) . "\xff\xff" ) {
        return simple_country_blocker_is_public_ip( inet_ntop( substr( $packed, 12 ) ) );
    }
    if ( strlen( $packed ) === 4 && ord( $packed[0] ) === 100 && ( ord( $packed[1] ) & 192 ) === 64 ) {
        return false;
    }
    return true;
}

function simple_country_blocker_lookup_ip( $ip ) {
    if ( ! simple_country_blocker_is_public_ip( $ip ) ) {
        return new WP_Error( 'invalid_ip', __( 'Enter a valid public IP address.', 'simple-country-blocker' ) );
    }

    $cache_key = 'scb_ip_' . md5( $ip );
    $cached = get_transient( $cache_key );
    if ( is_array( $cached ) && ! empty( $cached['country'] ) ) {
        return $cached;
    }

    $response = wp_remote_get( 'https://ipinfo.io/' . rawurlencode( $ip ) . '/json', array(
        'timeout' => 3,
        'redirection' => 0,
    ) );
    if ( is_wp_error( $response ) ) {
        return new WP_Error( 'lookup_failed', __( 'Could not reach IPinfo. Please try again later.', 'simple-country-blocker' ) );
    }
    if ( 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
        return new WP_Error( 'lookup_failed', __( 'IPinfo returned an unsuccessful response. Please try again later.', 'simple-country-blocker' ) );
    }

    $details = json_decode( wp_remote_retrieve_body( $response ), true );
    if ( ! is_array( $details ) || ! isset( $details['country'] ) || ! is_string( $details['country'] ) || ! preg_match( '/^[A-Za-z]{2}$/D', $details['country'] ) ) {
        return new WP_Error( 'lookup_failed', __( 'IPinfo did not return a valid country for this IP.', 'simple-country-blocker' ) );
    }
    $details['country'] = strtoupper( $details['country'] );
    set_transient( $cache_key, $details, 12 * HOUR_IN_SECONDS );
    return $details;
}

/** Restrict visitors from configured countries, allowing access if lookup fails. */
function simple_country_blocker_restrict_countries() {
    if ( is_admin() || current_user_can( 'manage_options' ) ) {
        return;
    }
    $blocked_countries = simple_country_blocker_sanitize_countries( get_option( 'simple_country_blocker_blocked_countries', array() ) );
    if ( empty( $blocked_countries ) ) {
        return;
    }

    $details = simple_country_blocker_lookup_ip( simple_country_blocker_get_client_ip() );
    if ( is_wp_error( $details ) || ! in_array( $details['country'], $blocked_countries, true ) ) {
        return;
    }

    status_header( 403 );
    nocache_headers();
    wp_enqueue_style( 'simple-country-blocker-style', plugins_url( 'assets/css/style.css', __FILE__ ), array(), '1.0.3' );
    include plugin_dir_path( __FILE__ ) . 'block-page-template.php';
    exit;
}
add_action( 'template_redirect', 'simple_country_blocker_restrict_countries' );
