<?php namespace StopBadBotsWPSettings;
$mypage = new Page('Stop Bad Bots', array('type' => 'menu'));
$settings = array();
require_once (STOPBADBOTSPATH. "guide/guide.php");
$settings['Startup Guide']['Startup Guide'] = array('info' => $ah_help );
$fields = array();   
$settings['Startup Guide']['Startup Guide']['fields'] = $fields;
$msg2 = '<b>'.__('Block all Bots included at Bad Bots Table?','stopbadbots').'</b>';
$msg2 .= '<br />';
$msg2 .= __('You need only check yes or no below. All Bad Bots enabled at Bad Bots Table will be blocked.','stopbadbots');
$msg2 .= '<br />';
$msg2 .= __('To manage the bots individually, go to Bad Bots Table (Dashboard=> Stop Bad Bots =>Bad Bots Table).','stopbadbots');
$msg2 .= '<br />'; 
$msg2 .= __('Then click SAVE CHANGES.','stopbadbots');
$msg2 .= '<br />'; 
$msg2 .= '<br />'; 

$msg2 .= '<b>'.__('Block all Bots included at Bad IPs Table?','stopbadbots').'</b>';
$msg2 .= '<br />';
$msg2 .= __('You need only check yes or no below. All Bad IPs enabled at Bad IPs Table will be blocked.','stopbadbots');
$msg2 .= '<br />';
$msg2 .= __('To manage the IPs individually, go to Bad IPs Table (Dashboard=> Stop Bad Bots =>Bad IPs Table).','stopbadbots');
$msg2 .= '<br />'; 
$msg2 .= __('Then click SAVE CHANGES.','stopbadbots');




$msg2 .= '<br />'; 
$msg2 .= '<br />'; 

$msg2 .= '<b>'.__('Participate in the Real-Time Bad Bots Security Network?','stopbadbots').'</b>';
$msg2 .= '<br />';
$msg2 .= __('Enabling this feature causes your site to anonymously share data with Stop Bad Bots on Bad Bots visits. In return your WordPress site receives updates at your Bad Bots Table with new Bad Bots Names.','stopbadbots');
$msg2 .= '<br />'; 
$msg2 .= __('No personally identifiable data is sent by this option and we also do not associate any of the data we do receive with your specific website. The data is aggregated on a real-time platform to determine which Bots are currently engaged in negative activity and need to be blocked by our community.','stopbadbots');
$settings['General Settings'][__('Instructions')] = array('info' => $msg2);


$fields = array();
$fields[] = array(
	'type' 	=> 'radio',
	'name' 	=> 'stop_bad_bots_active',
	'label' => __('Block all Bots included at Bad Bots Table?'),
	'radio_options' => array(
		array('value'=>'yes', 'label' => __('yes')),
		array('value'=>'no', 'label' => __('no'))
		)			
	); 
    

$fields[] = array(
	'type' 	=> 'radio',
	'name' 	=> 'stop_bad_bots_ip_active',
	'label' => __('Block all IPs included at Bad IPs Table?'),
	'radio_options' => array(
		array('value'=>'yes', 'label' => __('yes')),
		array('value'=>'no', 'label' => __('no'))
		)			
	); 
       
    
$fields[] = array(
	'type' 	=> 'radio',
	'name' 	=> 'stop_bad_bots_network',
	'label' => __('Participate in the Real-Time Bad Bots Security Network (to receive bot\'s table updates)?'),
	'radio_options' => array(
		array('value'=>'yes', 'label' => __('yes')),
		array('value'=>'no', 'label' => __('no'))
		)			
	);
