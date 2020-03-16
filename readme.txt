=== Github Embed ===
Contributors: leewillis77
Donate link: http://www.leewillis.co.uk/wordpress-plugins/?utm_source=wordpress&utm_medium=www&utm_campaign=github-embed
Tags: github, embed, oembed
Requires at least: 4.6
Tested up to: 5.4
Stable tag: 2.0.1

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

## Treeware

You're free to use this package for free, but if it makes it to your production environment please [buy the world a tree](https://offset.earth/ademtisoftware?gift-trees).

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

= 2.0.1 =

* Suggest Treeware donations

= 2.0 =

* Support for authenticating using personal access tokens

= 1.9 =

* Remove unused code - thanks to https://github.com/pjaudiomv

= 1.8 =

* New: All responses are now templated thanks to https://github.com/Zebouski

= 1.7 =

* Include GitHub logo rather than hotlinking to (no-longer supported) repo for image
* Add wp_github_oembed_logo_class filter

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
