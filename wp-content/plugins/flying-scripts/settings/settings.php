<?php
function flying_scripts_format_list($list) {
    $list = trim($list);
    $list = $list ? array_map('trim', explode("\n", str_replace("\r", "", sanitize_textarea_field($list)))) : [];
    return $list;
}

function flying_scripts_view_settings() {

    if (isset($_POST['submit'])) {
        update_option('flying_scripts_timeout', sanitize_text_field($_POST['flying_scripts_timeout']));
        update_option('flying_scripts_include_list', flying_scripts_format_list($_POST['flying_scripts_include_list']));
        update_option('flying_scripts_disabled_pages', flying_scripts_format_list($_POST['flying_scripts_disabled_pages']));
    }

    $timeout = esc_attr(get_option('flying_scripts_timeout'));

    $include_list = get_option('flying_scripts_include_list');
    $include_list = implode("\n", $include_list);
    $include_list = esc_textarea($include_list);

    $disabled_pages = get_option('flying_scripts_disabled_pages');
    $disabled_pages = implode("\n", $disabled_pages);
    $disabled_pages = esc_textarea($disabled_pages);

    ?>
<form method="POST">
    <?php wp_nonce_field('flying-scripts', 'flying-scripts-settings-form'); ?>
    <table class="form-table" role="presentation">
    <tbody>
        <tr>
            <th scope="row"><label>Include Keywords</label></th>
            <td>
                <textarea name="flying_scripts_include_list" rows="4" cols="50"><?php echo $include_list ?></textarea>
                <p class="description">Keywords that identify scripts that should load on user interaction. One keyword per line.</p>
            </td>
        </tr>
        <tr>
            <th scope="row"><label>Timeout</label></th>
            <td>
                <select name="flying_scripts_timeout" value="<?php echo $timeout; ?>">
                    <option value="1" <?php if ($timeout == 1) {echo 'selected';} ?>>1s</option>
                    <option value="2" <?php if ($timeout == 2) {echo 'selected';} ?>>2s</option>
                    <option value="3" <?php if ($timeout == 3) {echo 'selected';} ?>>3s</option>
                    <option value="4" <?php if ($timeout == 4) {echo 'selected';} ?>>4s</option>
                    <option value="5" <?php if ($timeout == 5) {echo 'selected';} ?>>5s</option>
                    <option value="6" <?php if ($timeout == 6) {echo 'selected';} ?>>6s</option>
                    <option value="7" <?php if ($timeout == 7) {echo 'selected';} ?>>7s</option>
                    <option value="8" <?php if ($timeout == 8) {echo 'selected';} ?>>8s</option>
                    <option value="9" <?php if ($timeout == 9) {echo 'selected';} ?>>9s</option>
                    <option value="10" <?php if ($timeout == 10) {echo 'selected';} ?>>10s</option>
                    <option value="5000" <?php if ($timeout == 5000) {echo 'selected';} ?>>Never</option>
                </select>
            <p class="description">Load scripts after a timeout when there is no user interaction</p>
            <td>
        </tr>
        <tr>
            <th scope="row"><label>Disable on pages</label></th>
            <td>
                <textarea name="flying_scripts_disabled_pages" rows="4" cols="50"><?php echo $disabled_pages; ?></textarea>
                <p class="description">Keywords of URLs where Flying Scripts should be disabled</p>
            </td>
        </tr>
    </tbody>
    </table>
    <p class="submit">
        <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
    </p>
</form>
<?php
}
