<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">END SIDEBAR</h3>';
}

// Set defaults Large event block.
$options = wp_parse_args(
    $args,
    [
        'sidebar_widget'   => null,
    ]
);

if (!is_admin()) :

?>
            </div>
            <div class="sidebar">
                <?php
                    if( !empty($options['sidebar_widget']) ) {
                        dynamic_sidebar( $options['sidebar_widget'] );
                    }
                ?>
            </div>
        </div>
    </div>
</div>

<?php endif; ?>