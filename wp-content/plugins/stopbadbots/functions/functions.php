<?php /**
 * @author Bill Minozzi
 * @copyright 2016
 */
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly 
if (is_admin()) {
    if (isset($_GET['page'])) {
        $page = sanitize_text_field($_GET['page']);
        if ($page == 'stop-bad-bots' or $page == 'sbb_my-custom-submenu-page' or $page == 'sbb_my-custom-submenu-page2' ) {
            add_filter('contextual_help', 'stopbadbots_contextual_help', 10, 3);
            function stopbadbots_contextual_help($contextual_help, $screen_id, $screen)
            {
                $myhelp = '<br>' . __("Stop Bad Bots from stealing you.", "stopbadbots");
                $myhelp .= '<br />' . __("Read the StartUp guide at Stop Bad Bots Settings page.",
                    "stopbadbots");
                $myhelp .= '<br />';
                $myhelp .= __('Visit the', 'stopbadbots');
                $myhelp .= '&nbsp<a href="http://stopbadbots.com" target="_blank">';
                $myhelp .= __('plugin site', 'stopbadbots');
                $myhelp .= '</a>&nbsp;';
                $myhelp .= __('for more details, video and online guide.', 'stopbadbots');
                $screen->add_help_tab(array(
                    'id' => 'sbb-overview-tab',
                    'title' => __('Overview', 'stopbadbots'),
                    'content' => '<p>' . $myhelp . '</p>',
                    ));
                return $contextual_help;
            }
        }
    }
}
function sbb_add_menu_items()
{
    $sbb_table_page = add_submenu_page('stop-bad-bots', // $parent_slug
        'Bad Bots Table', // string $page_title
        'Bad Bots Table', // string $menu_title
        'manage_options', // string $capability
        'sbb_my-custom-submenu-page', 'sbb_render_list_page');
    add_action("load-$sbb_table_page", 'stopbadbots_screen_options');
    $sbb_table_page = add_submenu_page('stop-bad-bots', // $parent_slug
        'Bad IPs Table', // string $page_title
        'Bad IPs Table', // string $menu_title
        'manage_options', // string $capability
        'sbb_my-custom-submenu-page2', 'sbb_render_list_page2');
    add_action("load-$sbb_table_page", 'stopbadbots_screen_options2');
}
function stopbadbots_screen_options()
{
    global $sbb_table_page;
    $screen = get_current_screen();
    if (trim($screen->id) != 'stop-bad-bots_page_sbb_my-custom-submenu-page')
        return;
    $args = array(
        'label' => __('Bots per page', 'stopbadbots'),
        'default' => 10,
        'option' => 'stopbadbots_per_page');
    add_screen_option('per_page', $args);
}
function stopbadbots_screen_options2()
{
    global $sbb_table_page;
    $screen = get_current_screen();
    if (trim($screen->id) != 'stop-bad-bots_page_sbb_my-custom-submenu-page2')
        return;
    $args = array(
        'label' => __('IPs per page', 'stopbadbots'),
        'default' => 10,
        'option' => 'stopbadbots_per_page');
    add_screen_option('per_page', $args);
}
function stopbadbots_set_screen_options($status, $option, $value)
{
    if ('stopbadbots_per_page' == $option)
        return $value;
}
function sbb_alertme($userAgentOri)
{
    global $stopbadbotsserver, $sbb_found, $sbb_admin_email, $stopbadbotsip;
    $subject = __("Detected Bot on ", "stopbadbots") . $stopbadbotsserver;
    $message[] = __("Bot was detected and blocked.", "stopbadbots");
    $message[] = "";
    $message[] = __('Date', 'stopbadbots') . "..............: " . date("F j, Y, g:i a");
    $message[] = __('User Agent', 'stopbadbots') . "........: " . $userAgentOri;
    $message[] = __('Robot IP Address', 'stopbadbots') . "..: " . $stopbadbotsip;
    $message[] = __('String Found:', 'stopbadbots') . "...... " . $sbb_found;
    $message[] = "";
    $message[] = __('eMail sent by Stop Bad Bots Plugin.', 'stopbadbots');
    $message[] = __('You can stop emails at the Notifications Settings Tab.',
        'stopbadbots');
    $message[] = __('Dashboard => Stop Bad Bots => Stop Bad Bots.', 'stopbadbots');
    $msg = join("\n", $message);
    wp_mail($sbb_admin_email, $subject, $msg);
    return;
}
function sbb_alertme2($stopbadbotsip)
{
    global $stopbadbotsserver, $sbb_found, $sbb_admin_email;
    
    
    $subject = __("Detected Bot on ", "stopbadbots") . $stopbadbotsserver;
    $message[] = __("Bot was detected and blocked.", "stopbadbots");
    $message[] = "";
    $message[] = __('Date', 'stopbadbots') . "..............: " . date("F j, Y, g:i a");
    $message[] = __('Robot IP Address', 'stopbadbots') . "..: " . $stopbadbotsip;
    $message[] = "";
    $message[] = __('eMail sent by Stop Bad Bots Plugin.', 'stopbadbots');
    $message[] = __('You can stop emails at the Notifications Settings Tab.',
        'stopbadbots');
    $message[] = __('Dashboard => Stop Bad Bots => Stop Bad Bots.', 'stopbadbots');
    $msg = join("\n", $message);
    wp_mail($sbb_admin_email, $subject, $msg);
    return;
}
function sbb_findip()
{
    $ip = '';
    $headers = array(
        'HTTP_CLIENT_IP', // Bill
        'HTTP_X_REAL_IP', // Bill
        'HTTP_X_FORWARDED', // Bill
        'HTTP_FORWARDED_FOR', // Bill
        'HTTP_FORWARDED', // Bill
        'HTTP_X_CLUSTER_CLIENT_IP', //Bill
        'HTTP_CF_CONNECTING_IP', // CloudFlare
        'HTTP_X_FORWARDED_FOR', // Squid and most other forward and reverse proxies
        'REMOTE_ADDR', // Default source of remote IP
        );
    for ($x = 0; $x < 8; $x++) {
        foreach ($headers as $header) {
           /*
           if(!array_key_exists($header, $_SERVER))
              continue;
           */
           if( !isset($_SERVER[$header]))
                   continue;
                $myheader = trim(sanitize_text_field($_SERVER[$header]));
                if(empty($myheader))   
                  continue;
            $ip = trim(sanitize_text_field($_SERVER[$header]));
            if (empty($ip)) {
                continue;
            }
            if (false !== ($comma_index = strpos(sanitize_text_field($_SERVER[$header]), ','))) {
                $ip = substr($ip, 0, $comma_index);
            }
            // First run through. Only accept an IP not in the reserved or private range.
            if ($ip == '127.0.0.1') {
                $ip = '';
                continue;
            }
            if (0 === $x) {
                $ip = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE |
                    FILTER_FLAG_NO_PRIV_RANGE);
            } else {
                $ip = filter_var($ip, FILTER_VALIDATE_IP);
            }
            if (!empty($ip)) {
                break;
            }
        }
        if (!empty($ip)) {
            break;
        }
    }
    if (!empty($ip))
        return $ip;
    else
        return 'unknow';
}
// $stopbadbotsip = sbb_findip();
function sbb_plugin_was_activated()
{
    global $wp_sbb_blacklist;
    add_option('sbb_was_activated', '1');
    update_option('sbb_was_activated', '1');
    $stopbadbots_installed = trim(get_option('stopbadbots_installed', ''));
    if (empty($stopbadbots_installed)) {
        add_option('stopbadbots_installed', time());
        update_option('stopbadbots_installed', time());
    }
    // require_once (STOPBADBOTSPATH . "functions/aBots.php");
    sbb_create_db();
    sbb_create_db2();
    sbb_fill_db_froma();
    sbb_fill_db_froma2();
    sbb_upgrade_db();
}
function sbb_fill_db_froma()
{
    global $wpdb, $wp_filesystem;
    $table_name = $wpdb->prefix . "sbb_blacklist";
    $charset_collate = $wpdb->get_charset_collate();
    $botsfile = STOPBADBOTSPATH . 'assets/bots.txt';
    $botshandle = @fopen($botsfile, "r");
    if ($botshandle) {
        //$delete = "delete from " . $table_name . " WHERE botblocked < 1 and botstate <> 'Disabled'";
        //$wpdb->query($delete);
        while (($botsbuffer = fgets($botshandle, 4096)) !== false) {
            $asplit = explode(',', $botsbuffer);
            if (count($asplit) < 3)
                continue;
            $botnickname = trim($asplit['0']);
            $botname = trim($asplit['1']);
            $newbotflag = trim($asplit['2']);
            if ($newbotflag == 'C')
                $botflag = '6';
            else
                $botflag = '3';
            $query = "SELECT * FROM " . $table_name . " where botnickname = '" . $botnickname .
                "' limit 1";
            $results9 = $wpdb->get_results($query);
            if (count($results9) > 0 or empty($botnickname))
                continue;
            $query = "INSERT INTO " . $table_name .
                " (botnickname, botname, botstate, botflag)
                  VALUES ('" . $botnickname . "', '" . $botname . "', 
                 'Enabled', '" . $botflag . "')";
            $r = $wpdb->get_results($query);
        } // End Loop
        if (!feof($botshandle)) {
            // echo "Error: unexpected fgets() fail\n";
            return false;
        }
    } // end open
    fclose($botshandle);
} // end Function
function sbb_fill_db_froma2()
{
    global $wpdb, $wp_filesystem;
    $table_name = $wpdb->prefix . "sbb_badips";
    if(!stopbadbots_tablexist($table_name))
        sbb_create_db2();
    $charset_collate = $wpdb->get_charset_collate();
    $botsfile = STOPBADBOTSPATH . 'assets/botsip.txt';
   // echo $botsfile;
  //  echo '<hr>';
    $botshandle = @fopen($botsfile, "r");
    if ($botshandle) {
        //$delete = "delete from " . $table_name . " WHERE botblocked < 1 and botstate <> 'Disabled'";
        //$wpdb->query($delete);
        while (($botsbuffer = fgets($botshandle, 4096)) !== false) {
            $asplit = explode(',', $botsbuffer);
            // echo count($asplit);
            if (count($asplit) < 2)
                continue;
            $botip = trim($asplit['0']);
            $newbotflag = trim($asplit['1']);
            if ($newbotflag == 'C')
                $botflag = '6';
            else
                $botflag = '3';
            $query = "SELECT * FROM " . $table_name . " where botip = '" . $botip .
                "' limit 1";
            $results9 = $wpdb->get_results($query);
            if (count($results9) > 0 or empty($botip))
                continue;
            //echo $query;
            $query = "INSERT INTO " . $table_name .
                " (botip, botstate, botflag, added)
                  VALUES ('" . $botip . "', 
                 'Enabled', '" . $botflag . "', 'Plugin')";
            $r = $wpdb->get_results($query);
        } // End Loop
        if (!feof($botshandle)) {
            // echo "Error: unexpected fgets() fail\n";
            return false;
        }
    } // end open
    fclose($botshandle);
} // end Function
function sbb_create_db()
{
    global $wpdb;
    require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
    // creates my_table in database if not exists
    $table = $wpdb->prefix . "sbb_blacklist";
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE IF NOT EXISTS $table (
        `id` mediumint(9) NOT NULL AUTO_INCREMENT,
        `botnickname` varchar(30) NOT NULL,
        `botname` text NOT NULL,
        `boturl` text NOT NULL,
        `botip` varchar(100) NOT NULL,
        `botobs` text NOT NULL,
        `botstate` varchar(10) NOT NULL,
        `botblocked` mediumint(9) NOT NULL,
        `botdate` timestamp NOT NULL,
        `botflag` varchar(1) NOT NULL,
        `botua` text NOT NULL,
    UNIQUE (`id`),
    UNIQUE (`botnickname`)
    ) $charset_collate;";
    // KEY `botnickname` (`botnickname`)
    dbDelta($sql);
}
function sbb_create_db2()
{
    global $wpdb;
    require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
    // creates my_table in database if not exists
    $table = $wpdb->prefix . "sbb_badips";
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE IF NOT EXISTS $table (
        `id` mediumint(9) NOT NULL AUTO_INCREMENT,
        `botip` varchar(100) NOT NULL,
        `botobs` text NOT NULL,
        `botstate` varchar(10) NOT NULL,
        `botblocked` mediumint(9) NOT NULL,
        `botdate` timestamp NOT NULL,
        `added` varchar(30)NOT NULL,        
        `botflag` varchar(1) NOT NULL,
    UNIQUE (`id`),
    UNIQUE (`botip`)
    ) $charset_collate;";
    // KEY `botnickname` (`botnickname`)
    dbDelta($sql);
}
function sbb_upgrade_db()
{
    global $wpdb;
    require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
    $table_name = $wpdb->prefix . "sbb_blacklist";
    if (!stopbadbots_tablexist($table_name))
        return;
    $query = "SHOW COLUMNS FROM " . $table_name . " LIKE 'botblocked'";
    $wpdb->query($query);
    if (empty($wpdb->num_rows)) {
        $alter = "ALTER TABLE " . $table_name . " ADD botblocked mediumint(9) NOT NULL";
        ob_start();
        $wpdb->query($alter);
        ob_end_clean();
    }
    // Upgrade to new names
    //$stopbadbots_option_name[0] = 'stop_bad_bots_active';
    $stopbadbots_option_name[1] = 'my_blacklist';
    $stopbadbots_option_name[2] = 'my_email_to';
    $stopbadbots_option_name[3] = 'my_radio_report_all_visits';
    for ($i = 1; $i < 4; $i++) {
        $stopbadbots_option = get_site_option($stopbadbots_option_name[$i]);
        $stopbadbots_new_name = 'stopbadbots_' . $stopbadbots_option_name[$i];
        add_site_option($stopbadbots_new_name, $stopbadbots_option);
        // update_site_option();
        delete_option($stopbadbots_option_name[$i]);
        // For site options in Multisite
        delete_site_option($stopbadbots_option_name[$i]);
    }
}
function sbbmoreone($userAgentOri)
{
    global $sbb_found, $wpdb;
    require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
    $table_name = $wpdb->prefix . "sbb_blacklist";
    $query = "UPDATE " . $table_name .
        " SET botblocked = botblocked+1 WHERE botnickname = '" . $sbb_found . "'";
    $wpdb->query($query);
}
function sbbmoreone2($stopbadbotsip)
{
    global $wpdb;
    require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
    $table_name = $wpdb->prefix . "sbb_badips";
    $query = "UPDATE " . $table_name .
        " SET botblocked = botblocked+1 WHERE botip = '" . $stopbadbotsip . "'";
    $wpdb->query($query);
}
function sbb_plugin_act_message()
{
    echo '<div class="updated"><p>';
    $sbb_msg = '<img src="' . STOPBADBOTSURL . '/images/infox350.png" />';
    $sbb_msg .= '<h2>';
    $sbb_msg .= __('Stop Bad Bots Plugin was activated!', 'stopbadbots');
    $sbb_msg .= '</h2>';
    $sbb_msg .= '<h3>';
    $sbb_msg .= __('For details and help, take a look at Stop Bad Bots at your left menu',
        'stopbadbots');
    $sbb_msg .= '<br />';
    $sbb_msg .= '  <a class="button button-primary" href="admin.php?page=stop-bad-bots">';
    $sbb_msg .= __('or click here', 'stopbadbots');
    $sbb_msg .= '</a>';
    // $sbb_msg .=  $sbb_url;
    echo $sbb_msg;
    echo "</p></h3></div>";
}
if (is_admin()) {
    if (get_option('sbb_was_activated', '0') == '1') {
        add_action('admin_notices', 'sbb_plugin_act_message');
        $r = update_option('sbb_was_activated', '0');
        if (!$r)
            add_option('sbb_was_activated', '0');
    }
}
////////////////////
function sbb_add_admin_menu()
{
    add_submenu_page('stop-bad-bots', // $parent_slug
        'Bad Bots Table', // string $page_title
        'Add Bad Bot to Table', // string $menu_title
        'manage_options', 'Add New Bad Bot', // Page Title
        'sbb_options_page');
}
function sbb_add_admin_menu2()
{
    add_submenu_page('stop-bad-bots', // $parent_slug
        'Bad IPs Table', // string $page_title
        'Add Bad IPs to Table', // string $menu_title
        'manage_options', 'Add New Bad IP', // Page Title
        'sbb_options_page2'); 
}
function sbb_settings_init()
{
    register_setting('pluginPage', 'sbb_settings');
    add_settings_section('sbb_pluginPage_section', __('Add new bad bot to the bad bots Table.',
        'stopbadbots'), 'sbb_settings_section_callback', 'pluginPage');
    add_settings_field('sbb_text_field_0', __('Bad Bot Nickname:', 'stopbadbots'),
        'sbb_text_field_0_render', 'pluginPage', 'sbb_pluginPage_section');
}
function sbb_settings2_init()
{
    register_setting('pluginPage2', 'sbb_settings2');
    add_settings_section('sbb_pluginPage2_section', __('Add new bad IP to the bad IPs Table.',
        'stopbadbots'), 'sbb_settings_section2_callback', 'pluginPage2');
    add_settings_field('sbb_text_field_2', __('Bad Bot IP:', 'stopbadbots'),
        'sbb_text_field_2_render', 'pluginPage2', 'sbb_pluginPage2_section');
  //  add_settings_field('sbb_text_field_0', __('Bad Bot IP2:', 'stopbadbots'),
  //      'sbb_text_field_0_render', 'pluginPage2', 'sbb_pluginPage2_section');
}
function sbb_text_field_0_render()
{
    $options = get_option('sbb_settings'); ?>
	<input type='text' name='sbb_settings[sbb_input_nickname]' value='<?php // echo $options['sbb_input_nickname']; ?>'>
	<?php }
