<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


public class Report_Common_Model extends CI_Model {

	
	function __construct()
	{ 
		parent::__construct();
	}

	function GetDocuments($OrderUID) 
	{
	 $this->db->select ( 'torderdocuments.*, torders.OrderDocsPath, mdocumenttypes.*, msearchmodes.*' ); 
	 $this->db->from ( 'torderdocuments' );
	 $this->db->join ( 'mdocumenttypes', 'mdocumenttypes.DocumentTypeUID = torderdocuments.DocumentTypeUID' , 'left' );
	 $this->db->join ( 'msearchmodes', 'msearchmodes.SearchModeUID = torderdocuments.SearchModeUID' , 'left' );
	 $this->db->join ( 'torders', 'torders.OrderUID = torderdocuments.OrderUID' , 'left' );
	 $this->db->where(array("torderdocuments.OrderUID"=>$OrderUID,"torderdocuments.TypeOfDocument"=>"Others"));
	 $this->db->order_by("torderdocuments.Position asc, torderdocuments.SearchModeUID asc");
	 $query = $this->db->get();
	 return $query->result();

	}

	function getBorrowers($OrderUID)
	{
		$query = $this->db->query("SELECT PRName FROM torderpropertyroles WHERE PropertyRoleUID = 5 and  OrderUID='$OrderUID' ");
		return $query->result();
	}



