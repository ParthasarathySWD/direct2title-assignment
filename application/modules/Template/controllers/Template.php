<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Template extends MY_Controller {

	public function __construct()
    {
        parent::__construct();
        $this->load->library('docx_reader');
        $this->load->model('template_model');
        $this->load->model('common_model');
        $this->load->library(array('form_validation'));
        $this->load->helper(array('form', 'url'));
        // if (($this->session->userdata('UserUID') == null) && (($this->session->userdata('RoleUID') == null))) {
        //     redirect(base_url().'users');
        // } else {
            $this->load->library('Excel');
            // $this->load->model('users/Mlogin');
            $this->loggedid = $this->session->userdata('UserUID');
            $this->RoleUID = $this->session->userdata('RoleUID');
            $this->UserName = $this->session->userdata('UserName');
            $this->lang->load('keywords');
        // }
    }

	public function index()
	{
		$data['content'] = 'index';
		$data['TemplateDetails']= $this->template_model->GetTemplateDetails();
        $data['PageSizeArray'] = array('A4'=>'A4','A3'=>'A3','Letter'=>'Letter','Legal'=>'Legal');
		$this->load->view('page', $data);
	}
	public function add_template()
	{
		// $data['content'] = 'add';
		// $this->load->view('page', $data);
		$data['content'] = 'add';
        $data['Action']="ADD";
        $data['ProductDetails']= $this->template_model->GetProductDetails();
        $data['PageSizeArray'] = array('A4'=>'A4','A3'=>'A3','Letter'=>'Letter','Legal'=>'Legal');
        $data['Fields'] = $this->common_model->getDocumentFields();
        $data['DocumentCategory'] = $this->common_model->get('mDocumentCategory', ['Active'=>1]);
        $data['DocumentTypes'] = $this->common_model->get('mdocumenttypes', ['Active'=>1]);
        $data['DocumentLanguages'] = $this->common_model->get('mDocumentLanguages', ['Active'=>1]);
        $data['LanguageGroup'] = $this->common_model->get('mLanguageGroups', ['Active'=>1]);
        $this->load->view('page', $data);
	}

	public function edit($TemplateUID)
    {

    	$post =$this->uri->segment(3);
    	$data['AuditlogDetails']=$this->template_model->getTemplateAuditsDetails($post);
        $data['content'] = 'add';
        $data['Action']="EDIT";
        $data['TemplateDetails']=$this->template_model->GetTemplateByID($TemplateUID);
        $data['TemplateUID']= $TemplateUID;
        $data['ProductDetails']= $this->template_model->GetProductDetails();
        $data['PageSizeArray'] = array('A4'=>'A4','A3'=>'A3','Letter'=>'Letter','Legal'=>'Legal');
        $data['Fields'] = $this->common_model->getDocumentFields();
        $data['DocumentCategory'] = $this->common_model->get('mDocumentCategory', ['Active'=>1]);
        $data['DocumentTypes'] = $this->common_model->get('mdocumenttypes', ['Active'=>1]);
        $data['DocumentLanguages'] = $this->common_model->get('mDocumentLanguages', ['Active'=>1]);
        $data['LanguageGroup'] = $this->common_model->get('mLanguageGroups', ['Active'=>1]);
        $data['FieldSection'] = $this->template_model->getTemplateFieldSectionDetails($TemplateUID);

        $data['Customers'] = $this->common_model->get('mcustomers', ['Active'=>1]);
        $data['States'] = $this->common_model->get('mstates', ['Active'=>1]);
        $data['TemplateClients'] = $this->template_model->_getTemplateClients($TemplateUID);
        $data['TemplateGeographic'] = $this->template_model->_getTemplateGeographic($TemplateUID);


        $this->load->view('page', $data);
    }


    public function getPDF()
    {
        // $this->load->model('document_language/template_model');
        $editorContent = $this->input->post('editorContent');
        $HeaderContent = $this->input->post('HeaderContent');
        $FooterContent = $this->input->post('FooterContent');

        $MarginTop = $this->input->post('MarginTop');
        $MarginBottom = $this->input->post('MarginBottom');
        $MarginLeft = $this->input->post('MarginLeft');
        $MarginRight = $this->input->post('MarginRight');
        $PageSize = $this->input->post('PageSize');


        $DocumentLanguage = $this->input->post('DocumentLanguage');
        $DocumentCategory = $this->input->post('DocumentCategory');
        $LanguageGroup = $this->input->post('LanguageGroup');

        $SavePreviewOrderNumber = $this->input->post('RandomString');

        $Multiplier = $this->input->post('Multiplier');
        if (empty($Multiplier)) {
            $Multiplier = 1;
        }

        $DocumentLanguage = $this->template_model->getLanguageDetails($DocumentLanguage);
        foreach ($DocumentLanguage as $key => $lang) {
            $CompleteLanguage = $this->template_model->getFullDocumentLanguage_Recursive($lang->DocumentLanguageUID);
            /*$Language = $lang->IsMultiType == 1 ? str_repeat($CompleteLanguage, $Multiplier) : $CompleteLanguage;*/
            $Language = $CompleteLanguage;
            $editorContent = str_replace("%%DocumentLanguage_" . $lang->LanguageName . "%%", $Language, $editorContent);
        }

        $DocumentCategory = $this->template_model->getdocumentcategories($DocumentCategory);
        foreach ($DocumentCategory as $key => $value) {
            if (empty($value)) {
                continue;
            }
            $Language = "";
            $DocumentCategoryLanguages = $this->template_model->getCategoryLanguageDetails([$value->DocumentCategoryUID]);
            foreach ($DocumentCategoryLanguages as $key => $lang) {
                $CompleteLanguage = $this->template_model->getFullDocumentLanguage_Recursive($lang->DocumentLanguageUID);
                $Language .= $lang->IsMultiType == 1 ? str_repeat($CompleteLanguage, $Multiplier) : $CompleteLanguage;
            }


            $editorContent = str_replace("%%DocumentCategory_" . $value->DocumentCategoryName . "%%", $Language, $editorContent);
        }

        $LanguageGroup = $this->template_model->getLanguageGroups($LanguageGroup);
        foreach ($LanguageGroup as $key => $value) {
            if (empty($value)) {
                continue;
            }
            $Language = "";
            $GroupLanguageDetails = $this->template_model->getLanguageGroupDetails($value->LanguageGroupUID);
            foreach ($GroupLanguageDetails as $key => $lang) {
                $CompleteLanguage = $this->template_model->getFullDocumentLanguage_Recursive($lang->DocumentLanguageUID);
                $Language .= $CompleteLanguage;

                /*$Language .= $lang->IsMultiType == 1 ? str_repeat($CompleteLanguage, $Multiplier) : $CompleteLanguage;*/
            }


            $editorContent = str_replace("%%LanguageGroup_" . $value->LanguageGroupName . "%%", $Language, $editorContent);
        }


        $HeaderContent = '<htmlpageheader name="MyCustomHeader"  style="display:none">' . $HeaderContent . '</htmlpageheader>';
        $FooterContent = '<htmlpagefooter name="MyCustomFooter"  style="display:none">' . $FooterContent . '</htmlpagefooter>';

        $filecontents.= "<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\"><html><head> <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" /><style>@page {header: html_MyCustomHeader;footer: html_MyCustomFooter;margin-top:".$MarginTop."%;margin-bottom:".$MarginBottom."%;margin-left:".$MarginLeft."%;margin-right:".$MarginRight."%;}</style></head></body> ";
        $filecontents .= $HeaderContent . $FooterContent . $editorContent . "</body></html>";
        // echo '<pre>';print_r($filecontents);exit;
        $this->load->library('pdf');
        $param = '"en-GB-x","'.$PageSize.'","","","'.$MarginLeft.'","'.$MarginRight.'","'.$MarginTop.'","'.$MarginBottom.'",6,3';
        unset($pdf);
        $pdf = $this->pdf->load($param);
        $pdf->packTableData = true;
        $pdf->shrink_tables_to_fit = 0;
        // $pdf->list_indent_first_level = 0;
        $html = mb_convert_encoding($filecontents, 'UTF-8', 'UTF-8');
        $pdf->WriteHTML($html); // write the HTML into the PDF
        $page_count = $pdf->page;
        $doc_save = $SavePreviewOrderNumber.'.pdf';
        $pdf->Output($doc_save, '');

        $dir = FCPATH.'Templates/Pdf/'.$SavePreviewOrderNumber.'.pdf';
        $urlpath = 'Templates/Pdf/'.$SavePreviewOrderNumber.'.pdf?r=' . strtotime("now");
        file_put_contents($dir, file_get_contents($doc_save));
        unlink($doc_save);

        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode(['status'=>'ok','message'=>"Success", "URL"=>$urlpath]))->_display();
        exit;
    }

    public function save_template()
    {
        $IsDynamicTemplate=($this->input->post('IsDynamicTemplate')) ? 1:0;

        $this->form_validation->set_rules('Code', '', 'required');
        $this->form_validation->set_rules('Name', '', 'required');
        /*$this->form_validation->set_rules('ProductUID', '', 'required');*/
        if ($IsDynamicTemplate == 1) {
            $this->form_validation->set_rules('PageSize', '', 'required');
            $this->form_validation->set_rules('MarginTop', '', 'required');
            $this->form_validation->set_rules('MarginBottom', '', 'required');
            $this->form_validation->set_rules('MarginLeft', '', 'required');
            $this->form_validation->set_rules('MarginRight', '', 'required');
        }
        $this->form_validation->set_message('required', 'This Field is required');
        if ($this->form_validation->run() == true) {
            $Code = $this->input->post('Code');
            $Name = $this->input->post('Name');
            $ProductUID = $this->input->post('ProductUID');
            $DocumentTypeUID = $this->input->post('DocumentTypeUID');
            $multipleproduct = implode('', $ProductUID);
            $ProjectUID = $this->input->post('ProjectUID');

            $PageSize = $this->input->post('PageSize');
            $MarginTop = $this->input->post('MarginTop');
            $MarginBottom = $this->input->post('MarginBottom');
            $MarginLeft = $this->input->post('MarginLeft');
            $MarginRight = $this->input->post('MarginRight');
            $FirstMarginTop = $this->input->post('FirstMarginTop');
            $FirstMarginBottom = $this->input->post('FirstMarginBottom');

            $data['TemplateCode'] = $Code;
            $data['TemplateName'] = $Name;
            $data['ProductUID'] = $multipleproduct;
            $data['Active'] = 1;
            $data['IsDynamicTemplate'] = $IsDynamicTemplate;

            $data['PageSize'] = $PageSize;
            $data['MarginTop'] = $MarginTop;
            $data['MarginBottom'] = $MarginBottom;
            $data['MarginLeft'] = $MarginLeft;
            $data['MarginRight'] = $MarginRight;
            $data['FirstMarginTop'] = $FirstMarginTop;
            $data['FirstMarginBottom'] = $FirstMarginBottom;
            $data['DocumentTypeUID'] = $DocumentTypeUID;

            $FilePath = "uploads/SampleTemplate/".$data['TemplateName'];
            mkdir($FilePath, 0777, true);

            $html = $this->input->post('editorContent');
            $Header = $this->input->post('HeaderContent');
            $Footer = $this->input->post('FooterContent');
            $BodyContent = $html;



            $FieldSectionDetails['DocumentCategory'] = $this->input->post('DocumentCategory');
            $FieldSectionDetails['DocumentLanguage'] = $this->input->post('DocumentLanguage');
            $FieldSectionDetails['LanguageGroup'] = $this->input->post('LanguageGroup');
            $FieldSectionDetails['Field'] = $this->input->post('Field');
            $FieldSectionDetails['TemplateFieldSectionUID'] = $this->input->post('TemplateFieldSectionUID');


            $HeaderContent = '<htmlpageheader name="MyCustomHeader"  style="display:none">' . $Header . '</htmlpageheader>';
            $FooterContent = '<htmlpagefooter name="MyCustomFooter"  style="display:none">' . $Footer . '</htmlpagefooter>';

            $FileContent = "";
            $FileContent.= "<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\"><html><head> <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" /><style>@page {header: html_MyCustomHeader;footer: html_MyCustomFooter;margin-top:".$MarginTop."%;margin-bottom:".$MarginBottom."%;margin-left:".$MarginLeft."%;margin-right:".$MarginRight."%;}</style></head></body> ";
            $FileContent .= $HeaderContent . $FooterContent . $html . "</body></html>";



            $FilePathName = FCPATH . $FilePath.'/'.$data['TemplateName'].'.php' ;
            file_put_contents($FilePathName, $FileContent);


            $HeaderFilePathName = $FilePath.'/'.$data['TemplateName'].'_header.php' ;
            $FooterFilePathName = $FilePath.'/'.$data['TemplateName'].'_footer.php' ;
            $BodyFilePathName   = $FilePath.'/'.$data['TemplateName'].'_body.php' ;
            file_put_contents($HeaderFilePathName, $Header);
            file_put_contents($FooterFilePathName, $Footer);
            file_put_contents($BodyFilePathName, $BodyContent);

            $File_Content = $FilePath.'/'.$data['TemplateName'].'.php' ;


            $data['TemplatePath'] = $File_Content;
            $data['TemplateTypeFile'] = $FilePath;
            $data['TemplateFileName'] = $data['TemplateName'];

            $data['HeaderFilePath'] = $HeaderFilePathName;
            $data['FooterFilePath'] = $FooterFilePathName;
            $data['BodyFilePath']   = $BodyFilePathName;


            /*Transaction Begin*/
            $this->db->trans_begin();

            $TemplateUID = $this->template_model->SaveTemplate($data, $ProjectUID);
            $this->template_model->saveFieldSectionDetails($FieldSectionDetails, $TemplateUID);


            $inserted = 0;
            /*verify is valid transaction*/
            if ($this->db->trans_status()) {
                /*commit transaction*/
                $this->db->trans_commit();
                $inserted = 1;
            } else {
                /*rollback transaction*/
                $this->db->trans_rollback();
                $inserted = 0;
            }

            if ($inserted > 0) {
                $Msg = $this->lang->line('Template_add');
                $result = array("validation_error" => 0,'message'=> $Msg);
            } else {
                $Msg = $this->lang->line('Template_error');
                $result = array("validation_error" => 1,'message'=> $Msg );
            }
            echo json_encode($result);
        } else {
            $Msg = $this->lang->line('Empty_Validation');
            $data = array(
    'validation_error' => 1,
    'message' => $Msg
   );
            echo json_encode($data);
        }
    }

