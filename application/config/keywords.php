<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| CONFIG TYPES
| -------------------------------------------------------------------
| For keywords 
|
*/

$config['keywords'] = array (
	'New Order'	=> '0',
	'Reopened Order'=>'5',
	'Order Assigned'=>'10',
	'Flood Api' => '11',
	'Order Work In Progress'=>'15',
	'Partial Draft Complete'=>'19',
	'Draft Complete'=>'20',
	'Review In Progress'=>'35',
	'Partial Review Complete'=>'39',
	'Review Complete'=>'40',
	'Exception Raised'=>'49',
	'Exception Clarified'=>'50',
	'Raised Special Case'=>'59',
	'Clarified'=>'60',
	'Order Exported'=>'90',
	'Order Completed'=>'100',
	'Cancelled'=>'110',
	'Billed'=>'115',
);

$config['PartyTypeUID'] = array(
	'Mortgagor'=> '1',
	'Mortgagee'=> '2',
	'Grantor'=>'3',
	'Grantee'=>'4',
	'Plaintiff'=> '5',
	'Defendent'=>'6',
);

$config['DocumentTypeUID'] = array(
	'Deeds'=>'1',
	'Mortgages'=>'2',
	'Property Info'=>'3',
	'Judgment'=>'4',
	'Liens'=>'5',
	'Taxes'=>'6',
);

$config['Propertyroles'] = array(
	'All' => '1',
	'Attorney in Fact' => '2',
	'Non Borrower' => '3',
	'Payer' => '4',
	'Borrowers' => '5',
	'Sellers' => '6',
	'Buyers' => '7',
	'Listing Agents' => '8',
	'Selling Agents' => '9',
	'Loan Officiers' => '10',
	'Servicers' => '11',
	'Others' => '12',
	'Subscribers' => '13',
	'GAC Counsel' => '14',
	'Underwrites Contact' => '15',
	'Non-Borrowing Spouse' => '16',
	'Non-Borrowing Obligator' => '17',
	'Property Contact' => '18',
	'Processor' => '19',
	'Nationstar' => '20',
	'Payoff Agent' => '21',
	'Broker' => '22',
	'Title Holder - Non' => '23',
	'Title Holder - Borrower' => '24',
	'Borrower - Non Owner' => '25',
	'Proposed Insurer' => '26',
	'Co Applicant' => '27',
	'Lender' => '28',
	/* Keystone Property Role Type Added*/
	'Loan Processor' => '30',
	'Appraiser' => '31',
	'Attorney' => '32',
	'Builder' => '33',
	'Closing Agent' => '34',
	'Appraiser Supervisor' => '35',
	'Correspondent Lender' => '36',
	'Gift Donor' => '37',
	'Lender Branch' => '38',
	'Loan Originator' => '39',
	'Notary' => '40',
	'Requesting Party' => '41',
	'Review Appraiser' => '42',
	'Spouse' => '43',
	'Third Party Originator' => '44',
	'Title Company' => '45',
	'Authorized Representative' => '46',
	'Authorized Third Party' => '47',
	'Bankruptcy Filer' => '48',
	'Bankruptcy Trustee' => '49',
	'Beneficial Interest Party' => '50',
	'Bill To Party' => '51',
	'Conservator' => '52',
	'Consumer Reporting Agency' => '53',
	'Cooperative Company' => '54',
	'Cosigner' => '55',
	'Credit Counseling Agent' => '56',
	'Credit Enhancement Risk Holder' => '57',
	'Custodian Note PayTo' => '58',
	'Defendant' => '59',
	'Deliver Rescission To' => '60',
	'Housing Counseling Agency' => '61',
	'Designated Contact' => '62',
	'ENote Controller' => '63',
	'ENote Controller Transferee' => '64',
	'ENote Custodian' => '65',
	'ENote Custodian Transferee' => '66',
	'ENote Delegatee For Transfers' => '67',
	'ENote Registering Party' => '68',
	'ENote Servicer' => '69',
	'ENote Servicer Transferee' => '70',
	'ENote Transfer Initiator' => '71',
	'Executor' => '72',
	'FHASponsor' => '73',
	'Flood Certificate Provider' => '74',
	'Fulfillment Party' => '75',
	'Housing Counseling Agent' => '76',
	'Grantee' => '77',
	'Grantor' => '78',
	'Hazard Insurance Agent' => '79',
	'HUD1 Settlement Agent' => '80',
	'Homeowners Association' => '81',
	'Interviewer' => '82',
	'Interviewer Employer' => '83',
	'Investor' => '84',
	'IRS Tax Form ThirdParty' => '85',
	'Law Firm' => '86',
	'Loan Closer' => '87',
	'Lien Holder' => '88',
	'Loan Delivery File Preparer' => '89',
	'Loan Funder' => '90',
	'Loan Origination Company' => '91',
	'Loan Seller' => '92',
	'Loan Underwriter' => '93',
	'Loss Payee' => '94',
	'Management Company' => '95',
	'MI Company' => '96',
	'NonTitle NonSpouse OwnershipInterest' => '97',
	'NonTitle Spouse' => '98',
	'Note Pay To' => '99',
	'Note Pay To Recipient' => '100',
	'Payee' => '101',
	'Plaintiff' => '102',
	'Pool Insurer' => '103',
	'Pool Issuer' => '104',
	'Pool Issuer Transferee' => '105',
	'Prepared By' => '106',
	'Project Developer' => '107',
	'Project Management Agent' => '108',
	'Property Access Contact' => '109',
	'Property Jurisdictional Authority' => '110',
	'Property Owner' => '111',
	'Property Preservation Agent' => '112',
	'Property Purchaser' => '113',
	'Receiving Party' => '114',
	'Registry Operator' => '115',
	'Regulatory Agency' => '116',
	'Responding Party' => '117',
	'Respond To Party' => '118',
	'Return To' => '119',
	'Security Issuer' => '120',
	'Service Bureau' => '121',
	'Service Provider' => '122',
	'Servicer Payment Collection' => '123',
	'Settlor' => '124',
	'Submitting Party' => '125',
	'Taxable Party' => '127',
	'Tax Assessor' => '128',
	'Client' => '129',
	'Assign From' => '130',
	'Assign To' => '131',
	'Title Holder' => '159',
	'Title Underwriter' => '132',
	'Trust' => '133',
	'Trust Beneficiary' => '49',
	'Trustee' => '134',
	'Unspecified' => '135',
	'Warehouse Lender' => '136',
	'Witness' => '137',
	'Tax Collector' => '138',
	'Tax payer' => '139',
	'Tax Service Provider' => '140',
	'Tax Servicer' => '141',
	'Third Party Investor' => '142',
	'Document Custodian' => '143',
	'Property Seller' => '144',
	'Real Estate Agent' => '145',
	'Mortgage Broker' => '146',
	'Buyer Client' => '148',
	'Buyer List Agent' => '149',
	'Buyer Lender' => '150',
	'Moving Company' => '151',
	'Roof Inspector' => '152',
	'Pool Inspector' => '153',
	'Chimney Inspector' => '154',
	'CL-100 Inspector' => '155',
	'Home Inspector' => '156',
	'Photographer' => '157',
	'Earnest Money Holder' => '158',
);