function sbb_text_field_2_render()
{
    $options = get_option('sbb_settings2'); ?>
	<input type='text' name='sbb_settings2[sbb_input_ip]' value=''>
	<?php } 
function sbb_settings_section_callback()
{
    echo __("In addiction to default system table, you can add one or more string to the table.",
        "stopbadbots");
    echo '<br />';
    echo __("Example: SpiderBot (no case sensitive)", "stopbadbots");
    echo '&nbsp;';
    echo __("Just a piece of the name is enough.", "stopbadbots");
    echo '&nbsp;';
    echo __('For example, if you put "bot" will block all bots with the string bot at user agent name.',
        'stopbadbots');
    echo '&nbsp;';
    echo __("Attention: In this case, you will block also google bot because their name is GoogleBot.",
        "stopbadbots");
    echo '<br />';
    echo '<b>';
    echo __('Do not use special characters.', 'stopbadbots');
    echo '</b>';
    echo '<br />';
    echo __("Add one bad bot each time. The table don't accept duplicate nicknames.",
        'stopbadbots');
}
function sbb_settings_section2_callback()
{
    echo __("In addiction to default ip table, you can add one or more ip to the table.",
        "stopbadbots");
    echo '<br />';
    echo __("Add one bad ip each time. The table don't accept duplicate ips.",
        'stopbadbots');
    echo '<br />';
    echo __("Be carefull. This IP will be blocked to access your site.",
        'stopbadbots');       
}
function stopbadbots_admin_notice__success()
{ ?>
    <div class="notice notice-success is-dismissible">
        <p><?php _e('Bot included at table!', 'stopbadbots'); ?></p>
    </div>
    <?php }
