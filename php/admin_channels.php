 <fieldset>
    <legend><h2><?php _e('Canaux de discussion', 'wpjschat') ?></h2></legend>
    <form id="add_channel" method="post" action="<?php bloginfo('wpurl'); ?>/wp-admin/admin-ajax.php">
            <h3><?php _e("Ajout d'un canal de discussion", 'wpjschat') ?></h3>
            <input type="hidden" name="action" value="add_channel" />
            <p>
                    <label for="channel_name">
                    <?php _e('Nom du canal', 'wpjschat') ?>
                    </label>
                <input type="text" name="channel_name" style="width: 80%;" />
            </p>
            <div class="submit">
                 <input type="submit" name="update_wp_jschatSettings" value="<?php _e('Ajouter', 'wpjschat') ?>" />
            </div>
    </form>
</fieldset>
<h2><?php _e('Liste des cannaux de discussion', 'wpjschat') ?></h2>
<div id="admin_channels_tab">
    <?php include('admin_channels_tab.php'); ?>
</div>
<script type="text/javascript">
//<![CDATA[
var _path = '<?php bloginfo('wpurl'); ?>/wp-admin/admin-ajax.php';
var pathLoad = '<?php bloginfo('wpurl'); ?>/wp-admin/images/wpspin_light.gif';
deleteChannelEvent = function()
{
    jQuery('.delete_channel').click(function(){
        if(confirm("<?php _e('Supprimer ce cannal de discussion ?','wpjschat'); ?>"))
        {
           jQuery('#admin_channels_tab').html('<div align="center"><img src="'+pathLoad+'" /></div>');
           var channelId = jQuery(this).attr('channel');
           jQuery.ajax({
               type: 'post',
               url: _path,
               data: {id:channelId, action:'delete_channel'},
               success: function(_data){
                 jQuery('#admin_channels_tab').html(_data);
                 deleteChannelEvent();
               }
             });
         }
         return false;
    });

}
deleteChannelEvent();
jQuery('#add_channel').submit(function(){
    jQuery('#admin_channels_tab').html('<div align="center"><img src="'+pathLoad+'" /></div>');
    jQuery.ajax({
           type: jQuery(this).attr('method'),
           url: jQuery(this).attr('action'),
           data: jQuery(this).serialize(),
           success: function(_data){
             jQuery('#admin_channels_tab').html(_data);
             deleteChannelEvent();
           }
         });
         return false;
});
//]]>
</script>