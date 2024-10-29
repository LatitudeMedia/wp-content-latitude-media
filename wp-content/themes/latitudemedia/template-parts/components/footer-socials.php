
<?php
// Set defaults footer socials.

$options = wp_parse_args(
    $args,
    [
        'class'     => 'social-footer',
    ]
);

extract($options);

$socials = get_field('footer_socials', 'options');

?>

<ul class="<?php echo $class; ?>">
    <?php
    if($socials) {
        foreach ($socials as $social) {
            printf('<li><a href="%1$s" class="ln-link" target="_blank">%2$s</a></li>', $social['url'], social_icons($social['type'], false));
        }
    }
    ?>
</ul>