$settings['General Settings']['']['fields'] = $fields;
/*
$msg2 = '<p style="font-family:arial; font-size:16px;">';
$msg2 .= __('In addiction to default system table, you can add one or more strings to the default table.','stopbadbots');
$msg2 .= '<br />'; 
$msg2 .= __('Example: SpiderBot (no case sensitive)','stopbadbots');
$msg2 .= '<br />';
$msg2 .= __('Just a piece of the name is enough. For example, if you put bot will block all bots with the string bot at user agent name.','stopbadbots');
$msg2 .= '<br />'; 
$msg2 .= __('Attention: In this case, you will block also google bot because their name is GoogleBot','stopbadbots');
$msg2 .= '<br />'; 
$msg2 .= '<b>'; 
$msg2 .= __('Do not use special characters.','stopbadbots');
$msg2 .= '</b>'; 
$msg2 .= '<br />'; 
$msg2 .= __('Just Click Add Bad Bot to Table under Stop Bad Bots voice at your dashboard.','stopbadbots');
$msg2 .= '<br />'; 
$msg2 .= '<br />'; 
$msg2 .= __('The bots into the My Default Table was merged with default bad bots table in version 2.0. Now it is easier to you manage them and read their statistics.','stopbadbots');
$msg2 .= '</p>';
$settings['My Black List'][__('Add Bots to the Table')] = array('info' => $msg2);
$fields = array();   
$settings['My Black List']['']['fields'] = $fields;
*/
$sbb_admin_email = get_option( 'admin_email' ); 
$msg_email = __('Fill out the email address to send messages.','stopbadbots');
$msg_email .= '<br />';
$msg_email .= __('Left Blank to use your default WordPress email. Then, click save changes.','stopbadbots');
$settings['Email']['email'] = array('info' => $msg_email );
$fields = array();
$fields[] = array(
	'type' 	=> 'text',
	'name' 	=> 'stopbadbots_my_email_to',
	'label' => 'email'
	);
$settings['Email']['email']['fields'] = $fields;
//$admin_email = get_option( 'admin_email' ); 
$notificatin_msg = __('Do you want receive email alerts for each bot attempt?','stopbadbots');
$notificatin_msg .= '<br />'; 
$notificatin_msg .= __('If you under brute force attack, you will receive a lot of emails.','stopbadbots');
$notificatin_msg .= '<br />'; 
$notificatin_msg .= __('You can see the bots attacks info at Bad Bots Table. (column Num Blocked).','stopbadbots');
$settings['Notifications'][__('Notifications')] = array('info' => $notificatin_msg );
$fields = array();
$fields[] = array(
	'type' 	=> 'radio',
	'name' 	=> 'stopbadbots_my_radio_report_all_visits',
	'label' => __('Alert me by email each Bots Attempts'),
	'radio_options' => array(
		array('value'=>'Yes', 'label' => 'Yes.'),
		array('value'=>'No', 'label' => 'No.'),
		)			
	);    
$settings['Notifications'][__('Notifications')]['fields'] = $fields;
require_once (STOPBADBOTSPATH. "guide/memory.php");
$settings['Memory Checkup'][__('Memory Checkup')] = array('info' => $sbb_memory_msg );
$fields = array();   
$settings['Memory Checkup'][__('Memory Checkup')]['fields'] = $fields;
//
$gopro = '<span style="font-size: 24pt; color: #CC3300;">Premium Features<font color="#000000">';
$gopro .= '<br />';
$gopro .= '<font size="4">';
$gopro .=  __('Get Premium and receive, automatically, weekly updates.', 'stopbadbots');
$gopro .= '<br />';
$gopro .= __('Help us to keep the bot database updated.', 'stopbadbots');
$gopro .= '<br />';
$gopro .= __('Visit our Premium Page for more details.', 'stopbadbots');
$gopro .= '<br />';
$gopro .= '<a href="http://stopbadbots.com/premium" class="button button-primary">'.__("Premium Page","stopbadbots").'</a>';
$gopro .= '<br />';
$gopro .= '<br />';
$gopro .=  __('Paste below the Item Purchase Code received by email from us when you bought the premium version.', 'stopbadbots');
// Form
$settings['Go Pro']['Go Pro'] = array('info' => $gopro );
// $fields = array();
$fields[] = array(
	'type' 	=> 'text',
	'name' 	=> 'stopbadbots_checkversion',
	'label' => 'Purchase Code:',
	);   
$settings['Go Pro']['Go Pro']['fields'] = $fields;
new OptionPageBuilderTabbed($mypage, $settings);