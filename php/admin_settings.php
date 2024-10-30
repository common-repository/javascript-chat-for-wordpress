<div class=wrap>
    <div class="icon32" id="icon-edit"><br /></div>
    <h2><?php _e('Javascript Chat pour  Wordpress', 'wpjschat') ?></h2>
    <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
        <div class="postbox " id="postexcerpt">
            <h3><?php _e('Activer le chat', 'wpjschat') ?></h3>
            <div class="inside">
                <p>
                    <label for="devloungeHeader_yes">
                        <input type="radio" name="enabled" value="true" <?php if ($options['enabled'] == "true") {
    print ' checked="checked"';
} ?> />
<?php _e('Oui', 'wpjschat') ?>
                    </label>
                    <label for="devloungeHeader_no">
                        <input type="radio" name="enabled" value="false" <?php if ($options['enabled'] == "false") {
    print ' checked="checked"';
} ?>/>
<?php _e('Non', 'wpjschat') ?>
                    </label>
                </p>
            </div>
        </div>
        <div class="postbox " id="postexcerpt">
            <h3><span><?php _e('IP bannies (séparées par des virgules)', 'wpjschat') ?></span></h3>
            <p>
                <textarea name="exclude_ips" style="width: 80%; height: 100px;"><?php _e(apply_filters('format_to_edit', $options['exclude_ips']), 'wpjschat') ?></textarea>
            </p>

        </div>
        <div class="submit">
            <input type="submit" name="update_wp_jschatSettings" value="<?php _e('Mettre à jour', 'wpjschat') ?>" />
        </div>
    </form>
<?php print include('admin_channels.php'); ?>
</div>