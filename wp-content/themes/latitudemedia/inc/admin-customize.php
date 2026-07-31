<?php
function add_category_column_section( $content, $column_name, $term_id ) {
    switch ( $column_name ) {
        case 'section':
            $section = get_field( 'section', 'category_' . $term_id );
            $content = $section ? sprintf('<a href="%s" target="_blank">%s</a>', get_edit_term_link($section->term_id, 'section'), $section->name) : '';
            break;
        default:
            break;
    }

    return $content;
}
add_filter( 'manage_category_custom_column', 'add_category_column_section', 10, 3 );

function add_new_category_column_section( $columns ) {
    $columns['section'] = __( 'Section', 'ltm' );
    return $columns;
}
add_filter( 'manage_edit-category_columns', 'add_new_category_column_section' );

function add_post_column_news_type( $column_name, $post_id ) {
    switch ( $column_name ) {
        case 'type':
            $type = get_field( 'news_type', $post_id);
            if( isset($type['label']) ) {
                printf('<span>%s</span>', $type['label']);
            }
            break;
    }
}
add_filter( 'manage_posts_custom_column', 'add_post_column_news_type', 10, 2 );

function add_new_post_column_type( $columns ) {
    $columns['type'] = __( 'News Type', 'ltm' );
    return $columns;
}
add_filter( 'manage_edit-post_columns', 'add_new_post_column_type' );

if ( ! function_exists( 'custom_block_editor_scripts' ) ) {
    /**
     * Enqueue block editor scripts
     *
     */
    function custom_block_editor_scripts() {
        wp_register_style('ltm-admin-cms', get_template_directory_uri() . '/dist/css/editor.min.css', array(), filemtime( get_template_directory() . '/dist/css/editor.min.css') );
        wp_enqueue_style('ltm-admin-cms');
    }
    add_action( 'enqueue_block_editor_assets', 'custom_block_editor_scripts' );

}

if( !function_exists( 'ltm_admin_styles' ) ) {
    /**
     * Enqueue site styles
     */
    function ltm_admin_styles() {
        if ( !is_user_logged_in() ) {
            return;
        }

        wp_register_style('ltm-admin-front', get_template_directory_uri() . '/dist/css/admin.min.css', array(), filemtime( get_template_directory() . '/dist/css/admin.min.css') );
        wp_enqueue_style('ltm-admin-front');
    }
    add_action( 'wp_enqueue_scripts', 'ltm_admin_styles' );
}

function my_acf_collor_pallete_script() {
//$black: #000000;
//$white: #ffffff;
//$primary-pink: #C6168D;
//$primary-dark-blue: #0F1E42;
//$green: #00B48D;
//$blue: #0095DA;
//$orange: #F99D1C;
//$pink_category_bg: #F9E8F4;
//$pink_shadow: #F9E8F4;
//$green_shadow: #CCF0E8;
//$blue_shadow: #E5F4FC;
//$orange_shadow: #FEEBD2;
//$grey_bg: #F5F5F5;
    ?>
    <script type="text/javascript">
        (function($){
            acf.add_filter('color_picker_args', function( args, $field ){
                // do something to args
                args.palettes = [
                    '#C6168D',
                    '#F9E8F4',
                    '#0095da',
                    '#E5F4FC',
                    '#00B48D',
                    '#CCF0E8',
                    '#F99D1C',
                    '#FEEBD2',
                    '#0F1E42'
                ];
                // return
                return args;
            });

        })(jQuery);
    </script>
    <?php
}

add_action('acf/input/admin_footer', 'my_acf_collor_pallete_script');

function categories_limit() {
    if ( is_admin() && get_post_type() === 'post' ) {
        echo "
	<script language=\"javascript\" type=\"text/javascript\">
			if(wp.data.select('core/editor')) {
					const { select, dispatch, subscribe } = wp.data;

					const saveableAction = select( 'core/editor' ).isEditedPostSaveable;
					const getTerms = (taxonomy) => { return select( 'core/editor' ).getEditedPostAttribute( taxonomy ); };

					var initValues = [
							{
									'type' : 'categories',
									'name' : 'Category',
									'values' : getTerms('categories')
							},
					];
					initValues.forEach( (taxonomy) => {
						 subscribe( () => {
								const newCategories = getTerms(taxonomy.type);
								const categoriesChanged = newCategories !== taxonomy.values;
								taxonomy.values = newCategories;
								if ( taxonomy.values && taxonomy.values.length >= 2 ) {
    								jQuery('.editor-post-taxonomies__hierarchical-terms-list[aria-label=\"Categories\"] input:not(:checked)').prop( \"disabled\", true );
								}
								else
								{
    								jQuery('.editor-post-taxonomies__hierarchical-terms-list[aria-label=\"Categories\"] input:not(:checked)').prop( \"disabled\", false );
								}
								if ( categoriesChanged ) {
									if ( taxonomy.values.length > 3 ) {
										// show notice
										dispatch( 'core/notices' ).createNotice(
											'error',
											'Please select less than four ' + taxonomy.name,
											{
												id: 'required_notice_' + taxonomy.type,
												isDismissible: false,
											}
										);

										// Make sure post cannot be saved, by adding a save lock
										dispatch( 'core/editor' ).lockPostSaving( taxonomy.type + '_lock' );
										dispatch( 'core/editor' ).lockPostAutosaving( taxonomy.type + '_lock' );

										select( 'core/editor' ).isEditedPostSaveable = function() { return false; };
									} else {
										// remove notice
										dispatch( 'core/notices' ).removeNotice( 'required_notice_' + taxonomy.type );

										//remove save lock
										dispatch( 'core/editor' ).unlockPostSaving( taxonomy.type +'_lock' );
										dispatch( 'core/editor' ).unlockPostAutosaving( taxonomy.type + '_lock' );

										if(!select('core/editor').isPostAutosavingLocked()) {
											select( 'core/editor' ).isEditedPostSaveable = function() { return saveableAction; };
										}
									}
								}
							} );
					});

			} else {
					jQuery(document).ready(function() {
							jQuery( '#publish, #save-post' ).click(function(){
									let publish = true;
									if( $('#taxonomy-category input:checked').length <= 0) {
											alert( 'Please select at least one Category' );
											publish = false;
									}

                                    if(!publish) {
											return false;
									}

						 });
					});
			}
	</script>			
	";
    }//end if
}
add_action( 'admin_head', 'categories_limit' );

add_action('admin_init', function () {
    // Redirect any user trying to access comments page
    global $pagenow;

    if ($pagenow === 'edit-comments.php') {
        wp_safe_redirect(admin_url());
        exit;
    }

    // Remove comments metabox from dashboard
    remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');

    // Disable support for comments and trackbacks in post types
    foreach (get_post_types() as $post_type) {
        if (post_type_supports($post_type, 'comments')) {
            remove_post_type_support($post_type, 'comments');
            remove_post_type_support($post_type, 'trackbacks');
        }
    }
});

// Close comments on the front-end
add_filter('comments_open', '__return_false', 20, 2);
add_filter('pings_open', '__return_false', 20, 2);

// Hide existing comments
add_filter('comments_array', '__return_empty_array', 10, 2);

// Remove comments page in menu
add_action('admin_menu', function () {
    remove_menu_page('edit-comments.php');
});

// Remove comments links from admin bar
add_action('init', function () {
    if (is_admin_bar_showing()) {
        remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
    }
});