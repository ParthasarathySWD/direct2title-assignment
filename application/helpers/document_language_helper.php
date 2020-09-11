<?php 


/**
   * Document_language_helper
   *
   * @package    CI
   * @subpackage Helper
   * @author     Parthasarathy <parthasarathy.m@avanzegroup.com>
   */


function generateOutputPDF($html)
{
  	$CI =& get_instance();
	$CI->load->library('pdf');
	$param = '"en-GB-x","A4","","",10,10,10,10,6,3';
	$pdf = $CI->pdf->load($param);
	$pdf->packTableData = true;
	$pdf->shrink_tables_to_fit = 1;
	$html = mb_convert_encoding($html, 'UTF-8', 'UTF-8');
	$pdf->WriteHTML($html); /* write the HTML into the PDF*/
	$page_count = $pdf->page;
	$doc_save = time() . '.pdf';
	$pdfView = $pdf->Output($doc_save, '');
	echo base64_encode(file_get_contents($doc_save));
	unlink($doc_save);

}

?>
