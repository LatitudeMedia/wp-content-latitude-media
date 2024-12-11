<?php
// Set defaults footer socials.

$options = wp_parse_args(
    $args,
    [
        'team_member' => null,
    ]
);

if( empty($options['team_member']) ) {
    return;
}

$teamMember = get_post($options['team_member']);
$memberData = ltm_get_team_member_data($teamMember->ID);
?>

<div class="team-member-hero-unit">
    <div class="container">
        <div class="team-member-hero-unit-wrapper">
            <div class="image-folder">
                <span>
                    <?php
                        if( has_post_thumbnail($teamMember->ID) ) {
                            do_action('thumbnail_formatting', get_the_ID(), ['link' => false, 'size' => 'author-archive-hero']);
                        }
                        else {
                            $teamMemberDefaultImg = get_template_directory_uri() . '/src/images/latitude_author_default.png';
                            jetpack_get_resized_image($teamMemberDefaultImg, 427, 427, get_the_title($teamMember->ID));
                        }
                    ?>
                </span>
            </div>
            <div class="contact-folder">
                <?php
                    //Author title
                    printf('<h1>%s</h1>', get_the_title($teamMember->ID));
                    //Author bio
                    the_content();
                    //Author socials
                    get_template_part('template-parts/author/social','links', ['links' => [
                        'x_twitter'     => $memberData['x_twitter'],
                        'linkedin'      => $memberData['linkedin'],
                        'other_social'  => $memberData['other_socials'],
                    ]]);
                ?>
            </div>
        </div>
    </div>
</div>