	function  get_torders($OrderUID = '')
	{	
		$q = $this->db->query("SELECT mstates.*,torders.*,mcities.*,torderpropertyinfo.*,mcounties.*,torderdocuments.*,Max(SearchAsOfDate) AS SearchAsOfDate, Max(SearchFromDate) As SearchFromDate,msubproducts.*,mcustomers.*,
		(SELECT GROUP_CONCAT(PartyName) FROM tordermortgageparties t2 WHERE PartyTypeUID = 2 AND t2.MortgageSNo = tordermortgages.MortgageSNo) AS Mortgagee,
		(SELECT GROUP_CONCAT(PartyName) FROM torderdeedparties t2 WHERE PartyTypeUID = 4 AND t2.DeedSNo = torderdeeds.DeedSNo) AS Grantee,
		(SELECT Max(DeedRecorded) FROM `torderdeeds` WHERE OrderUID = '$OrderUID' LIMIT 1) AS LastDeedRecorded,
        (SELECT StateCode FROM mstates WHERE mstates.StateUID = mcustomers.CustomerStateUID) AS CustomerStateCode,
        (SELECT CountyName FROM mcounties WHERE mcounties.CountyUID = mcustomers.CustomerCountyUID) AS CustomerCountyName,
        (SELECT CityName FROM mcities WHERE mcities.CityUID = mcustomers.CustomerCityUID) AS CustomerCityName
        
     	 FROM torders 
        				LEFT JOIN torderdeeds ON torderdeeds.OrderUID = torders.OrderUID
						LEFT JOIN tordermortgages ON tordermortgages.OrderUID = torders.OrderUID
						LEFT JOIN mcounties ON mcounties.CountyUID = torders.PropertyCountyUID 
						LEFT JOIN mstates ON mstates.StateUID = torders.PropertyStateUID 
						LEFT JOIN mcities ON mcities.CityUID = torders.PropertyCity
						LEFT JOIN mcustomers ON mcustomers.CustomerUID = torders.CustomerUID
                        LEFT JOIN torderpropertyinfo ON torderpropertyinfo.OrderUID = torders.OrderUID
                        LEFT JOIN msubproducts ON msubproducts.SubProductUID = torders.SubProductUID
                        LEFT JOIN torderdocuments ON torderdocuments.OrderUID = torders.OrderUID
						WHERE torders.OrderUID = '$OrderUID' ");
		return $q->row();
	}

	function get_Address($OrderUID = '')
	{
	$query = $this->db->query("SELECT * FROM 
			(SELECT OrderUID,PropertyAddress1 AS PropertyAddr1,PropertyAddress2 AS PropertyAddr2,PropertyCity,PropertyZipcode AS PropertyZip,PropertyCountyUID,PropertyStateUID FROM torders WHERE OrderUID='$OrderUID')a
			LEFT JOIN 
			(SELECT * FROM torderaddress)b
			ON a.OrderUID=b.OrderUID
			LEFT JOIN 
			(SELECT CountyUID, CountyName AS AssessedCountyName FROM mcounties)c
			ON b.AssessedCounty=c.CountyUID
			LEFT JOIN 
			(SELECT CountyUID, CountyName AS USPSCountyName FROM mcounties)d
			ON b.USPSCounty=d.CountyUID
			LEFT JOIN 
			(SELECT StateUID,StateCode AS AssessedStateCode FROM mstates)e 
			ON b.AssessedState = e.StateUID
			LEFT JOIN 
			(SELECT StateUID,StateCode AS USPSStateCode FROM mstates)f 
			ON b.USPSState = f.StateUID
			LEFT JOIN
			(SELECT CityUID,CityName AS AssessedCityName FROM mcities)g 
			ON b.AssessedCity = g.CityUID
			LEFT JOIN 
			(SELECT CityUID,CityName AS USPSCityName FROM mcities)h 
			ON b.USPSCity = h.CityUID
            LEFT JOIN 
            (SELECT CountyUID, CountyName AS PropertyCountyName FROM mcounties)i 
            ON a.PropertyCountyUId = i.CountyUID
            LEFT JOIN
            (SELECT StateUID,StateCode AS PropertyStateCode FROM mstates)j 
            ON a.PropertyStateUID = j.StateUID
            LEFT JOIN
            (SELECT CityUID,CityName AS PropertyCityName FROM mcities)k
            ON a.PropertyCity = k.CityUID
            LEFT JOIN
            (SELECT OrderTypeUID,OrderTypeName AS SearchLengthType FROM mordertypes)l 
            ON b.SearchLength = l.OrderTypeUID
            
            
            ");
		return $query->result();
	}


	function get_torderdeeds($OrderUID = '')
	{

		$query = $this->db->query("SELECT torderdeeds.*, mdocumenttypes.*,mmaritalstatus.*,torderpropertyinfo.*,mcustomers.*,torders.*,mestateinterests.*,
			(SELECT GROUP_CONCAT(PartyName) FROM torderdeedparties t1 WHERE PartyTypeUID = 3 AND t1.DeedSNo = torderdeeds.DeedSNo) AS Grantor, 
			(SELECT GROUP_CONCAT(PartyName) FROM torderdeedparties t2 WHERE PartyTypeUID = 4 AND t2.DeedSNo = torderdeeds.DeedSNo) AS Grantee 
			FROM torderdeeds 
			LEFT JOIN torderdeedparties ON torderdeedparties.DeedSNo = torderdeeds.DeedSNo 
			LEFT JOIN mdocumenttypes ON mdocumenttypes.DocumentTypeUID = torderdeeds.DocumentTypeUID
            LEFT JOIN torderpropertyinfo ON torderpropertyinfo.OrderUID = torderdeeds.OrderUID
            LEFT JOIN mmaritalstatus ON torderpropertyinfo.MaritalStatusUID = mmaritalstatus.MaritalStatusUID
            LEFT JOIN torders ON torders.OrderUID = torderdeeds.OrderUID
			LEFT JOIN mcustomers ON mcustomers.CustomerUID = torders.CustomerUID
            LEFT JOIN mestateinterests ON torderdeeds.EstateInterestUID = mestateinterests.EstateInterestUID
			WHERE torderdeeds.OrderUID = '$OrderUID' GROUP BY torderdeeds.DeedSNo ORDER BY torderdeeds.DeedDated DESC,torderdeeds.DeedPosition");
		return $query->result();
	}


	function get_tordermortgageparties($OrderUID = '')
	{
		$query = $this->db->query("SELECT tordermortgages.*, mdocumenttypes.*,mmortgagedbvtypes.*,mlientypes.*,mmortgagedbvtypes.*,
				(SELECT GROUP_CONCAT(PartyName) FROM tordermortgageparties t1 WHERE PartyTypeUID = 1 AND t1.MortgageSNo = tordermortgages.MortgageSNo) AS Mortgagor, 
				(SELECT GROUP_CONCAT(PartyName) FROM tordermortgageparties t2 WHERE PartyTypeUID = 2 AND t2.MortgageSNo = tordermortgages.MortgageSNo) AS Mortgagee,
	
				(SELECT Mortgage_DBVTypeValue_1 FROM tordermortgages WHERE Mortgage_DBVTypeUID_1 = 6 LIMIT 1) AS DocumentNo
				
				FROM tordermortgages 
				LEFT JOIN tordermortgageparties ON tordermortgageparties.MortgageSNo = tordermortgages.MortgageSNo 
				LEFT JOIN mdocumenttypes ON mdocumenttypes.DocumentTypeUID = tordermortgages.DocumentTypeUID
                LEFT JOIN mmortgagedbvtypes ON tordermortgages.Mortgage_DBVTypeUID_1 =mmortgagedbvtypes.DBVTypeUID
                LEFT JOIN mlientypes ON mlientypes.LienTypeUID = tordermortgages.LienTypeUID
				WHERE tordermortgages.OrderUID = '$OrderUID' GROUP BY tordermortgages.MortgageSNo ORDER BY tordermortgages.MortgagePosition");
		return $query->result();
	}


	function get_torderjudgements($OrderUID = '')
	{
		$query = $this->db->query("SELECT torderjudgements.*, mdocumenttypes.*,mmaritalstatus.*,torderpropertyinfo.*,mmortgagedbvtypes.*,
                                (SELECT GROUP_CONCAT(PartyName) FROM torderjudgementparties t1 WHERE PartyTypeUID = 5 AND t1.JudgementSNo = torderjudgements.JudgementSNo) AS Plaintiff, 
                                (SELECT GROUP_CONCAT(PartyName) FROM torderjudgementparties t2 WHERE PartyTypeUID = 6 AND t2.JudgementSNo = torderjudgements.JudgementSNo) AS Defendent,
                                
                                (SELECT GROUP_CONCAT(PartyName) FROM torderdeedparties t2 WHERE PartyTypeUID = 4 AND t2.DeedSNo = torderdeeds.DeedSNo) AS Grantee 
                                FROM torderjudgements 
					
                                 LEFT JOIN torderdeeds ON torderdeeds.OrderUID = torderjudgements.OrderUID
                                 LEFT JOIN torderpropertyinfo ON torderpropertyinfo.OrderUID = torderjudgements.OrderUID
                                 LEFT JOIN mmaritalstatus ON torderpropertyinfo.MaritalStatusUID = mmaritalstatus.MaritalStatusUID
                                LEFT JOIN torderjudgementparties ON torderjudgementparties.JudgementSNo = torderjudgements.JudgementSNo 
                                LEFT JOIN mdocumenttypes ON mdocumenttypes.DocumentTypeUID = torderjudgements.DocumentTypeUID
                                LEFT JOIN mmortgagedbvtypes ON mmortgagedbvtypes.DBVTypeUID = torderjudgements.Judgement_DBVTypeUID_1
                                WHERE torderjudgements.OrderUID = '$OrderUID' GROUP BY torderjudgements.JudgementSNo  ORDER BY torderjudgements.JudgmentPosition");
		return $query->result();
	}


	function get_torderliens($OrderUID = '')
	{
		$query = $this->db->query("SELECT torderleins.*, mdocumenttypes.* ,mlientypes.*,mmortgagedbvtypes.*,
			(SELECT GROUP_CONCAT(PartyName) FROM torderdeedparties t2 WHERE PartyTypeUID = 4 AND t2.DeedSNo = torderdeeds.DeedSNo) AS Grantee 
                                FROM torderleins 
                                 LEFT JOIN torderdeeds ON torderdeeds.OrderUID = torderleins.OrderUID
                                LEFT JOIN mmortgagedbvtypes ON mmortgagedbvtypes.DBVTypeUID = torderleins.Lien_DBVTypeUID_1
                                LEFT JOIN mdocumenttypes ON mdocumenttypes.DocumentTypeUID = torderleins.DocumentTypeUID
                                LEFT JOIN tordermortgages ON tordermortgages.OrderUID = torderleins.OrderUID
                                LEFT JOIN mlientypes ON mlientypes.LienTypeUID = tordermortgages.LienTypeUID
                                WHERE torderleins.OrderUID = '$OrderUID' GROUP BY torderleins.LeinSNo ORDER BY torderleins.LienPosition");
		return $query->result();
	}


	function get_tordertaxcerts($OrderUID = '')
	{

		$query = $this->db->query("SELECT tordertaxcerts.*, mdocumenttypes.*, torderassessment.*, torderpropertyinfo.*, mtaxstatus.*,mpropertyclass.*,mtaxcertbasis.*,mtaxexemptions.* ,mtaxauthority.*,mtaxcertinstallments.*,mtaxauthority.* FROM tordertaxcerts 
                                LEFT JOIN tordertaxexemptions ON tordertaxexemptions.OrderUID = tordertaxcerts.OrderUID
                                LEFT JOIN mtaxstatus ON mtaxstatus.TaxStatusUID = tordertaxcerts.TaxStatusUID
                                LEFT JOIN torderassessment ON torderassessment.OrderUID = tordertaxcerts.OrderUID
                                LEFT JOIN torderpropertyinfo ON torderpropertyinfo.OrderUID = tordertaxcerts.OrderUID
                                LEFT JOIN mdocumenttypes ON mdocumenttypes.DocumentTypeUID = tordertaxcerts.DocumentTypeUID
                                LEFT JOIN mpropertyclass ON mpropertyclass.PropertyClassUID = torderpropertyinfo.PropertyClassUID
                                LEFT JOIN mtaxcertbasis ON mtaxcertbasis.TaxBasisUID = tordertaxcerts.TaxBasisUID
                                LEFT JOIN mtaxexemptions ON mtaxexemptions.TaxExemptionUID = tordertaxexemptions.TaxExemptionUID
                                LEFT JOIN mtaxauthority ON mtaxauthority.TaxAuthorityUID = tordertaxcerts.TaxAuthorityUID
                                LEFT JOIN mtaxcertinstallments ON mtaxcertinstallments.TaxInstallmentUID = tordertaxcerts.TaxInstallmentUID
                                WHERE tordertaxcerts.OrderUID = '$OrderUID' GROUP BY tordertaxcerts.TaxCertSNo ORDER BY tordertaxcerts.TaxPosition
                                ");
		return $query->result();
	}

	function getExemptionname($OrderUID = '',$TaxCertSNo)
	{
		$query = $this->db->query("SELECT GROUP_CONCAT(TaxExemptionName) AS TaxExemptionName FROM mtaxexemptions t1,tordertaxexemptions t2 WHERE t1.TaxExemptionUID = t2.TaxExemptionUID AND t2.OrderUID = '$OrderUID'AND t2.TaxCertSNo = '$TaxCertSNo' ");
		$result =  $query->row();
		return $result->TaxExemptionName;
	}

	function gettaxExemption($OrderUID = '',$TaxCertSNo)
	{
		$query = $this->db->query("SELECT * FROM mtaxexemptions t1,tordertaxexemptions t2 WHERE t1.TaxExemptionUID = t2.TaxExemptionUID AND t2.OrderUID = '$OrderUID' AND t2.TaxCertSNo = '$TaxCertSNo' ");
		return $query->result();
	}

	function getsubmortgage($OrderUID = '',$Mortgage)
	{
		$query = $this->db->query("SELECT * FROM tordermortgageassignment LEFT JOIN msubdocumentmortgages ON msubdocumentmortgages.DocumentTypeUID = tordermortgageassignment.DocumentTypeUID WHERE OrderUID  = $OrderUID AND MortgageSNo = $Mortgage");
		return $query->result();
	}

	function gettaxinstallment($OrderUID = '',$TaxCertSNo)
	{
		$query = $this->db->query("SELECT * FROM `tordertaxinstallment` LEFT JOIN mtaxcertinstallments ON mtaxcertinstallments.TaxInstallmentUID = tordertaxinstallment.TaxInstallmentUID LEFT JOIN mtaxstatus ON mtaxstatus.TaxStatusUID = tordertaxinstallment.TaxStatusUID WHERE OrderUID = $OrderUID AND TaxCertSNo =$TaxCertSNo");
		return $query->result();
	}

	function getMortgageCount($OrderUID = '')
	{
		$query = $this->db->query("SELECT count(*)  AS MortgageCount from tordermortgages where tordermortgages.OrderUID = '$OrderUID' ");
		$result =  $query->row();
		return $result->MortgageCount;
	}
	function getLienCount($OrderUID = '')
	{
		$query = $this->db->query("SELECT count(*)  AS LienCount FROm torderleins where torderleins.OrderUID = '$OrderUID' ");
		$result =  $query->row();
		return $result->LienCount;
	}
	function getJudgementCount($OrderUID = '')
	{
		$query = $this->db->query("SELECT count(*)  AS JudgementCount FROM torderjudgements where torderjudgements.OrderUID = '$OrderUID' ");
		$result =  $query->row();
		return $result->JudgementCount;
	}
	function getGranteeGrantor($OrderUID = '')
	{
		$q = $this->db->query("SELECT MAX(DeedDated) AS LatestDeedDated,MAX(DeedRecorded) AS LatestDeedRecorded FROM torderdeeds WHERE OrderUID = '$OrderUID' ");
		$result =  $q->row();
		$LatestDeedDated = $result->LatestDeedDated;
		$LatestDeedRecorded = $result->LatestDeedRecorded;
		if($LatestDeedDated !== '0000-00-00' || $LatestDeedRecorded !== '0000-00-00')
		{
			if(strtotime($LatestDeedDated) > strtotime($LatestDeedRecorded))
			{
				
					$query = $this->db->query("SELECT *, (SELECT GROUP_CONCAT(PartyName) FROM torderdeedparties t1 WHERE PartyTypeUID = 3 AND t1.DeedSNo = torderdeeds.DeedSNo) AS Grantor, (SELECT GROUP_CONCAT(PartyName) FROM torderdeedparties t2 WHERE PartyTypeUID = 4 AND t2.DeedSNo = torderdeeds.DeedSNo) AS Grantee FROM torderdeeds LEFT JOIN mestateinterests ON torderdeeds.EstateInterestUID = mestateinterests.EstateInterestUID LEFT JOIN mtenancytype ON torderdeeds.TenancyUID = mtenancytype.TenancyUID WHERE torderdeeds.DeedDated = '$LatestDeedDated' ");
					return $query->result();
				
			}

			if(strtotime($LatestDeedRecorded) > strtotime($LatestDeedDated))
			{
				$query = $this->db->query("SELECT *, (SELECT GROUP_CONCAT(PartyName) FROM torderdeedparties t1 WHERE PartyTypeUID = 3 AND t1.DeedSNo = torderdeeds.DeedSNo) AS Grantor, (SELECT GROUP_CONCAT(PartyName) FROM torderdeedparties t2 WHERE PartyTypeUID = 4 AND t2.DeedSNo = torderdeeds.DeedSNo) AS Grantee FROM torderdeeds LEFT JOIN mestateinterests ON torderdeeds.EstateInterestUID = mestateinterests.EstateInterestUID LEFT JOIN mtenancytype ON torderdeeds.TenancyUID = mtenancytype.TenancyUID WHERE torderdeeds.DeedRecorded = '$LatestDeedRecorded' ");
					return $query->result();
			}

			if(strtotime($LatestDeedRecorded) == strtotime($LatestDeedDated))
			{
				$query = $this->db->query("SELECT *, (SELECT GROUP_CONCAT(PartyName) FROM torderdeedparties t1 WHERE PartyTypeUID = 3 AND t1.DeedSNo = torderdeeds.DeedSNo) AS Grantor, (SELECT GROUP_CONCAT(PartyName) FROM torderdeedparties t2 WHERE PartyTypeUID = 4 AND t2.DeedSNo = torderdeeds.DeedSNo) AS Grantee FROM torderdeeds LEFT JOIN mestateinterests ON torderdeeds.EstateInterestUID = mestateinterests.EstateInterestUID LEFT JOIN mtenancytype ON torderdeeds.TenancyUID = mtenancytype.TenancyUID WHERE torderdeeds.DeedRecorded = '$LatestDeedDated' ");
					return $query->result();
			}

	    }
	}

	function get_PropertyInformation($OrderUID = '')
	{



		// $query = $this->db->query("SELECT *,(SELECT count(*) from tordermortgages where tordermortgages.OrderUID = '$OrderUID') as MortgageCount,(SELECT count(*) from torderjudgements where torderjudgements.OrderUID = '$OrderUID') as JudgementCount, (SELECT count(*) from torderleins where torderleins.OrderUID = '$OrderUID') as LienCount,(SELECT GROUP_CONCAT(PartyName) FROM torderdeedparties t1 WHERE PartyTypeUID = 3 AND t1.DeedSNo = torderdeeds.DeedSNo) AS Grantor, (SELECT GROUP_CONCAT(PartyName) FROM torderdeedparties t2 WHERE PartyTypeUID = 4 AND t2.DeedSNo = torderdeeds.DeedSNo) AS Grantee 
		// 	FROM torderdeeds
			
		// 	LEFT JOIN torderdeedparties ON torderdeedparties.DeedSNo = torderdeeds.DeedSNo 
		// 	LEFT JOIN mdocumenttypes ON mdocumenttypes.DocumentTypeUID = torderdeeds.DocumentTypeUID
		// 	LEFT JOIN torderpropertyinfo ON torderpropertyinfo.OrderUID = torderdeeds.OrderUID
		// 	LEFT JOIN tordertaxcerts ON torderdeeds.OrderUID = tordertaxcerts.OrderUID 
  //           LEFT JOIN tordermortgages ON tordermortgages.OrderUID = tordermortgages.OrderUID 
		// 	LEFT JOIN mmaritalstatus ON torderpropertyinfo.MaritalStatusUID = mmaritalstatus.MaritalStatusUID 
		// 	LEFT JOIN mestateinterests ON torderdeeds.EstateInterestUID = mestateinterests.EstateInterestUID
  //           LEFT JOIN mtaxstatus ON mtaxstatus.TaxStatusUID = tordertaxcerts.TaxStatusUID
  //           LEFT JOIN mpropertyuse ON mpropertyuse.PropertyUseUID = torderpropertyinfo.PropertyUseUID
		// 	WHERE torderdeeds.OrderUID = '$OrderUID' GROUP BY torderdeeds.DeedSNo");



		// return $query->result();
		
	}

	function GetUserName($UserUID)
	{
		$query = $this->db->query("SELECT UserName FROM musers WHERE UserUID ='".$UserUID."' ");
		if($query->num_rows()>0)
		{
			$result = $query->row();
			return $result->UserName;
		}
	}


	function getPropertyInformation($OrderUID = '')
	{
		$query = $this->db->query("SELECT * FROM torderpropertyinfo LEFT JOIN mpropertyuse ON mpropertyuse.PropertyUseUID = torderpropertyinfo.PropertyUseUID LEFT JOIN mmaritalstatus ON torderpropertyinfo.MaritalStatusUID = mmaritalstatus.MaritalStatusUID 
			WHERE OrderUID = '$OrderUID' ");
			return $query->result();
	}

	function get_LegalDescription($OrderUID = '')
	{
		$query = $this->db->query("SELECT * FROM torderlegaldescription WHERE OrderUID ='".$OrderUID."' ");
		if($query->num_rows()>0)
		{
			return $query->result();
		}
	}

	function get_OrderAssessment($OrderUID = '')
	{
		$query = $this->db->query("SELECT * FROM torderassessment WHERE OrderUID ='".$OrderUID."' ");
		if($query->num_rows()>0)
		{
			return $query->result();
		}
	}

	function get_tax($OrderUID = '')
	{
		$query = $this->db->query("SELECT * FROM tordertaxcerts LEFT JOIN mtaxstatus ON mtaxstatus.TaxStatusUID = tordertaxcerts.TaxStatusUID WHERE OrderUID ='$OrderUID' ");
		if($query->num_rows()>0)
		{
			return $query->result();
		}
	}

	function get_taxExemption($OrderUID = '')
	{
		$Year = date("Y");
		$query = $this->db->query("SELECT * FROM  tordertaxexemptions LEFT JOIN tordertaxcerts ON tordertaxcerts.OrderUID = tordertaxexemptions.OrderUID WHERE tordertaxcerts.OrderUID ='$OrderUID' and tordertaxcerts.TaxYear = '$Year' LIMIT 1");
		if($query->num_rows()>0)
		{
			return '1';
		}
		else{
			return '0';
		}
	}

	function get_tax_latest($OrderUID = '')
	{
		$Year = date("Y");
		$query = $this->db->query("SELECT * FROM tordertaxcerts LEFT JOIN mtaxstatus ON mtaxstatus.TaxStatusUID = tordertaxcerts.TaxStatusUID WHERE OrderUID ='$OrderUID' and TaxYear = '$Year' ");
		if($query->num_rows()>0)
		{
			return $query->result();
		}
	}

	function GetDBVTypes($Mortgage_DBVTypeUID_1)
	{
		$query = $this->db->query("SELECT DBVTypeName FROM mmortgagedbvtypes WHERE DBVTypeUID ='".$Mortgage_DBVTypeUID_1."' ");
		if($query->num_rows()>0)
		{
			$result = $query->row();
			return $result->DBVTypeName;
		}
	}


	function get_DisclaimerNote($StateName)
	{
		$query = $this->db->query("SELECT DisclaimerNote FROM mstates WHERE StateName ='".$StateName."' ");
		if($query->num_rows()>0)
		{
			$result = $query->row();
			return $result->DisclaimerNote;
		}
	}

     function Gettordersby_UID($orderUID) {
    $query = $this->db->get_where('torders', array('OrderUID' => $orderUID));
    return $query->result();
  }


  function Getsearchsites($OrderUID) 
  {

   $query = $this->db->query("SELECT * FROM msearchmodes JOIN mcountysearchmodes ON mcountysearchmodes.SearchModeUID = msearchmodes.SearchModeUID JOIN mcounties ON mcounties.CountyUID = mcountysearchmodes.CountyUID JOIN mcities ON mcities.CountyUID =mcounties.CountyUID  JOIN torders ON torders.PropertyZipcode = mcities.ZipCode WHERE torders.OrderUID = ".$OrderUID." Order By FIELD(SearchModeName, 'Free', 'Paid', 'Abstractor')");

   return $query->result();
 }


 function Gettorders($data)
 { 
   $this->db->where(array("OrderUID"=>$data['OrderUID']));
   $query = $this->db->get('torders');
   return $query->row_array();
 }


   function GetOrderUID($OrderUID)
 { 
      $this->db->select("OrderDocsPath");
      $this->db->from('torders');
      $this->db->where(array("OrderUID"=>$OrderUID));
      $query = $this->db->get();
     return $query->row();
 }



 function Getdocumenttypes() 
 {
  $query = $this->db->query("SELECT * FROM mdocumenttypes Order By FIELD(DOCUMENTTYPENAME, 'Property Info', 'Deeds', 'Mortgages', 'Judgment', 'Liens', 'Taxes')");
  return $query->result();
}

/*function GetDocuments($OrderUID) 
{
 $this->db->select ( 'torderdocuments.*, torders.OrderDocsPath, mdocumenttypes.*, msearchmodes.*' ); 
 $this->db->from ( 'torderdocuments' );
 $this->db->join ( 'mdocumenttypes', 'mdocumenttypes.DocumentTypeUID = torderdocuments.DocumentTypeUID' , 'left' );
 $this->db->join ( 'msearchmodes', 'msearchmodes.SearchModeUID = torderdocuments.SearchModeUID' , 'left' );
 $this->db->join ( 'torders', 'torders.OrderUID = torderdocuments.OrderUID' , 'left' );
 $this->db->where(array("torderdocuments.OrderUID"=>$OrderUID));
 $this->db->order_by("torderdocuments.Position asc, torderdocuments.SearchModeUID asc");
 $query = $this->db->get();
 return $query->result();

}*/

function SendtoAbstractor($result)
{

  $postparameter="";
  $arraylen = count($result);
  $i=0;
  foreach ($result as $key => $value) {
    $postparameter .= $key . "=" . $value . "&";

    if($i==$arraylen)
    {
      $postparameter .= $key . "=" . $value;      
    }
    $i++;
  }

  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => "http://staging.direct2abstract.com/api/insertorder",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => $postparameter,
    CURLOPT_HTTPHEADER => array(
      "cache-control: no-cache",
      "content-type: application/x-www-form-urlencoded"
    ),
  ));

  $response = curl_exec($curl);
  $err = curl_error($curl);


  if(!empty($err))
  {
    $response['status'] = 'failed';
    $response['msg'] = $err;
  }
  curl_close($curl);
  return $response;

}

function GetPreferedSites($OrderUID) 
{
  $query = $this->db->query("SELECT *, CASE WHEN msearchmodes.SearchModeName = 'Others' THEN mcountysearchmodes.WebsiteURL ELSE msearchmodes.SearchSiteURL END AS SiteURL FROM msearchmodes JOIN mcountysearchmodes ON mcountysearchmodes.SearchModeUID = msearchmodes.SearchModeUID JOIN mcounties ON mcounties.CountyUID = mcountysearchmodes.CountyUID JOIN mcities ON mcities.CountyUID =mcounties.CountyUID  JOIN torders ON torders.PropertyZipcode = mcities.ZipCode WHERE torders.OrderUID = ".$OrderUID." AND mcountysearchmodes.PreferenceNo <>5 Order By FIELD(SearchModeName, 'Free', 'Paid')");


  $data['PreferedSite'] = $query->result();

  return $data;

}

function Gettordersabstractorrowcount($OrderUID) 
{
  $query = $this->db->query("SELECT * FROM torderabstractor WHERE OrderUID = ".$OrderUID);

  $rowcount = $query->num_rows();

  return $rowcount;

}

function Inserttordersabstractor($result) 
{
  $data['OrderUID'] = $result['OrderUID'];
  $data['OrderTypeUID'] = $result['OrderTypeUID'];
  $data['DocumentReceived'] = 0;

  $this->db->insert('torderabstractor', $data);
  $rowcount = $this->db->affected_rows();
  if($rowcount>0)
  {
    $response['status'] = 'Success';
    $response['msg'] = 'Order Placed Successfully to Abstractor';

  }
  else
  {
    $response['status'] = 'Failed';
    $response['msg'] = 'Unable to sent Order to Abstractor'; 
  }
  return $response;
}


function gettorderOrderUID($orderno) 
{
  echo $orderno; exit;
    $this->db->where(array("OrderNumber"=>$orderno));
    $query = $this->db->get('torders');
    return $query->result();
}


function StoreDocuments($data)
{
  $this->db->insert('torderdocuments', $data);
  return $this->db->affected_rows();
}

function UpdatePosition($passingData)
{

  $query = $this->db->query("UPDATE torderdocuments SET Position = ".$passingData['Position']." WHERE DocumentFileName='". $passingData['DocumentFileName'] ."' AND OrderUID = ". $passingData['OrderUID']);
  return $query;
}

function GetFilesandPositions($passingData)
{
  $this->db->select ( 'torderdocuments.*, torders.OrderDocsPath' ); 
  $this->db->from ( 'torderdocuments' );
  $this->db->join ( 'torders', 'torders.OrderUID = torderdocuments.OrderUID' , 'left' );
  $this->db->where(array("torderdocuments.DocumentTypeUID"=>$passingData["DocumentTypeUID"], "torderdocuments.OrderUID"=>$passingData["OrderUID"]));
  $this->db->order_by("torderdocuments.Position asc, torderdocuments.SearchModeUID asc");
  $query = $this->db->get();
  return $query->result();
}

function DeleteRow($passingData)
{
  $this->db->where($passingData);
  $this->db->delete('torderdocuments');
  if($this->db->affected_rows()>0)
    return true;
  else return false;
}


function GetPDFdocuments($data)
{

 $query = $this->db->query("SELECT * FROM torderdocuments WHERE OrderUID = ".$data['OrderUID']. " ORDER BY Position ASC");
 $result = $query->result();
 return $result;
}

function GetAbstractorDetails($OrderUID)
{
  $query = $this->db->query("SELECT `torders`.`OrderNumber`, `torders`.`OrderInfoNotes`, `torders`.`PropertyAddress1`, `torders`.`PropertyAddress2`, `mcustomers`.`CustomerPContactEmailID`,
   `morderpriority`.`PriorityName`, `mcounties`.`CountyName`, `mcities`.`CityName`, `mstates`.`StateCode`, 
   `torders`.`PropertyZipcode`, 'USA' As Country, `torders`.`LoanNumber`, `mordertypes`.`OrderTypeName`, `torders`.`OrderTypeUID`,
   GROUP_CONCAT(`torderpropertyroles`.`PRName` SEPARATOR ',') as `Borrower`, '12345' AS Org_Code,
   'Property Report' As Doc_Type1, 'Mortgages' as Doc_Type2, 'Deeds' as Doc_Type3, 'Judgment' as Doc_Type4,
   'Liens' as Doc_Type5 
   FROM `torders` LEFT JOIN `torderpropertyroles` ON `torderpropertyroles`.`OrderUID` = `torders`.`OrderUID` 
   LEFT JOIN `mpropertyroles` ON `mpropertyroles`.`PropertyRoleUID` = `torderpropertyroles`.`PropertyRoleUID`
   LEFT JOIN `morderpriority` ON `morderpriority`.`PriorityUID` = `torders`.`PriorityUID`
   LEFT JOIN `mcities` ON `mcities`.`CityUID` = `torders`.`PropertyCity`
   LEFT JOIN `mcounties` ON `mcounties`.`CountyUID` = `torders`.`PropertyCountyUID`
   LEFT JOIN `mstates` ON `mstates`.`StateUID` = `torders`.`PropertyStateUID`
   LEFT JOIN `mordertypes` ON `mordertypes`.`OrderTypeUID` = `torders`.`OrderTypeUID`
   LEFT JOIN `mcustomers` ON `mcustomers`.`CustomerUID` = `torders`.`CustomerUID`
   WHERE `torders`.`OrderUID` ='" . $OrderUID . "' AND mpropertyroles.PropertyRoleName = 'Borrowers'");

  if($query->num_rows() >0)
  {
    $result = $query->result();
    $data['OrderNumber'] = $result[0]->OrderNumber;
    $Borrower = explode(",", $result[0]->Borrower);

    for($i=0; $i<=2; $i++)
    {
      if(array_key_exists($i, $Borrower))
      {
        $itr = $i+1;
        $data['Borrower'.$i] = $Borrower[$i];
        $split = explode(" ", $Borrower[$i]);
        if(array_key_exists(0, $split))
        {
          $data['bfname'.$i] = $split[0];
        }
        else
        {
         $data['bfname'.$i] = " "; 
        }
        if(array_key_exists(1, $split))
        {
          $data['blname'.$i] = $split[1];
        }
        else
        {
          $data['blname'.$i] = " "; 
        }
      }
      else
      {
        $data['blname'. $i] = " ";
        $data['bfname'. $i] = " ";
      } 
    }

    $data['Customer'] = $result[0]->CustomerPContactEmailID; 
    $data['PriorityCode'] = $result[0]->PriorityName;
    $data['Notes'] = $result[0]->OrderInfoNotes;
    $data['AddressLine1'] = $result[0]->PropertyAddress1;
    $data['AddressLine2'] = $result[0]->PropertyAddress2;
    $data['County'] = $result[0]->CountyName;
    $data['City'] = $result[0]->CityName;
    $data['State_Code'] = $result[0]->StateCode;
    $data['ZipCode'] = $result[0]->PropertyZipcode;
    $data['OrderType'] = $result[0]->OrderTypeName;
    $data['Loan Num'] = $result[0]->LoanNumber;
    $data['OrderTypeUID'] = $result[0]->OrderTypeUID;
    $data['OrderUID'] = $OrderUID;


    // $data['Org_Code'] = $result[0]->Org_Code'];
    // $data['Doc_Type1'] = $result['Doc_Type1'];
    // $data['Doc_Type2'] = $result['Doc_Type2'];
    // $data['Doc_Type3'] = $result['Doc_Type3'];
    // $data['Doc_Type4'] = $result['Doc_Type4'];
    // $data['Doc_Type5'] = $result['Doc_Type5'];
    return $data;
  }
}


function make_dir($year)
{
  $parent = 'uploads/';
  if (!is_dir($parent.$year)) 
  {
    for($i = 1; $i <= 12; $i++)
    {
          // $i = str_pad($i, 2, '0', STR_PAD_LEFT);
      $monthdir = getMonth($i);

      for($j=1; $j<=cal_days_in_month(CAL_GREGORIAN, $i, $year); $j++)
      {
        $the_dir = $parent.$year.'/'.$monthdir . '/' . str_pad($j, 2, '0', STR_PAD_LEFT) . str_pad($i, 2, '0', STR_PAD_LEFT) . $year;
        mkdir($the_dir, 0777, TRUE);            
      }
    }
  }
}
// $year = date('Y');
// $year = '2018';
// make_dir($year);DA

function getMonth($monthindex)
{
  switch ($monthindex) {
    case 1:
    return strtolower('JAN');
    break;
    case 2:
    return strtolower('FEB');
    break;
    case 3:
    return strtolower('MAR');
    break;
    case 4:
    return strtolower('APR');
    break;
    case 5:
    return strtolower('MAY');
    break;
    case 6:
    return strtolower('JUN');
    break;
    case 7:
    return strtolower('JUL');
    break;
    case 8:
    return strtolower('AUG');
    break;
    case 9:
    return strtolower('SEP');
    break;
    case 10:
    return strtolower('OCT');
    break;
    case 11:
    return strtolower('NOV');
    break;        
    case 12:
    return strtolower('DEC');
    break;
    default:
    return strtolower(date('M'));
    break;
  }
}


function gettordersdocumentsRowCount($data) 
  {
   $query = $this->db->get_where('torderdocuments', $data);

   $response['count'] = $query->num_rows();
   $response['status'] = "Success";

   if($this->db->_error_message())
   {
     $response['count'] = 0;
     $response['status'] = $this->db->_error_message();    
   }

   return $response;
 }

}
?>





