<?php
// Exercise the admin form without sending requests to the IP lookup provider.
if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', dirname( __DIR__ ) . '/' );
}

class WP_Error { private $message; public function __construct($code, $message) { $this->message = $message; } public function get_error_message() { return $this->message; } }
function is_wp_error($x) { return $x instanceof WP_Error; }
function current_user_can($cap) { return true; }
function wp_die($message) { throw new Exception($message); }
function esc_html__($s, $d = '') { return $s; }
function esc_html_e($s, $d = '') { echo esc_html($s); }
function esc_attr($s) { return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8'); }
function esc_html($s) { return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8'); }
function __($s, $d = '') { return $s; }
function esc_attr__($s, $d = '') { return esc_attr($s); }
function sanitize_text_field($s) { return trim((string) $s); }
function wp_unslash($s) { return $s; }
function wp_nonce_field($action, $name) { echo '<input type="hidden" name="' . $name . '" value="valid">'; }
function wp_verify_nonce($nonce, $action) { return $nonce === 'valid'; }
function submit_button($text = 'Submit', $type = 'primary', $name = 'submit') { echo '<button type="submit" name="' . esc_attr($name) . '">' . esc_html($text) . '</button>'; }
function simple_country_blocker_get_client_ip() { return '8.8.8.8'; }
function simple_country_blocker_lookup_ip($ip) { $GLOBALS['lookups'][] = $ip; if (filter_var($ip, FILTER_VALIDATE_IP) === false) return new WP_Error('invalid_ip', 'Please enter a valid IP address.'); return array('ip' => $ip, 'country' => 'US', 'city' => '<script>alert(1)</script>'); }
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = array('test_ip_submit' => 'Test IP', 'ip_to_test' => 'invalid-address', '_scb_test_nonce' => 'valid');
$GLOBALS['lookups'] = array();
ob_start(); include __DIR__ . '/../admin/ip-test.php'; $html = ob_get_clean();
if (strpos($html, 'Please enter a valid IP address.') === false) { fwrite(STDERR, "FAIL: invalid IP must produce a clear error\n"); exit(1); }
if (strpos($html, '<script>alert(1)</script>') !== false) { fwrite(STDERR, "FAIL: lookup response must be escaped\n"); exit(1); }
if (strpos($html, 'name="_scb_test_nonce"') === false) { fwrite(STDERR, "FAIL: form must include a nonce\n"); exit(1); }
echo "PASS: invalid IP, escaped output, and nonce field\n";
