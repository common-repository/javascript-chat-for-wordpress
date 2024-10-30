<table width="80%" border="1" class="widefat post fixed">
        <tr>
            <th width="50"><?php _e('ID', 'wpjschat') ?></th>
            <th><?php _e('Nom', 'wpjschat') ?></th>
            <th width="100"><?php _e('Action', 'wpjschat') ?></th>
        </tr>
        <?php foreach($channels as $channel): ?>
        <tr>
            <td align="center"><?php print $channel->id; ?></td>
            <td><?php print $channel->name; ?></td>
            <td><a href="#" class="delete_channel" channel="<?php print $channel->id; ?>"><?php _e('Supprimer', 'wpjschat') ?></a></td>
        </tr>
        <?php endforeach; ?>
    </table>