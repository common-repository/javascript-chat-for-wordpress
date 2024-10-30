=== Javascript Chat for Wordpress ===
Contributors: oulalahakabu
Donate link: http://www.ligams.com/Publications/Wordpress/Plugin-chat-Javascript-pour-Wordpress
Tags: chat, tchat, ajax, javascript, wordpress, plugin
Requires at least: 3.0.0
Tested up to: 3.5
Stable tag: 3.5

wp_jschat is chat plugin for wordpress based on Javascript/Ajax that can be used in page or as a shoutbox.

== Description ==

wp_jschat is chat plugin for wordpress based on Javascript/Ajax that can be used in page with shorttags or as a shoutbox.
wp_jschat is multichannel and you may add as many chats as you need in your pages.
It has been develop to make an exemple of wordpress plugin in a whole <a href="http://www.ligams.com/Publications/Wordpress/">french tutorial about developping wordpress plugin</a>

== Installation ==

This section describes how to install the wp_jschat and make it working.

e.g.

1. Upload the folder `wp_jschat` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. that's it :)

== Frequently Asked Questions ==

= How to add the shoutbox ? =

Simply Add the chat widget in your sidebar

= How to add a channel ? =

Use the form in admin pannel to add your channel, alls channels are listed there.

= How to add a chat in my page ? =

Use the [wpjschat] bbcode as follow :
[wpjschat channel="__CHANNEL_ID__"]Default text[/wpjschat] ie :
[wpjschat channel="1"]Default text[/wpjschat]

= Can i add my own smileys ? =

Not at this time.

== Screenshots ==

1. The shoutbox
2. Chats inside topic placed with markup

== Changelog ==

= 1.0.0 =
* First release

= 1.0.1 =
* Fix bug path

= 1.0.2 =
* Update for last wordpress version
* Fix prepare() method second argument
* Fix channel admin that was not working in lastest wordpress version

= 1.0.3 =
* Fix Autoscroll (/kiss nitrou26)
* Fix version number
