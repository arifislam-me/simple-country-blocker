# Plugin audit checklist

Baseline: plugin 1.0.3, WordPress 7.1.4 and PHP 8.2. Geolocation is an estimate based on IP, not proof of location.

- [x] Bundle country choices so the retired REST Countries endpoint cannot empty the settings screen.
- [x] Sanitize stored choices and support clearing all choices; save and clear verified in WordPress.
- [x] Validate IPs, ignore untrusted forwarding headers, and do not invent a country for local addresses. Reject mapped private IPv4 and shared carrier space.
- [x] Use HTTPS and a timeout for lookups; handle errors, validate responses and cache successes.
- [x] Protect the IP form with a capability check and nonce; validate input and escape output.
- [x] Check frontend block (403) and allow (200) with a controlled IPinfo response, and no lookup when the list is empty.
- [x] Document proxy, caching, API limits, fail-open behaviour, privacy and endpoint coverage; add a warning for a private connection IP.
- [x] Run PHP regression scripts, syntax checks, and browser checks in the local WordPress installation.
- [x] Test a public IP through the PHP container and browser: IPinfo returned US for 8.8.8.8 after network recovery. A temporary host-network test also verified 403 and 200 using the real IPinfo response and a simulated visitor IP.
- [x] Verify administrator bypass: with a simulated US visitor IP and real IPinfo result, an anonymous visitor received 403 while a logged-in administrator could view the homepage.
- [ ] Verify cache/CDN behaviour and the complete supported PHP/WordPress version matrix on representative deployments.

The web server must restore client IPs from explicitly trusted reverse proxies; otherwise a private proxy IP cannot be geolocated and blocking fails open. Frontend page caching and routes outside WordPress template handling can bypass the plugin. See README for details.

## WordPress.org Submission Readiness Audit (2026-11-12)

- [x] Official WordPress Plugin Check (PCP) version 2.2.0: 0 errors, 0 warnings.
- [x] Third-party service disclosure in `readme.txt` per WordPress.org Guidelines 6 & 7 (IPinfo service name, provider, links to site/terms/privacy, exact data transmitted, conditions of transmission, transient caching, and rate limits).
- [x] Privacy and consent section in `readme.txt` plus `wp_add_privacy_policy_content` suggested text hook for site privacy policies.
- [x] PHP and WordPress compatibility headers aligned between `readme.txt` and `simple-country-blocker.php` (`Tested up to: 7.1`, `Requires PHP: 7.2`, `Requires at least: 5.2`).
- [x] Direct file access guard (`if ( ! defined( 'ABSPATH' ) ) exit;`) added to all plugin PHP files.
- [x] Stylesheet registration/enqueueing compliance: replaced hardcoded `<link>` with `wp_enqueue_style()` / `wp_print_styles()`.
- [x] Input sanitization and unslashing on `$_SERVER['REMOTE_ADDR']` and `$_POST['_scb_test_nonce']`.
- [x] Global variables and identifiers namespaced with `simple_country_blocker_` prefix to satisfy `WordPress.NamingConventions.PrefixAllGlobals`.
- [x] Translators comment added for internationalized string placeholders.
- [x] Translation template generated in `languages/simple-country-blocker.pot` (`Domain Path: /languages`).
- [x] Standalone test scripts updated with `ABSPATH` guards and stubbing; all unit and integration tests passing.
- [x] Clean installable ZIP created without repository/development artifacts (`simple-country-blocker.zip`).
