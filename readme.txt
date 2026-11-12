=== Simple Country Blocker ===
Contributors: arifislamdev
Tags: country block, website block, ip block, security, geolocation
Requires at least: 5.2
Tested up to: 7.1
Requires PHP: 7.2
Stable tag: 1.0.3
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Restricts access to your WordPress website based on visitor country using IP geolocation.

== Description ==

The Simple Country Blocker plugin allows you to restrict access to your WordPress website based on selected countries. When a visitor from a configured country attempts to view your site, they receive an HTTP 403 Forbidden response along with a clean access restriction notice.

Features:
* Restrict website access based on visitor country of origin.
* Bundled offline ISO 3166-1 country dataset with all countries and territories.
* Fast IP geolocation powered by IPinfo with 12-hour transient caching.
* Built-in admin tool to test any IP address and inspect returned geolocation data.
* Privacy-first fail-open design: if IP lookup fails or is unreachable, visitor access is never blocked.
* Administrative bypass: logged-in administrators and the WordPress dashboard are never blocked.

== Third-Party Services ==

This plugin relies on an external third-party service to determine visitor location from connection IP addresses:

* **Service Name:** IPinfo (ipinfo.io)
* **Service Provider:** IPinfo, Inc.
* **Website:** https://ipinfo.io/
* **Terms of Service:** https://ipinfo.io/terms
* **Privacy Policy:** https://ipinfo.io/privacy-policy
* **What Data is Sent:** The visitor's public IP address is transmitted in the HTTPS request path (`https://ipinfo.io/<ip>/json`). No cookies, HTTP headers, authentication credentials, or other personal data are sent.
* **When Data is Sent:** External IP lookups are performed only when at least one country is configured in the blocked countries list and an unauthenticated visitor requests a front-end page, or when an administrator explicitly tests an IP address via the "Test IP Address" admin screen. Lookups for public IPs are cached locally in WordPress transients for 12 hours to minimize outbound requests. If no countries are blocked, or for logged-in administrators and admin dashboard requests, no external requests are made.
* **Service Terms & Limits:** Uses IPinfo's free public endpoint. Lookups are subject to IPinfo's terms and fair-use rate limits.

== Privacy Policy & Consent ==

Site owners using Simple Country Blocker should include appropriate disclosure in their site's privacy policy:
* Visitor IP addresses are sent to IPinfo (ipinfo.io) for geolocation purposes to evaluate geographic access restrictions and enhance website security.
* Geolocation results are stored in local WordPress transients on your server for up to 12 hours.
* Because geolocation is required to determine whether incoming visits originate from blocked countries, lookups are triggered automatically upon visit when country blocking is actively configured.

== Installation ==

= Minimum Requirements =

* WordPress 5.2 or greater
* PHP version 7.2 or greater
* MySQL version 5.6 or greater

= Automatic Installation =

1. In your WordPress admin dashboard, navigate to "Plugins > Add New".
2. Click "Upload Plugin" and choose the `simple-country-blocker.zip` package.
3. Click "Install Now" and then "Activate".

= Manual Installation =

1. Download and extract the plugin ZIP file.
2. Upload the `simple-country-blocker` directory to the `/wp-content/plugins/` directory on your server.
3. Activate the plugin through the "Plugins" menu in WordPress.

== Frequently Asked Questions ==

= Is Simple Country Blocker free? =
Yes, it is completely free open-source software licensed under the GNU General Public License v2 or later.

= How does country detection work? =
The plugin retrieves the connection IP address (`$_SERVER['REMOTE_ADDR']`) and looks up the corresponding country code using the IPinfo API. Results are cached locally in WordPress transients for 12 hours.

= What happens if the external lookup fails? =
The plugin fails open. If IPinfo is temporarily unreachable, times out, or returns an error, the visitor is granted access so legitimate visitors are not blocked due to service disruptions.

= Can administrators be locked out? =
No. Users with administrator capabilities (`manage_options`) and requests to the WordPress admin dashboard (`/wp-admin/`) are never blocked.

= Does it support reverse proxies and CDNs? =
The plugin relies on `$_SERVER['REMOTE_ADDR']`. If your site is behind a CDN, reverse proxy, or load balancer (such as Cloudflare or AWS ALB), configure your web server (e.g., Nginx `real_ip` module or Apache `mod_remoteip`) to restore the real visitor IP from trusted proxy addresses.

== Screenshots ==

1. Settings screen to select blocked countries.
2. Test IP Address tool in the WordPress admin.
3. Front-end restricted access screen for visitors from blocked countries.

== Changelog ==

= 1.0.3 =
* Bundled ISO 3166-1 country dataset for reliable offline administration.
* Implemented public IP validation, loopback filtering, and carrier-grade NAT protection.
* Added 12-hour transient caching for IP lookups.
* Hardened admin forms with nonces, capability checks, and output escaping.
* Added comprehensive third-party service disclosure and privacy documentation for IPinfo.
* Added suggested privacy policy text via WordPress core privacy tools.
* Updated compatibility headers to WordPress 7.1 and PHP 7.2+.

= 1.0.0 =
* Initial release.

== Upgrade Notice ==

= 1.0.3 =
Recommended update: includes offline country selection, IP lookup caching, and security enhancements.
