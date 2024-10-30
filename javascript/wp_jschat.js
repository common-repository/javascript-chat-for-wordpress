retrieveData = function()
{
    var nbre = jQuery('.wp_jschat_widget_form').length;
    var index = 1;
    jQuery('.wp_jschat_widget_form').each(function(){
        var t = jQuery(this);
        var _dateStr = t.find('.wp_jschat_last_date').val();
        jQuery.ajax({
           type: "POST",
           url:  t.find('.wp_jschat_widget_form').attr('action'),
           data: { wp_jschat_last_date: (_dateStr.trim()!='')? _dateStr : '', action : 'retrieve', wp_jschat_channel: t.find('.wp_jschat_canal').val() },
           dataType :'json',
           success: function(_data)
           {
             if(null!=_data && _data.hasResult)
             {
                for(var i=0;i<_data.result.length;i++)
                {
                    var _urlPath = t.find('.blog_url').val();
                    var bSmiles = (t.find('.hasSmileys').val()=='true');
                    var _message = _data.result[i].message;
                    if(bSmiles)
                    {
                        var oReg = new RegExp(":([a-z]+):",'g');
                        _message = _message.replace(oReg,'<img src="'+_urlPath+'/wp-includes/images/smilies/icon_$1.gif" alt="$1" />');
                    }
                    var _html = '<p><span class="author"><u>'+_data.result[i].pseudo+'</u></span> : '+_message+'</p>';
                     t.find('.wp_jschat_widget_text').append(_html);
                     t.find('.wp_jschat_widget_text').animate({scrollTop: t.find('.wp_jschat_widget_text').prop('scrollHeight')}, 500);
                     t.find('.wp_jschat_last_date').val(_data.result[i].created_at);
                }
             }
             if(nbre==index)
             {
                setTimeout(retrieveData,500);
             }
             index++;
           }
         });
        });
}

jQuery(document).ready(function(){
    jQuery('.wp_jschat_widget_text').attr( {scrollTop:jQuery('.wp_jschat_widget_text').attr('scrollHeight')} );
    jQuery('.wp_jschat_widget_refresh').click(function()
    {
        jQuery(this).parent().find('.wp_jschat_widget_text').html('');
        return false;
    });
    jQuery('.wp_jschat_widget_form').submit(function(){
        var t = jQuery(this);
        var obj = {};
        obj.wp_jschat_text = t.find('.wp_jschat_text').val();
        obj.action = 'post';
        obj.wp_jschat_canal = t.find('.wp_jschat_canal').val();
        obj.wp_jschat_submit = 'true';
        jQuery.ajax({
           type: jQuery(this).attr('method'),
           url: jQuery(this).attr('action'),
           data: obj,
           success: function(){
             t.find('.wp_jschat_text').val('');
             t.find('.wp_jschat_text').focus();
           }
         });
         return false;
    });
    jQuery('.wp_jschat_smile').click(function(){
   
       var icon = jQuery(this).find('img').attr('alt');
       var _val = jQuery(this).parent().parent().find('.wp_jschat_text').val();
       _val += ' :'+icon+':';
       jQuery(this).parent().parent().find('.wp_jschat_text').val(_val);
       return false;
    });
    setTimeout(retrieveData,1000);
});