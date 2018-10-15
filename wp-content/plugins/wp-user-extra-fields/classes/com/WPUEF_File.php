<?php 
class WPUEF_File
{
	public function __construct()
	{
	}
	public function generate_unique_file_name($dir, $name, $ext = "")
	{
		return rand(0,1000000)."_".$name.$ext;
	}
	public function delete_all_user_files($user_id)
	{
		$upload_dir = wp_upload_dir();
		try{
				//@unlink($upload_dir['basedir'].'/wpuef/'.$user_id);
				$this->recursiveRemoveDirectory($upload_dir['basedir'].'/wpuef/'.$user_id);

			}catch(Exception $e){};
	}
	private function recursiveRemoveDirectory($directory)
	{
		foreach(glob("{$directory}/*") as $file)
		{
			if(is_dir($file))
				$this->recursiveRemoveDirectory($file);
			else 
				@unlink($file);
		}
		@rmdir($directory);
	}
	public function save_files($user_id, $files_data)
	{
		 $links_to_notify_via_mail = array();
		 $links_to_attach_to_mail = array();
		 $file_info = array();
		 foreach($files_data as $id => $file_data) //$files_data[{cid}]
		 {
			//decode data
			$upload_dir = wp_upload_dir();
			$upload_complete_dir = $upload_dir['basedir']. '/wpuef/'.$user_id.'/';
			if (!file_exists($upload_complete_dir)) 
					mkdir($upload_complete_dir, 0775, true);
				
			$unique_file_name = $this->generate_unique_file_name(null, $_POST['wpuef_files'][$id]['file_name']);
			$ifp = fopen($upload_complete_dir.$unique_file_name, "w"); 
			fwrite($ifp, base64_decode($file_data)); 
			fclose($ifp); 
		
			if( !file_exists ($upload_dir['basedir'].'/wpuef/index.html'))
				touch ($upload_dir['basedir'].'/wpuef/index.html');
				
			
			if( !file_exists ($upload_dir['basedir'].'/wpuef/'.$user_id.'/index.html'))
				touch ($upload_dir['basedir'].'/wpuef/'.$user_id.'/index.html');
			
			$file_info[$id]['absolute_path'] = $upload_complete_dir.$unique_file_name;
			$file_info[$id]['url'] = $upload_dir['baseurl'].'/wpuef/'.$user_id.'/'.$unique_file_name;
			$file_info[$id]['id'] = $id;
			
			//$options passed via parameter
			/* foreach($options as $option)
			{
				if($option['id'] == $id && $option['notify_admin'] )
					array_push($links_to_notify_via_mail, array('title' => $file_info[$id]['title'], 'url'=> $file_info[$id]['url']));
				if($option['id'] == $id && $option['notify_admin'] && $option['notify_attach_to_admin_email'])
					array_push($links_to_attach_to_mail, $file_info[$id]['absolute_path'] );
			} */
				 
			
		 }
		 //Notification via mail
		/* if(count($links_to_notify_via_mail) > 0)
		{
			$this->email_sender = new wpuef_Email();
			$this->email_sender->trigger($links_to_notify_via_mail, $order, $links_to_attach_to_mail );	
		} */
		return $file_info;
	}
	function delete_files($files_to_delete)
	{
		if(!isset($files_to_delete) || empty($files_to_delete))
			return;
		
		foreach($files_to_delete as $file_data)
		{
			try{
				@unlink($file_data['absolute_path']);
			}catch(Exception $e){};
		}
	}
}
?>