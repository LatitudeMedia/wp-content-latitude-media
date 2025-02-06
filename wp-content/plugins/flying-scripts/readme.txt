=== Flying Scripts: Delay JavaScript to Improve Site Speed & Performance ===

Contributors: gijo
Donate link: https://www.buymeacoffee.com/gijovarghese
Tags: defer javascript, 3rd party scripts
Requires at least: 4.5
Tested up to: 6.6
Requires PHP: 5.6
Stable tag: 1.2.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Delay JavaScript to improve speed and performance by loading scripts only when needed, reducing render-blocking for a faster and smoother user experience.

== Description ==

Download and execute JavaScript on user interaction.

[Demo video](https://youtu.be/YJ8TQ3bh-TA)

Flying Scripts delay the execution of JavaScript until there is no user activity. You can specify keywords to include JavaScripts to be delayed. There is also a timeout which executes JavaScript when there is no user activity.

### Why should I use this plugin?
JavaScript is very resource-heavy. By delaying the execution of non-critical JavaScript (that are not needed for the initial render), you're prioritizing and giving more resources to critical JavaScript files. This way you will reduce render time, time to interactive, first CPU idle, max Potential input delay etc. This will also reduce initial payload to browsers by reducing the no. of requests.

## Support
- [Official Support Forum](https://wordpress.org/support/plugin/flying-scripts/)
- [Facebook Group](https://www.facebook.com/groups/wpspeedmatters)

## Our premium products
- [FlyingPress](https://flyingpress.com)
- [FlyingCDN](https://flyingcdn.com)

## Our free plugins
- [Flying Pages](https://wordpress.org/plugins/flying-pages/)
- [Flying Images](https://wordpress.org/plugins/nazy-load/)
- [Flying Scripts](https://wordpress.org/plugins/flying-scripts/)
- [Flying Analytics](https://wordpress.org/plugins/flying-analytics/)
- [Flying Fonts](https://wordpress.org/plugins/flying-fonts/)

#### Contributors
- [Gijo Varghese - WP Speed Matters](https://wpspeedmatters.com/)
- [Shay Toder](https://www.shaytoder.com/) 

== Installation ==

1. Visit 'Plugins > Add New'
1. Search for 'Flying Scripts'
1. Activate Flying Scripts for WordPress from your Plugins page.
1. Visit Settings -> Flying Scripts to configure

== Screenshots ==
1. Flying Scripts Settings

== Frequently Asked Questions ==

= What are the ideal scripts to be included? =
Any script that is not crucial for rendering the first view or above fold contents. 3rd party scripts like tracking scripts, chat plugins, etc are ideal.

= What should I put in include keywords =
Any keyword inside your inline script that uniquely identifies that script. For example "fbevents.js" for Facebook Pixel, "gtag" for Google Tag Manager, "customerchat.js" for Facebook Customer Chat plugin.

= How is it different from `defer` =
`defer` tells browser to download the script when found and execute it when HTML parsing is complete. When you include a script in Flying Scripts, those scripts won't be executed until there is a user interaction.

= What is user interaction? =
Events from the user like mouse hover, scroll, keyboard input, touch in mobile device, etc.

= What is timeout? =
Even if there is no user interaction, scripts will be executed after the specified timeout.

== Changelog ==

= 1.2.4 =
- Updated list of event listeners to trigger user interaction

= 1.2.3 =
- Increased DOM size limit
- Updated "Optimize more" tab

= 1.2.2 =
- Fix - Added mouse wheel event for user interaction

= 1.2.1 =
- Fix - FacetWP compatibility

= 1.2.0 =
- New - Exclude on pages
- New - Set timeout to Never

= 1.1.9 =
- Security updates

= 1.1.8 =
- [BUGFIX] Disable for admin interface when using W3 Total Cache

= 1.1.7 =
- [BUGFIX] Support for W3 Total Cache

= 1.1.6 =
- [REMOVED] Unnecessary "scroll" event

= 1.1.5 =
- [UPDATE] Set `data-type='lazy'` instead of `type='lazy'`. Removed setting `type='text/javascript'` via JavaScript

= 1.1.4 =
- [BUGFIX] Remove event listeners after scripts are loaded

= 1.1.3 =
- [UPDATE] Copy updates

= 1.1.2 =
- [UPDATE] Minified JavaScript
- [UPDATE] Updated copy, FAQ

= 1.1.1 =
- [BUGFIX] Exclude json and other script tags

= 1.1.0 =
- [NEW] Load scripts on user interaction
- [REMOVED] Load scripts after delay

= 1.0.0 =
- Initial release
