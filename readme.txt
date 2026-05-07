=== IAMLL706 AASA ===
Contributors: iamll706
Tags: apple-app-site-association, aasa, webcredentials, redirects, rewrite
Requires at least: 6.0
Tested up to: 6.9.4
Requires PHP: 7.4
Stable tag: 1.0.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A simple plugin that serves Apple's App Site Association JSON at `/.well-known/apple-app-site-association`.

== Description ==

IAMLL706 AASA provides a dedicated endpoint for the Apple App Site Association file. When activated, it registers a rewrite rule and serves the JSON payload at `/.well-known/apple-app-site-association` without HTML or redirects.

This plugin is useful for enabling `webcredentials` support for iOS apps and other Apple services that depend on an AASA file.

== Installation ==

1. Upload the `iamll706-aasa` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. If your host delays rewrite propagation, go to Settings > Permalinks and click Save Changes.

== Frequently Asked Questions ==

= What URL does this plugin serve? =
It serves the AASA file at `https://your-domain.com/.well-known/apple-app-site-association`.

= Does it output HTML? =
No. The endpoint returns only JSON with the correct `application/json` content type.

== Changelog ==

= 1.0.1 =
* Added GitHub-based automatic update checks with Plugin Update Checker.

= 1.0.0 =
* Initial release.
