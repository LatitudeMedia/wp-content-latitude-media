<?php
/**
 * The header.
 *
 * This is the template that displays all of the <head> section and everything up until main.
 *
 */

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> >
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?php wp_title("",true);?></title>
    <link rel="preload" href="<?php echo get_template_directory_uri() . '/src/images/events_logo.svg'; ?>" as="image">

    <?php wp_head(); ?>
</head>

<?php
    $menu = wp_get_nav_menu_object( 'podcasts-menu' );
    $hideMenu = get_field('hide_menu', $menu);
?>
<body <?php body_class($hideMenu ? 'podcasts-off' : ''); ?>>
<?php wp_body_open(); ?>

    <div class="wrapper">

    <!-- HEADER -->
<?php //get_template_part( 'template-parts/header/nav', 'event' ); ?>

        <!-- HEADER -->
        <?php
        $disableMenu = get_field('disable_top_menu');
        get_template_part( 'template-parts/header/nav', ($disableMenu ? 'disable-menu' : '') );
        ?>
