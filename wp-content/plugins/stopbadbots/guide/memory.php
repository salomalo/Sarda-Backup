<?php /**
 * @author William Sergio Minossi
 * @copyright 2017
 */
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
$sbb_memory = sbb_check_memory();
ob_start();
echo '<div id="sbb-memory-page">';
echo '<div class="sbb-block-title">';
    if ( $sbb_memory['msg_type'] == 'notok')
       {
        echo 'Unable to get your Memory Info';
        echo '</div>';
    }
    else
    {
        echo 'Memory Info';
        echo '</div>';
        echo '<div id="memory-tab">';
        echo '<br />';
        if($sbb_memory['msg_type']  == 'ok')
         $mb = 'MB';
        else
         $mb = '';
        echo 'Current memory WordPress Limit: ' . $sbb_memory['wp_limit'] . $mb .
            '&nbsp;&nbsp;&nbsp;  |&nbsp;&nbsp;&nbsp;';
        $perc = $sbb_memory['usage'] / $sbb_memory['wp_limit'];
        if ($perc > .7)
           echo '<span style="'.$sbb_memory['color'].';">';
        echo 'Your usage now: ' . $sbb_memory['usage'] .
            'MB &nbsp;&nbsp;&nbsp;';
        if ($perc > .7)
           echo '</span>';    
        echo '|&nbsp;&nbsp;&nbsp;   Total Server Memory: ' . $sbb_memory['limit'] .
            'MB';
        // echo 'Current memory WordPress Limit: '.$sbb_memory['wp_limit'].$mb.'&nbsp;&nbsp;&nbsp;  |&nbsp;&nbsp;&nbsp;   Your usage: '.$sbb_memory['usage'].'MB of '.$sbb_memory['limit'];
           echo '<br />';    
           echo '<br />'; 
           echo '<br />';
        ?>  
           </strong>
        <!-- <div id="memory-tab"> -->
            <br />
            To increase the WordPress memory limit, add this info to your file wp-config.php (located at root folder of your server)
            <br />
            (just copy and paste)
            <br />    <br />
        <strong>    
        define('WP_MEMORY_LIMIT', '128M');
        </strong>
            <br />    <br />
            before this row:
            <br />
            /* That's all, stop editing! Happy blogging. */
            <br />
            <br />
            If you need more, just replace 128 with the new memory limit.
            <br /> 
            To increase your total server memory, talk with your hosting company.
            <br />   <br />
            <hr />
            <br />    
        <strong>    How to Tell if Your Site Needs a Shot of more Memory:</strong>
                <br />    <br />
            If your site is behaving slowly, or pages fail to load, you 
            get random white screens of death or 500 
            internal server error you may need more memory. 
        Several things consume memory, such as WordPress itself, the plugins installed, the 
        theme you're using and the site content.
             <br />  
        Basically, the more content and features you add to your site, 
        the bigger your memory limit has to be.
        if you're only running a small 
        site with basic functions without a Page Builder and Theme 
        Options (for example the native Twenty Sixteen). However, once 
        you use a Premium WordPress theme and you start encountering 
        unexpected issues, it may be time to adjust your memory limit 
        to meet the standards for a modern WordPress installation.
             <br /> <br />    
            Increase the WP Memory Limit is a standard practice in 
        WordPress and you find instructions also in the official 
        WordPress documentation (Increasing memory allocated to PHP).
            <br /><br />
        Here is the link:    
        <br />
        <a href="https://codex.wordpress.org/Editing_wp-config.php" target="_blank">https://codex.wordpress.org/Editing_wp-config.php</a>
        <br /><br />
        </div>
        </div>
        <?php
}
$sbb_memory_msg = ob_get_clean();

function sbb_message_low_memory()
{
    echo '<div class="notice notice-warning">
                     <br />
                     <b>
                     Stop Bad Bots Plugin Warning: Your server running Low Memory !
                     <br />
                     Please, check 
                     <br />
                     Dashboard => Stop Bad Bots => (tab) Memory Checkup
                     <br /><br />
                     </b>
                     </div>';
}
Function sbb_check_memory()
{
      global $sbb_memory;
      $sbb_memory['limit'] = (int) ini_get('memory_limit') ;	
      $sbb_memory['usage'] = function_exists('memory_get_usage') ? round(memory_get_usage() / 1024 / 1024, 0) : 0;
      if(!defined("WP_MEMORY_LIMIT"))
      {
        $sbb_memory['msg_type'] = 'notok';  
        return;
      }
      $sbb_memory['wp_limit'] =  trim(WP_MEMORY_LIMIT) ;
    if ($sbb_memory['wp_limit'] > 9999999)
        $sbb_memory['wp_limit'] = ($sbb_memory['wp_limit'] / 1024) / 1024;
    if (!is_numeric($sbb_memory['usage'])) {
        $sbb_memory['msg_type'] = 'notok';  
        return;
    }
    if (!is_numeric($sbb_memory['limit'])) {
        $sbb_memory['msg_type'] = 'notok';  
        return;
    }
    if ($sbb_memory['usage'] < 1) {
        $sbb_memory['msg_type'] = 'notok';  
        return;
    }
  $wplimit = $sbb_memory['wp_limit'];  
  $wplimit = substr($wplimit,0,strlen($wplimit)-1);
  $sbb_memory['wp_limit'] = $wplimit;
  $sbb_memory['percent'] = $sbb_memory['usage'] / $sbb_memory['wp_limit'];
  $sbb_memory['color'] = 'font-weight:normal;';
  if ($sbb_memory['percent'] > .7) $sbb_memory['color'] = 'font-weight:bold;color:#E66F00';
  if ($sbb_memory['percent'] > .85) $sbb_memory['color'] = 'font-weight:bold;color:red';
  $sbb_memory['msg_type'] = 'ok';  
  return $sbb_memory;
}