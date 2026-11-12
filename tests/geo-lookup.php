<?php
// Run with: php tests/geo-lookup.php
if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', dirname( __DIR__ ) . '/' );
}

$GLOBALS['http_calls'] = array();
$GLOBALS['http_result'] = array('response' => array('code' => 200), 'body' => '{"ip":"8.8.8.8","country":"US","city":"Mountain View"}');
$GLOBALS['cache'] = array();
$GLOBALS['options'] = array();
class WP_Error {
    public $code;
    public $message;
    public function __construct($code, $message) { $this->code = $code; $this->message = $message; }
}
function is_wp_error($x) { return $x instanceof WP_Error; }
function add_action() {}
function register_setting() {}
function __($text, $domain = '') { return $text; }
function wp_remote_get($url, $args = array()) { $GLOBALS['http_calls'][] = array($url, $args); return $GLOBALS['http_result']; }
function wp_remote_retrieve_response_code($response) { return $response['response']['code']; }
function wp_remote_retrieve_body($response) { return $response['body']; }
function get_transient($key) { return isset($GLOBALS['cache'][$key]) ? $GLOBALS['cache'][$key] : false; }
function set_transient($key, $value, $ttl) { $GLOBALS['cache'][$key] = $value; }
function get_option($key, $default = false) { return isset($GLOBALS['options'][$key]) ? $GLOBALS['options'][$key] : $default; }
function current_user_can($capability) { return false; }
function is_admin() { return false; }
function apply_filters($tag, $value) { return $value; }
function sanitize_text_field($str) { return trim((string) $str); }
function wp_unslash($val) { return $val; }
function status_header($code) {}
function nocache_headers() {}
function wp_enqueue_style() {}
function plugins_url($path = '', $plugin = '') { return 'http://example.com/wp-content/plugins/simple-country-blocker/' . $path; }
define('HOUR_IN_SECONDS', 3600);
require dirname(__DIR__) . '/simple-country-blocker.php';
function expect($condition, $description) { if (!$condition) { fwrite(STDERR, "FAIL: $description\n"); exit(1); } echo "PASS: $description\n"; }
expect(function_exists('simple_country_blocker_lookup_ip'), 'lookup API exists');
expect(function_exists('simple_country_blocker_get_client_ip'), 'client IP API exists');
expect(simple_country_blocker_sanitize_countries(array('', 'bd', 'BD', 'x1', array())) === array('BD'), 'sanitizes and deduplicates country codes');
expect(simple_country_blocker_sanitize_countries('broken') === array(), 'safely handles malformed stored settings');
expect(simple_country_blocker_sanitize_countries(array('')) === array(), 'clearing all selected countries works');
$_SERVER['REMOTE_ADDR'] = '8.8.8.8'; $_SERVER['HTTP_CLIENT_IP'] = '1.1.1.1'; $_SERVER['HTTP_X_FORWARDED_FOR'] = '9.9.9.9';
expect(simple_country_blocker_get_client_ip() === '8.8.8.8', 'ignores untrusted forwarding headers');
$result = simple_country_blocker_lookup_ip('8.8.8.8');
expect(!is_wp_error($result) && $result['country'] === 'US', 'returns country from valid IPinfo response');
expect(strpos($GLOBALS['http_calls'][0][0], 'https://ipinfo.io/') === 0, 'uses HTTPS');
expect(count($GLOBALS['http_calls']) === 1, 'makes one HTTP call');
simple_country_blocker_lookup_ip('8.8.8.8');
expect(count($GLOBALS['http_calls']) === 1, 'caches successful lookups');
expect(is_wp_error(simple_country_blocker_lookup_ip('127.0.0.1')), 'rejects loopback without fabricating a country');
expect(is_wp_error(simple_country_blocker_lookup_ip('::ffff:127.0.0.1')), 'rejects mapped loopback IPv6');
expect(is_wp_error(simple_country_blocker_lookup_ip('100.64.0.1')), 'rejects shared carrier address');
expect(is_wp_error(simple_country_blocker_lookup_ip('not-an-ip')), 'rejects invalid IP');
$GLOBALS['http_result'] = new WP_Error('http_error', 'unavailable');
expect(is_wp_error(simple_country_blocker_lookup_ip('1.1.1.1')), 'returns an error when lookup fails');
$GLOBALS['http_result'] = array('response' => array('code' => 200), 'body' => '{}');
expect(is_wp_error(simple_country_blocker_lookup_ip('9.9.9.9')), 'rejects responses missing a country');
$GLOBALS['http_calls'] = array();
simple_country_blocker_restrict_countries();
expect(count($GLOBALS['http_calls']) === 0, 'does not look up IP when no countries are blocked');
echo "All geolocation tests passed.\n";