function stopbadbots_admin_notice__fail()
{ ?>
    <div class="notice notice-error is-dismissible">
        <p><?php _e('Fail to include bot! Check bot nickname and remember Duplicates are not allowed. ',
'stopbadbots'); ?></p>
    </div>
    <?php }
function stopbadbots_admin_notice2__success()
{ ?>
    <div class="notice notice-success is-dismissible">
        <p><?php _e('IP included at table!', 'stopbadbots'); ?></p>
    </div>
    <?php }
function stopbadbots_admin_notice2__fail()
{ ?>
    <div class="notice notice-error is-dismissible">
        <p><?php _e('Fail to include IP! Check bot IP and remember Duplicates are not allowed. ',
'stopbadbots'); ?></p>
    </div>
    <?php }
function sbb_options_page()
{ ?>
	<form action='options.php' method='post'>
		<h1>Stop Bad Bots Plugin</h1>
		<?php     settings_fields('pluginPage');
    do_settings_sections('pluginPage');
    submit_button(); ?>
      <?php sbb_update_db(); ?>     
	</form>
	<?php }
function sbb_options_page2()
{ ?>
	<form action='options.php' method='post'>
		<h1>Stop Bad Bots Plugin</h1>
		<?php     
    settings_fields('pluginPage2');
    do_settings_sections('pluginPage2');
    submit_button(); ?>
      <?php sbb_update_db2(); ?>     
	</form>
	<?php } 