$config['PropertyRolesCode'] = array(
	'ALL' => 'All',
	'' => 'Attorney in Fact',
	'' => 'Non Borrower',
	'' => 'Payer',
	'BORR' => 'Borrowers',
	'Borrower' => 'Borrowers',
	'' => 'Sellers',
	'' => 'Buyers',
	'' => 'Listing Agents',
	'' => 'Selling Agents',
	'Loan Officer' => 'Loan Officiers',
	'Servicer' => 'Servicers',
	'OTHERS' => 'Others',
	'' => 'Subscribers',
	'' => 'GAC Counsel',
	'' => 'Underwrites Contact',
	'' => 'Non-Borrowing Spouse',
	'' => 'Non-Borrowing Obligator',
	'' => 'Property Contact',
	'' => 'Processor',
	'' => 'Nationstar',
	'' => 'Payoff Agent',
	'' => 'Broker',
	'' => 'Title Holder - Non',
	'' => 'Title Holder - Borrower',
	'' => 'Borrower - Non Owner',
	'' => 'Proposed Insurer',
	'COAPP' => 'Co Applicant',
	'LEND' => 'Lender',
	'Lender' => 'Lender',

	/* Keystone Property Role Type Added*/
	'Loan Processor' => 'Loan Processor',
	'Appraiser' => 'Appraiser',
	'Attorney' => 'Attorney',
	'Builder' => 'Builder',
	'Closing Agent' => 'Closing Agent',
	'Appraiser Supervisor' => 'Appraiser Supervisor',
	'Correspondent Lender' => 'Correspondent Lender',
	'Gift Donor' => 'Gift Donor',
	'Lender Branch' => 'Lender Branch',
	'Loan Originator' => 'Loan Originator',
	'Notary' => 'Notary',
	'Requesting Party' => 'Requesting Party',
	'Review Appraiser' => 'Review Appraiser',
	'Spouse' => 'Spouse',
	'Third Party Originator' => 'Third Party Originator',
	'Title Company' => 'Title Company',
	'Authorized Representative' => 'Authorized Representative',
	'Authorized Third Party' => 'Authorized Third Party',
	'Bankruptcy Filer' => 'Bankruptcy Filer',
	'Bankruptcy Trustee' => 'Bankruptcy Trustee',
	'Beneficial Interest Party' => 'Beneficial Interest Party',
	'Bill To Party' => 'Bill To Party',
	'Conservator' => 'Conservator',
	'Consumer Reporting Agency' => 'Consumer Reporting Agency',
	'Cooperative Company' => 'Cooperative Company',
	'Cosigner' => 'Cosigner',
	'Credit Counseling Agent' => 'Credit Counseling Agent',
	'Credit Enhancement Risk Holder' => 'Credit Enhancement Risk Holder',
	'Custodian Note PayTo' => 'Custodian Note PayTo',
	'Defendant' => 'Defendant',
	'Deliver Rescission To' => 'Deliver Rescission To',
	'Housing Counseling Agency' => 'Housing Counseling Agency',
	'Designated Contact' => 'Designated Contact',
	'ENote Controller' => 'ENote Controller',
	'ENote Controller Transferee' => 'ENote Controller Transferee',
	'ENote Custodian' => 'ENote Custodian',
	'ENote Custodian Transferee' => 'ENote Custodian Transferee',
	'ENote Delegatee For Transfers' => 'ENote Delegatee For Transfers',
	'ENote Registering Party' => 'ENote Registering Party',
	'ENote Servicer' => 'ENote Servicer',
	'ENote Servicer Transferee' => 'ENote Servicer Transferee',
	'ENote Transfer Initiator' => 'ENote Transfer Initiator',
	'Executor' => 'Executor',
	'FHASponsor' => 'FHASponsor',
	'Flood Certificate Provider' => 'Flood Certificate Provider',
	'Fulfillment Party' => 'Fulfillment Party',
	'Housing Counseling Agent' => 'Housing Counseling Agent',
	'Grantee' => 'Grantee',
	'Grantor' => 'Grantor',
	'Hazard Insurance Agent' => 'Hazard Insurance Agent',
	'HUD1 Settlement Agent' => 'HUD1 Settlement Agent',
	'Homeowners Association' => 'Homeowners Association',
	'Interviewer' => 'Interviewer',
	'Interviewer Employer' => 'Interviewer Employer',
	'Investor' => 'Investor',
	'IRS Tax Form ThirdParty' => 'IRS Tax Form ThirdParty',
	'Law Firm' => 'Law Firm',
	'Loan Closer' => 'Loan Closer',
	'Lien Holder' => 'Lien Holder',
	'Loan Delivery File Preparer' => 'Loan Delivery File Preparer',
	'Loan Funder' => 'Loan Funder',
	'Loan Origination Company' => 'Loan Origination Company',
	'Loan Seller' => 'Loan Seller',
	'Loan Underwriter' => 'Loan Underwriter',
	'Loss Payee' => 'Loss Payee',
	'Management Company' => 'Management Company',
	'MI Company' => 'MI Company',
	'NonTitle NonSpouse OwnershipInterest' => 'NonTitle NonSpouse OwnershipInterest',
	'NonTitle Spouse' => 'NonTitle Spouse',
	'Note Pay To' => 'Note Pay To',
	'Note Pay To Recipient' => 'Note Pay To Recipient',
	'Payee' => 'Payee',
	'Plaintiff' => 'Plaintiff',
	'Pool Insurer' => 'Pool Insurer',
	'Pool Issuer' => 'Pool Issuer',
	'Pool Issuer Transferee' => 'Pool Issuer Transferee',
	'Prepared By' => 'Prepared By',
	'Project Developer' => 'Project Developer',
	'Project Management Agent' => 'Project Management Agent',
	'Property Access Contact' => 'Property Access Contact',
	'Property Jurisdictional Authority' => 'Property Jurisdictional Authority',
	'Property Owner' => 'Property Owner',
	'Property Preservation Agent' => 'Property Preservation Agent',
	'Property Purchaser' => 'Property Purchaser',
	'Receiving Party' => 'Receiving Party',
	'Registry Operator' => 'Registry Operator',
	'Regulatory Agency' => 'Regulatory Agency',
	'Responding Party' => 'Responding Party',
	'Respond To Party' => 'Respond To Party',
	'Return To' => 'Return To',
	'Security Issuer' => 'Security Issuer',
	'Service Bureau' => 'Service Bureau',
	'Service Provider' => 'Service Provider',
	'Servicer Payment Collection' => 'Servicer Payment Collection',
	'Settlor' => 'Settlor',
	'Submitting Party' => 'Submitting Party',
	'Taxable Party' => 'Taxable Party',
	'Tax Assessor' => 'Tax Assessor',
	'Client' => 'Client',
	'Assign From' => 'Assign From',
	'Assign To' => 'Assign To',
	'Title Holder' => 'Title Holder',
	'Title Underwriter' => 'Title Underwriter',
	'Trust' => 'Trust',
	'Trust Beneficiary' => 'Trust Beneficiary',
	'Trustee' => 'Trustee',
	'Unspecified' => 'Unspecified',
	'Warehouse Lender' => 'Warehouse Lender',
	'Witness' => 'Witness',
	'Tax Collector' => 'Tax Collector',
	'Tax payer' => 'Tax payer',
	'Tax Service Provider' => 'Tax Service Provider',
	'Tax Servicer' => 'Tax Servicer',
	'Third Party Investor' => 'Third Party Investor',
	'Document Custodian' => 'Document Custodian',
	'Property Seller' => 'Property Seller',
	'Real Estate Agent' => 'Real Estate Agent',
	'Mortgage Broker' => 'Mortgage Broker',
	'Buyer Client' => 'Buyer Client',
	'Buyer List Agent' => 'Buyer List Agent',
	'Buyer Lender' => 'Buyer Lender',
	'Moving Company' => 'Moving Company',
	'Roof Inspector' => 'Roof Inspector',
	'Pool Inspector' => 'Pool Inspector',
	'Chimney Inspector' => 'Chimney Inspector',
	'CL-100 Inspector' => 'CL-100 Inspector',
	'Home Inspector' => 'Home Inspector',
	'Photographer' => 'Photographer',
	'Earnest Money Holder' => 'Earnest Money Holder',
);

$config['WorkflowModule'] = array(
	'Assessment'=> '2',
	'Mortgage'=> '2',
	'Deed'=>'2',
	'PropertyInfo'=>'2',
	'Exception'=> '2',
	'Taxes'=>'3',
	'OrderSearch'=>'1',
);

$config['WorkflowModuleUID'] = array(
	'OrderSearch'=>'1',
	'Typing'=> '2',
	'TaxCert'=>'3',
	'Review'=>'4',
	'Printing' => '5',
	'Document_Review' => '6',
	'Impediments' => '7',
	'AOM' => '8',
	'Scheduling' => '9',
	'Signing' => '10',
	'Shipping' => '11',
	'Bill_Complete' => '12'
);

$config['NotAssignableWorkflows'] = array(
	'Printing' => '5'
);

$config['BROWSER_DEFAULT_VERSION']=array(
	"IE"=>11.0,
	"Chrome"=>67.0,
	"Mozilla"=>61.0,
	"Safari"=>11.0,
);

$config['BROWSERS']=array(
	"IE"=>'Internet Explorer',
	"Chrome"=>'Chrome',
	"Mozilla"=>'Mozilla',
	"Safari"=>'Safari',
);

