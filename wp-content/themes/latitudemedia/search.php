<?php

get_header();

$postItemTemplate = get_wrap_rows_from_template('<li>
                        <div class="image-folder">
                            [thumb]
                        </div>
                        <div class="content-folder">
                            <h4>[title]</h4>
                            <div class="info">
                                [date]
                                <span></span>
                                [excerpt]
                            </div>
                        </div>
                </li>');

$args = [
    'display'       => true,
    'forced'        => true,
    'pagination'    => true,
];

get_template_part('template-parts/components/titles/topic', 'title',
    ['title' => 'Search results']
);

get_template_part('template-parts/components/articles-list', 'block',
    array_merge($args, $postItemTemplate)
);

get_footer();
?>
