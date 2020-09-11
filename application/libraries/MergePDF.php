<?php  
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class MergePDF
{
    public function __construct()
    {
        require_once APPPATH.'third_party/pdf_to_image.php';
    }

   	function merge($files, $outputfile)
   	{

   		$outputfile = escapeshellarg($outputfile);

		// check current OS
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {

			$command = FCPATH.'assets/gs/win/bin/gs.exe -q -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -sOutputFile='.$outputfile.' ';
		}else{

			$command = 'gs -q -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -sOutputFile='.$outputfile.' ';
		}
   		
		//Add each pdf file to the end of the command
		foreach($files as $file) {
			if (file_exists($file)) {

				$command .= escapeshellarg($file) . ' ';

			}
		}
		$result = exec($command);

		if (empty($result)) {
			// chmod($outputfile, 0777);
			return true;
			
		}
		else{
			if (stripos($result, "Output may be incorrect")) {
				echo "Completed with Warnings";
				return true;
			}
			return false;
		}

   	}

}

?>