$config['JPMSubProduct']=array(
	"1"=>"Search",
	"19"=>"L&V (Field)",
	"22"=>"L&V (Online)",
);

// Orderimport subjects
$config["import_subject"] = "direct2title-order-import-mail-";
$config["reimport_subject"] = "direct2title-order-reimport-mail-";
$config['Captcha']=array(			'img_path' => './uploads/captcha/',
	'img_url' => base_url().'uploads/captcha/',
	'expiration' => 7200,
	'word_lenght' => 8,
	'font_size' => 22);

$config['CustomerNumber'] = [
	'Chase Bank' => '11111222',
];

$config['Products'] = [
	'Closing' => ['Closing'],
];

$config['ProductUIDs'] = [
	'Closing' => [12],
];

$config['SubProducts'] = [
	'Onlilne L&V & Closing' => 70,
	'Field L&V & Closing' => 126,
];

$config['SubProductUIDs'] = [
	'Online L&V' => 22,
	'Field L&V' => 19,
];

$config['Closign Current Queue'] = [
	0 => (object)['Queue' => 'New', 'color'=>'#4285f4'],
	1 => (object)['Queue' => 'Signing Complete', 'color'=>'#6600EF'],
	2 => (object)['Queue' => 'Shipping Complete ', 'color'=>'#ff9800'],
	3 => (object)['Queue' => 'Schedule Complete', 'color'=>'#2ECC71'],
	4 => (object)['Queue' => 'Settlement Agent Assigned', 'color'=>'#E67E22'],
	5 => (object)['Queue' => 'Signing Cancelled', 'color'=>'#EE3322'],
	
	6 => (object)['Queue' => 'Doc Send to Lender', 'color'=>'#428933'],
	7 => (object)['Queue' => 'Post Closing QC', 'color'=>'#428933'],
	8 => (object)['Queue' => 'Critical Docs Back', 'color'=>'#428933'],
	9 => (object)['Queue' => 'Signing Confirmed', 'color'=>'#428933'],
	10 => (object)['Queue' => '2Hours Signing', 'color'=>'#428933'],
	11 => (object)['Queue' => 'Logistics Call', 'color'=>'#428933'],
	12 => (object)['Queue' => 'Docs Send', 'color'=>'#428933'],
	13 => (object)['Queue' => 'Vendor Assigned', 'color'=>'#428933'],
	14 => (object)['Queue' => 'Pre Closing QC', 'color'=>'#428933'],
	15 => (object)['Queue' => 'Shipping InProgress ', 'color'=>'#ff9800'],
	16 => (object)['Queue' => 'Order Completed', 'color'=>'#0ca931'],
	17 => (object)['Queue' => 'Cancelled', 'color'=>'#EC0808'],

];


$config['SuperAccess'] = [1,2,3,4,5,6];

$config['WorkflowStatus'] = [

	'Assigned' => 0,
	'InProgress' => 3,
	'Onhold' => 4,
	'Completed' => 5,
];


$config['ClosingWorkflows'] = [
	'Scheduling' => '9',
	'Signing' => '10',
	'Shipping' => '11'

];

$config['AutomationEmailTAT'] = 24;

// END OF FILE
/*MENU WORKFLOWS*/
/*1-Search,2-Typing,3-TaxCert,4-Review,5-Printing,6-Document Review,7-Impediments,8-Package & AOM*/
$config['Menu_Workflows'] = '1,2,3,6,8';
$config['pricing_customer_Approvaloveridefunctionarray'] = array('CustomerPricingOverride','CustomerPricingAdjustments');
$config['pricing_customer_Approvalfunctionarray'] = array('CustomerActualPricing','CustomerPricingOverride','CustomerPricingAdjustments','Grading A','Grading B','Grading C','Grading D');
$config['pricing_customer_Approvalfunctionall'] = "'" . implode ( "', '", $config['pricing_customer_Approvalfunctionarray'] ) . "'";
$config['pricing_customer_Approvalfunctionarray'] = array('CustomerPricingOverride','CustomerPricingAdjustments','Grading A','Grading B','Grading C','Grading D');
$config['pricing_customer_Approvalfunction'] = "'" . implode ( "', '", $config['pricing_customer_Approvalfunctionarray'] ) . "'";

$config['pricing_customer_Approvalfunctiongradingarray'] = array('Grading A','Grading B','Grading C','Grading D');
$config['pricing_customer_Approvalfunctiongrading'] = "'" . implode ( "', '", $config['pricing_customer_Approvalfunctiongradingarray'] ) . "'";


// END OF FILE


$config['mDocType'] = array('Note'=>1,'1st Mortgage'=>2,'2nd Mortgage'=>3,'Subordination'=>4,'Mobile Home'=>5,'Corrective Assignment'=>6,'Gap Assignment'=>7,'Title Policy Endorsement'=>8,'Title Policy Missing'=>9,'Prior Lien - Lien Release'=>10);

//EmailUploadImport keyfiles
$config["emailuploadimport_matchingtext"] = "Vendor Closing Notification Email";
$config["emailuploadreimport_matchingtext"] = "Change to Previous Order";

/* D-2-T9 GENEARATE PACKAGE NUMBER BASED ON LOAN NUMBER*/
$config['PackageCode'] = 'D2T-';

$config['date_format'] = 'm/d/Y h:i A';
$config['dateonly_format'] = 'm/d/Y';
$config['excel_date_format'] = 'm-d-Y';
$config['backup_date_format'] = 'm-d-Y_H-i-s';
$config['mysql_date_format'] = '%m/%d/%Y %h:%i %p';


/* Start - Shipping Master Data */

$config['ShipServiceCode'] = array(
/*'01' => 'Next Day Air',
'02' => '2nd Day Air',
'03' => 'Ground',
'07' => 'Express',
'08' => 'Expedited',
'11' => 'UPS Standard',
'12' => '3 Day Select',
'13' => 'Next Day Air Saver',
'14' => 'UPS Next Day Air® Early',
'17' => 'UPS Worldwide Economy Lite',
'54' => 'Express Plus',
'59' => '2nd Day Air A.M.',
'65' => 'UPS Saver',
'M2' => 'First Class Mail',
'M3' => 'Priority Mail',
'M4' => 'Expedited MaiI Innovations',
'M5' => 'Priority Mail Innovations',
'M6' => 'Economy Mail Innovations',
'M7' => 'MaiI Innovations (MI) Returns',
'70' => 'UPS Access Point™ Economy',
'71' => 'UPS Worldwide Express Freight Midday',
'72' => 'UPS Worldwide Economy',
'74' => 'UPS Express®12:00',
'82' => 'UPS Today Standard',
'83' => 'UPS Today Dedicated Courier',
'84' => 'UPS Today Intercity',
'85' => 'UPS Today Express',
'86' => 'UPS Today Express Saver',
'96' => 'UPS Worldwide Express Freight'*/


'UPS Standard' => '11' ,
'UPS Worldwide Expedited' => '08' ,
'UPS Worldwide Express' => '07' ,
'UPS Worldwide Express Plus' => '54' ,
'UPS Worldwide Saver' => '65' ,
'UPS Worldwide Economy DDP' => '72' ,
'UPS Worldwide Economy DDU' => '17' ,
'UPS 2nd Day Air' => '02' ,
'UPS 2nd Day Air A.M.' => '59' ,
'UPS 3 Day Select' => '12' ,
'UPS Expedited Mail Innovations' => 'M4' ,
'UPS First-Class Mail' => 'M2' ,
'UPS Ground' => '03' ,
'UPS Next Day Air' => '01' ,
'UPS Next Day Air Early' => '14' ,
'UPS Next Day Air Saver' => '13' ,
'UPS Priority Mail' => 'M3' ,
'UPS Expedited' => '02' ,
'UPS Express Saver' => '13' ,
'UPS Access Point Economy' => '70' ,
'UPS Express' => '01' ,
'UPS Express Early' => '14' ,
'UPS Express Saver' => '65' ,
'UPS Express Early' => '54' ,
'UPSTM Worldwide Economy DDP' => '72' ,
'UPSTM Worldwide Economy DDU' => '17' ,
'UPS Expedited' => '08' ,
'UPS Express' => '07' ,
'UPS Express®12:00' => '74' ,
'UPSTM Economy DDP' => '72' ,
'UPSTM Economy DDU' => '17' ,
'UPS Express Plus' => '54' ,
'UPS Today Dedicated Courier' => '83' ,
'UPS Today Express' => '85' ,
'UPS Today Express Saver' => '86' ,
'UPS Today Standard' => '82' ,
'UPS Worldwide Express Freight' => '96' ,
'UPS Priority Mail Innovations'=> 'M5 ' ,
'UPS Economy Mail Innovations' => 'M6' ,
'UPS Worldwide Express Freight Mid-day' => '71' ,
);

