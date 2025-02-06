<?php
function flying_scripts_format_list($list) {
    $list = trim($list);
    $list = $list ? array_map('trim', explode("\n", str_replace("\r", "", $list))) : [];
    return $list;
}

function flying_scripts_settings_scripts() {

    if (isset($_POST['submit'])) {
        update_option('flying_scripts_timeout', sanitize_text_field($_POST['flying_scripts_timeout']));
        update_option('flying_scripts_include_list', flying_scripts_format_list($_POST['flying_scripts_include_list']));
    }

    $timeout = get_option('flying_scripts_timeout');
    $include_list = get_option('flying_scripts_include_list');

    ?>
<form method="POST">
    <?php wp_nonce_field('flying-scripts', 'flying-scripts-settings-form'); ?>
    <table class="form-table" role="presentation">
    <tbody>
        <tr>
            <th scope="row"><label>Include Keywords</label></th>
            <td>
                <textarea name="flying_scripts_include_list" rows="4" cols="50"><?php echo implode('&#10;', $include_list); ?></textarea>
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
                </select>
            <td>
        </tr>
    </tbody>
    </table>
    <p class="submit">
        <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
    </p>
</form>
<?php
}
