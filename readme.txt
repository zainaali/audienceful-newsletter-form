=== Audienceful Newsletter Form ===
Contributors: your-wporg-username
Tags: newsletter, ajax, subscription, form, audienceful
Requires at least: 5.0
Tested up to: 6.6
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A simple AJAX-powered newsletter signup form with direct Audienceful API integration. Includes success/error messages and a settings page for API key.

== Description ==

Audienceful Newsletter Form is a lightweight plugin that adds a modern AJAX newsletter signup form to your WordPress site.  
It integrates directly with the **Audienceful API**, so new subscribers are sent straight to your Audienceful account without page reloads.

**Features:**

* AJAX-powered signup form (no reloads).
* Clean, modern UI with loading animation.
* Direct Audienceful API integration.
* Customizable success and error messages.
* Settings page to configure API key and messages.
* “Settings” link directly on the Plugins page.
* Translation-ready.

This plugin is ideal for landing pages, blogs, or any site that needs a simple, fast, and reliable newsletter form.

== Installation ==

1. Upload the plugin folder to `/wp-content/plugins/` or install via the Plugins page in WordPress.
2. Activate the plugin from the **Plugins** page.
3. Go to **Settings → Audienceful Form** to enter your Audienceful API key and customize messages.
4. Add the shortcode `[audienceful_form]` to any post, page, or widget area.

== Usage ==

Simply place the shortcode:

[audienceful_form]


The form will appear with an email field and submit button.  
When a visitor submits their email, it will be sent to Audienceful using your API key.

== Frequently Asked Questions ==

= Does this plugin support multiple forms? =  
Currently, you can use the `[audienceful_form]` shortcode in multiple places, but all forms will share the same Audienceful API key and settings.

= Is spam protection included? =  
Yes. A honeypot field is planned for a future release to block most bots. For now, AJAX + nonce validation helps block automated spam.

= Can I customize the form design? =  
Yes. The plugin ships with basic CSS, which you can override via your theme or child theme.

== Screenshots ==

1. The simple email signup form on a landing page.
2. Plugin settings page with API key and message customization.

== Changelog ==

= 1.0.0 =
* Initial release.
* AJAX form with email field.
* API key and messages configurable in settings.
* Success/error messages displayed inline.

== Upgrade Notice ==

= 1.0.0 =
First release — AJAX Audienceful signup form with settings page.

== License ==

This plugin is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License version 2 or later.  
This plugin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY.