$config['PackingWeight'] = array(
'UPS' => '150',
'FedEx' => '200',
'DHL' => '100',
);

$config['PackagingCode'] = array(
'01' =>'UPS Letter',
'02' =>'Customer Supplied Package',
'03' =>'Tube ',
'04' =>'PAK',
'21' =>'UPS Express Box',
'24' =>'UPS 25KG Box',
'25' =>'UPS 10KG Box',
'30' =>'Pallet',
'2a' =>'Small Express Box',
'2b' =>'Medium Express Box',
'2c' =>'Large Express Box',
'56' =>'Flats',
'57' =>'Parcels',
'58' =>'BPM',
'59' =>'First Class',
'60' =>'Priority',
'61' =>'Machineables',
'62' =>'Irregulars',
'63' =>'Parcel Post',
'64' =>'BPM Parcel',
'65' =>'Media Mail',
'66' =>'BPM Flat',
'67' =>'Standard Flat',
);

$config['UPSTrackingStatus'] = array(
'I' => 'In Transit',
'D' => 'Delivered',
'X' => 'Exception',
'P' => 'Pickup',
'M' => 'Manifest Pickup',
);

/* End - Shipping Master Data */


$config['TaskStatus'] = array(
'Open' => 'Task Open',
'Completed' => 'Task Complete',
'Reopen' => 'Task Reopen',
'Delete' => 'Task Delete',
);

$config['WorkflowLevelStatus'] = array(
 'Workflow Assign' => 'Workflow Assign',
 'Workflow Complete' => 'Workflow Complete',

);

$config['OrderLevelStatus'] = array(
'Order Open' => 'Order Open',
'Completed' => 'Completed',

);


/* Start - X1 Master Data */

$config['TransactionType'] = array(
'Refinance' => 'Refinance',
'Equity' => 'Equity',
'EquityLV' => 'EquityLV',
'EquityPR' => 'EquityPR',
'EquityPRU' => 'EquityPRU',
'Purchase' => 'Purchase',
'Modification' => 'Modification',
'Modification2' => 'Modification2',
'Pre-Qual' => 'Pre-Qual',
'Assessor' => 'Assessor',
'ReverseMortgage' => 'ReverseMortgage',
'Other' => 'Other',
);

$config['BorrowerType'] = array(
'Individual' => 'Individual',
'Corporation' => 'Corporation',
'partnership' => 'partnership',
'LLC' => 'LLC',
);

$config['PropertyType'] = array(
"Single Family" => "Single Family",
"SFR" => "SFR",
"PUD" => "PUD",
"Condominium" => "Condominium",
"Condo" => "Condo",
"2-4 Plex" => "2-4 Plex",
"Cooperative" => "Cooperative",
"Unimproved" => "Unimproved",
"Vacant Land" => "Vacant Land",
"Multiple Family Residence" => "Multiple Family Residence",
"Commercial" => "Commercial",
"Property" => "Property",
"Mobile Homes" => "Mobile Homes",
"Apartment" => "Apartment",
"Agriculture" => "Agriculture",
"Indian land" => "Indian land",
"Leased Land" => "Leased Land",
"Gov't Land" => "Gov't Land",
"Other" => "Other",
);

/* End - X1 Master Data */


$config['VendorTypes'] = [
"Abstractor" => "Abstractor",
"Attorney" => "Attorney",
"Notary" => "Notary",
];

$config['SLAactionNoted'] = [
"2" => [
'firstnoted' => '32',
'lastnoted' => '33',],
"5" => [
'firstnoted' => '34',
'lastnoted' => '35',],
"8" => [
'firstnoted' => '36',
'lastnoted' => '37',],
"11" => [
'firstnoted' => '38',
'lastnoted' => '39',],
"27" => [
'firstnoted' => '40',
'lastnoted' => '41',],
"14" => [
'firstnoted' => '42',
'lastnoted' => '43',],
"17" => [
'firstnoted' => '44',
'lastnoted' => '45',],
"20" => [
'firstnoted' => '46',
'lastnoted' => '47',],
];

$config['SLAaction'] = [
	"Order Placed" => "1",
	'WorkflowModuleUID' => [
		'1' => [
			'Opened' => "2",
			'Assigned' => "3",
			'Completed' => "4",
			'Vendor_Opened' => "5",
			'Vendor_Assigned' => "6",
			'Vendor_Completed' => "7",
		],
		'2' => [
			'Opened' => "8",
			'Assigned' => "9",
			'Completed' => "10",
		],
		'3' => [
			'Opened' => "11",
			'Assigned' => "12",
			'Completed' => "13",
		],
		'4' => [
			'Opened' => "27",
			'Assigned' => "28",
			'Completed' => "29",
		],
		'9' => [
			'Opened' => "14",
			'Assigned' => "15",
			'Completed' => "16",
		],
		'10' => [
			'Opened' => "17",
			'Assigned' => "18",
			'Completed' => "19",
		],
		'11' => [
			'Opened' => "20",
			'Assigned' => "21",
			'Completed' => "24",
		],
	],
	"Completed" => "30",
	"Billed" => "31",
];

// Default User (System User)
$config['DefaultUserLoginID'] = 'isgn';

// Default FollowUpType (for Issue FollowUp Creation)
$config['DefaultFollowUpType'] = 'IssueFollowUp';

// Default Priority (for Issue FollowUp Creation)
$config['DefaultPriority'] = 'Normal';

//Reverse Mortgage Document Status
$config['RMS_DocumentStatus'] = ['Active - WIP','RMS Action Needed - WIP','PIF - Closed and Billed','Cancelled - Closed and Billed','Foreclosure - Closed and Billed','Packaged and Submitted - Closed and Billed','Default - default','Closed - Closed and Billed','Purgatory - Closed and Billed','Default - Conveyed Title','Default - Death','Default - Insurance','Default - Occupancy','Default - T&I','Default - Taxes','REO - Pre Claim','Foreclosure','Missing Critical Docs'];

/* Keystone Title Events */
$config['TitleEvents'] = [
	"DeliverPolicy" => (Object)['EventType' => 'Doc',
								 'Description' => 'Final Title Policy '],
	"DeliverProduct" => (Object)['EventType' => 'Doc',
								 'Description' => 'Product Delivered by the Vendor'],
	"ClearedTitle" => (Object)['EventType' => 'Comment',
								 'Description' => 'Curative Cleared'],
	"CurativePending" => (Object)['EventType' => 'Comment',
								 'Description' => 'Curative Pending'],
	"InternalSubordination-NotificationToLender" => (Object)['EventType' => 'Comment',
								 'Description' => 'Internal Subordination–  Notification to Lender'],
	"InternalSubordination-ReceivedByTitleProvider" => (Object)['EventType' => 'Comment',
								 'Description' => 'Internal Subordination–Received by Title Provider'],
	"InternalSubordination-ApprovedByTitleProvider" => (Object)['EventType' => 'Comment',
								 'Description' => 'Internal Subordination–Approved by Title Provider'],
	"InternalSubordination-NotApprovedByTitleProvider" => (Object)['EventType' => 'Doc',
								 'Description' => 'Internal Subordination–Not Approved by Title Provider'],
	"ExternalSubordination-NotificationToLender" => (Object)['EventType' => 'Comment',
								 'Description' => 'External Subordination–Notification to Lender'],
	"ExternalSubordination-SubPackageSentToJLH" => (Object)['EventType' => 'Doc',
								 'Description' => 'External Subordination–Sub Package Sent to JLH'],
	"ExternalSubordination-ReceivedFromJLH" => (Object)['EventType' => 'Comment',
								 'Description' => 'External Subordination–Received from JLH'],
	"ExternalSubordination-ApprovedByTitleProvider" => (Object)['EventType' => 'Comment',
								 'Description' => 'External Subordination–Approved by Title Provider'],
	"ExternalSubordination-RevisionDelivered" => (Object)['EventType' => 'Doc',
								 'Description' => 'External Subordination-Revision Delivered'],
];