function sbb_update_db()
{
    global $wpdb;
    $table_name = $wpdb->prefix . "sbb_blacklist";
    if (!stopbadbots_tablexist($table_name))
        return;
    $options = get_option('sbb_settings');
    if (isset($options['sbb_input_nickname'])) {
        $nickname = $options['sbb_input_nickname'];
        $query = "INSERT INTO $table_name (botnickname,botname,botstate,botflag,botdate) VALUES ('$nickname','$nickname','Enabled', '1' , now())";
        if (!empty($nickname))
            $r = $wpdb->query($query);
        else
            $r = false;
        if ($r)
            stopbadbots_admin_notice__success();
        else
            stopbadbots_admin_notice__fail();
        // clear sbb_input_nickname
        unset($options['sbb_input_nickname']);
        update_option('sbb_settings', $options);
    }
    return;
}
function sbb_update_db2()
{
    global $wpdb, $_POST ;
    $table_name = $wpdb->prefix . "sbb_badips";
    if (!stopbadbots_tablexist($table_name))
        return;
    $options = get_option('sbb_settings2');
    if (isset($options['sbb_input_ip']))     
    {
        $ip = $options['sbb_input_ip'];
        $query = "INSERT INTO $table_name (botip,botstate,botflag,botdate,added) VALUES ('$ip', 'Enabled', '1' , now(), 'User')";
        $r = false;
        $ip = trim($ip);
        if (! empty($ip))
        { 
              if ( filter_var($ip, FILTER_VALIDATE_IP))
                 $r = $wpdb->query($query);
            if ($r)
                stopbadbots_admin_notice2__success();
            else
                stopbadbots_admin_notice2__fail();
        }    
        // clear sbb_input_ip
        unset($options['sbb_input_ip']);
        update_option('sbb_settings2', $options);
    }
    return;
}
function check_db_sbb_blacklist()
{
    global $wpdb;
    $table_name = $wpdb->prefix . "sbb_blacklist";
    if (!stopbadbots_tablexist($table_name))
        return;
    $res = $wpdb->get_col("DESC {$table_name}", 0);
    $num_files = count($res);
    if ($num_files < 11) {
        $query = 'ALTER TABLE  `' . $table_name . '` 
       ADD  `botdate` TIMESTAMP NOT NULL,
       ADD  `botflag` VARCHAR( 1 ) NOT NULL,
       ADD  `botua` TEXT NOT NULL';
        $r = $wpdb->query($query);
    }
    //delete +5Bot
    $query = "DELETE FROM `" . $table_name . "` 
       WHERE botnickname = '+5Bot' LIMIT 1";
    $r = $wpdb->query($query);
    $query = "DELETE FROM  `" . $table_name . "` 
       WHERE  `botua` LIKE  '%wordpress%'";
    $r = $wpdb->query($query);
}
function upload_new_bots()
{
    global $wpdb;
    $table_name = $wpdb->prefix . "sbb_blacklist";
    $query = 'select * from ' . $table_name .
        ' where botflag = "2" or botflag = "1" ';
    $result = $wpdb->get_row($query);
    if (!$result)
        return;
    $id = $result->id;
    $ua = $result->botua;
    $ip = $result->botip;
    $date = $result->botdate;
    $nickname = $result->botnickname;
    $myarray = array(
        'ua' => $ua,
        'ip' => $ip,
        'date' => $date,
        'nickname' => $nickname,
        'version' => STOPBADBOTSVERSION,
        );
    $url = "http://stopbadbots.com/api/httpapi.php";
    $response = wp_remote_post($url, array(
        'method' => 'POST',
        'timeout' => 45,
        'redirection' => 5,
        'httpversion' => '1.0',
        'blocking' => true,
        'headers' => array(),
        'body' => $myarray,
        'cookies' => array()));
    if (is_wp_error($response)) {
        // $error_message = $response->get_error_message();
        // echo "Something went wrong: $error_message";
    } else {
        $botflag = '4';
        if (!empty($ua) and !empty($ip))
            $botglag = '6';
        $query = "update " . $table_name . " set botflag = '" . $botflag .
            "' WHERE id ='" . $id . "'";
        $result = $wpdb->query($query);
    }
}
function sbb_get_ua()
{
    if (! isset ($_SERVER['HTTP_USER_AGENT']))
      return "mozilla compatible";
    $ua = trim(sanitize_text_field($_SERVER['HTTP_USER_AGENT']));
    $ua = sbb_clear_extra($ua);
    return $ua;
}
function sbb_clear_extra($mystring)
{
    $mystring = str_replace('$', 'S;', $mystring);
    $mystring = str_replace('{', '!', $mystring);
    $mystring = str_replace('shell', 'chell', $mystring);
    $mystring = str_replace('curl', 'kurl', $mystring);
    $mystring = str_replace('<', '&lt;', $mystring);
    return $mystring;
}
function sbb_complete_bot_data($nickname)
{
    global $wpdb;
    if (empty($nickname))
        return;
    $table_name = $wpdb->prefix . "sbb_blacklist";
    $query = 'select * from ' . $table_name . ' where botnickname =  "' . $nickname .
        '" and botflag <> "6" limit 1';
    $result = $wpdb->get_row($query);
    if (!$result)
        return;
    $id = $result->id;
    $uadb = $result->botua;
    $ipdb = $result->botip;
    if (empty($uadb) and empty($ipdb)) {
    } else
        return;
    $ua = sbb_get_ua();
    $ip = sbb_findip();
    $maybe = false;
    if (empty($uadb) and !empty($ua))
        $maybe = true;
    if (empty($ipdb) and !empty($ip))
        $maybe = true;
    if ($maybe) {
    } else
        return;
    $ua = json_encode($ua);
    $sql = "update " . $table_name . " SET 
     botua = '" . $ua . "',
     botip = '" . $ip . "',
     botflag = '2'
     WHERE
     id = '" . $id . "'
     limit 1";
    $result = $wpdb->query($sql);
    return;
}


