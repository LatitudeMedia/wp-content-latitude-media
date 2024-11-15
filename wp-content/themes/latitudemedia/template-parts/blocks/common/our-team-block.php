<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Our team block', 'ltm') . '</h3>';
}
// Set defaults Our team block.
$options = wp_parse_args(
    array_merge($args, get_fields() ?? []),
    [
        'title'         => false,
        'members'       => false,
        'display'       => false,
        'blockAttributes' => [],
    ]
);

extract($options);

if(!$display && !is_admin()) {
    return;
}

if( empty($members) ) {
    return;
}

$blockAttrs = wp_kses_data(
  get_block_wrapper_attributes(
      [
          "class" => 'content-block our-team-section',
          "id" => 'our-team-block' . ($options['blockAttributes']['anchor'] ? ' ' . $options['blockAttributes']['anchor'] : ''),
      ]
  )
);

$queryArgs = [
    'post_type'         => "team",
    'posts_per_page'    => -1,
];
$teamMembers = \LatitudeMedia\Manage_Data()->curated_query($queryArgs, $members);

if(!$teamMembers->have_posts()) {
    return;
}
?>

<div <?php echo $blockAttrs; ?>
>
    <div class="container-narrow">
        <div class="our-team-section-wrapper">
            <?php do_action('section_title', $title, '<div class="bordered-title">%1$s</div>'); ?>
            <ul class="team">
                <?php while($teamMembers->have_posts()) :
                    $teamMembers->the_post();
                    $memberData = ltm_get_team_member_data(get_the_ID());
                    ?>
                    <li>
                        <div class="image-folder">
                            <a href="#">
                                <?php
                                if( has_post_thumbnail() ) {
                                    do_action('thumbnail_formatting', get_the_ID(), ['link' => false]);
                                }
                                else {
                                    printf('<img src="%s/src/images/latitude_author_default.png" alt="member">', get_template_directory_uri());
                                }
                                ?>
                            </a>
                        </div>
                        <div class="content-folder">
                            <a href="#" class="name"><?php the_title(); ?></a>
                            <?php
                            if( !empty($memberData['job_title']) ) {
                                printf('<div class="occupation">%s</div>', $memberData['job_title']);
                            }
                            ?>
                        </div>
                    </li>
                <?php endwhile; ?>
            </ul>
        </div>
    </div>
</div>