/* Keystone Closing Events */
$config['ClosingEvents'] = [
	"OrderedPayoff" => (Object)['EventType' => 'Payoff', 'Description' => 'Payoff Ordered'],
	"CompletedPayoff" => (Object)['EventType' => 'PayoffDoc', 'Description' => 'Payoff Delivered'],
	"SentToRecord" => (Object)['EventType' => 'RecordSet', 'Description' => 'Recording submitted by Vendor'],
	"DocShipInfo" => (Object)['EventType' => 'RecordSet', 'Description' => 'Documents Shipped Information'],
	"CompletedRecording" => (Object)['EventType' => 'Recording', 'Description' => 'Recording Completed by vendor'],
	"DeliverVendorSS" => (Object)['EventType' => 'Doc', 'Description' => 'Closing Statement Delivered'],
	"CompleteSSRevision" => (Object)['EventType' => 'Doc', 'Description' => 'Closing Statement Modifications Complete'],
	"DeliverFinalSS" => (Object)['EventType' => 'Doc', 'Description' => 'Closing Statement Distributed'],
	"CompletedSigning" => (Object)['EventType' => 'Comment', 'Description' => 'Closing Documents Executed'],
	"FailedSigning" => (Object)['EventType' => 'Comment', 'Description' => 'Closing Documents Not Executed'],
	"AddExecutedDocs" => (Object)['EventType' => 'Doc', 'Description' => 'Critical docs  uploaded'],
	"ReceiveClosingPackage" => (Object)['EventType' => 'Comment', 'Description' => 'Document Package Received by Vendor'],
	"ClearToFund" => (Object)['EventType' => 'Comment', 'Description' => 'Clear to Fund'],
	"FundedLoanByVendor" => (Object)['EventType' => 'Comment', 'Description' => 'The Vendor Disbursed Funds'],
	"FundsReceived" => (Object)['EventType' => 'Comment', 'Description' => 'Funds Received by Vendor'],
	"ScheduledSigning" => (Object)['EventType' => 'Closing', 'Description' => 'Signing Scheduled'],
	//"RescheduledSigning" => (Object)['EventType' => 'Closing', 'Description' => 'Singing Rescheduled'],
];

$config['mVendorTypes'] = array(
'Abstractor'=>'1',
'Attorney'=>'2',
'Notary'=>'3',
);


$config['ProcessesUID'] = [
	"Order Search"		=>	1,
	"Assessment"		=>	2,
	"Deed"				=>	3,
	"Judgements"		=>	4,
	"Legal Description"	=>	5,
	"Address"			=>	6,
	"Mortgage"			=>	7,
	"Property Info"		=>	8,
	"Lein"				=>	9,
	"Taxes"				=>	10,
	"AOM"				=>	11,
	"Document Review"	=>	12,
	"Scheduling"		=>	13,
	"Signing"			=>	14,
	"Shipping"			=>	15,

];

// BillingPeriods 
$config['BillingPeriods'] = [
	'Immediate',
	'1st of every month',
	'last day of the month',
	'15th and last day of every month',
	'1st and 15th of every month',
	'monday of every week',
	'friday of every week'
];


$config['vendorsupport']  = 'VendorSupport@sourcepointmortgage.com';


/* Starts - Keystone Version 2 Manual Events*/
$config['Version2TitleEvents'] = [
	"DeliverPolicy" => (Object)['EventType' => 'Doc', 'Description' => 'Final Title Policy '],
	"DeliverProduct" => (Object)['EventType' => 'Doc', 'Description' => 'Product Delivered by the Vendor'],
	"ClearedTitle" => (Object)['EventType' => 'Comment', 'Description' => 'Curative Cleared'],
	"CurativePending" => (Object)['EventType' => 'Comment', 'Description' => 'Curative Pending'],
	"InternalSubordination-NotificationToLender" => (Object)['EventType' => 'Comment', 'Description' => 'Internal Subordination–  Notification to Lender'],
	"InternalSubordination-ReceivedByTitleProvider" => (Object)['EventType' => 'Comment', 'Description' => 'Internal Subordination–Received by Title Provider'],
	"InternalSubordination-ApprovedByTitleProvider" => (Object)['EventType' => 'Comment', 'Description' => 'Internal Subordination–Approved by Title Provider'],
	"InternalSubordination-NotApprovedByTitleProvider" => (Object)['EventType' => 'Doc', 'Description' => 'Internal Subordination–Not Approved by Title Provider'],
	"ExternalSubordination-NotificationToLender" => (Object)['EventType' => 'Comment', 'Description' => 'External Subordination–Notification to Lender'],
	"ExternalSubordination-SubPackageSentToJLH" => (Object)['EventType' => 'Doc', 'Description' => 'External Subordination–Sub Package Sent to JLH'],
	"ExternalSubordination-ReceivedFromJLH" => (Object)['EventType' => 'Comment', 'Description' => 'External Subordination–Received from JLH'],
	"ExternalSubordination-ApprovedByTitleProvider" => (Object)['EventType' => 'Comment', 'Description' => 'External Subordination–Approved by Title Provider'],
	"ExternalSubordination-RevisionDelivered" => (Object)['EventType' => 'Doc', 'Description' => 'External Subordination-Revision Delivered'],
];

/* Keystone Closing Events */
$config['Version2ClosingEvents'] = [
	"OrderedPayoff" => (Object)['EventType' => 'Payoff', 'Description' => 'Payoff Ordered'],
	"CompletedPayoff" => (Object)['EventType' => 'PayoffDoc', 'Description' => 'Payoff Delivered'],
	"SentToRecord" => (Object)['EventType' => 'RecordSet', 'Description' => 'Recording submitted by Vendor'],
	"CompletedRecording" => (Object)['EventType' => 'Recording', 'Description' => 'Recording Completed by vendor'],
	"DeliverVendorSS" => (Object)['EventType' => 'Doc', 'Description' => 'Closing Statement Delivered'],
	"CompleteSSRevision" => (Object)['EventType' => 'Doc', 'Description' => 'Closing Statement Modifications Complete'],
	"DeliverFinalSS" => (Object)['EventType' => 'Doc', 'Description' => 'Closing Statement Distributed'],
	"CompletedSigning" => (Object)['EventType' => 'Comment', 'Description' => 'Closing Documents Executed'],
	"FailedSigning" => (Object)['EventType' => 'Comment', 'Description' => 'Closing Documents Not Executed'],
	"AddExecutedDocs" => (Object)['EventType' => 'Doc', 'Description' => 'Critical docs  uploaded'],
	"ReceiveClosingPackage" => (Object)['EventType' => 'Comment', 'Description' => 'Document Package Received by Vendor'],
	"ClearToFund" => (Object)['EventType' => 'Comment', 'Description' => 'Clear to Fund'],
	"FundedLoanByVendor" => (Object)['EventType' => 'Comment', 'Description' => 'The Vendor Disbursed Funds'],
	"FundsReceived" => (Object)['EventType' => 'Comment', 'Description' => 'Funds Received by Vendor'],
	"ScheduleConfirmed" => (Object)['EventType' => 'Closing', 'Description' => 'Schedule Confirmed'],
	"RescheduleConfirmed" => (Object)['EventType' => 'Closing', 'Description' => 'Reschedule Confirmed'],
	"RescheduleRequired" => (Object)['EventType' => 'Comment', 'Description' => 'Reschedule Required'],
	"RescheduleRequiredConfirmed" => (Object)['EventType' => 'Comment', 'Description' => 'Reschedule Required Confirmed'],
	"VendorDisbursedFunds" => (Object)['EventType' => 'Comment', 'Description' => 'Vendor Disbursed Funds'],
	"AttorneyDocsFinalized" => (Object)['EventType' => 'Doc', 'Description' => 'Attorney Docs Finalized'],
	"ClosingPackageSentToNotary" => (Object)['EventType' => 'RecordSet', 'Description' => 'Closing Package Sent to Notary'],
	"ClosingPackageApprovedByVendor" => (Object)['EventType' => 'Comment', 'Description' => 'Closing Package Approved By Vendor'],
	"ClosingPackageRejectedByVendor" => (Object)['EventType' => 'Comment', 'Description' => 'Closing Package Rejected By Vendor '],
	"NotaryAssigned" => (Object)['EventType' => 'NotaryAssigned', 'Description' => 'Notary Assigned']
];

