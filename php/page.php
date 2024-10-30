<form class="wp_jschat_widget_form" action="<?php bloginfo('wpurl'); ?>/wp-admin/admin-ajax.php" method="post">
    <div class="wp_jschat_widget_text">
        <?php for ($i = count($messages) - 1; $i >= 0; $i--): ?>
            <p><span class='author'><u><?php print $messages[$i]->pseudo; ?></u></span> : <?php print strToSmileys($messages[$i]->message,$enable_smileys); ?></p>
        <?php endfor; ?>
        <?php print $initialContent; ?>
    </div>
    <?php if($enable_smileys!='false'): ?>
    <div class="wp_jschat_smileys">
        <a href="#" class="wp_jschat_smile"><img src="<?php bloginfo('wpurl'); ?>/wp-includes/images/smilies/icon_razz.gif" alt="razz" /></a>
        <a href="#" class="wp_jschat_smile"><img src="<?php bloginfo('wpurl'); ?>/wp-includes/images/smilies/icon_lol.gif" alt="lol" /></a>
        <a href="#" class="wp_jschat_smile"><img src="<?php bloginfo('wpurl'); ?>/wp-includes/images/smilies/icon_mrgreen.gif" alt="mrgreen" /></a>
        <a href="#" class="wp_jschat_smile"><img src="<?php bloginfo('wpurl'); ?>/wp-includes/images/smilies/icon_question.gif" alt="question" /></a>
        <a href="#" class="wp_jschat_smile"><img src="<?php bloginfo('wpurl'); ?>/wp-includes/images/smilies/icon_rolleyes.gif" alt="rolleyes" /></a>
        <a href="#" class="wp_jschat_smile"><img src="<?php bloginfo('wpurl'); ?>/wp-includes/images/smilies/icon_sad.gif" alt="sad" /></a>
        <a href="#" class="wp_jschat_smile"><img src="<?php bloginfo('wpurl'); ?>/wp-includes/images/smilies/icon_wink.gif" alt="wink" /></a>
        <a href="#" class="wp_jschat_smile"><img src="<?php bloginfo('wpurl'); ?>/wp-includes/images/smilies/icon_surprised.gif" alt="surprised" /></a>
        <a href="#" class="wp_jschat_smile"><img src="<?php bloginfo('wpurl'); ?>/wp-includes/images/smilies/icon_idea.gif" alt="idea" /></a>
    </div>
    <?php endif; ?>
    <div class="form">
        <input type="hidden" class="hasSmileys" value="<?php print $enable_smileys; ?>" />
        <input type="hidden" class="blog_url" value="<?php bloginfo('wpurl'); ?>" />
        <input type="hidden" class="wp_jschat_last_date" name="wp_jschat_last_date" value="<?php print current_time('mysql'); ?>" />
        <input type="hidden" class="wp_jschat_canal" name="wp_jschat_canal" value="<?php print $channel; ?>" />
        <input type="text" class="wp_jschat_text" name="wp_jschat_text" style="width:150px;" />
        <input type="submit" name="wp_jschat_submit" value="ok" />
        <p><a href="#" class="wp_jschat_widget_refresh"><?php _e('Rafraîchir', 'wpjschat'); ?></a></p>
    </div>
</form>