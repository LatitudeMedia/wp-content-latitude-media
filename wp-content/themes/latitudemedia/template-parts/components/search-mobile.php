<form action="<?php echo esc_url( home_url( '/' ) ); ?>" class="top-search-form">
    <div class="icon">
        <img alt="Search" src="<?php echo get_stylesheet_directory_uri(); ?>/src/images/icon_search_dark.svg">
    </div>
    <input placeholder="<?php _e('SEARCH', 'ltm'); ?>" type="search" value="" name="s">
</form>