/* Starts - Keystone Version 2 Manual Events*/

/* Starts - General DocType Values */

$config['DefaultDoctypes'] = [
	'Reports'=>'Reports',
	'Search'=>'Search Package',
	'Final Reports'=>'Final Reports',
	'Flood Certificate'=>'Flood Certificate',
	'Others'=>'Others',
	'ClosingPackage'=>'Closing Package',
	'Doc Sent'=>'Doc Sent',
	'Screenshot'=>'Screenshot',
	'Order Form'=>'Order Form', 
	'Supplier Cover Letter'=>'Supplier Cover Letter',
	'Signed Package'=>'Signed Package', 
	'Shipping Label'=>'Shipping Label',
	'DecisionReport'=>'Decision Report',
	'AuxiliaryReport'=>'Auxiliary Report',
	'X1FinalReport'=>'X1 Final Report',
];

/* Ends - General DocType Values */

/* Starts - RealEC DocType Values */

$config['DocType460'] = [
'BORRAUTH' => 'Borrower Authorization ',
'CLEARTOCLOSE' => 'Clear to Close',
'CLOSINGPROTECTIONLETTER' => 'Closing Protection Letter',
'CLSTMT' => 'Closing Statements',
'CURATIVE' => 'Curative Documentation ',
'DEEDOFTRUST' => 'Deed of Trust',
'EQUITYPOLICY' => 'Equity Policy',
'EXECPROCESSDISCLOSURE' => 'Executed Processing Disclosure',
'FEESHT' => 'Fee Sheet ',
'FORECLOSESRCH' => 'Foreclosure Search / Report',
'FPOL' => 'Final Policy',
'HUD1' => 'HUD1 (Final)',
'HUD1A' => 'HUD1A (Final)',
'HUD1APRE' => 'HUD1A (Preliminary) ',
'HUD1PRE' => 'HUD1 (Preliminary) ',
'LASTDEED' => 'Last Deed of Record',
'LCP' => 'Limited Coverage Policy',
'LENDPOLICY' => 'Lenders Policy',
'LGLDESC' => 'Legal Description',
'LIFELOANCERT' => 'Life of Loan Certificate',
'LOANAPPLICATION' => 'Loan Application',
'LOANMODAGREE' => 'Loan Modification Agreement',
'MARRIAGECERTIFICATE' => 'Marriage Certificate',
'NOTE' => 'Note',
'OERPT' => 'O & E Report',
'ORIGTAP' => 'Original TAP File',
'OTHER' => 'Other',
'OWNPOLICY' => 'Owners Policy',
'PAYOFF' => 'PayOff',
'PRELIMRPT' => 'Preliminary Title Report',
'PURCHAGREE' => 'Purchase Agreement',
'SECURITYINSTRUMENT' => 'Security Instrument',
'SECURITYINSTRUMENTMOD' => 'Security Instrument Mod',
'SERVINV' => 'Service Invoice',
'SETTLSTMT' => 'Settlement Statements',
'SUPPL1' => 'Supplement 1',
'SUPPL2' => 'Supplement 2',
'TAXDOC' => 'Tax Document',
'TAXLINEDATA' => 'Tax Line Data',
'TITLEINSURANCEPOLICY' => 'Title Insurance Policy',
'TITLESRCH' => 'Title Search',
'TRAILDOCS' => 'Trailing Documents',
'TRUSTOPIN' => 'Trust Opinion ',
'VESTING' => 'Vesting Information',
'Others' => 'Others',
];

/* Ends - RealEC DocType Values */


/* Starts - Keystone DocType Values */

$config['KeystoneDocTypes'] = [
"WiringInstructions" => "Wiring Instructions",
"FeeSheet" => "Fee Sheet",
"ClosingProtectionLetter" => "Closing Protection Letter",
"DeedofTrust" => "Deed of Trust",
"LastDeedOfRecord" => "Last Deed of Record",
"OEReport" => "O & E Report",
"TaxCertificate" => "Tax Document",
"PUD" => "PUD",
"PhotoID" => "Photo ID",
"MarriageCertificate" => "M. Cert",
"AoAff" => "AoAff",
"BankruptcyDoc" => "Bankruptcy Doc",
"Survey" => "Survey",
"PrelimTitleReport" => "Preliminary Title Report",
"CurativeDocumentation" => "Curative Documentation",
"ClearToClose" => "Clear to Close",
"VestingInformation" => "Vesting Information",
"CriticalPostClosingDocuments" => "Critical Post Closing Documents",
"DisbursementLedger" => "Disbursement Ledger",
"DeathCertificate" => "Death Certificate",
"DFC Document" => "DFC Document",
"DivorceDecree" => "Divorce Decree",
"Title Commitment" => "Final – Title Commitment",
"LimitedPowerofattorney" => "Limited Power of attorney (LPOA)",
"SettlementStatement" => "Settlement Statements",
"SubordinationAgreement" => "Subordination Agreement",
"PayoffDemand" => "Payoff Demand",
"Other" => "Other",
"CDCollaborationInstructions" => "CD Collaboration Instructions",
"ClosingDisclosure" => "Closing Disclosure",
"ClosingInstructions" => "Closing Instructions",
"CDCollaborationApproved" => "CD Collaboration Approved",
"CollaborationAdditionalAttachment" => "Collaboration Additional Attachment",
"ClosingDisclosureApproved" => "Closing Disclosure - Approved",
"FinalPolicy" => "Final Policy",
"TitleCommitment" => "Title Commitment",
"ExecutedClosingPackage" => "Executed Closing Package",
"Payoff" => "Payoff",
"Judgs" => "Judgs",
"CSArrears" => "CSArrears",
"MtgCopies" => "Mtg Copies",
"AssessorDoc" => "Assessor Doc",
"MunicipalSearch" => "Municipal Search",
"PatriotSearch" => "Patriot Search",
"DeathCert" => "Death Cert",
"JudgmentPayoff" => "Judgment Payoff",
"QCD" => "QCD",
"NameChangeDocument" => "Name Change Document",
"DeedRequestForm" => "Deed Request Form",
"TitleCommitmentRevised" => "Title Commitment Revised",
"Borrower’sAuthorization" => "Borrower’s Authorization",
"SubordinationAgreement" => "Subordination Agreement",
"POA" => "POA",
"Application" => "Application",
"CreditInfo" => "Credit Info",
"LienRelease" => "Lien Release",
"TrustDocs" => "Trust Docs",
"DivorceDecree" => "Divorce Decree",
"HUDCD" => "HUD/CD",
"OwnerPolicy" => "Owner’s/ title policy",
"UCC" => "UCC Term",
"Appraisal" => "Appraisal",
"PIF" => "PIF ( Paid in full letter)",
"OHDOR" => "OH DOR form",
];

/* Ends - Keystone DocType Values */

/* D2TINT-44 Starts - Final Reports Keystone DocType Values */
$config['KeystoneTitleFinalDocTypes'] = [
	"PrelimTitleReport" => "Preliminary Title Report",
	"TitleCommitment" => "Title Commitment",
];

$config['KeystoneClosingFinalDocTypes'] = [
	"CDCollaborationApproved" => "CD Collaboration Approved",
	"CollaborationAdditionalAttachment" => "Collaboration Additional Attachment",
	"ClosingDisclosureApproved" => "Closing Disclosure - Approved",
];
/* D2TINT-44 Ends - Final Reports Keystone DocType Values */

