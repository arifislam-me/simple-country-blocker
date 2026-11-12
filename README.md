# Simple Country Blocker

A WordPress plugin that restricts frontend page access based on the country returned by an IP geolocation service.

## Setup

1. Upload the `simple-country-blocker` directory to `wp-content/plugins/` and activate it in WordPress.
2. Open **Simple Country Blocker → Settings**, select countries, and save. Clear every checkbox and save to disable blocking.
3. Use **Test IP Address** to check a public IPv4 or IPv6 address with IPinfo.

## How it works and limitations

- The plugin looks up the visitor's `REMOTE_ADDR` via the IPinfo API over HTTPS. Country is an estimate based on an IP address, not proof of physical location. VPNs and proxies can change the result.
- It ignores visitor-supplied forwarding headers. Behind a reverse proxy or CDN, configure the web server to set `REMOTE_ADDR` to the real visitor IP **only from trusted proxies**; otherwise a proxy address may be checked or no lookup performed.
- Successful lookup results are cached for 12 hours. With no countries selected, no visitor lookup is made. IPinfo's public service has a shared rate limit; check [IPinfo's current limits](https://support.ipinfo.io/hc/en-us/articles/30792479436562-What-Is-the-Difference-Between-Using-the-Authenticated-Free-Plan-and-the-Public-API-With-No-Account).
- If the IP is private or invalid, or the lookup fails, access is allowed. This avoids locking visitors out during an outage, but means the plugin cannot guarantee blocking when the service is unavailable or rate-limited.
- The plugin checks normal WordPress frontend requests. It is not a firewall: login, admin, REST API, AJAX, static files, cached pages, and requests handled outside WordPress may bypass it. Configure blocking at the proxy or firewall when those paths must be protected. Admin users with `manage_options` bypass blocking.
- IP addresses are sent to IPinfo for lookups. Review your privacy policy and the provider's terms before use. Local or private addresses cannot be geolocated as public visitors.

## Testing

Run `php tests/geo-lookup.php`, `php tests/admin-settings-test.php`, and `php tests/admin-ip-test.php` from the plugin directory. Tests use local stubs; also test on a WordPress installation and verify your proxy and caching configuration.

Licensed under GPL-2.0-or-later.
