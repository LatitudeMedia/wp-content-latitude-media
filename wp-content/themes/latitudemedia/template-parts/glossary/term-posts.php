<?php
/**
 * List block on a glossary term page showing linked content ("About {term}"
 * or "Mentions {term}"), styled like the editor's picks block via the
 * `editorsPickBlock` mixin (see _glossary_term.scss).
 *
 * Args:
 * - title    Block heading.
 * - post_ids Linked post IDs, newest first (from LTMCore\PostTypes\Glossary).
 */

$options = wp_parse_args(
    $args,
    [
        'title'    => '',
        'post_ids' => [],
    ]
);

if ( ! $options['post_ids'] ) {
    return;
}

$type_labels = [
    'post'          => __( 'Article', 'ltm' ),
    'podcasts'      => __( 'Podcast', 'ltm' ),
    'industry-news' => __( 'Industry News', 'ltm' ),
    'resources'     => __( 'Resource', 'ltm' ),
    'events'        => __( 'Event', 'ltm' ),
];
$heading_id = wp_unique_id( 'glossary-term-posts-' );
?>
<section class="sidebar-block glossary-term-posts" aria-labelledby="<?php echo esc_attr( $heading_id ); ?>">
    <div class="editors-picks-block-wrapper">
        <h2 class="bordered-title" id="<?php echo esc_attr( $heading_id ); ?>"><?php echo esc_html( $options['title'] ); ?></h2>
        <ul class="posts">
            <?php foreach ( $options['post_ids'] as $post_id ) : ?>
                <?php
                $post_type = get_post_type( $post_id );
                // Articles show their News Type (Analysis, Opinion, …); other types their content type.
                $label = 'post' === $post_type ? ltm_get_news_type( $post_id ) : '';
                $label = $label ?: ( $type_labels[ $post_type ] ?? '' );

                ob_start();
                do_action( 'print_article_authors', $post_id, [ 'link_class' => 'author' ] );
                $authors = trim( ob_get_clean() );
                // Posts without authors (e.g. events) still get an empty author link.
                $has_authors = '' !== trim( wp_strip_all_tags( $authors ) );

                $event_start = ( 'events' === $post_type && function_exists( 'get_event_start_date' ) ) ? get_event_start_date( $post_id, 'M j, Y' ) : '';
                ?>
                <li>
                    <ul class="tags-list">
                        <?php if ( $label ) : ?>
                            <li><span><?php echo esc_html( $label ); ?></span></li>
                        <?php endif; ?>
                        <?php do_action( 'print_article_sponsored_label', $post_id, [ 'wrap' => '<li><span class="sponsored">%s</span></li>' ] ); ?>
                    </ul>
                    <?php do_action( 'print_article_title', $post_id ); ?>
                    <div class="info">
                        <?php if ( $has_authors ) : ?>
                            <?php echo $authors; // Escaped by print_article_authors(). ?><span></span>
                        <?php endif; ?>
                        <?php if ( $event_start ) : ?>
                            <div class="date"><?php echo esc_html( $event_start ); ?></div>
                        <?php else : ?>
                            <?php do_action( 'print_article_date', $post_id, [ 'format' => 'M j, Y' ] ); ?>
                        <?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>
