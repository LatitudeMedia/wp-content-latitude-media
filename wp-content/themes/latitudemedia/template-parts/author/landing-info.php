<?php
// Set defaults footer socials.

$options = wp_parse_args(
    $args,
    [
        'author' => null,
    ]
);

if( empty($options['author']) ) {
    return;
}

$author = $options['author'];
$authorData = ltm_get_author_data($author);

?>

<div class="team-member-hero-unit">
    <div class="container">
        <div class="team-member-hero-unit-wrapper">
            <div class="image-folder">
                <a href="<?php echo get_term_link($author,'author')?>">
                    <?php
                    if( !empty($authorData['logo']) ) {
                        do_action('thumbnail_formatting', ['link' => false], ['image_id' => $authorData['logo']['ID']]);
                    }
                    else {
                        printf('<img src="%s/src/images/latitude_author_default.png" alt="member">', get_template_directory_uri());
                    }
                    ?>
                </a>
            </div>
            <div class="contact-folder">
                <?php
                    //Author title
                    printf('<h1>%s</h1>', $author->name);
                    //Author bio
                    echo $authorData['bio'];
                    //Author socials
                    get_template_part('template-parts/author/social','links', ['author' => $author]);
                ?>
            </div>
        </div>
    </div>
</div>