public function update_template()
    {
        $IsDynamicTemplate=($this->input->post('IsDynamicTemplate')) ? 1:0;

        $this->form_validation->set_rules('Code', '', 'required');
        $this->form_validation->set_rules('Name', '', 'required');
        /*$this->form_validation->set_rules('ProductUID', '', 'required');*/
        if ($IsDynamicTemplate == 1) {
            $this->form_validation->set_rules('PageSize', '', 'required');
            $this->form_validation->set_rules('MarginTop', '', 'required');
            $this->form_validation->set_rules('MarginBottom', '', 'required');
            $this->form_validation->set_rules('MarginLeft', '', 'required');
            $this->form_validation->set_rules('MarginRight', '', 'required');
        }
        $this->form_validation->set_message('required', 'This Field is required');
        if ($this->form_validation->run() == true) {
            $TemplateUID = $this->input->post('TemplateUID');
            $Code = $this->input->post('Code');
            $Name = $this->input->post('Name');
            $ProductUID = $this->input->post('ProductUID');
            $DocumentTypeUID = $this->input->post('DocumentTypeUID');
            $multipleproduct = implode(',', $ProductUID);
            $data['TemplateCode'] = $Code;
            $data['TemplateName'] = $Name;
            $data['DocumentTypeUID'] = $DocumentTypeUID;
            $data['ProductUID'] = $multipleproduct;

            $PageSize = $this->input->post('PageSize');
            $MarginTop = $this->input->post('MarginTop');
            $MarginBottom = $this->input->post('MarginBottom');
            $MarginLeft = $this->input->post('MarginLeft');
            $MarginRight = $this->input->post('MarginRight');
            $FirstMarginTop = $this->input->post('FirstMarginTop');
            $FirstMarginBottom = $this->input->post('FirstMarginBottom');
            $ProjectUID = $this->input->post('ProjectUID');
            $TemplateFileName = $Name;
    
            $FieldSectionDetails['DocumentCategory'] = $this->input->post('DocumentCategory');
            $FieldSectionDetails['DocumentLanguage'] = $this->input->post('DocumentLanguage');
            $FieldSectionDetails['LanguageGroup'] = $this->input->post('LanguageGroup');
            $FieldSectionDetails['Field'] = $this->input->post('Field');
            $FieldSectionDetails['TemplateFieldSectionUID'] = $this->input->post('TemplateFieldSectionUID');


            $data['PageSize'] = $PageSize;
            $data['MarginTop'] = $MarginTop;
            $data['MarginBottom'] = $MarginBottom;
            $data['MarginLeft'] = $MarginLeft;
            $data['MarginRight'] = $MarginRight;
            $data['FirstMarginTop'] = $FirstMarginTop;
            $data['FirstMarginBottom'] = $FirstMarginBottom;


            $FilePath = "uploads/SampleTemplate/".$data['TemplateName'];
            $this->common_model->CreateDirectoryToPath($FilePath);
            $File_Content = $FilePath.'/'.$TemplateFileName.'.php' ;

            $FilePathName = FCPATH . $FilePath.'/'.$TemplateFileName.'.php' ;
            $HeaderFilePathName = $FilePath.'/'.$TemplateFileName.'_header.php' ;
            $FooterFilePathName = $FilePath.'/'.$TemplateFileName.'_footer.php' ;
            $BodyFilePathName   = $FilePath.'/'.$TemplateFileName.'_body.php' ;




            if ($this->input->post('Active')) {
                $data['Active'] = 1;
            } else {
                $data['Active'] = 0;
            }


            $data['TemplatePath'] = $File_Content;
            $data['TemplateTypeFile'] = $FilePath;
            $data['TemplateFileName'] = $TemplateFileName;

            $data['HeaderFilePath'] = $HeaderFilePathName;
            $data['FooterFilePath'] = $FooterFilePathName;
            $data['BodyFilePath']   = $BodyFilePathName;


            $data['IsDynamicTemplate'] = $IsDynamicTemplate;


            /*Transaction Begin*/
            $this->db->trans_begin();

            $this->template_model->UpdateTemplate($TemplateUID, $data, $ProjectUID);
            $this->template_model->saveFieldSectionDetails($FieldSectionDetails, $TemplateUID);


  
            /*verify is valid transaction*/
            if ($this->db->trans_status()) {
                /*commit transaction*/
                $this->db->trans_commit();
                $update = 1;
            } else {
                /*rollback transaction*/
                $this->db->trans_rollback();
                /*Code*/
                $update = 0;
            }

            if ($update==1) {
                $html = $this->input->post('editorContent');
                $Header = $this->input->post('HeaderContent');
                $Footer = $this->input->post('FooterContent');
                $BodyContent = $html;


                $HeaderContent = '<htmlpageheader name="MyCustomHeader"  style="display:none">' . $Header . '</htmlpageheader>';
                $FooterContent = '<htmlpagefooter name="MyCustomFooter"  style="display:none">' . $Footer . '</htmlpagefooter>';

                $FileContent = "";
                $FileContent.= "<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\"><html><head> <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" /><style>@page {header: html_MyCustomHeader;footer: html_MyCustomFooter;margin-top:".$MarginTop."%;margin-bottom:".$MarginBottom."%;margin-left:".$MarginLeft."%;margin-right:".$MarginRight."%;}</style></head></body> ";
                $FileContent .= $HeaderContent . $FooterContent . $html . "</body></html>";



                file_put_contents($FilePathName, $FileContent);
                file_put_contents($HeaderFilePathName, $Header);
                file_put_contents($FooterFilePathName, $Footer);
                file_put_contents($BodyFilePathName, $BodyContent);

                $Msg = $this->lang->line('Template_update');
                $result = array("validation_error" => 0,'message'=>$Msg );
            } else {
                $Msg = $this->lang->line('Template_error');
                $result = array("validation_error" => 1,'message'=>$Msg);
            }
            echo json_encode($result);
        } else {
            $Msg = $this->lang->line('Empty_Validation');
            $data = array(
      'validation_error' => 1,
      'message' => $Msg
    );
            echo json_encode($data);
        }
    }
    public function GetTemplateFileName()
    {
        $TemplateUID = $this->input->post('TemplateUID');

        $mtemplates = $this->template_model->GetTemplateFileName($TemplateUID);
        $TemplateFileName = $mtemplates->TemplatePath;
        $HeaderFilePath   = $mtemplates->HeaderFilePath;
        $FooterFilePath   = $mtemplates->FooterFilePath;
        $BodyFilePath     = $mtemplates->BodyFilePath;


        $file          = FCPATH.$TemplateFileName;

        $Rep_msg       = file_get_contents($file);
        $HeaderContent = file_get_contents($HeaderFilePath);
        $FooterContent = file_get_contents($FooterFilePath);
        $BodyContent   = file_get_contents($BodyFilePath);

        $MainContent   = !empty($BodyContent) ? $BodyContent : $Rep_msg;
        $HeaderContent = !empty($HeaderContent) ? $HeaderContent : "";
        $FooterContent = !empty($FooterContent) ? $FooterContent : "";

        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode(['status'=>'ok','message'=>"Success", "MainContent"=>$MainContent, "HeaderContent"=>$HeaderContent, "FooterContent"=>$FooterContent]))->_display();
        exit;
    }

    public function GetProjectDetails()
    {
        $ProductUID = $this->input->post('ProductUID');
        $TemplateUID = $this->input->post('TemplateUID');
        $this->db->select('*');
        $this->db->from('mProjects');
        $this->db->where_in('mProjects.ProductUID', $ProductUID);
        $query = $this->db->get();
        $Projects =  $query->result();

        $ProjectDetailsByTemplateUID = $this->GetProjectDetailsByTemplateUID($TemplateUID);

        $str = '';
        $str .= '<option value=""></option>';
        foreach ($Projects as $crow) {
            $str.= '<option value="'.$crow->ProjectUID.'">'.$crow->ProjectName.'</option>';
        }

        $res = array("success" => 1,'Projects' => $str,'ProjectDetailsByTemplateUID' => $ProjectDetailsByTemplateUID);
        echo json_encode($res);
    }

    public function GetProjectDetailsByTemplateUID($TemplateUID)
    {
        $this->db->select("*");
        $this->db->from('mTemplateProject');
        $this->db->where('mTemplateProject.TemplateUID', $TemplateUID);
        $query = $this->db->get();
        return $query->result();
    }
     /**
     * @description Getting Audit Details
     * @author Sathis Kannan.P <sathish.kannan@avanzegroup.com>
     * @return JSON ARRAY
     * @since  22/09/2020 
     */
    public function ajax_auditlogs()
    {
        /*$formData = $this->input->post('formData');*/
        $data = $this->process_get_data($_POST['formData']);
        $post = $data['post'];

        $totalorders = $this->template_model->getTemplateAudits($post, true);
        $output = array(
            "draw" => $post['draw'],
            "recordsTotal" => $totalorders,
            "recordsFiltered" => $totalorders,
            "data" => $data['data'],
        );
        if (isset($post['search_value']) && $post['search_value']!='') {
            $output['recordsTotal']=$this->template_model->getTemplateAudits($post, true);
        }
        unset($post);
        unset($data);
        echo json_encode($output);
    }


}
