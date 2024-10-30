<?php
/*
  Plugin Name: Javascript chat for wordpress
  Plugin URI: http://www.ligams.com/Publications/Wordpress/Plugin-chat-Javascript-pour-Wordpress
  Description: wp_jschat is chat plugin for wordpress based on Javascript/Ajax that can be used in page with shorttags or as a shoutbox. wp_jschat is multichannel and you may add as many chats as you need in your pages. It has been develop to make an exemple of wordpress plugin in a whole <a href="http://www.ligams.com/Publications/Wordpress/">french tutorial about developping wordpress plugin</a>
  Version: 1.0.3
  Author: <a href="http://www.ligams.com/">Stéphane Le Merre</a>
  Author URI: http://www.ligams.com
 */

require('php/string_utils.php');

if (!class_exists("wp_jschat"))
{
    class wp_jschat
    {
        var $name = 'javascript-chat-for-wordpress';
        var $adminOptionsName = 'wp_jschatAdminOptions';
        var $userOptionsName = 'wp_jschatUserOptions';
        var $userPseudoOptions = 'wp_jschatPseudoOptions';
        var $version = '1.0.2';

        /**
         * Constructeur
         */
        function wp_jschat()
        {
            
        }
        /**
         * Initialisation
         */
        function init()
        {
            $this->getAdminOptions();
            $this->getUserOptions();
        }
        /**
         * Initialise la widget de chat
         */
        function initWidget()
        {
            register_sidebar_widget(__('Chat'), array(&$this, 'widget_myJsChat'));
        }
        /**
         *
         * @param <type> $str 
         */
        function strToSmileys($str)
        {
             $patterns = array("@\:([a-z]+)\:@Us");
             $replace = array('<img src="'.bloginfo('wpurl').'/wp-includes/images/smilies/icon_\\1.gif" alt="\\1" />');
             return preg_replace($patterns, $replace, $str);
        }
        /**
         * Ajout du header
         */
        function addHeaderCode()
        {
            print "<!-- wp_jschat dans le header -->\n";
            wp_enqueue_script('jquery');
            wp_enqueue_script('wp_jschat', '/wp-content/plugins/'.$this->name.'/javascript/wp_jschat.js');
            wp_enqueue_style('wp_jschat', '/wp-content/plugins/'.$this->name.'/css/wp_jschat.css');
            print "<!-- END wp_jschat dans le header -->\n";
        }



        function addContent($content = '')
        {
            $content .= '<p>wp_jschat</p>';
            return $content;
        }
        function authorUpperCase($author = '')
        {
            return strtoupper($author);
        }
        /**
         * Renvoi le tableau des options d'administration
         * @return array
         */
        function getAdminOptions() {
            $wp_jschatAdminOptions = array(
                'enabled' => 'true',
                'exclude_ips' => ''
            );
            $wp_jschatOptions = get_option($this->adminOptionsName);
            if (!empty($wp_jschatOptions)) {
                foreach ($wp_jschatOptions as $key => $option)
                    $wp_jschatAdminOptions[$key] = $option;
            }
            update_option($this->adminOptionsName, $wp_jschatAdminOptions);
            return $wp_jschatAdminOptions;
        }
        /**
         * Renvoi le tableau des options utilisateur
         * @global string $user_email
         * @return string
         */
        function getUserOptions() {
            global $user_email;
            if (empty($user_email)) {
                get_currentuserinfo();
            }
            if (empty($user_email)) {
                return '';
            }
            $options = get_option($this->userOptionsName);
            if (!isset($options)) {
                $options = array();
            }
            if (empty($options[$user_email])) {
                $options[$user_email] = 'true,true';
                update_option($this->userOptionsName, $options);
            }
            return $options;
        }
        /**
         * Renvoi les derniers messages du chat
         * @global wpdb $wpdb
         * @return array $messages
         */
        function getLastMessages($channel = 1,$last_date = null)
        {
            global $wpdb;
            $options = get_option($this->adminOptionsName);
            $excludes = preg_split('@\s*,\s*@', $options['exclude_ips']);
            if($options['enabled']!='false' && !in_array($_SERVER['REMOTE_ADDR'], $excludes))
            {
                $table_name = $wpdb->prefix.'jschat_messages';
                //print "-$last_date-";
                if($last_date==null || trim($last_date)=='')
                {
                    $sql = $wpdb->prepare("SELECT * FROM `$table_name` WHERE canal=%s ORDER BY created_at DESC LIMIT 0,9", array($channel));
                    $messages = $wpdb->get_results($sql);
                }
                else
                {
                    $sql = $wpdb->prepare("SELECT * FROM `$table_name` WHERE UNIX_TIMESTAMP(created_at)>UNIX_TIMESTAMP(%s) AND canal=%s ORDER BY created_at DESC LIMIT 0,9", array($last_date,$channel));
                    $messages = $wpdb->get_results($sql);
                }
                return $messages;
            }
            return '';
        }
        /**
         * Renvoi le résultat des messages au navigateur
         */
        function ajax_getLastMessages()
        {
            $options = get_option($this->adminOptionsName);
            $excludes = preg_split('@\s*,\s*@', $options['exclude_ips']);
            if($options['enabled']!='false' && !in_array($_SERVER['REMOTE_ADDR'], $excludes))
            {
                $channel = (isset($_POST['wp_jschat_channel']) && is_numeric($_POST['wp_jschat_channel']))? $_POST['wp_jschat_channel'] : 1;
                $messages = $this->getLastMessages($channel, $_POST['wp_jschat_last_date']);
                $ret = array(
                    'hasResult' => (count($messages)>0),
                    'result' => $messages
                );
                print json_encode($ret);
            }
            die();
        }
        /**
         * Ajout d'un canal de discussion (administration)
         * @global wpdb $wpdb
         */
        function ajax_addChannel()
        {
            global $wpdb;
            if(!is_admin()) die();
            $name = strip_tags($_POST['channel_name']);
            $table_canal_name = $wpdb->prefix.'jschat_canal';
            if(trim($name)!='')
            {
                $wpdb->insert($table_canal_name, array(
                    'name' => $name,
                    'created_at' => current_time('mysql')
                ));
            }
            $this->ajax_getChannels();
            die();
        }
        /**
         * Suppression d'un canal de discussion (administration)
         * @global wpdb $wpdb
         */
        function ajax_deleteChannel()
        {
            if(!is_admin()) die();
            global $wpdb;
            $id = $_POST['id'];
            $table_canal_name = $wpdb->prefix.'jschat_canal';
            $query = $wpdb->prepare("DELETE FROM `$table_canal_name` WHERE id=%d",array($id));
            $wpdb->query($query);
            $this->ajax_getChannels();
            die();
        }
        /**
         * affiche les canaux de discussion pour le management
         * @global wpdb $wpdb 
         */
        function ajax_getChannels()
        {
            if(!is_admin()) die();
            global $wpdb;
            $table_canal_name = $wpdb->prefix.'jschat_canal';
            $query = $wpdb->prepare("SELECT * FROM `$table_canal_name` WHERE id!='1'", array() );
            $channels = $wpdb->get_results($query);
            ob_start();
            include('php/admin_channels_tab.php');
            $strTab = ob_get_contents();
            ob_clean();
            print $strTab;
        }
        /**
         * Post un message sur le chat
         * @global wpdb $wpdb
         * @global user $current_user
         */
        function postMessage()
        {
            global $wpdb;
            global $current_user;
            $options = get_option($this->adminOptionsName);
            $excludes = preg_split('@\s*,\s*@', $options['exclude_ips']);
            if($options['enabled']!='false' && !in_array($_SERVER['REMOTE_ADDR'], $excludes))
            {
                require_once (ABSPATH . WPINC . '/pluggable.php');
                get_currentuserinfo();

                $pseudo = '';
                $user_ip = $_SERVER['REMOTE_ADDR'];

                if(isset($current_user->user_login) && !empty($current_user->user_login))
                {
                    $pseudo = $current_user->user_login;
                }
                else
                {
                    $options = get_option($this->userPseudoOptions);
                    if(isset($options) && isset($options[$user_ip]))
                        $pseudo = $options[$user_ip];
                    else
                    {
                        $alea = rand(0,9999);
                        $pseudo = 'anonym'.$alea;
                        $options[$user_ip] = $pseudo;
                        update_option($this->userPseudoOptions, $options);
                    }
                }

                $table_name = $wpdb->prefix.'jschat_messages';
                $table_canal_name = $wpdb->prefix.'jschat_canal';
                $wp_jschat_text = $_POST['wp_jschat_text'];
                $submit = $_POST['wp_jschat_submit'];
                $query = $wpdb->prepare("SELECT * FROM `$table_canal_name` WHERE id=%d",array($_POST['wp_jschat_canal']));
                $canal = $wpdb->get_results($query);
                $channel = (count($canal))? $_POST['wp_jschat_canal'] : '1';

                if(isset($submit) && trim($wp_jschat_text)!='')
                {
                    $wpdb->insert($table_name, array(
                        'id_user' => $current_user->ID,
                        'pseudo' =>  $pseudo,
                        'message' => strip_tags(trim($wp_jschat_text)),
                        'created_at' => current_time('mysql'),
                        'canal' => $channel,
                        'ip' => $_SERVER['REMOTE_ADDR']
                    ));
                }
            }
            die();
        }
        /**
         * Affichage du panneau d'administration du chat
         * @global wpdb $wpdb
         */
        function printAdminPage()
        {
            global $wpdb;
            if(!is_admin()) die();
            $options = $this->getAdminOptions();
            if (isset($_POST['update_wp_jschatSettings'])) {
                if (isset($_POST['enabled'])) {
                    $options['enabled'] = $_POST['enabled'];
                }
                if (isset($_POST['exclude_ips'])) {
                    $options['exclude_ips'] = $_POST['exclude_ips'];
                }
                update_option($this->adminOptionsName, $options);
                print '<div class="updated"><p><strong>';
                _e("Paramètres mis à jour", "wpjschat");
                print '</strong></p></div>';
            }
            //récupération des canaux de discussion sauf le premier
            $table_canal_name = $wpdb->prefix.'jschat_canal';
            $query = $wpdb->prepare("SELECT * FROM `$table_canal_name` WHERE id!='1'", array() );
            $channels = $wpdb->get_results($query);
            include('php/admin_settings.php');
            //exit();
        }
        /**
         * Affichage de la configuration utilisateur du plugin
         * @global string $user_email
         */
        function printUsersOptionPage()
        {
            global $user_email;
            if(!is_user_logged_in()) die();
            if (empty($user_email))
            {
                get_currentuserinfo();
            }
            $options = $this->getUserOptions();
            if (isset($_POST['update_wp_jschatSettings']) && isset($_POST['enable_chat']) && isset($_POST['enable_smileys'])) {
                if (isset($user_email))
                {
                    $options[$user_email] = $_POST['enable_chat'] . "," . $_POST['enable_smileys'];
                    print '<div class="updated"><p>';
                    _e("Paramètres mis à jour", "wpjschat");
                    print '</strong></p></div>';
                    update_option($this->userOptionsName, $options);
                }
            }
            $opts = explode(",", $options[$user_email]);
            $enable_chat = 'true';
            $enable_smileys = 'true';
            if (sizeof($opts) >= 2)
            {
                $enable_chat = $opts[0];
                $enable_smileys = $opts[1];
            }
            include('php/options_settings.php');
        }
        /**
         * Affichage de la widget
         * @param array $args
         * @global string $user_email
         */
        function widget_myJsChat($args)
        {
            global $user_email;
            $options = get_option($this->adminOptionsName);
            $excludes = preg_split('@\s*,\s*@', $options['exclude_ips']);

            if($options['enabled']!='false' && !in_array($_SERVER['REMOTE_ADDR'], $excludes))
            {
                $enable_chat = 'true';
                $enable_smileys = 'true';
                if(!empty($user_email))
                {
                    $options = $this->getUserOptions();
                    $opts = explode(",", $options[$user_email]);
                     if (sizeof($opts) >= 2)
                    {
                        $enable_chat = $opts[0];
                        $enable_smileys = $opts[1];
                    }
                }
                if($enable_chat!='false')
                {
                    extract($args);
                    $messages = $this->getLastMessages();
                    echo $before_widget;
                    echo $before_title;
                    _e('Chat', 'wpjschat');
                    echo $after_title;
                    include('php/widget.php');
                    echo $after_widget;
                }
            }
        }
        /**
         * Affichage du chat sur la page via les shortcodes
         * @param array $args
         * @global string $user_email
         */
        function shortcode_myJsChat($attributes, $initialContent = '')
        {
            global $user_email;
            $options = get_option($this->adminOptionsName);
            $excludes = preg_split('@\s*,\s*@', $options['exclude_ips']);
            if($options['enabled']!='false' && !in_array($_SERVER['REMOTE_ADDR'], $excludes))
            {
                $enable_chat = 'true';
                $enable_smileys = 'true';
                if(!empty($user_email))
                {
                    $options = $this->getUserOptions();
                    $opts = explode(",", $options[$user_email]);
                     if (sizeof($opts) >= 2)
                    {
                        $enable_chat = $opts[0];
                        $enable_smileys = $opts[1];
                    }
                }
                if($enable_chat!='false')
                {
                    extract(shortcode_atts(array(
                      'channel' => '1'
                    ), $attributes));
                    $messages = $this->getLastMessages($channel);
                    ob_start();
                    include('php/page.php');
                    $chat = ob_get_contents();
                    ob_clean();
                    return $chat;
                }else{
                    return '';
                }
            }
        }
        /**
         * Installation du plugin wp_jschat
         * @global wpdb $wpdb
         */
        function wp_jschat_install()
        {
            global $wpdb;
            $table_name = $wpdb->prefix.'jschat_messages';
            $table_canal_name = $wpdb->prefix.'jschat_canal';
            if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name)
            {
                //création de la table
                $sql = "CREATE TABLE `$table_name` (
                    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
                    `id_user` BIGINT UNSIGNED NOT NULL ,
                    `pseudo` VARCHAR( 30 ) NOT NULL ,
                    `message` VARCHAR( 255 ) NOT NULL ,
                    `canal` VARCHAR( 255 ) NOT NULL ,
                    `created_at` DATETIME NOT NULL ,
                    `ip` VARCHAR( 20 ) NOT NULL
                    ) ;";
                $sql .= "CREATE TABLE `$table_canal_name` (
                    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
                    `name`  VARCHAR( 255 ) NOT NULL,
                    `created_at` DATETIME NOT NULL 
                    ) ;";
                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                dbDelta($sql);
                //ajout d'une ligne de message
                $initialMessage = __('Bienvenue sur le chat');
                $initialPseudo = __('Système');
                $wpdb->insert($table_name, array(
                    'id_user' => 0,
                    'pseudo' => $initialPseudo,
                    'message' => $initialMessage,
                    'created_at' => current_time('mysql') //attention au type de db
                ));
                //ajout du canal par défaut 'widget'
                $wpdb->insert($table_canal_name, array(
                    'name' => 'widget',
                    'title' => __("Voici le canal de discussion par défaut widget"),
                    'description' => '',
                    'created_at' => current_time('mysql') //attention au type de db
                ));
                $option['wp_jschat_version'] = $this->version;
            }
            add_option('wp_jschat_version',$option);
        }
        /**
         * Désinstallation du plugin wp_jschat
         * @global wpdb $wpdb
         */
        function wp_jschat_uninstall()
        {
            global $wpdb;
            $table_name = $wpdb->prefix.'jschat_messages';
            $table_canal_name = $wpdb->prefix.'jschat_canal';
            if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name)
            {
                //création de la table
                $sql = "DROP TABLE `$table_name`";
                $wpdb->query($sql);
                $sql = "DROP TABLE `$table_canal_name`";
                $wpdb->query($sql);
            }
            delete_option('wp_jschat_version');
            delete_option($this->adminOptionsName);
            delete_option($this->userOptionsName);
            delete_option($this->userPseudoOptions);
        }
    }

}
if (class_exists("wp_jschat"))
{
    $inst_wp_jschat = new wp_jschat();

    if (isset($inst_wp_jschat))
    {
        if(function_exists('register_activation_hook'))
            register_activation_hook(__FILE__, array(&$inst_wp_jschat, 'wp_jschat_install'));
        if(function_exists('register_deactivation_hook'))
            register_deactivation_hook(__FILE__, array(&$inst_wp_jschat, 'wp_jschat_uninstall'));

        $plugin_dir = basename(dirname(__FILE__));
        load_plugin_textdomain('wpjschat',null,$plugin_dir.'/lang/');
        
        if(function_exists('add_action'))
        {
            add_action('wp_head', array(&$inst_wp_jschat, 'addHeaderCode'), 1);
            add_action('activate_wp_jschat/wp_jschat.php', array(&$inst_wp_jschat, 'init'));
            add_action('admin_menu', 'wp_jschat_ap');
            add_action("plugins_loaded", array(&$inst_wp_jschat, 'initWidget'));
        }
        if(function_exists('add_filter'))
        {
            //add_filter('the_content', array(&$inst_wp_jschat, 'addContent'), 1, 1);
            //add_filter('get_comment_author', array(&$inst_wp_jschat, 'authorUpperCase'));
        }
        if(function_exists('add_shortcode'))
        {
            add_shortcode('wpjschat',array(&$inst_wp_jschat, 'shortcode_myJsChat'));
        }
    }
    if(isset($_POST['action']))
    {
        switch($_POST['action'])
        {
            case 'retrieve':
                $inst_wp_jschat->ajax_getLastMessages();
                break;
            case 'post':
                $inst_wp_jschat->postMessage();
                break;
            case 'add_channel':
                if(is_admin())
                    $inst_wp_jschat->ajax_addChannel();
                break;
            case 'delete_channel':
                if(is_admin())
                    $inst_wp_jschat->ajax_deleteChannel();
                break;
            default:
                break;
        }
    }
}
if (!function_exists("wp_jschat_ap"))
{
    function wp_jschat_ap()
    {
        global $inst_wp_jschat;
        if (!isset($inst_wp_jschat)) {
            return;
        }
        if (function_exists('add_options_page')) {
            add_options_page('WP jsChat', 'WP jsChat', 9, basename(__FILE__), array(&$inst_wp_jschat, 'printAdminPage'));
        }
        if (function_exists('add_submenu_page'))
        {
            add_submenu_page('profile.php', "WP jsChat options","WP jsChat options", 0, basename(__FILE__), array(&$inst_wp_jschat, 'printUsersOptionPage'));
	}
    }
}
?>
