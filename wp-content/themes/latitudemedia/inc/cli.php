<?php
/**
 * @package Create ACF gutenberg block
 * @version 1.0.0
 */
if( !defined('WP_CLI') || !WP_CLI ) {
    return;
}

class Latitude_CLI_Command {


    /**
     * Clear all of the caches for memory management
     */
    private function clearAllCaches()
    {
        global $wpdb, $wp_object_cache;

        $wpdb->queries = []; // or define( 'WP_IMPORTING', true );

        if (!is_object($wp_object_cache)) {
            return;
        }

        $wp_object_cache->group_ops      = [];
        $wp_object_cache->stats          = [];
        $wp_object_cache->memcache_debug = [];
        $wp_object_cache->cache          = [];

        if (is_callable($wp_object_cache, '__remoteset')) {
            $wp_object_cache->__remoteset(); // important
        }
    }

    /**
     * Subcommand to assign coauthors to a post based on a given meta key
     *
     * @since      3.0
     *
     * @subcommand assign-author-by-meta-key
     * @synopsis [--meta_key=<key>] [--post_type=<ptype>] [--append_author=<bool>] [--posts_per_page=<num>] [--paged=<page>] [--post_status=<string>]
     * ---
     *
     * ## EXAMPLES
     *
     *    wp latitude assign-author-by-meta-key --post_type=podcasts --meta_key=source_authors
     *
     * @when after_wp_load
     *
     */
    public function assign_author_by_meta_key($args, $assocArgs)
    {
        $defaults   = [
            'meta_key'         => 'source_authors', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
            'post_type'        => 'post',
            'order'            => 'ASC',
            'orderby'          => 'ID',
            'posts_per_page'   => 100,
            'paged'            => 1,
            'append_author' => false,
            'date_query' => array(
                array(
                    'column'     => 'post_date',
                    'after'   => '- 10 days'
                ),
            ),
        ];
        $this->args = wp_parse_args($assocArgs, $defaults);

        $postsTotal             = 0;
        $postsAlreadyAssociated = 0;
        $postsMissingCoauthor   = 0;
        $postsAssociated        = 0;
        $missionCoauthors       = [];

        $posts = new WP_Query($this->args);
        while ($posts->post_count) {
            foreach ($posts->posts as $singlePost) {
                $postsTotal++;

                // See if the value in the post meta field is the same as any of the existing coauthors
                $importedAuthors = get_post_meta($singlePost->ID, $this->args['meta_key'], true);
                $originalAuthors = explode(',', $importedAuthors);
                $coauthors = [];
                if(empty($originalAuthors)) {
                    continue;
                }

                foreach ($originalAuthors as $originalAuthor) {
                    if(empty($originalAuthor)) {
                        continue;
                    }

                    $coauthor = MultipleAuthors\Classes\Objects\Author::get_by_term_slug(sanitize_title($originalAuthor));
                    if (false === $coauthor || is_wp_error($coauthor)) {
                        $postsMissingCoauthor++;
                        $missionCoauthors[] = $originalAuthor;
                        \WP_CLI::line(
                            $postsTotal . ': Post #' . $singlePost->ID . ' does not have "' . $originalAuthor . '" associated as a coauthor but there is not a coauthor profile'
                        );
                        continue;
                    }

                    $coauthors[] = $coauthor;
                }

                if(empty($coauthors)) {
                    continue;
                }

                MultipleAuthors\Classes\Utils::set_post_authors($singlePost->ID, $coauthors);

                \WP_CLI::line(
                    $postsTotal . ': Post #' . $singlePost->ID . ' has been assigned "' . $originalAuthor . '" as the author'
                );
                $postsAssociated++;
                clean_post_cache($singlePost->ID);
            }

            $this->args['paged']++;
            $this->clearAllCaches();
            $posts = new WP_Query($this->args);
        }

        \WP_CLI::line('All done! Here are your results:');
        if ($postsAlreadyAssociated) {
            \WP_CLI::line("- {$postsAlreadyAssociated} posts already had the coauthor assigned");
        }
        if ($postsMissingCoauthor) {
            \WP_CLI::line("- {$postsMissingCoauthor} posts reference coauthors that don't exist. These are:");
            \WP_CLI::line('  ' . implode(', ', array_unique($missionCoauthors)));
        }
        if ($postsAssociated) {
            \WP_CLI::line("- {$postsAssociated} posts now have the proper coauthor");
        }
    }

    /**
     * Subcommand to assign new type to a post based on a given meta key
     *
     * @since      3.0
     *
     * @subcommand assign-news-type
     * @synopsis [--meta_key=<key>]  [--posts_per_page=<num>] [--paged=<page>] [--post_status=<string>]
     * ---
     *
     * ## EXAMPLES
     *
     *     wp latitude assign-news-type --meta_key=source_newstyperequired
     *
     * @when after_wp_load
     *
     */
    public function assign_news_type($args, $assocArgs)
    {
        $defaults   = [
            'meta_key'         => 'source_newstyperequired', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
            'post_type'        => 'post',
            'order'            => 'ASC',
            'orderby'          => 'ID',
            'posts_per_page'   => 100,
            'paged'            => 1,
        ];
        $this->args = wp_parse_args($assocArgs, $defaults);

        $postsTotal             = 0;
        $postsAssociated        = 0;

        $posts = new WP_Query($this->args);
        while ($posts->post_count) {
            foreach ($posts->posts as $singlePost) {
                $postsTotal++;

                $originalNewsType = get_post_meta($singlePost->ID, $this->args['meta_key'], true);
                if(empty($originalNewsType)) {
                    continue;
                }

                update_field('news_type', strtolower($originalNewsType), $singlePost->ID);

                \WP_CLI::line(
                    $postsTotal . ': Post #' . $singlePost->ID . ' has been assigned "' . $originalNewsType. '" as the news type'
                );
                $postsAssociated++;
                clean_post_cache($singlePost->ID);
            }

            $this->args['paged']++;
            $this->clearAllCaches();
            $posts = new WP_Query($this->args);
        }

        \WP_CLI::line('All done! Here are your results:');
        if ($postsAssociated) {
            \WP_CLI::line("- {$postsAssociated} posts now have the proper news type");
        }
    }

    /**
     * Subcommand assign sections to category based on a given meta key
     *
     * @since      3.0
     *
     * @subcommand assign-sections
     * @synopsis
     * ---
     *
     * ## EXAMPLES
     *
     *     wp latitude assign-sections
     *
     * @when after_wp_load
     *
     */
    public function assign_sections($args, $assocArgs)
    {
        $sections_terms = get_terms( 'section', array(
            'hide_empty' => 0
        ) );

        $updated = 0;
        foreach ($sections_terms as $sections_term) {
            $source_categories = get_term_meta($sections_term->term_id, 'source_subcategories', true);
            $categories = explode(';', $source_categories);
            if(empty($categories)) {
                continue;
            }
            foreach ($categories as $category) {
                $category = get_term_by('slug', trim($category), 'category');
                update_field('section', $sections_term->term_id, 'category_' . $category->term_id);
                \WP_CLI::line(
                    'Section #' . $sections_term->name . ' has been assigned "' . $category->name. '" as the section'
                );
                $updated++;
            }
        }

        \WP_CLI::line('All done! Here are your results:');
        if ($updated) {
            \WP_CLI::line("- {$updated} posts now have the proper news type");
        }
    }
}

/**
 * Registers our command when cli get's initialized.
 *
 */
function latitude_cli_register_commands() {
    WP_CLI::add_command( 'latitude', 'Latitude_CLI_Command' );
}

add_action( 'cli_init', 'latitude_cli_register_commands' );
