<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Subscribe form block TYPE 2', 'ltm') . '</h3>';
}
// Set defaults Logo and text.

$options = wp_parse_args(
    $args,
    [
        'title'         => 'Get Latitude Media in your inbox',
        'form_code'     => false,
        'left_content'  => 'Subscribe to Latitude\'s free newsletters today to receive the latest news on the energy transition:',
        'display'       => false,
        'blockAttributes' => [],
    ]
);

extract($options);

$blockAttrs = wp_kses_data(
    get_block_wrapper_attributes(
        [
            "class" => 'content-block subscribe-form-section type-2',
            "id" => 'subscribe-form-block' . ($options['blockAttributes']['anchor'] ? ' ' . $options['blockAttributes']['anchor'] : ''),
        ]
    )
);

?>

<div
    <?php echo $blockAttrs; ?>
>
    <div class="container">
        <div class="subscribe-form-section-wrapper">
            <?php do_action('section_title', $title, '<h2>%1$s</h2>'); ?>
            <div class="flex-wrapper">
                <div class="text-side">
                    <p>
                        <?php _e($left_content); ?>
                    </p>
                </div>
                <div class="form-side">
                    <div class="form-block pink">
                        <div class="form-block-wrapper">
                            <?php echo $form_code; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="subscribe-form-section">
    <div class="subscribe-form-section-wrapper">
        <div class="flex-wrapper">
            <div class="text-side">
                <p>Subscribe to Latitude's free newsletters today to receive the latest news on the energy transition:</p>
            </div>
            <div class="form-side">
                <div class="form-block pink">
                    <div class="form-block-wrapper">
                        <form action="">
                            <div class="fieldset">
                                <input type="email" placeholder="Your email work address*">
                            </div>
                            <p>Which newsletter(s) would you like to receive?*</p>
                            <div class="fieldset checkbox">
                                <label for="subscribe">
                                    <input type="checkbox" id="subscribe">
                                    <span>The Latitude Daily (M-F)</span>
                                </label>
                            </div>
                            <div class="fieldset checkbox">
                                <label for="weekly">
                                    <input type="checkbox" id="weekly">
                                    <span>The Latitude Weekly (F)</span>
                                </label>
                            </div>
                            <div class="fieldset checkbox">
                                <label for="nexus">
                                    <input type="checkbox" id="nexus">
                                    <span>AI-Energy Nexus (W)</span>
                                </label>
                            </div>
                            <div class="fieldset">
                                <select name="Industry" id="">
                                    <option value="Industry">Industry</option>
                                    <option value="Industry">Industry</option>
                                    <option value="Industry">Industry</option>
                                    <option value="Industry">Industry</option>
                                    <option value="Industry">Industry</option>
                                </select>
                            </div>
                            <input type="submit" value="subscribe">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>