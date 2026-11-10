# Marc's Custom WP Functions Plugin

## Description

**Marc's Custom WP Functions Plugin** helps you centralize common WordPress customizations and provides a settings page for configuration. Your modifications persist through theme updates, allowing for easy, code-free adjustments.

### Key Features
- **System Information Dashboard Widget**: Optional dashboard widget displaying WordPress, PHP and database versions, memory consumption, PHP configuration and other useful server information. Disabled by default and configurable via the plugin settings.
- **Comment Settings**: Remove only the comment URL field or disable comments entirely (code sourced from [WPBeginner](https://www.wpbeginner.com/)).
- **Disable Emojis**: For GDPR compliance and improved performance.
- **Remove WordPress Logo in Admin Bar**: Create a cleaner, more professional admin interface.
- **Change "Howdy" to "Hello"**: More user-friendly greeting in the admin bar.
- **Theme-Color Meta Tag**: Easily set a custom theme color for modern browsers.
- **White-Label the Login Page**: Use your site's custom logo on the login screen.
- **Disable XML-RPC**: Improve security by disabling XML-RPC if not needed.
- **Disable Update Notification Emails**: Selectively disable emails for core updates, plugin updates, and theme updates.
- **Dedicated Settings Page**: Manage all these settings under **Settings > Marc's Custom WP Functions**.
- **Site Health Controls (new in v1.7.0)**: Disable individual Site Health tests directly from the plugin settings page.

## Installation

1. Download or clone the repository.
2. Upload `marcs-custom-wp-functions` to `/wp-content/plugins/`.
3. Activate **Marc's Custom WP Functions Plugin** in **Plugins > Installed Plugins**.

## Usage

- After activation, visit **Settings > Marc's Custom WP Functions**:
  - Adjust comment behavior.
  - Set the theme-color.
  - Disable XML-RPC if desired.
  - Control which update notification emails to disable.

## Customization

- Use the settings page to configure various options.
- Extend functionality by adding new files in the `inc` directory.

## Changelog

**Version 1.8.0:**
- Added optional System Information Dashboard Widget.
- Displays WordPress, PHP and database versions.
- Shows current and peak PHP memory consumption, memory limit and usage percentage.
- Displays active theme, web server, PHP execution time and upload limit.
- Widget is disabled by default and can be enabled via plugin settings.

**1.7.0**
- Added new Site Health control options to disable specific tests (like PHP version and theme version).
- Improved admin bar “Howdy” replacement — now runs only for English locales and avoids PHP warnings.
- Minor code cleanup and consistency updates.

**1.6.1:**
- Improvement: "Howdy" is now only replaced with "Hello" for English admin locales (e.g., en_US), allowing localized greetings in other languages (e.g., WPML)

**Version 1.6:**
- Added the option to selectively disable update notification emails for core, plugin, and theme updates.

**Version 1.5:**
- Added an option to disable XML-RPC for improved security.

**Version 1.4.1:**
- Enhanced the "disable comments" option to fully remove the comment icon/count from the admin bar.

**Version 1.4:**
- Added a setting to choose between removing the comment URL field only or disabling comments entirely (code from WPBeginner).

**Version 1.3:**
- Refactored code into separate files for better maintainability.

**Version 1.2:**
- Introduced a settings page to configure the theme-color meta tag.

**Version 1.1:**
- White-labelling the login page with a custom logo.

**Version 1.0:**
- Initial release.

## License

This plugin is licensed under GPL2.

## Author

Marc Chiroiu  
[Website](https://chiroiu.com)  
[GitHub Repository](https://github.com/schischa/marcs-custom-wp-functions)