if (get_option('stop_bad_bots_network', '') == 'yes')
{
      add_action('plugins_loaded', 'sbb_chk_update');
      add_action('plugins_loaded', 'sbb_chk_update2');
}
    
    
function sbb_chk_update()
{
    global $wpdb;
    $table_name = $wpdb->prefix . "sbb_blacklist";
    $last_checked = get_option('stopbadbots_last_checked', '0');
    $stopbadbots_checkversion = trim(get_option('stopbadbots_checkversion', ''));
    if(empty($stopbadbots_checkversion))
      $days = 120;
    else
      $days = 7;
    $write = time() - (8 * 24 * 3600);  
    if ($last_checked == '0') {
        if (!add_option('stopbadbots_last_checked', $write))
            update_option('stopbadbots_last_checked', $write);
        return;
    } elseif (($last_checked + ($days * 24 * 3600)) > time()) {
        return;
    }
    ob_start();
    $domain_name = get_site_url();
    $urlParts = parse_url($domain_name);
    $domain_name = preg_replace('/^www\./', '', $urlParts['host']);
    $myarray = array(
        'last_checked' => $last_checked,
        'stopbadbots_checkversion' => $stopbadbots_checkversion,
        'version' => STOPBADBOTSVERSION,
        'domain_name' => $domain_name,
        );
    $url = "http://stopbadbots.com/api/httpapi.php";
    //$bot_nickname = 'test';
    $response = wp_remote_post($url, array(
        'method' => 'POST',
        'timeout' => 15,
        'redirection' => 5,
        'httpversion' => '1.0',
        'blocking' => true,
        'headers' => array(),
        'body' => $myarray,
        'cookies' => array()));
    if (is_wp_error($response)) {
        $error_message = $response->get_error_message();
        // echo "Something went wrong: $error_message";
        ob_end_clean();
        return;
    }
    $r = trim($response['body']);
    $r = json_decode($r, true);
    $q = count($r);
    for ($i = 0; $i < $q; $i++) {
        $botnickname = trim($r[$i]['botnickname']);
        $botname = trim($r[$i]['botname']);
        $botip = trim($r[$i]['botip']);
        $botua = trim($r[$i]['botua']);
        if (empty($botnickname) or empty($botname) or empty($botip) or empty($botua))
            continue;
        // delete
        if ($botip == '-1') {
            $query = "DELETE FROM  " . $table_name . " WHERE botnickname = '" . $botnickname .
                "' LIMIT 1";
            $ret = $wpdb->get_results($query);
            continue;
        } else {
            $query = "select COUNT(*) from " . $table_name . " WHERE botnickname = '" . $botnickname .
                "' LIMIT 1";
            if ($wpdb->get_var($query) > 0)
                continue;
            $query = "INSERT INTO " . $table_name .
                " (botnickname, botname, botip, botua, botstate, botflag)
              VALUES ('" . $botnickname . "', '" . $botname . "', '" . $botip .
                "', '" . $botua . "', 'Enabled', '9')";
            $ret = $wpdb->get_results($query);
        }
    }
    if (!add_option('stopbadbots_last_checked', time()))
        update_option('stopbadbots_last_checked', time());
    ob_end_clean();
}
function sbb_chk_update2()
{
    global $wpdb;
    $table_name = $wpdb->prefix . "sbb_badips";
    $last_checked = get_option('stopbadbots_last_checked2', '0');
    $stopbadbots_checkversion = trim(get_option('stopbadbots_checkversion', ''));
    /*
    if(empty($stopbadbots_checkversion))
      $days = 120;
    else
      $days = 7;
    */
    $days = 7;  
    $write = time() - (8 * 24 * 3600);  
    if ($last_checked == '0') {
        if (!add_option('stopbadbots_last_checked2', $write))
            update_option('stopbadbots_last_checked2', $write);
        return;
    } elseif (($last_checked + ($days * 24 * 3600)) > time()) {
        return;
    }
    ob_start();
    $domain_name = get_site_url();
    $urlParts = parse_url($domain_name);
    $domain_name = preg_replace('/^www\./', '', $urlParts['host']);
    $myarray = array(
        'last_checked' => $last_checked,
        'stopbadbots_checkversion' => $stopbadbots_checkversion,
        'version' => STOPBADBOTSVERSION,
        'domain_name' => $domain_name,
        );
    $url = "http://stopbadbots.com/api/httpapiip.php";
    $response = wp_remote_post($url, array(
        'method' => 'POST',
        'timeout' => 15,
        'redirection' => 5,
        'httpversion' => '1.0',
        'blocking' => true,
        'headers' => array(),
        'body' => $myarray,
        'cookies' => array()));
    if (is_wp_error($response)) {
        $error_message = $response->get_error_message();
        // echo "Something went wrong: $error_message";
        ob_end_clean();
        return;
    }
    $r = trim($response['body']);
    $r = json_decode($r, true);
    $q = count($r);
    for ($i = 0; $i < $q; $i++) {
        $botip = trim($r[$i]['ip']);
        $botflag = trim($r[$i]['flag']);
        if (empty($botip))
            continue;
        // delete
        if ($botflag == '-1') {
            $query = "DELETE FROM  " . $table_name . " WHERE botip = '" . $botip .
                "' LIMIT 1";
            $ret = $wpdb->get_results($query);
            continue;
        } else {
            $query = "select COUNT(*) from " . $table_name . " WHERE botip = '" . $botip .
                "' LIMIT 1";
            if ($wpdb->get_var($query) > 0)
                continue;
            $query = "INSERT INTO " . $table_name .
                " (botip, botstate, botflag, added)
              VALUES ('" . $botip . "', 'Enabled', '9', 'Plugin')";
            $ret = $wpdb->get_results($query);
        }
    }
    if (!add_option('stopbadbots_last_checked2', time()))
        update_option('stopbadbots_last_checked2', time());
    ob_end_clean();
}
function sbbvisitoripDetect($stopbadbotsip)
{
  global $wpdb;
  $current_table = $wpdb->prefix . 'sbb_badips';
  $result = $wpdb->get_results("SELECT botip FROM $current_table WHERE `botip` = '$stopbadbotsip' ");
 if($wpdb->num_rows > 0)
    return true;
 else
    return false;
}
function sbbcrawlerDetect($userAgent)
{
    global $wpdb, $sbb_found, $stopbadbotsip, $userAgentOri;
    $foundit = strpos($userAgent, 'wordpress');
    if ($foundit !== false)
        return false;
    $current_table = $wpdb->prefix . 'sbb_blacklist';
    $result = $wpdb->get_results("SELECT botnickname, id FROM $current_table WHERE `botstate` LIKE 'Enabled' ");
    $sbb_found = '';
    foreach ($result as $results) {
        $botnickname = trim($results->botnickname);
        if (strlen($botnickname) < 3)
            continue;
        if (stripos($userAgent, $botnickname) !== false)
            $sbb_found = $botnickname;
    }
    if (!empty($sbb_found))
        return true;
    if (get_option('stop_bad_bots_network', '') != 'yes')
        return false;
    ////////// / New
    // not found
    $lookfor = array(
        'bot',
        'crawler',
        'spider',
        'link',
        'fetcher',
        'scanner',
        'grabber',
        'collector',
        'capture',
        'seo',
        '.com');
    $maybefoundbot = false;
    for ($i = 0; $i < count($lookfor); $i++) {
        $foundit = strpos($userAgent, strtolower($lookfor[$i]));
        if ($foundit !== false) {
            $maybefoundbot = true;
            break;
        }
    }
    if ($maybefoundbot == false)
        return false;
    // else have bot at ua
    $agentsok = array(      
            ' link ',
            '_seon',
            'adsbot',
            'adsbot-google',
            'appcontrols.com',
            'aranhabot', // amazon 
            'avant browser',
            'avantbrowser',
            'baidu',
            'baiduspider',
            'binarycanary.com',
            'bingbot',
            'bla',
            'blogger.com',
            'blogmuraBot',
            'bot@eright.com',
            'botwarz',
            'boxcar',
            'bsalsa.com',
            'build/prolink',
            'campus bot',
            'cablink',
            'callpage',
            'chainlink',
            'choosito.com',
            'code.google.com/apis/maps/',
            'conbot',
            'crisp.chat',
            'cronless',
            'cubot',
            'cubot_note',
            'docs.google.com',
            'dpdesk.com',
            'drive.google.com',
            'dynamic Wrapper',
            'elinks/0',
            'entireweb',
            'exalead',
            'ezine',
            'facebook',
            'fdm',
            'feed',
            'feedfetcher-google',
            'feedparser',
            'feedzirra',
            'fuelbot',
            'galaxy',
            'google-analytics.com',
            'google-youtube-links',
            'google.com',
            'google.com/merchants',
            'googlebot',
            'googleimageproxy',
            'ichiro-goo',
            'iis.net',
            'ithemes.com',
            'kinsta-bot', 
            'lcc',
            'libwww',
            'link+',
            'link5',
            'linkedin',
            'linklinklove',
            'live',
            'm.tigo.com',
            'mailpoet.com',
            'mainwp.com',
            'mclinkface',
            'mediapartners-google',
            'mobilink',
            'msn bot/1.0',
            'msnbot',
            'nonli',
            'newsbank.com',
            'nsoftware.com',
            'ohdearapp.com', 
            'oercommons.org',
            'opendns',
            'orcabrowser.com',
            'ostermiller.org',
            'pagosonline.com',
            'pantechp8010',
            'paypal.com/ipn',
            'picofeed',
            'pingdom.com',
            'plukkie',
            'printfriendly',
            'question2answer.org',
            'Register.Com.GR',
            'rtbtr.com',
            'ridder.co',
            'riseofglory',
            'rss',
            'sarafanbot', 
            'savepagenow',
            's2member.com',
            'salesforce.com',
            'scoutjet',
            'secondlife.com',
            'seznam',
            'shareaholic',
            'shopping.com',
            'shopwiki',
            'silk',
            'sitemap',
            'siteuptime.com',
            'slurp',
            'snowhaze.com',
            'stripe.com',
            'swanson',
            'tbrss.com',
            'tigo.com',
            'tripadvisor',
            'twitter',
            'unoeuro.com',
            'unfurlist',
            'uptime.com',
            'url.com',
            'vagabondo_wiseguys',
            'VBA-Web',
            'voila',
            'vuhuv.com',
            'webcron.org',
            'wikimedia',
            'windows nt',
            'wp-rocket.me',
            'wprocketbot',
            'yahoo',
            'yandex',
            'yellowpages',
            'yeti_naver');    
    for ($i = 0; $i < count($agentsok); $i++) {
        $foundit = stripos($userAgent, $agentsok[$i]);
        if ($foundit !== false)
            return false;
    }
    //Especificos
    $auako2 = array(
        'Ant',
        '2ip',
        'AHC',
        'bot',
        'git');
    for ($i = 0; $i < count($auako2); $i++) {
        if (trim($sbb_found) == trim($auako2[$i]))
            return false;
    }
    $nickname = (string )time();
    $myarray = array(
        'ua' => $userAgentOri,
        'botip' => $stopbadbotsip,
        'nickname' => $nickname,
        'version' => STOPBADBOTSVERSION,
        );
    if (empty($userAgentOri) or empty($stopbadbotsip) or empty($nickname))
        return false;
    ob_start();
    $url = "http://stopbadbots.com/api/httpapi.php";
    $response = wp_remote_post($url, array(
        'method' => 'POST',
        'timeout' => 10,
        'redirection' => 5,
        'httpversion' => '1.0',
        'blocking' => true,
        'headers' => array(),
        'body' => $myarray,
        'cookies' => array()));
    ob_end_clean();
    return false;
}
function stopbadbots_tablexist($table)
{
    global $wpdb;
    $table_name = $table;
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name)
        return true;
    else
        return false;
} 
add_filter( 'plugin_row_meta', 'stopbadbots_custom_plugin_row_meta', 10, 2 );
$stopbadbots_checkversion = trim(get_option('stopbadbots_checkversion', ''));
function stopbadbots_custom_plugin_row_meta( $links, $file ) {
    global $stopbadbots_checkversion;
 if ( strpos( $file, 'stopbadbots.php' ) !== false ) {
     $new_links = array(
				'OnLine Guide' => '<a href="http://stopbadbots.com/help/" target="_blank">OnLine Guide</a>');
     if (empty ($stopbadbots_checkversion))
     {
            $new_links['Pro'] = '<a href="http://stopbadbots.com/premium/" target="_blank"><b><font color="#FF6600">Go Pro</font></b></a>';
     }
		$links = array_merge( $links, $new_links );
  }
	return $links;
}
function sbb_bill_ask_for_upgrade()
 { 
    $x = rand(0,4);
    if ($x == 0)
    {
       $banner_image = STOPBADBOTSIMAGES.'/introductory.png';
       $bill_banner_bkg_color = 'turquoise';
       $banner_txt = __( 'Go Pro: Your bad bots table is always updated.', 'stopbadbots'); 
    }
    elseif ($x == 1)
    {
       $banner_image = STOPBADBOTSIMAGES.'/lion.jpg';  
       $bill_banner_bkg_color = 'turquoise';
       $banner_txt = __( 'Power for block bad bots: Your bad bots table is always updated.', 'stopbadbots'); 
    }
       elseif ($x == 2)
     {
       $banner_image = STOPBADBOTSIMAGES.'/apple.jpg';
       $bill_banner_bkg_color = 'orange';
       $banner_txt = __( 'Get Premium Performance: Stop Bad Bots and save bandwidth.', 'stopbadbots'); 
    } 
       elseif ($x == 3)
    {
       $banner_image = STOPBADBOTSIMAGES.'/racing.jpg';
       $bill_banner_bkg_color = 'orange';
      $banner_txt = __( 'Become Pro: Your bad bots table is always updated.', 'stopbadbots'); 
    }     
    else
    {
       $banner_image = STOPBADBOTSIMAGES.'/keys_from_left.png';
       $bill_banner_bkg_color = 'orange';
       $banner_txt = __( 'Become Pro: Your bad bots table is always updated.', 'stopbadbots'); 
    }
   $banner_tit = __( 'It is time to upgrade your', 'stopbadbots'); 
    echo '<script type="text/javascript" src="' .STOPBADBOTSURL .
            'assets/js/c_o_o_k_i_e.js' . '"></script>';
    ?>
	<script type="text/javascript">
        jQuery(document).ready(function() {
        	var hide_message = jQuery.cookie('sbb_bill_go_pro_hide');
        /*	 hide_message = false; */
        	if (hide_message == "true") {
        		jQuery(".sbb_bill_go_pro_container").css("display", "none");
        	} else {
                   setTimeout( function(){ 
                   //  jQuery(".bill_go_pro_container").slideDown("slow");
          		   jQuery(".sbb_bill_go_pro_container").css("display", "block");
                  }  , 2000 );
        	};
        	jQuery(".sbb_bill_go_pro_close_icon").click(function() {
        		jQuery(".sbb_bill_go_pro_message").css("display", "none");
        		jQuery.cookie("sbb_bill_go_pro_hide", "true", {
        			expires: 15
        		});
        		jQuery(".sbb_bill_go_pro_container").css("display", "none");
        	});
        	jQuery(".sbb_bill_go_pro_dismiss").click(function(event) {
        		jQuery(".sbb_bill_go_pro_message").css("display", "none");
        		jQuery.cookie("sbb_bill_go_pro_hide", "true", {
        			expires: 15
        		});
        		event.preventDefault()
        		jQuery(".sbb_bill_go_pro_container").css("display", "none");
        	});
        }); // end (jQuery);
	</script>
    <style type="text/css">
            .sbb_bill_go_pro_close_icon {
            width:31px;
            height:31px;
            border: 0px solid red;
            /* background: url("http://xxxxxx.com/wp-content/plugins/cardealer/assets/images/close_banner.png") no-repeat center center; */
            box-shadow:none;
            float:right;
            margin:8px;
            margin:60px 40px 8px 8px;
            }
            .sbb_bill_hide_settings_notice:hover,.sbb_bill_hide_premium_options:hover {
            cursor:pointer;
            }
            .sbb_bill_hide_premium_options {
            position:relative;
            }
            .sbb_bill_go_pro_image {
            float:left;
            margin-right:20px;
            max-height:90px !important;
            }
            .sbb_bill_image_go_pro {
            max-width:200px;
            max-height:88px;
            }
            .sbb_bill_go_pro_text {
            font-size:18px;
            padding:10px;
            margin-bottom: 5px;
            }
            .sbb_bill_go_pro_button_primary_container {
            float:left;
            margin-top: 0px;
            }
            .sbb_bill_go_pro_dismiss_container
            {
              margin-top: 0px;
            }
            .sbb_bill_go_pro_buttons {
              display: flex;
              max-height: 30px;
              margin-top: -10px;
            }        
            .sbb_bill_go_pro_container {
                border:1px solid darkgray;
                height:88px;
                padding: 0; 
                margin: 0; 
                  background: <?php echo $bill_banner_bkg_color; ?>
            }
            .sbb_bill_go_pro_dismiss {
              margin-left:15px !important;
            }
             .button {
                vertical-align: top;
            }           
            @media screen and (max-width:900px) {
                .sbb_bill_go_pro_text {
                  font-size:16px;
                  padding:5px;
                  margin-bottom: 10px;
                }
            }
            @media screen and (max-width:800px) {
                .sbb_bill_go_pro_container {
                  display:none !important;
                }
            }
	</style>
    <div class="notice notice-success sbb_bill_go_pro_container" style="display: none;">
    	<div class="sbb_bill_go_pro_message sbb_bill_banner_on_plugin_page sbb_bill_go_pro_banner">
    		<div class="sbb_bill_go_pro_image">
    			<img class="sbb_bill_image_go_pro" title="" src="<?php echo $banner_image;?>" alt="" />
    		</div>
    		<div class="sbb_bill_go_pro_text">
			  	      <?php echo $banner_tit;?>
    				<strong>
    					Weekly Updates!
    				</strong>
    						<br />
    						<span>
 						  	    <?php echo $banner_txt;?>
    						</span>
    		</div>
            <div class="sbb_bill_go_pro_buttons">
        		<div class="sbb_bill_go_pro_button_primary_container">
        			<a class="button button-primary" target="_blank" href="http://stopbadbots.com/premium/"><?php _e('Learn More',
        			'stopbadbots'); ?></a>
        		</div>
        		<div class="sbb_bill_go_pro_dismiss_container">
        			<a class="button button-secondary sbb_bill_go_pro_dismiss" target="_blank" href="http://stopbadbots.com/premium/"><?php _e('Dismiss',
        			'stopbadbots'); ?></a>
        		</div>
            </div>
    	</div>
    </div>
<?php               
 } // end Bill ask for upgrade 
 $when_installed = get_option('bill_installed');
 $now = time();
 $delta = $now - $when_installed;
 if ($delta > (3600 * 24 * 8))
 {
    $stopbadbotsurl = esc_url($_SERVER['REQUEST_URI']);
    $stopbadbots_checkversion = trim(get_option('stopbadbots_checkversion', ''));
    if (strpos($stopbadbotsurl, 'sbb_') !== false and empty($stopbadbots_checkversion))
       if (strpos($stopbadbotsurl, 'settings') === false)
          add_action( 'admin_notices', 'sbb_bill_ask_for_upgrade' );
 }
function sbb_control_availablememory()
{
    $sbb_memory = sbb_check_memory();
    if ( $sbb_memory['msg_type'] == 'notok')
       return;
     if ($sbb_memory['percent'] > .7) 
      add_action( 'admin_notices', 'sbb_message_low_memory' ); 
}     
add_action( 'wp_loaded', 'sbb_control_availablememory' );
?>