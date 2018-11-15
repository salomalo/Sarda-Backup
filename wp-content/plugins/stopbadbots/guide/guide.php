<?php
/**
 * @author William Sergio Minossi
 * @copyright 2016
 */
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
$ah_help = '<p style="font-family:arial; font-size:16px;">';
$ah_help .= '1) '.__('Open the General Settings Tab and click over Yes  (to begin to block).','stopbadbots');
$ah_help .= '<br>';
$ah_help .= '<br>';
$ah_help .= '2) '.__('You can also add  Bad Bots at bad bots table. Dashboard => Stop Bad Bots => Add Bad Bot to Table.','stopbadbots');
$ah_help .= '<br>';
$ah_help .= '<br>';
$ah_help .= '3) '.__('You can also add  Bad IPs at bad IPs table. Dashboard => Stop Bad Bots => Add Bad IP to Table.','stopbadbots');
$ah_help .= '<br>';
$ah_help .= '<br>';
$ah_help .= '4) '.__('At eMail Settings tab, you can customize your contact email or left blank to use your WordPress eMail.','stopbadbots');
$ah_help .= '<br>';
$ah_help .= '<br>';
$ah_help .= '5) '.__('At Notification Settings Tab, you can record your option by receive or not email alerts about bots attempts.','stopbadbots');
$ah_help .= '<br>';
$ah_help .= '<br>';
$ah_help .= '6) '.__('At Memory CheckUp tab, you can know your memory status.','stopbadbots');
$ah_help .= '<br>';
$ah_help .= '<br>';
$ah_help .= '<span style="background-color: #FFFF00">';
$ah_help .= '<big>';
$ah_help .= __('Please, read this:','stopbadbots'); 
$ah_help .= '</big>';
$ah_help .= '<br>';
$ah_help .= __('Maybe not all bots are bad for you. You need manage the bots table.','stopbadbots'); 
$ah_help .= '<br>';
$ah_help .= __('Open the page Bad Bots Table  (under Stop Bad Bots Menu) and take a look at Default Bad Bots List. If you wish, you can turn off some. (Just check, Bulk Actions, Apply). Our plugin will create a table with more than 2500 Bots.','stopbadbots');
$ah_help .= __('You can see how many times each bot was blocked at the column Num Blocked. Click the title (Num Blocked) to order by.','stopbadbots');
$ah_help .= '<br><b>';
$ah_help .= __("Check the bot's table frequently, especially in the first days.", "stopbadbots");
$ah_help .= __('Confirm if you want block all that bots. Maybe you want unblock Baidu, Yandex, Seznam or another search engine in your language or some social sites or some usefull bot for you.','stopbadbots');
$ah_help .= '<br>';
$ah_help .= __('If you use RSS FEED services, probably they have their bot to read your feeds.','stopbadbots'); 
$ah_help .= __('Remember to deactivate their bot.','stopbadbots'); 
$ah_help .= __('Same thing if you create some smartphone APP.','stopbadbots');
$ah_help .= __('StopBadBots it is a powerfull tool. Then, like all powerfull tools it is necessary to use carefully.','stopbadbots');
$ah_help .= '<br>';
$ah_help .= '<br>';
$ah_help .= __('We have also a table of bad IPs. They use fake or blank User Agent. Then, we need block them by IP.','stopbadbots'); 
$ah_help .= '<br>';
$ah_help .= '<br>';
$ah_help .= __('Note: If you use the plugin WPFastestCache, deactivate the bot named "test" at Bad Bots Table. (Dashboard => Stop Bad Bots => Bad Bots Table)','stopbadbots'); 
$ah_help .= '<br>';
$ah_help .= __('If you need more info about each bot, visit our site www.StopBadBots.com (page Bots Table)','stopbadbots'); 
$ah_help .= '</span>';
$ah_help .= '</b><br>';
$ah_help .= '<br>';
$ah_help .= __('Remember to click Save Changes before to left each tab.','stopbadbots');
$ah_help .= '<br>';
$ah_help .= '<br>';
$ah_help .= __("You don't need create any robots.txt or htaccess file. ","stopbadbots");
$ah_help .= '<br>';
$ah_help .= '<br>';
$ah_help .= __("The Plugin doesn't block main Google, Yahoo and Bing (Microsoft).",'stopbadbots');
$ah_help .= '<br>';
$ah_help .= '<br>';
$ah_help .= __('Visit the plugin site for more details, video, online guide and bot\'s and IP\'s details.','stopbadbots');
$ah_help .= '<br>';
$ah_help .= '<br>';
$ah_help .= '<a href="http://stopbadbots.com/help/" class="button button-primary">'.__("OnLine Guide","stopbadbots").'</a>';
$ah_help .= '&nbsp;&nbsp;';
$ah_help .= '<a href="http://stopbadbots.com/faq/" class="button button-primary">'.__("Faq Page","stopbadbots").'</a>';
$ah_help .= '&nbsp;&nbsp;';
$ah_help .= '<a href="http://billminozzi.com/dove/" class="button button-primary">'.__("Support Page","stopbadbots").'</a>';
$ah_help .= '&nbsp;&nbsp;';
$ah_help .= '<a href="http://siterightaway.net/troubleshooting/" class="button button-primary">'.__("Troubleshooting Page","stopbadbots").'</a>';
$ah_help .= '<br>';
$ah_help .= '<br>';
/*
$ah_help .= __('That is all. Enjoy it.','stopbadbots');
$ah_help .= '<br>';
$ah_help .= __( 'If you like this product, please write a few words about it. It will help other people find this useful plugin more quickly.', 'stopbadbots'); 
$ah_help .= '<br>';
$ah_help .= '<a href="http://stopbadbots.com/share/" class="button button-primary">'.__("Share","stopbadbots").'</a>';
*/
$ah_help .= '</p>';?>