/* D2TINT-55 Starts - Add Attachment Keystone DocType Values */
$config['KeystoneDocumentDocTypes'] = [
	"WiringInstructions" => "Wiring Instructions",
	"FeeSheet" => "Fee Sheet",
	"ClosingProtectionLetter" => "Closing Protection Letter",
	"DeedofTrust" => "Deed of Trust",
	"LastDeedOfRecord" => "Last Deed of Record",
	"OEReport" => "O & E Report",
	"TaxCertificate" => "Tax Document",
	"PUD" => "PUD",
	"PhotoID" => "Photo ID",
	"MarriageCertificate" => "M. Cert",
	"AoAff" => "AoAff",
	"BankruptcyDoc" => "Bankruptcy Doc",
	"Survey" => "Survey",
	"PrelimTitleReport" => "Preliminary Title Report",
	"CurativeDocumentation" => "Curative Documentation",
	"ClearToClose" => "Clear to Close",
	"VestingInformation" => "Vesting Information",
	"CriticalPostClosingDocuments" => "Critical Post Closing Documents",
	"DisbursementLedger" => "Disbursement Ledger",
	"DeathCertificate" => "Death Certificate",
	"DFC Document" => "DFC Document",
	"DivorceDecree" => "Divorce Decree",
	"Title Commitment" => "Final – Title Commitment",
	"LimitedPowerofattorney" => "Limited Power of attorney (LPOA)",
	"SettlementStatement" => "Settlement Statements",
	"SubordinationAgreement" => "Subordination Agreement",
	"TaxCertificate" => "Tax Document",
	"PayoffDemand" => "Payoff Demand",
	"Other" => "Other",
];
/* D2TINT-55 Ends - Add Attachment Keystone DocType Values */

/* ******** WorkflowPermissions ******** */
$config['WorkflowPermissions'] = [

	$config['WorkflowModuleUID']['OrderSearch'] => 'OrderSearch',
	$config['WorkflowModuleUID']['Typing'] => 'OrderTyping',
	$config['WorkflowModuleUID']['TaxCert'] => 'OrderTaxCert',
	$config['WorkflowModuleUID']['Review'] => 'OrderReview',
	$config['WorkflowModuleUID']['Printing'] => 'CanPrinting',
	$config['WorkflowModuleUID']['Document_Review'] => 'OrderDocReview',
	$config['WorkflowModuleUID']['Impediments'] => 'OrderImpedience',
	$config['WorkflowModuleUID']['AOM'] => 'OrderAOM',
	$config['WorkflowModuleUID']['Scheduling'] => 'OrderScheduling',
	$config['WorkflowModuleUID']['Signing'] => 'OrderSigning',
	$config['WorkflowModuleUID']['Shipping'] => 'OrderShipping',
	$config['WorkflowModuleUID']['Bill_Complete'] => '',
];

/* ******** X1 Auxiliary Reports ******** */
$config['X1AuxiliaryReports'] = [
	"PropertyHistoryReport" => (Object)['ReportName' => 'Property History Report', 'FileFormat' => 'html'],
	"LenderWorkflowDecisionReport" =>(Object)['ReportName' => 'Lender Workflow Decision Report', 'FileFormat' => 'pdf'],
	"MaskedDecisionReport" => (Object)['ReportName' => 'Masked Decision Report', 'FileFormat' => 'pdf'],
	"GeneralIndexHistoryReport" =>(Object)['ReportName' => 'General Index History Report', 'FileFormat' => 'html'],
	"X1XpressImagesReport" => (Object)['ReportName' => 'X1Xpress Images Report', 'FileFormat' => 'pdf'],
	"PropertyAssessorReport" =>(Object)['ReportName' => 'Property Assessor Report', 'FileFormat' => 'html'],
	"DeedImagesReport" =>(Object)['ReportName' => 'Deed Images Report', 'FileFormat' => 'pdf'],
	"ExtractedDataRecordsReport" =>(Object)['ReportName' => 'Extracted DataRecords Report', 'FileFormat' => 'html'],
	"ExtractedDataRecordsReportXML" =>(Object)['ReportName' => 'Extracted DataRecords Report XML', 'FileFormat' => 'xml'],
	"X1XpressATOReport" =>(Object)['ReportName' => 'X1Xpress ATO Report', 'FileFormat' => 'pdf'],
	"OffshoreReport" =>(Object)['ReportName' => 'Offshore Report', 'FileFormat' => 'pdf'],
	"DocumentImage" =>(Object)['ReportName' => 'Document Image', 'FileFormat' => ''],
];

/* X1 - Lender Messaging */
$config['LenderMessaging'] = [
	"Express" =>"Sourcepoint is in receipt of Order Number <<ORDERNO>>. This order qualifies for Express title processing and your title product shall be returned shortly.",
	"Traditional" =>"Sourcepoint is in receipt of Order Number <<ORDERNO>>. Please note, order does not qualify for Express title processing. We have begun processing the order and shall return title product within standard turn times.",
	"RensponseRemainder" =>"Sourcepoint is in receipt of Order Number <<ORDERNO>>. Please note, unanswered Requests over 3 minutes.",
];


// Reware Triggers Client Request Queue Events
$config['ReswareEvents'] = [
	// RealEC & Stewart
	'220','222','240','303','310', 'cancel_request', 'dispute_request',
	// KeyStone
	'AddNote', 'AddNoteAck', 'UpdateOrderInfo' ,'Escalation', 'OrderReopenRequest', 'RequestRevision', 'CancelOrder'
];



/*** API EVents ****/
/**
* @purpose : For event report
* @author  : D.Samuel Prabhu
* @since   : 28 May 2020
**/ 
//Stewart Events
$config['StewartEvents'] = [
	[ 'EventCode' => 'product_request',	'Description' => 'Service Received'],
	[ 'EventCode' => 'product_reply', 'Description' => 'Standard Data File Delivered by Provider'],
	[ 'EventCode' => 'product_reply_report', 'Description' => 'Product Delivered by Provider'],
	[ 'EventCode' => 'cancel_request', 'Description' => 'Service Cancelled'],
	[ 'EventCode' => 'cancel_complete', 'Description' => 'Service Cancelled - Approval'],
	[ 'EventCode' => 'additional_info_request', 'Description' => 'Service On Hold'],
	[ 'EventCode' => 'additional_info_reply', 'Description' => 'Service Cancelled'],
	[ 'EventCode' => 'dispute_request', 'Description' => 'Exception Raise'],
	[ 'EventCode' => 'product_dispute_reply', 'Description' => 'Clear Disputes'],
	[ 'EventCode' => 'note', 'Description' => 'Comment'],

];

//LoansPQ
$config['LoansPQEvents'] = [	
	[ 'EventCode' => 'OrderRequest', 'Description' => 'Order Request Send to D2T'],
	[ 'EventCode' => 'StatusPoll', 'Description' => 'Check the status of the order'],
	[ 'EventCode' => 'loanspq_pdf_reply', 'Description' => 'PDF delivery'],
	[ 'EventCode' => 'mortgage_info', 'Description' => 'Mortage Info XML'],
	[ 'EventCode' => 'internal_comments', 'Description' => 'Notes'],	
];

//RealEC events
$config['RealECEvents'] = [
	[ 'EventCode' => '100', 'Description' => 'Service Received'],
	[ 'EventCode' => '130', 'Description' => 'Service Confirmed by Provider'],
	[ 'EventCode' => '140', 'Description' => 'Order not Accepted by Provider'],
	[ 'EventCode' => '150', 'Description' => 'Product Delivered by Provider'],
	[ 'EventCode' => '180', 'Description' => 'Document Delivered by Provider'],
	[ 'EventCode' => '220', 'Description' => 'Comment'],
	[ 'EventCode' => '222', 'Description' => 'Comment Action Required'],
	[ 'EventCode' => '230', 'Description' => 'Service On Hold'],
	[ 'EventCode' => '240', 'Description' => 'Service Cancelled'],
	[ 'EventCode' => '260', 'Description' => 'Service Resumed'],
	[ 'EventCode' => '301', 'Description' => 'Schedule Confirmed'],
	[ 'EventCode' => '303', 'Description' => 'Schedule Information Update'],
	[ 'EventCode' => '310', 'Description' => 'Re-Schedule'],
	[ 'EventCode' => '385', 'Description' => 'Standard Data File Delivered by Provider'],
	[ 'EventCode' => '460', 'Description' => 'Document Attached by Lender'],
	[ 'EventCode' => '500', 'Description' => 'Curative Cleared   (PC 3 Only)'],
	[ 'EventCode' => '537', 'Description' => 'Order Fulfillment PDF & Data (Provider Only)'],
	[ 'EventCode' => '690', 'Description' => 'Tracking Information'],
	[ 'EventCode' => '761', 'Description' => 'Document Package Received by Provider'],
	[ 'EventCode' => '765', 'Description' => 'Closing Documents Executed'],
	[ 'EventCode' => '766', 'Description' => 'Closing Documents Not Executed'],
	[ 'EventCode' => '780', 'Description' => 'Final Docs Posted'],
];

