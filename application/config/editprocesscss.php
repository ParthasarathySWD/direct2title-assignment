<?php
	
$config['process'] = [


	"CSS" => [
				1 => "div#document-merge .btn:not(.editable):not(.btn-xs):not(#btnStartProcess):not(#btnEndProcess):not(#btnEditOrder):not(#btnCloseOrder):not(.fileview){
							display:none;
						}
						table.custom_vendor_table .btnfileadd-orderform, table.custom_vendor_table .abs_cancel{
							display:none;
						}
						",
				2 => "div#assessment_container .btn:not(.editable):not(.btn-xs):not(#btnStartProcess):not(#btnEndProcess):not(#btnEditOrder):not(#btnCloseOrder){
							display:none;
						}",
				3 => "div#document_deed .btn:not(.editable):not(.btn-xs):not(#btnStartProcess):not(#btnEndProcess):not(#btnEditOrder):not(#btnCloseOrder){
							display:none;
						}",
				4 => "div#document_tax .btn:not(.editable):not(.btn-xs):not(#btnStartProcess):not(#btnEndProcess):not(#btnEditOrder):not(#btnCloseOrder){
							display:none;
						}",
				5 => "div#legal_description_container .btn:not(.editable):not(.btn-xs):not(#btnStartProcess):not(#btnEndProcess):not(#btnEditOrder):not(#btnCloseOrder){
							display:none;
						}",
				6 => "div#address_container .btn:not(.editable):not(.btn-xs):not(#btnStartProcess):not(#btnEndProcess):not(#btnEditOrder):not(#btnCloseOrder){
							display: none;
						}",
				7 => "div#document_mortgage .btn:not(.editable):not(.btn-xs):not(#btnStartProcess):not(#btnEndProcess):not(#btnEditOrder):not(#btnCloseOrder){
							display: none;
						}",
				8 => "div#document_property_info .btn:not(.editable):not(.btn-xs):not(#btnStartProcess):not(#btnEndProcess):not(#btnEditOrder):not(#btnCloseOrder){
							display: none;
						}",
				9 => "div#document_tax .btn:not(.editable):not(.btn-xs):not(#btnStartProcess):not(#btnEndProcess):not(#btnEditOrder):not(#btnCloseOrder){
							display:none;
						}",
				10 => "div#document_tax .btn:not(.editable):not(.btn-xs):not(#btnStartProcess):not(#btnEndProcess):not(#btnEditOrder):not(#btnCloseOrder){
							display:none;
						}",
				11 => "div#order_info_container .btn:not(.editable):not(.btn-xs):not(#btnStartProcess):not(#btnEndProcess):not(#btnEditOrder):not(#btnCloseOrder){
							display:none;
						}",
				12 => "div#document_received_container .btn:not(.editable):not(.collapse-open):not(.btnIssueLogs):not(#btnStartProcess):not(#btnEndProcess):not(#btnEditOrder):not(#btnCloseOrder){
							display:none;
						}
						select.ddlDocumentStatus{
							disabled: 
						}
						.dimmed {position: relative;} 
						.dimmed:after {content: ' ';z-index: 10;display: block;position: absolute;height: 100%;top: 0;left: 0;right: 0;background: rgba(0, 0, 0, 0.5);}
						",
				13 => "div#shedule .btn:not(.editable):not(.btn-xs):not(#btnStartProcess):not(#btnEndProcess):not(#btnEditOrder):not(#btnCloseOrder){
							display:none;
						}
						div#workflowbutton_container .btn:not(.editable):not(.btn-xs):not(#btnStartProcess):not(#btnEndProcess):not(#btnEditOrder):not(#btnCloseOrder){
							display:none;
						}
						.dimmed {position: relative;} 
						.dimmed:after {content: ' ';z-index: 10;display: block;position: absolute;height: 100%;top: 0;left: 0;right: 0;background: rgba(0, 0, 0, 0.5);}
						",
				14 => "div#shedule .btn:not(.editable):not(.btn-xs):not(#btnStartProcess):not(#btnEndProcess):not(#btnEditOrder):not(#btnCloseOrder){
							display:none;
						}
						.dimmed {position: relative;} 
						.dimmed:after {content: ' ';z-index: 10;display: block;position: absolute;height: 100%;top: 0;left: 0;right: 0;background: rgba(0, 0, 0, 0.5);}
						",
				15 => "div#shipping .btn:not(.editable):not(.btn-xs):not(#btnStartProcess):not(#btnEndProcess):not(#btnEditOrder):not(#btnCloseOrder){
							display:none;
						}",
				16 => "div#document_deed .btn:not(.editable):not(.btn-xs):not(#btnStartProcess):not(#btnEndProcess):not(#btnEditOrder):not(#btnCloseOrder){
							display:none;
						}",
				17 => "div#document_mortgage .btn:not(.editable):not(.btn-xs):not(#btnStartProcess):not(#btnEndProcess):not(#btnEditOrder):not(#btnCloseOrder){
							display:none;
						}",
				18 => "div#document_tax .btn:not(.editable):not(.btn-xs):not(#btnStartProcess):not(#btnEndProcess):not(#btnEditOrder):not(#btnCloseOrder){
							display:none;
						}",
				19 => "div#document_tax .btn:not(.editable):not(.btn-xs):not(#btnStartProcess):not(#btnEndProcess):not(#btnEditOrder):not(#btnCloseOrder){
							display:none;
						}",
				20 => "div#document_tax .btn:not(.editable):not(.btn-xs):not(#btnStartProcess):not(#btnEndProcess):not(#btnEditOrder):not(#btnCloseOrder){
							display:none;
						}
						div.TaxAuthority_details .btn:not(.editable):not(.btn-xs):not(#btnStartProcess):not(#btnEndProcess):not(#btnEditOrder):not(#btnCloseOrder){
							display:none;
						}",

				"Mischellaneous" => "",
				"order_summary" => "div.Main_Page .btn:not(.editable):not(.btn-xs):not(#btnStartProcess):not(#btnEndProcess):not(#btnEditOrder):not(#btnCloseOrder){
							display:none;
						}
						div#Copy_Page .btn:not(.editable):not(.btn-xs):not(#btnStartProcess):not(#btnEndProcess):not(#btnEditOrder):not(#btnCloseOrder){
							display:none;
						}
						div#fee-section .btn:not(.editable):not(.btn-xs):not(#btnStartProcess):not(#btnEndProcess):not(#btnEditOrder):not(#btnCloseOrder){
							display:none;
						}",
				"task" => "",
				"issue_management" => "",
				"orderchecklistitems" => "",
				"reports" => "",
				"notes" => "",
				"attachments" => "",
				"delivery" => "",

			   ], 
	"SCRIPT" => [
				1 => "$('#upload_file').prop('disabled', true); 
					  $('#upload-preview-table select, #upload-preview-table input[type=checkbox]').prop('disabled', true);
					 ",
				2 => "$('#assessment_container input, #assessment_container textarea').prop('disabled', true); ",
				3 => "$('#document_deed input, #document_deed textarea, #document_deed select').prop('disabled', true); 
				",
				4 => "$('#document_tax input, #document_tax textarea, #document_tax select').prop('disabled', true); ",
				5 => "$('#legal_description_container input, #legal_description_container textarea, #legal_description_container select').prop('disabled', true); ",
				6 => "$('#address_container input, #address_container textarea, #address_container select').prop('disabled', true); ",
				7 => "$('#document_mortgage input, #document_mortgage textarea, #document_mortgage select, #document_mortgage button, #document_mortgage i').prop('disabled', true); ",
				8 => "$('#document_property_info input, #document_property_info textarea, #document_property_info select, #document_property_info button, #document_property_info i').prop('disabled', true); ",
				9 => "$('#document_tax input, #document_tax textarea, #document_tax select').prop('disabled', true); ",
				10 => "$('#document_tax input, #document_tax textarea, #document_tax select').prop('disabled', true); ",
				11 => "$('#order_info_container input, #order_info_container textarea, #order_info_container select, #order_info_container button, #order_info_container i').prop('disabled', true); ",
				12 => "$('select#ddlDocumentStatus, select.DocumentReview, select.Impediment, input[name=IssueDetails], input[name=NotesIssue]').prop('disabled', true);
						",
				13 => "$('#a').prop('disabled', true); 
					  $('#a select, #a input[type=checkbox],#a input, #a button, #a i').prop('disabled', true);
					  $('.queue_dropdown_listui li').prop('disabled', true);$('.queue_dropdown_listui .QueueBtn').prop('disabled', true);
					  ",
				14 => "$('#shedule input, #shedule button, #shedule textarea, #shedule span').prop('disabled', true); $('#shedule #btnStartProcess').prop('disabled', false);
					  $('.queue_dropdown_listui li').prop('disabled', true);$('.queue_dropdown_listui .QueueBtn').prop('disabled', true);",
				15 => "$('#shipping button').prop('disabled', true); $('#shipping #btnStartProcess').prop('disabled', false); ",
				16 => "$('#document_deed input, #document_deed textarea, #document_deed select').prop('disabled', true); ",
				17 => "$('#document_mortgage input, #document_mortgage textarea, #document_mortgage select, #document_mortgage button, #document_mortgage i').prop('disabled', true); ",
				18 => "$('#document_tax input, #document_tax textarea, #document_tax select').prop('disabled', true); ",
				19 => "$('#document_tax input, #document_tax textarea, #document_tax select').prop('disabled', true); ",
				20 => "$('#document_tax input, #document_tax textarea, #document_tax select').prop('disabled', true); ",
				"Mischellaneous" => "$('table#top-fix-menu_table select').prop('disabled', true);",

				"order_summary" => "
						$('div.Main_Page input, div.Main_Page textarea, div.Main_Page select, div.Main_Page input[type=\"checkbox\"], div.Main_Page input[type=\"radio\"]').prop('disabled', true); 
						$('div#Copy_Page input, div#Copy_Page textarea, div#Copy_Page select, div#Copy_Page input[type=\"checkbox\"], div#Copy_Page input[type=\"radio\"]').prop('disabled', true); 
						$('div#fee-section input, div#fee-section textarea, div#fee-section select, div#fee-section input[type=\"checkbox\"], div#fee-section input[type=\"radio\"]').prop('disabled', true); 
						$('.PRSocialNumber').prop('disabled', false);
						",
				"task" => "",
				"issue_management" => "",
				"orderchecklistitems" => "",
				"notes" => "",

				"attachments" => "",
				"delivery" => "",
			 ], 
];

?>
