<?php
// REMEMBER TO CHECK IF BUBBLE IS ACTIVE THEN NO PRINT STYLE
$ns_custom_css = '';
$custom_tabs = get_meta_values( 'custom_tab_enabled' );

if( !empty( $custom_tabs ) ) {

	foreach( $custom_tabs as $bubble_id ){
			
			$bubble_font_size =  get_post_meta($bubble_id, 'custom_tab_font_size_ns', true);
			$bubble_color =  get_post_meta($bubble_id, 'custom_tab_color', true);
			$bubble_color_text =  get_post_meta($bubble_id, 'custom_tab_color_text', true);
			$bubble_enabled =  get_post_meta($bubble_id, 'custom_tab_enabled', true);

			$bubble_font_size2 =  get_post_meta($bubble_id, 'custom_tab_font_size_ns2', true);
			$bubble_color2 =  get_post_meta($bubble_id, 'custom_tab_color2', true);
			$bubble_color_text2 =  get_post_meta($bubble_id, 'custom_tab_color_text2', true);
			$bubble_enabled2 =  get_post_meta($bubble_id, 'custom_tab_enabled_two', true);
			$bubble_top_pos =  get_post_meta($bubble_id, 'custom_tab_top', true);
			$bubble_left_post =  get_post_meta($bubble_id, 'custom_tab_left', true);			
			$bubble_top2_pos =  get_post_meta($bubble_id, 'custom_tab_top2', true);
			$bubble_left2_post =  get_post_meta($bubble_id, 'custom_tab_left2', true);			


			if ($bubble_enabled == 'yes'){
				$ns_custom_css .= "\n";
				$ns_custom_css .= '#Bubble1PosNS'.$bubble_id.' {';
				$ns_custom_css .= "\n";
					if (!empty($bubble_top_pos)){ $ns_custom_css .= 'top: '.$bubble_top_pos.'px;'; } else {  $ns_custom_css .= 'top: 0px;'; }
					$ns_custom_css .= "\n";
					if (!empty($bubble_left_post)){ $ns_custom_css .= 'left: '.$bubble_left_post.'px;'; } else {  $ns_custom_css .= 'left: 0px;'; }
				$ns_custom_css .= "\n";
				$ns_custom_css .= '}';
				$ns_custom_css .= "\n";				
				$ns_custom_css .= '#textBubleNS'.$bubble_id.' {';
				$ns_custom_css .= "\n";
					if (!empty($bubble_font_size)){ $ns_custom_css .= 'font-size: '.$bubble_font_size.'px;'; $ns_custom_css .= "\n"; }
					if (!empty($bubble_color_text)){ $ns_custom_css .= 'color: '.$bubble_color_text.';'; }
				$ns_custom_css .= "\n";
				$ns_custom_css .= '}';

				$ns_custom_css .= "\n\n";

				$ns_custom_css .= '#colorNS'.$bubble_id.' {';
				$ns_custom_css .= "\n";
					if (!empty($bubble_color)){ $ns_custom_css .= 'background: '.$bubble_color.';'; } else {  $ns_custom_css .= 'background: #6baa01;'; }
				$ns_custom_css .= "\n";
				$ns_custom_css .= '}';

			}


			if ($bubble_enabled2 == 'yes'){
				$ns_custom_css .= "\n";
				$ns_custom_css .= '#BubblePosNS'.$bubble_id.' {';
				$ns_custom_css .= "\n";
					if (!empty($bubble_top2_pos)){ $ns_custom_css .= 'top: '.$bubble_top2_pos.'px;'; } else {  $ns_custom_css .= 'top: 70px;'; }
					$ns_custom_css .= "\n";
					if (!empty($bubble_left2_post)){ $ns_custom_css .= 'left: '.$bubble_left2_post.'px;'; } else {  $ns_custom_css .= 'left: 0px;'; }
				$ns_custom_css .= "\n";
				$ns_custom_css .= '}';
				$ns_custom_css .= "\n";
				$ns_custom_css .= '#text2BubleNS'.$bubble_id.' {';
				$ns_custom_css .= "\n";
					if (!empty($bubble_font_size2)){ $ns_custom_css .= 'font-size: '.$bubble_font_size2.'px;'; $ns_custom_css .= "\n"; }
					if (!empty($bubble_color_text2)){ $ns_custom_css .= 'color: '.$bubble_color_text2.';'; }
				$ns_custom_css .= "\n";
				$ns_custom_css .= '}';

				$ns_custom_css .= "\n\n";

				$ns_custom_css .= '#color2NS'.$bubble_id.' {';
				$ns_custom_css .= "\n";
					if (!empty($bubble_color2)){ $ns_custom_css .= 'background: '.$bubble_color2.';'; } else {  $ns_custom_css .= 'background: #6baa01;'; }
				$ns_custom_css .= "\n";
				$ns_custom_css .= '}';


			}
	}


}

// Write Custom CSS
$ns_file = BUBBLE_NS_PLUGIN_DIR .'/assets/css/ns-bubble-dynamic-style.css';
$myfile = fopen($ns_file, "w") or die("Unable to open file!");
$txt = $ns_custom_css;
fwrite($myfile, $txt);
fclose($myfile);


?>