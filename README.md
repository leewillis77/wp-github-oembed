# Github Embed

Plugin that allows you to embed details from github just by pasting in the URL as you would any other embed source. Currently supports:

### Repositories
__https://github.com/leewillis77/wp-github-oembed__
![Sample output for repository](https://raw.github.com/leewillis77/wp-github-oembed/master/screenshot-1.png)

### User profiles
__https://github.com/leewillis77/__
![Sample output for a user](https://raw.github.com/leewillis77/wp-github-oembed/master/screenshot-2.png)

### Milestone summaries
__https://github.com/leewillis77/wp-github-oembed/issues?milestone=1&state=open__
![Sample output for a milestone](https://raw.github.com/leewillis77/wp-github-oembed/master/screenshot-3.png)

### Repository contributors
__https://github.com/leewillis77/wp-github-oembed/graphs/contributors__
![Sample output for a list of contributors](https://raw.github.com/leewillis77/wp-github-oembed/master/screenshot-4.png)

Coming soon:

* Gists...

The plugin provides very basic styling, but adds classes so you can style as you see fit. If anyone has some ideas for a better default stylesheet - pull requests welcome!
lugin that allows you to embed details about a github user, or repo just by pasting in the repo URL as you would any other embed source.

## Installation

1. Upload the plugin to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Paste a Github repo, or user URL into a post, or page

## Frequently Asked Questions

### Can I change the layout?
Not yet, we're hoping to add templating - all contributions welcome!

## Screenshots

##Development

###Directory structure
  Versioned files
```
  -+ /
   + doc/       Docs files
   + features/  Behat feature files and bootstrap
   + src/       Module source files
   + tools/     Automated plugin packager scripts
```

  Not versioned files (autogenerated).
```
  -+ /
   + bin/       Behat binaries. Generated by composer.
   + out/       The module package to deploy. Generated by tools/release script.
   + vendor/    Useful libraries. Generated by composer.
```

###Recommended environment
####Testing server
1. Install and configure two english Wordpress sites running at http://localhost/sandbox/wordpress/simply/ and http://localhost/sandbox/wordpress/multisite/

  If you prefer other location, change the *behat.yml* file.
```yml
  base_url: http://localhost/sandbox/wordpress
```

**Wordpress admin must be in english for BDD tests**

2. Create a admin user named *test* which email *test@test.test* and password *testtest*. Set Administrator profile to this user.

  If you prefer other other test user credentials, change the *features/bootstrap/FeatureContext.php*] file.
```php
  define('WORDPRESS_ADMIN_USER', 'test');
  define('WORDPRESS_ADMIN_PASSWORD', 'testtest');
```

3. Configure the http://localhost/sandbox/wordpress/multisite/ as multisite, check the Codex for more info on this topic: [Create_A_Network](http://codex.wordpress.org/Create_A_Network)

4. Create a blog named *test1* in http://localhost/sandbox/wordpress/multisite/test1/

5. Link src project directory to your Wordperfect site plugins directory
```shell
  ln -s /home/developer/projects/wp-github-oembed/src/ /var/www/sandbox/wordpress/simply/wp-content/plugins/github-embed
  ln -s /home/developer/projects/wp-github-oembed/src/ /var/www/sandbox/wordpress/multisite/wp-content/plugins/github-embed
```


####BDD
Use [Composer](http://getcomposer.org/) to install [Behat](http://behat.org) and all necesary files.
```shell
  curl -s https://getcomposer.org/installer | php
  ./composer.phar install
```

You can test the development environment configuration
```shell
	./bin/behat features/enviroment/
```

**Wordpress admin must be in english for BDD tests**

## Changelog

- 1.3
    - Expire the oEmbed cache daily
- 1.2
    - Support for milestone summaries
    - Support for contributor lists
- 1.1
    - Default CSS and more styling
- 1.0
  	- First release
