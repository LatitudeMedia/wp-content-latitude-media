=== Latitude Media Core ===
Contributors:      Latitude Media
Tags:              block
Tested up to:      6.8
Stable tag:        1.0.0
License:           GPL-2.0-or-later
License URI:       https://www.gnu.org/licenses/gpl-2.0.html

Native Gutenberg blocks for Latitude Media (Title Block, Featured Post) and the Thematic Pages post type.

== Description ==

Provides the `latitudemedia/title-block` and `latitudemedia/featured-post-block` blocks, plus the
`thematic-pages` post type and `thematic-page-types` taxonomy. Requires the `latitudemedia` theme to be
active, since these blocks call theme helpers directly (`get_post_sponsor()`, the `print_article_*`
action hooks, `Page_Data()`, and the `sponsor` taxonomy).

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/ltm-core` directory, or install the plugin
   through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress. The `latitudemedia` theme must be
   active for the blocks to register.

== Changelog ==

= 1.0.0 =
* Extracted from the latitudemedia theme.
