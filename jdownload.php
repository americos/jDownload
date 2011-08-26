<?php
	// initialise functions
	if($_GET['action'] && $_GET['path']) {
		
		// Append full document root to provided file path
		$file_path = $_SERVER['DOCUMENT_ROOT'].$_GET['path'];
		
		if(!file_exists($file_path) || !is_file($file_path)) {
			
			echo "error";
			// Quit
			
			
		} else {
		
			// fetch Mime type here so avialable to both functions
			$mime_types=array(
				"pdf" => "application/pdf",
				"txt" => "text/plain",
				"html" => "text/html",
				"htm" => "text/html",
				"zip" => "application/zip",
				"doc" => "application/msword",
				"xls" => "application/vnd.ms-excel",
				"ppt" => "application/vnd.ms-powerpoint",
				"gif" => "image/gif",
				"png" => "image/png",
				"jpeg"=> "image/jpg",
				"jpg" =>  "image/jpg",
			);
			
			// Get the extension of the file
			$ext = substr($file_path, strrpos($file_path, '.') + 1);
			
			if(!array_key_exists($ext, $mime_types)){
				echo json_encode(array('error'=>'denied'));
				die;
			}
			
			// Remove null bytes
			$file_path = str_replace("\0", "", $file_path);
			$file_path = str_replace("%00", "", $file_path);
			
			// call appropriate function
			switch($_GET['action']) {
				case "download":
					get_file($file_path);
				break;
				
				case "info":
					get_info($file_path);
				break;
			
				default:
					echo 'error';
				break;
			}
			
		}
	}
	
	// This function checks for the file, checks to see if can be opened and then forces the file to the browser
	function get_file($file_path){
				
		global $mime_types, $ext;		
				
		// Check if mimetypes exists in our list
		$content_type = array_key_exists($ext, $mime_types) ? $mime_types[$ext] : "application/force-download";
			
		// Turn off gzip for IE browsers
		if(ini_get('zlib.output_compression')){
		 	ini_set('zlib.output_compression', 'Off');
		}
	  				
		// Set headers to force file download
		header("Pragma:  public");
		header("Expires:  0");
		header("Cache-Control:  must-revalidate, pre-check=0");
		header("Content-Disposition:  attachment; filename=".basename($file_path)."");
		header("Content-Type: ".$content_type);
	 	header("Content-Transfer-Encoding: binary");
	 	header("Content-Length:  ". filesize($file_path));
	 			
	 	// Discards the contents of the output buffer
	 	ob_clean();
		flush();
    			
		// Read the file
		readfile($file_path);
    			
   		// Exit
    	exit;			

	}
	
	function get_info($file_path) {
		
		global $mime_types, $ext;		
		
		// get file info
		$filename = basename($file_path);
		$filetype = array_key_exists($ext, $mime_types) ? $mime_types[$ext] : "Unknown";
		$filesize = round(filesize($file_path) / 1024); // file size in KB
		
		$data = array(
			"filename" => $filename,
			"filetype" => $filetype,
			"filesize" => $filesize,
 		);
			
		echo json_encode($data);
	
	}	
?>
