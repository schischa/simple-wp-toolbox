=== Simple WP Toolbox ===
Contributors: schischa
Tags: admin, security, comments, dashboard, customization
Requires at least: 5.2
Tested up to: 7.1
Requires PHP: 7.0
Stable tag: 1.9.3
License: GPLv2
License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.html

A lightweight toolbox for configuring common WordPress features from one settings page.

== Description ==

Simple WP Toolbox collects a small set of optional WordPress customizations in one settings page. It includes comment controls, login rate limiting, selected Site Health controls, update email controls, XML-RPC control, and an optional dashboard information widget.

The login limiter is enabled by default. It blocks an IP address for 24 hours after four failed login attempts. Attempts are stored in a dedicated database table and looked up by an indexed IP hash. Expired records are cleaned up daily by WordPress Cron. Administrators can reset all login limiter records from the settings page.

The login limiter stores a hash of the visitor IP address, failed attempt counts, and expiration timestamps in the site database. It does not store login names or passwords and does not send this information to an external service. The plugin adds suggested privacy policy text in the WordPress Privacy settings.

== Installation ==

1. Upload the `simple-wp-toolbox` folder to the `/wp-content/plugins/` directory, or install the plugin through the WordPress Plugins screen.
2. Activate the plugin through the **Plugins** screen.
3. Visit **Settings > Simple WP Toolbox** to configure the available features.

== Frequently Asked Questions ==

= How does the login limiter work? =

After four failed login attempts from an IP address, the limiter blocks further login attempts from that IP for 24 hours. A successful login clears the attempt record for that IP. The settings page includes a control to reset all records.

= Does the login limiter send data to an external service? =

No. The limiter stores a hash of the IP address and attempt metadata in the WordPress site's own database. It does not send this information to an external service.

= Can I disable the login limiter? =

Yes. The limiter is enabled by default and can be switched off under **Settings > Simple WP Toolbox**.

== Changelog ==

= 1.9.3 =
* Renamed the plugin to Simple WP Toolbox and changed its code, option, hook, and database table prefixes.
* Corrected settings checkbox handling and Site Health test controls.
* Added privacy policy information for the login limiter and cleanup of its data on deactivation and uninstall.
* Preserved the original core update email filter value for email types the setting does not change.
* Prefixed remaining plugin functions and made admin interface text translatable.

= 1.9.2 =
* Stores login limiter data in a dedicated database table and migrates active limiter data from transient storage.
* Uses an indexed lookup for the current IP during login checks.

= 1.9.1 =
* Added a settings button to reset recorded login attempts and active IP lockouts.

= 1.9.0 =
* Added a login limiter that blocks an IP address for 24 hours after four failed login attempts.
* Added a setting to enable or disable the login limiter.

== External Services ==

This plugin does not connect to external services or transmit plugin data.

== Development ==

Development takes place on [GitHub](https://github.com/schischa/simple-wp-toolbox).