//Keystone Events
$config['KeystoneEvents'] = [
	[ 'EventCode' => 'AcceptOrder', 'Description' => 'Service Confirmed by Vendor'],
	[ 'EventCode' => 'AddAttachment', 'Description' => 'Document Upload'],
	[ 'EventCode' => 'AddExecutedDocs', 'Description' => 'Critical docs  uploaded'],
	[ 'EventCode' => 'AddNote', 'Description' => 'Comment'],
	[ 'EventCode' => 'AddNoteAck', 'Description' => 'Comment Acknowledgment Required'],
	[ 'EventCode' => 'ApproveSS', 'Description' => 'Closing Statement Approvedby Lender'],
	[ 'EventCode' => 'CancelOrder', 'Description' => 'Order Cancelled'],
	[ 'EventCode' => 'ClearedTitle', 'Description' => 'Curative Cleared'],
	[ 'EventCode' => 'ClearToFund', 'Description' => 'Clear to Fund'],
	[ 'EventCode' => 'CompletedPayoff', 'Description' => 'Payoff Delivered'],
	[ 'EventCode' => 'CompletedRecording', 'Description' => 'Recording Completed by vendor'],
	[ 'EventCode' => 'CompletedSigning', 'Description' => 'Closing Documents Executed'],
	[ 'EventCode' => 'CompleteOrder', 'Description' => 'Order Completed'],
	[ 'EventCode' => 'CompleteSSRevision', 'Description' => 'Closing Statement Modifications Complete'],
	[ 'EventCode' => 'CurativePending', 'Description' => 'Curative Pending'],
	[ 'EventCode' => 'DeliverClosingPackage', 'Description' => 'Document Package Delivered by Lender'],
	[ 'EventCode' => 'DeliverFinalSS', 'Description' => 'Closing Statement Distributed'],
	[ 'EventCode' => 'DeliverLenderSS', 'Description' => 'Closing Instructions'],
	[ 'EventCode' => 'DeliverPolicy', 'Description' => 'Final Title Policy'],
	[ 'EventCode' => 'DeliverProduct', 'Description' => 'Product Delivered by the Vendor'],
	[ 'EventCode' => 'DeliverVendorSS', 'Description' => 'Closing Statement Delivered'],
	[ 'EventCode' => 'DenyOrder', 'Description' => 'Order Denied by the Vendor'],
	[ 'EventCode' => 'DocShipInfo', 'Description' => ''],
	[ 'EventCode' => 'Escalation', 'Description' => 'Escalation by Lender'],
	[ 'EventCode' => 'EscalationResponse', 'Description' => 'Escalation Response by Vendor'],
	[ 'EventCode' => 'ExternalSubordination-ApprovedByLender', 'Description' => 'External Subordination–Approved by Lender'],
	[ 'EventCode' => 'ExternalSubordination-ApprovedByTitleProvider', 'Description' => 'External Subordination–Approved by Title Provider'],
	[ 'EventCode' => 'ExternalSubordination-NotificationToLender', 'Description' => 'External Subordination–Notification to Lender'],
	[ 'EventCode' => 'ExternalSubordination-ReceivedFromJLH', 'Description' => 'External Subordination–Received from JLH'],
	[ 'EventCode' => 'ExternalSubordination-RevisionDelivered', 'Description' => 'External Subordination-Revision Delivered'],
	[ 'EventCode' => 'ExternalSubordination-RevisionRequest', 'Description' => 'External Subordination-Revision Request'],
	[ 'EventCode' => 'ExternalSubordination-SubPackageSentToJLH', 'Description' => 'External Subordination–Sub Package Sent to JLH'],
	[ 'EventCode' => 'FailedSigning', 'Description' => 'Closing Documents Not Executed'],
	[ 'EventCode' => 'FundedLoanByLender', 'Description' => 'Loan Funded'],
	[ 'EventCode' => 'FundedLoanByVendor', 'Description' => 'The Vendor Disbursed Funds'],
	[ 'EventCode' => 'FundsReceived', 'Description' => 'Funds Received by Vendor'],
	[ 'EventCode' => 'InternalSubordination-ApprovedByTitleProvider', 'Description' => 'Internal Subordination–Approved by Title Provider'],
	[ 'EventCode' => 'InternalSubordination-CompletedByLender', 'Description' => 'Internal Subordination–Completed by Lender'], 
	[ 'EventCode' => 'InternalSubordination-NotApprovedByTitleProvider', 'Description' => 'Internal Subordination–Not Approved by Title Provider'],
	[ 'EventCode' => 'InternalSubordination-NotificationToLender', 'Description' => 'Internal Subordination–  Notification to Lender'],
	[ 'EventCode' => 'InternalSubordination-ReceivedByTitleProvider', 'Description' => 'Internal Subordination–Received by Title Provider'],
	[ 'EventCode' => 'NotClearToFund', 'Description' => 'Not Clear to Fund'],
	[ 'EventCode' => 'OrderedPayoff', 'Description' => 'Payoff Ordered'],
	[ 'EventCode' => 'OrderReopenApproved', 'Description' => 'Order Reopen Approved'],
	[ 'EventCode' => 'OrderReopenRequest', 'Description' => 'Order Reopen Request'],
	[ 'EventCode' => 'PlaceOrder', 'Description' => 'New Order Request'],
	[ 'EventCode' => 'QCCorrectionsCompleted', 'Description' => 'Clear to Close Revision is Completed'],
	[ 'EventCode' => 'ReadyToSchedule', 'Description' => 'Ready to Schedule'],
	[ 'EventCode' => 'ReceiveClosingPackage', 'Description' => 'Document Package Received by Vendor'],
	[ 'EventCode' => 'RequestPayoff', 'Description' => 'Lender Sending'],
	[ 'EventCode' => 'RequestRevision', 'Description' => 'Revision Requested'],
	[ 'EventCode' => 'RequestSSRevision', 'Description' => 'Closing Statement Modifications Requested'],
	[ 'EventCode' => 'RescheduledSigning', 'Description' => 'Signing Rescheduled'],
	[ 'EventCode' => 'ResumeOrder', 'Description' => 'Service Resume'],
	[ 'EventCode' => 'RevisedDeliverProduct', 'Description' => 'Revised Deliver Product'],
	[ 'EventCode' => 'RevisionRequestApproved', 'Description' => 'Revision Request Approved'],
	[ 'EventCode' => 'RevisionRequestNotApproved', 'Description' => 'Revision Request Not Approved'],
	[ 'EventCode' => 'ScheduledSigning', 'Description' => 'Signing Scheduled'],
	[ 'EventCode' => 'SentToRecord', 'Description' => 'Recording submitted by Vendor'],
	[ 'EventCode' => 'SuspendOrder', 'Description' => 'Service On Hold'],
	[ 'EventCode' => 'UpdateOrderInfo', 'Description' => 'Order Information Update'],

];

$config['RealECClosingEvents'] = [	
	[ 'EventCode' => '303', 'Description' => 'Schedule Information Update (303)'],
	[ 'EventCode' => '310', 'Description' => 'Re-Schedule (310)']
];

$config['KeystoneClosingEvents'] = [
	[ 'EventCode' => 'ReadyToSchedule', 'Description' => 'Ready to Schedule'],
	[ 'EventCode' => 'RescheduledSigning', 'Description' => 'Signing Rescheduled'],
];

$config['KeystoneClosingEventStatus'] = [
	[ 'EventCode' => 'CompletedSigning', 'Description' => 'Closing Documents Executed'],
	[ 'EventCode' => 'FailedSigning', 'Description' => 'Closing Documents Not Executed'],
];

$config['StewartClosingEvents'] = [];
$config['LoansPQClosingEvents'] = [];

$config['NamingConventions'] = [
	'BorrowerName-OrderNumber',
	'BorrowerName-AltORderNumber',
	'BorrowerName-LoanNo',
	'_',
	'OrderNumber',
	'LoanNumber',
	'PropertyStateCode',
	'PropertyCountyName',
	'PropertyCityName',
	'PropertyZipcode',
	'OrderTypeName',
	'SubProductName',
	'BorrowerType',
	'PropertyType',
	'TransactionType'
];

//For bulk keystone events in order entry
$config['KeystoneEventsBulk'] = ['ClearToFund','CompletedSigning','ScheduledSigning'];


/*** /API EVents ****/
