<?php
if(!function_exists('strToSmileys'))
{
    /**
     * @param <type> $str
     */
    function strToSmileys($str,$enable_smileys = 'true')
    {
        if($enable_smileys=='false') return $str;
         $patterns = array("@:([a-z]+):@s");
         $replace = array('<img src="'.get_bloginfo('wpurl').'/wp-includes/images/smilies/icon_\\1.gif" alt="\\1" />');
         return preg_replace($patterns, $replace, $str);
    }
}
?>