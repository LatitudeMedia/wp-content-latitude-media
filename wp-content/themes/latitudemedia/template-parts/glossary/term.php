<?php
/**
 * Glossary term body: breadcrumbs, title (with abbreviation), short summary
 * (excerpt) and synonyms across the top; then the definition and related
 * terms, beside a sidebar of linked content ("Subject of" / "Mentioned in",
 * set in each post's editor sidebar).
 *
 * Data helpers come from the ltm-core plugin (LTMCore\PostTypes\Glossary,
 * LTMCore\Blocks\GlossaryIndex). The breadcrumb trail is Yoast's, with the
 * glossary page inserted by LTMCore\Seo\GlossarySeo; schema.org markup is
 * added separately by LTMCore\Seo\GlossarySchema.
 */

$term_id      = get_the_ID();
$has_core     = class_exists( '\LTMCore\PostTypes\Glossary' );
$definition   = function_exists( 'get_field' ) ? get_field( 'definition', $term_id ) : '';
$abbreviation = $has_core ? \LTMCore\PostTypes\Glossary::get_abbreviation( $term_id ) : '';
$synonyms     = $has_core ? \LTMCore\PostTypes\Glossary::get_synonyms( $term_id ) : [];
$related      = $has_core ? \LTMCore\PostTypes\Glossary::get_related_term_ids( $term_id ) : [];
$index_id     = $has_core ? \LTMCore\Blocks\GlossaryIndex::get_index_page_id() : 0;
$subject_of   = $has_core ? \LTMCore\PostTypes\Glossary::get_subject_of_ids( $term_id, 10 ) : [];
$mentioned_in = $has_core ? \LTMCore\PostTypes\Glossary::get_mentioned_in_ids( $term_id, 10 ) : [];
?>
<article class="glossary-term">
    <div class="container-narrow">
        <?php if ( function_exists( 'yoast_breadcrumb' ) ) : ?>
            <?php yoast_breadcrumb( '<nav class="glossary-term__breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'ltm' ) . '">', '</nav>' ); ?>
        <?php elseif ( $index_id ) : ?>
            <a class="glossary-term__back" href="<?php echo esc_url( get_permalink( $index_id ) ); ?>">
                &larr; <?php echo esc_html( sprintf( __( 'Back to %s', 'ltm' ), get_the_title( $index_id ) ) ); ?>
            </a>
        <?php endif; ?>

        <h1 class="glossary-term__title">
            <?php the_title(); ?>
            <?php if ( $abbreviation ) : ?>
                <span class="glossary-term__abbr">(<?php echo esc_html( $abbreviation ); ?>)</span>
            <?php endif; ?>
        </h1>

        <?php if ( has_excerpt() ) : ?>
            <p class="glossary-term__summary"><?php echo esc_html( get_the_excerpt() ); ?></p>
        <?php endif; ?>

        <?php if ( $synonyms ) : ?>
            <p class="glossary-term__synonyms">
                <span class="label"><?php _e( 'Also known as:', 'ltm' ); ?></span>
                <?php echo esc_html( implode( ', ', $synonyms ) ); ?>
            </p>
        <?php endif; ?>
    </div>

    <div class="right-sidebar-layout">
        <div class="container-narrow">
            <div class="right-sidebar-layout-wrapper">
                <div class="main-column">
                    <?php if ( $definition ) : ?>
                        <div class="glossary-term__definition">
                            <?php echo $definition; // ACF-formatted WYSIWYG output. ?>
                        </div>
                    <?php endif; ?>

                    <?php if ( $related ) : ?>
                        <aside class="glossary-term__related">
                            <h2><?php _e( 'Related terms', 'ltm' ); ?></h2>
                            <ul>
                                <?php foreach ( $related as $related_id ) : ?>
                                    <li><a href="<?php echo esc_url( get_permalink( $related_id ) ); ?>"><?php echo esc_html( get_the_title( $related_id ) ); ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </aside>
                    <?php endif; ?>
                </div>

                <?php if ( $subject_of || $mentioned_in ) : ?>
                    <div class="sidebar glossary-term__sidebar">
                        <?php
                        get_template_part( 'template-parts/glossary/term', 'posts', [ 'title' => __( 'Subject of', 'ltm' ), 'post_ids' => $subject_of ] );
                        get_template_part( 'template-parts/glossary/term', 'posts', [ 'title' => __( 'Mentioned in', 'ltm' ), 'post_ids' => $mentioned_in ] );
                        ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</article>
