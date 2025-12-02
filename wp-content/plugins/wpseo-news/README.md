[![Coverage Status](https://coveralls.io/repos/github/Yoast/wpseo-news/badge.svg?branch=trunk)](https://coveralls.io/github/Yoast/wpseo-news?branch=trunk)

Yoast News SEO for Yoast SEO
==========================
Requires at least: 6.5
Tested up to: 6.7
Stable tag: 13.3
Requires PHP: 7.2.5
Depends: Yoast SEO

Yoast News SEO module for the Yoast SEO plugin.

This repository uses [the Yoast grunt tasks plugin](https://github.com/Yoast/plugin-grunt-tasks).

Installation
============

1. Go to Plugins -> Add New.
2. Click "Upload" right underneath "Install Plugins".
3. Upload the zip file that this readme was contained in.
4. Activate the plugin.
5. Go to SEO -> Extensions -> Licenses, enter your license key and Save.
6. Your license key will be validated.
7. You can now use Yoast News SEO. See also https://kb.yoast.com/kb/configuration-guide-for-news-seo/

Frequently Asked Questions
--------------------------

You can find the [Yoast News SEO FAQ](https://kb.yoast.com/kb/category/news-seo/) in our knowledge base.

Changelog
=========

## 13.3

Release date: 2025-02-04

#### Enhancements

* Allows for News Sitemap items to change the language via a new filter `Yoast\WP\News\publication_language`. Props to [dgwatkins](https://github.com/dgwatkins).

#### Bugfixes

* Stops PHP notices on WordPress 6.7 about `_load_textdomain_just_in_time` loading incorrectly.

#### Other

* Sets the minimum required Yoast SEO version to 24.4.
* Sets the minimum supported WordPress version to 6.5.
* Sets the _WordPress tested up to_ version to 6.7.

## 13.2

Release date: 2024-03-05

#### Enhancements

* Adds a `wpseo_news_sitemap_content` filter to append custom content to the XML sitemap. Props to @wccoder.
* This PR introduces a new way of retrieving translations for Yoast News SEO, by utilizing the TranslationPress service. Instead of having to ship all translations with every release, we can now load the translations on a per-install basis, tailored to the user's setup. This means smaller plugin releases and less bloat on the user's server.

#### Bugfixes

* Fixes a bug where a warning would be thrown on activation.
* Fixes a bug where using the `&` character in the publication name would break the XML sitemap.

#### Other

* Sets the minimum required Yoast SEO version to 22.2.
* Sets the minimum supported WordPress version to 6.3.
* Sets the WordPress tested up to version to 6.4.
* Sets the minimum supported WordPress version to 6.3.
* The plugin has no known incompatibilities with PHP 8.3.
* Drops compatibility with PHP 5.6, 7.0 and 7.1.
* Users requiring this package via [WP]Packagist can now use the `composer/installers` v2.
* Improves discoverability of security policy.

### Earlier versions
For the changelog of earlier versions, please refer to [the changelog on yoast.com](https://yoa.st/news-seo-changelog).
