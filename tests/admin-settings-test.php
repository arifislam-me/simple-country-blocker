<?php
// Render the real admin page with the external country service unavailable.
if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', dirname( __DIR__ ) . '/' );
}

function current_user_can($cap) { return true; }
function wp_die($message) { throw new Exception($message); }
function get_option($name, $default = array()) { return array('bd'); }
function simple_country_blocker_sanitize_countries($values) { return array_map('strtoupper', (array) $values); }
function simple_country_blocker_get_client_ip() { return '127.0.0.1'; }
function simple_country_blocker_is_public_ip($ip) { return false; }
function get_pages() { return array(); }
function wp_remote_get($url) { return new Exception('Offline'); }
function is_wp_error($value) { return $value instanceof Exception; }
function esc_html__($value, $domain = '') { return $value; }
function esc_html_e($value, $domain = '') { echo esc_html($value); }
function __($value, $domain = '') { return $value; }
function settings_fields($group) {}
function do_settings_sections($group) {}
function submit_button() {}
function esc_attr($value) { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); }
function esc_html($value) { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); }
function checked($actual, $expected = true, $echo = true) { $value = $actual === $expected ? ' checked="checked"' : ''; if ($echo) echo $value; return $value; }
ob_start();
include __DIR__ . '/../admin/settings.php';
$html = ob_get_clean();
if (strpos($html, 'value="BD"') === false || strpos($html, 'Bangladesh') === false) { fwrite(STDERR, "FAIL: Bangladesh must be selectable even when the external service is unavailable\n"); exit(1); }
if (!preg_match('/value="BD"[^>]*checked/', $html)) { fwrite(STDERR, "FAIL: existing selections must remain checked\n"); exit(1); }
if (!preg_match('/type="hidden"[^>]*name="simple_country_blocker_blocked_countries\[\]"/', $html)) { fwrite(STDERR, "FAIL: clearing all countries must submit an empty selection\n"); exit(1); }
echo "PASS: offline list, saved selection, and clearing all countries\n";
