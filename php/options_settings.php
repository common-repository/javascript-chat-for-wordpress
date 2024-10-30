<div class=wrap>
    <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
        <h2><?php _e('Options du chat', "wpjschat"); ?></h2>
        <h3><?php _e('Activer le chat ?', "wpjschat"); ?></h3>
        <p>
            <label for="enable_chat_yes">
                <input type="radio" id="enable_chat_yes" name="enable_chat" value="true" <?php print ($enable_chat == "true")?  'checked="checked"' : ''; ?> />
                <?php _e('Oui', "wpjschat"); ?>
            </label>
            <label for="enable_chat_no">
                <input type="radio" id="enable_chat_no" name="enable_chat" value="false" <?php print ($enable_chat != "true")?  'checked="checked"' : ''; ?> />
                <?php _e('Non', "wpjschat"); ?>
            </label>
        </p>
        <h3><?php _e('Activer les smileys ?', "wpjschat"); ?></h3>
        <p>
            <label for="enable_smileys_yes">
                <input type="radio" id="enable_smileys_yes" name="enable_smileys" value="true" <?php print ($enable_smileys == "true")?  'checked="checked"' : ''; ?> />
                <?php _e('Oui', "wpjschat"); ?>
            </label>
            <label for="enable_smileys_no">
                <input type="radio" id="enable_smileys_no" name="enable_smileys" value="false" <?php print ($enable_smileys != "true")?  'checked="checked"' : ''; ?> />
                <?php _e('Non', "wpjschat"); ?>
            </label>
        </p>
        <div class="submit">
            <input type="submit" name="update_wp_jschatSettings" value="<?php _e('Mettre à jour', 'wpjschat') ?>" /></div>
    </form>
</div>