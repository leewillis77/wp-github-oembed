=== Github Embed ===
Contributors: leewillis77
Donate link: http://www.leewillis.co.uk/wordpress-plugins/?utm_source=wordpress&utm_medium=www&utm_campaign=github-embed
Tags: github, embed, oembed
Requires at least: 4.6
Tested up to: 5.1
Stable tag: 1.6

== Description ==

Plugin that allows you to embed details from github just by pasting in the URL as you would any other embed source. Currently supports:

* Repositories
* User profiles
* Project milestone summaries
* Project contributors

Coming soon:

* Gists...

The plugin provides very basic styling, but adds classes so you can style as you see fit. If anyone has some ideas for a better default stylesheet - pull requests welcome!

The main development is all going on on [GitHub](https://github.com/leewillis77/wp-github-oembed).

== Installation ==

1. Upload the plugin to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Paste a Github repo, or user URL into a post, or page

== Frequently Asked Questions ==

= Can I change the layout? =
Not yet, we're hoping to add templating - [all contributions welcome](https://github.com/leewillis77/wp-github-oembed)!

== Screenshots ==

1. GitHub Repository
2. GitHub user profile
3. Project milestone summaries
4. Project contributors

== Changelog ==

= 1.6 =

* Internal code cleanups
* Remove unnecessary debug code

= 1.5 =

* Fixes for newer GitHub milestone URLs

= 1.4 =

* Fix PHP warning that could block contributor embeds.
* Fix Contributor embeds to use correct API call
* Pass correct initial value to credentials filters

= 1.3 =

* Expire the oEmbed cache daily

= 1.2 =

* Split API calls into separate class
* Implement milestone summaries
* Implement contributor lists

= 1.1 =

* Default CSS and more styling

= 1.0 =

* First release
