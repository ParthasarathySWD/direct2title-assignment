<!DOCTYPE html>
<html>
<head>
	<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
	<style type="text/css">
	        @page {
	            sheet-size: LEGAL;
	             size: auto;
	            footer: html_MyCustomFooter;/* display <htmlpagefooter name="MyCustomFooter"> on all pages */
	        }

	        @page {
	           size: auto;
	            margin-bottom:4cm;
	        }

	        @page nofooter {
	            footer: _blank;
	        }
	        div.nofooter {
	            page-break-before: right;
	            page: nofooter;
	        }

	       @page {
	           sheet-size: LEGAL;
	       }
	       
	       body {
	           margin: 0;
	           padding: 0;
	           font-family: arial;
	       }
	       
	       p {
	           margin: 0pt !important;
	           padding: 2pt;
	       }
	       
	       * {
	           font-size: 8pt;
	       }
	       
	       tbody:before,
	       tbody:after {
	           display: none;
	       }
	       
	       table {
	           border-collapse: separate;
	           border-spacing: 0px;
	       }
	</style>
	<title></title>
</head>
<body>
<!-- Footer Starts-->
    <htmlpagefooter name="MyCustomFooter">
	<table border="0">
		<tr>
			<td colspan="3" style="border-bottom: 0px; border-left: 0px; border-right: 0px; font-family: arial; text-align: justify; font-size: 8pt; padding:4pt; line-height: 11pt;">
				<p>%%DisclaimerNote%%</p><br>
				<p></p>
			</td>
		</tr>
	</table>
    </htmlpagefooter>
<!-- Footer Ends-->
<!-- Title Starts-->
	<div style="width: 100%; display: inline-block; margin-top:-20px; padding: 0;">
		<div style="width:75%; float:left; text-align: left"><img src=%%ImageUrl%% style="width:100pt; margin:0 auto; display:block;padding-top:35px;"></div>
		<div style="width:25%;float:left;padding-top:40px;">
			<p style="font-family: Arial; font-size:8pt;">File No: %%Ordernumber%% &nbsp;&nbsp;&nbsp;Page#</p>
			<p style="font-family: Times New Roman; font-size:9pt;"><b>Tel: %%CustomerPContactMobileNo%%</b></p>
			<p style="font-family: Times New Roman; font-size:9pt;"><b>Web: www.isgnsolutions.com</b></p>
		</div>
	</div>
<!-- Title Ends-->
	<table cellpadding="2" cellspacing="0" style="border-top:0.01em solid grey;border-bottom:0.01em solid grey;border-left:0.01em solid grey;border-right:0.01em solid grey;" width="100%">
		<tr>
			<td colspan="3" style="border: 0.01em solid grey; border-top:0px; border-left:0px; border-right:0px; text-align: center;">
				<p align="center" style="font-family:Times New Roman; font-size: 12pt; padding:3pt;"><b>EZ Close Property Report</b></p>
			</td>
		</tr>
<!-- Order Information Starts -->
		<tr>
			<td colspan="3" style="border: 0.01em solid grey; border-top:0pt; border-left:0px; border-right:0px;">
				<p align="left" style="font-family:Arial; font-size: 8pt; padding:3pt;"><b>ORDER INFO</b></p>
			</td>
		</tr>
		<tr>
			<td style="font-family:Arial; font-size: 8pt; padding:3pt;">
				<p align="left">Order Date:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span style="width:15px;display:inline-block;"></span>%%OrderDate%%</p>
			</td>
			<td style="font-family:Arial; font-size: 8pt; padding:3pt;">
				<p align="left">Client File No:&nbsp;&nbsp;&nbsp; <span style="width:5pt;display:inline-block;"></span>%%Loannumber%%</p>
			</td>
			<td style="font-family:Arial; font-size: 8pt; padding:3pt;">
				<p align="left">FSD File No:&nbsp;&nbsp;&nbsp; <span style="width:5pt;display:inline-block;"></span>%%Ordernumber%%</p>
			</td>
		</tr>
		<tr>
			<td colspan="3" style="font-family:Arial; font-size: 8pt; padding:3pt;">
				<p align="left">Client:&nbsp;&nbsp;&nbsp; <span style="width:35pt;display:inline-block;"></span><span style="">%%CustomerName%%</span></p>
			</td>
		</tr>
		<tr>
			<td colspan="3" style="font-family:Arial; font-size: 8pt; padding:3pt;">
				<p align="left">Client Address:&nbsp;&nbsp;&nbsp; <span style="width:10pt;display:inline-block;"></span> <span style="">%%CustomerAddress1%% %%CustomerAddress2%% %%CustomerStateCode%% %%CustomerCountyName%% %%CustomerCityName%% %%CustomerZipCode%%</span></p>
			</td>
		</tr>
		<tr>
			<td colspan="3" style="font-family:Arial; font-size: 8pt; padding:3pt;">
				<p align="left">Attention:&nbsp;&nbsp;&nbsp; <span style="width:30pt;display:inline-block;"></span><span style="">%%AttentionName%% </span></p>
			</td>
		</tr>
		<tr>
			<td colspan="3" style="font-family:Arial; font-size: 8pt; padding:3pt; border-top:0.01em solid grey;">
				<p align="left">Applicant Name:&nbsp;&nbsp;&nbsp; <span style="width:27pt;display:inline-block;"></span>%%Borrowers%%</p>
			</td>
		</tr>
		<tr>
			<td colspan="3" style="font-family:Arial; font-size: 8pt; padding:3pt;">
				<p align="left">Property Address:&nbsp;&nbsp;&nbsp; <span style="width:20pt;display:inline-block;"></span>%%Propertyaddress1%% %%Propertyaddress2%%</p>
			</td>
		</tr>
		<tr>
			<td colspan="3" style="font-family:Arial; font-size: 8pt; padding:3pt;">
				<p align="left">Village/Town/City:&nbsp;&nbsp;&nbsp; <span style="width:20pt;display:inline-block;"></span>%%Cityname%%</p>
			</td>
		</tr>
		<tr>
			<td style="font-family:Arial; font-size: 8pt; padding:3pt;">
				<p align="left">County: <span style="width:55pt;display:inline-block;"></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; %%Countyname%%</p>
			</td>
			<td style="font-family:Arial; font-size: 8pt; padding:3pt;">
				<p align="left">State: <span style="width:10pt;display:inline-block;"></span>&nbsp;&nbsp;&nbsp; %%Statecode%%</p>
			</td>
			<td style="font-family:Arial; font-size: 8pt; padding:3pt;">
				<p align="left">Zip: <span style="width:10pt;display:inline-block;"></span>&nbsp;&nbsp;&nbsp;%%Zip%%</p>
			</td>
		</tr>
<!-- Order Information Ends -->
<!-- Special Notes Starts -->
		<tr>
			<td colspan="3" style="border-top:0.01em solid grey;">
				<p align="left" style="font-family:Arial; font-size: 8pt; padding:1pt;"><b>SPECIAL NOTES:</b></p>
			</td>
		</tr>
		<tr>
			<td colspan="3" style="border-top:0.01em solid grey;">
				<p align="left" style="font-family:Arial; font-size: 8pt; padding:1pt;">ADDRESS HAS BEEN VERIFIED TO MATCH PUBLIC RECORDS - %%AddressComments%%<br>%%CountyVariance%%</p>
			</td>
		</tr>
<!-- Special Notes Ends -->
<!-- Record Searched Starts-->
		<tr>
			<td colspan="1" style="border-top:0.01em solid grey;">
				<p align="left" style="font-family:Arial; font-size: 8pt; padding:3pt;"><b>RECORDS SEARCHED</b></p>
				<p>&nbsp;</p>
			</td>
			<td colspan="2" style="border-top:0.01em solid grey; font-family:Arial; font-size: 8pt;line-height: 25pt">
				<p align="left" style="padding:3pt;">From Date:&nbsp;<span style="width:50pt;display:inline-block;">%%SearchFromDate%%</span></p>
				<p align="left" style="padding:3pt;"></p>
				<p></p>Through Date:&nbsp;<span style="width:50pt;display:inline-block;">%%SearchAsOfDate%%</span>
				<p></p>
			</td>
		</tr>
<!-- Record Searched Ends -->
<!-- Property Reference Starts -->
		<tr>
			<td colspan="3" style="border:0.01em solid grey; border-left:0px; border-right:0px;">
				<p align="left" style="font-family:Arial; font-size: 8pt; padding:1pt;"><b>PROPERTY REFERENCE</b></p>
			</td>
		</tr>
		<tr>
			<td colspan="3">
			<div class="torderproperty">
				<table cellpadding="2" cellspacing="0" style="font-family:Arial; font-size: 8pt;" width="100%">
					<tr style="font-family:arial !important; font-size: 8pt;">
						<td colspan="2">Assesors Map:&nbsp;%%sdMapNo%%</td>
						<td colspan="2">Section:&nbsp;%%dSection%%</td>
						<td colspan="2">Block:&nbsp;%%Block%%</td>
						<td colspan="2">Lot:&nbsp;%%Lot%%</td>
					</tr>
					<tr style="font-family:Arial; font-size: 8pt;">
						<td colspan="8" width="15%">APN #:&nbsp;%%APN%%</td>
					</tr>
					<tr>
						<td>
							<p>Township:&nbsp;%%Township%%</p>
						</td>
					</tr>
				</table>
				</div>
			</td>
		</tr>
<!-- Property Reference Ends -->
	</table>
<!-- Deed Starts -->
		<div class="torderdeeds">
		<table cellpadding="2" cellspacing="0" style="border-bottom:0.01em solid grey;border-bottom:0.01em solid grey;border-left:0.01em solid grey;border-right:0.01em solid grey;page-break-inside: avoid; " width="100%">
			%%DeedHeading%%
		<tr>
			<td colspan="3">
				<table border="0" cellpadding="2" cellspacing="0" style="font-family:Arial; font-size: 9pt; width: 100%;" width="100%">
					<tr>
						<td colspan="3">
                            <table>
                                <tr align="left" style="font-family: Arial; font-size: 8pt; text-align: left;">
                                    <td style="width: 20%; white-space: nowrap; vertical-align: top; font-family: Arial; font-size: 8pt;">Title Held By: </td>
                                    <td style="font-family: Arial; font-size: 8pt;">%%DeedGrantee%% </td>
                                </tr>
                            </table>

							<!-- <p align="left" style="font-family: Arial; font-size: 8pt; text-align: left;">Title Held By:&nbsp; %%DeedGrantee%%</p> -->
						</td>
					</tr>
					<tr>
						<td colspan="" width="20%">
							<p style="font-family:Arial; font-size: 8pt;">Estate Type:&nbsp;%%DeedEstateInterestName%%</p>
						</td>

						<td colspan="" width="40%">
							<p style="font-size: 10px; text-align: right; display: inline-block;">Last Conveyance Deed Type:&nbsp;%%DeedDocumentTypeName%%</p>
						</td>
					</tr>
					<tr>
						<td style="width: 25pt;" >
							<p style="font-family: Arial; font-size: 8pt;">Consider:&nbsp;%%ConsiderAmount%%</p>
						</td>
					</tr>
					<tr>
						<td  width="10%" colspan="3">
                            <table>
                                <tr align="left" style="font-family: Arial; font-size: 8pt; text-align: left;">
                                    <td style="width: 20%; white-space: nowrap; vertical-align: top; font-family: Arial; font-size: 8pt;">Grantee: </td>
                                    <td style="font-size: 8pt; font-family: Arial; font-size: 8pt;">%%DeedGrantee%% </td>
                                </tr>
                            </table>

							<!-- <p style="font-family: Arial; font-size: 8pt;">Grantee:&nbsp;%%DeedGrantee%%</p> -->
						</td>
					</tr>
					<tr>
						<td colspan="3">
                            <table>
                                <tr align="left" style="font-family: Arial; font-size: 8pt; text-align: left;">
                                    <td style="width: 20%; white-space: nowrap; vertical-align: top; font-family: Arial; font-size: 8pt;">Grantor: </td>
                                    <td style="font-size: 8pt; font-family: Arial; font-size: 8pt;">%%DeedGrantor%% </td>
                                </tr>
                            </table>

							<!-- <p style="font-family: Arial; font-size: 8pt;">Grantor:&nbsp;%%DeedGrantor%%</p> -->
						</td>
					</tr>
					<tr>
						<td colspan="3" width="100%">
							<table border="0" cellpadding="2" cellspacing="0" width="100%">
								<tr>
									<td style="font-size: 8pt; font-family: arial;" width="70%">
										<p style="font-size: 8pt; font-family: arial;">%%DBVTypeName_1%%:&nbsp;%%Deed_DBVTypeValue_1%%</p>
									</td>
									<td style="font-size: 8pt; font-family: arial;" width="20%">
										<p style="font-size: 8pt; font-family: arial;">%%DBVTypeName_2%%:&nbsp;%%Deed_DBVTypeValue_2%%</p>
									</td>
									<td style="font-size: 8pt; font-family: arial;" width="20%">
										<p style="font-size: 8pt; font-family: arial;">Deed Date:&nbsp;%%DeedDate%%</p>
									</td>
									<td style="font-size: 8pt; font-family: arial;" width="30%">
										<p style="font-size: 8pt; font-family: arial;">Recorded Date:&nbsp;%%RecordedDate%%</p>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td style="font-family:Arial; font-size: 8pt;" width="25.8%%">
				<p>Certificate #:&nbsp;<span style="width: 40pt;display:inline-block;"></span>%%CertificateNo%%</p>
			</td>
			<td style="font-family:Arial; font-size: 8pt;" width="25.5%">
				<p>Document #:&nbsp;<span style="width:45pt;display:inline-block;"></span>%%DocumentNo%%</p>
			</td>
			<td style="font-family:Arial; font-size: 8pt;" width="33%">
				<p>Instrument #:&nbsp;%%InstrumentNo%%</p>
			</td>
		</tr>
		<tr>
			<td colspan="3" style="border-bottom:0.01em solid grey;">
                <table>
                    <tr align="left" style="font-family: Arial; font-size: 8pt; text-align: left;">
                        <td style="width: 20%; white-space: nowrap; vertical-align: top; font-family: Arial; font-size: 8pt;"><u>Comments: </u></td>
                        <td style="font-size: 8pt; font-family: Arial; font-size: 8pt;">%%DeedComments%% </td>
                    </tr>
                </table>

<!-- 				<p style="font-family:Arial; font-size: 8pt;"><u>Comments:</u>%%DeedComments%%</p>
				<p style="height: 15pt;"></p> -->
			</td>
		</tr>
		</table>
		</div>
<!-- Deed Ends -->
<!-- Tax Starts -->
		<div class="tordertaxcerts">
		<table cellpadding="2" cellspacing="0" style="border-top:0.01em solid grey;border-bottom:0.01em solid grey;border-left:0.01em solid grey;border-right:0.01em solid grey;page-break-inside: avoid;" width="100%">
			%%TaxHeading%%
		<tr>
			<td colspan="3">
				<table border="0" cellpadding="2" cellspacing="0" width="100%">
					<tr>
						<td style="font-family:Arial; font-size: 8pt; padding:3pt;padding-right: 75pt;" width="35%">
							<p>Total Annual Tax Due:&nbsp;%%LatestGrossAmount%%</p>
						</td>
						<td style="font-family:Arial; font-size: 8pt; padding:3pt;" width="35%">
							<p>Tax Year:&nbsp;%%TaxYears%%</p>
						</td>
						<td style="font-family:Arial; font-size: 8pt; padding:3pt;" width="30%">
							<p>100% Assessed Value:&nbsp;%%TotalValue%%</p>
						</td>
					</tr>
					<tr>
						<td style="font-family:Arial; font-size: 8pt; padding:3pt;" width="30%">Certified:<span style="width:45pt;display:inline-block;"></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src=%%Url%%/assets/Pictures/%%CertifiedStatus%% style="height: 15px;width: 15px;vertical-align: top;"></td>
						<td style="font-family:Arial; font-size: 8pt; padding:3pt;" width="30%">Assessed Land Value:&nbsp;%%Land%%</td>
						<td style="font-family:Arial; font-size: 8pt; padding:3pt;" width="30%">Assessed Improvement:&nbsp;%%Buildings%%</td>
					</tr>
					<tr>
						<td style="font-family:Arial; font-size: 8pt; padding:3pt;" width="30%">Installment Type:&nbsp;%%TaxBasisName%%</td>
						<td colspan="2" style="font-family:Arial; font-size: 8pt; padding:3pt;">Agricultural Value:&nbsp;%%Agriculture%%</td>
					</tr>
					<tr>
						<td style="font-family:Arial; font-size: 8pt; padding:3pt;" width="30%">Amount Due:&nbsp;N/A <span style="width:25pt;display:inline-block;"></span></td>
						<td style="font-family:Arial; font-size: 8pt; padding:3pt;" width="30%">Amount Paid:&nbsp;%%AmtPaid%%</td>
						<td style="font-family:Arial; font-size: 8pt; padding:3pt;" width="30%">Status:&nbsp;%%StatusName%%</td>
					</tr>
					<tr>
						<td style="font-family:Arial; font-size: 8pt; padding:3pt;">Due Date:&nbsp;<span style="width:40pt;display:inline-block;"></span>N/A</td>
						<td style="font-family:Arial; font-size: 8pt; padding:3pt;">Next Due Date:&nbsp;<span style="width:10pt;display:inline-block; text-align: right; font-size: 8pt;">%%NextTaxDate%%</span></td>
						<td style="font-family:Arial; font-size: 8pt; padding:3pt;">Delinquent Due Amount:&nbsp;%%AmountDelinquent%%<span style="width:25pt;display:inline-block; font-size: 8pt;"></span></td>
					</tr>
					<tr>
						<td style="font-family:Arial; font-size: 8pt; padding:3pt;">Interest Amount:&nbsp;<span style="width:20pt;display:inline-block;"></span>N/A</td>
						<td style="font-family:Arial; font-size: 8pt; padding:3pt;">Gross Amount:&nbsp;<span style="width:25pt;display:inline-block; font-size: 8pt;">%%LatestGrossAmount%%</span></td>
						<td style="font-family:Arial; font-size: 8pt; padding:3pt;">Penalty Amount:&nbsp;<span style="width:25pt;display:inline-block;">N/A</span></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="3">
				<table cellpadding="2" cellspacing="0" width="100%">
					<tr>
						<td style="font-family:Arial; font-size: 8pt; padding:3pt;" width="20%">
							<p>Tax Exemptions:<span style="width:5pt;display:inline-block;"></span></p>
						</td>
						<td style="font-family:Arial; font-size: 8pt; padding:3pt;" width="25%">Homestead: <img src=%%Url%%/assets/Pictures/%%ExmpStatus%% style="height: 15px;width: 15px;vertical-align: top;"></td>
						<td style="font-family:Arial; font-size: 8pt; padding:3pt;" width="30%">Agricultural: <img src=%%Url%%/assets/Pictures/%%AgriExmpStatus%% style="height: 15px;width: 15px;vertical-align: top;"></td>
						<td style="font-family:Arial; font-size: 8pt; padding:3pt;" width="35%">Rural: <img src=%%Url%%/assets/Pictures/%%RuralExmpStatus%% style="height: 15px;width: 15px;vertical-align: top;"></td>
						<td style="font-family:Arial; font-size: 8pt; padding:3pt;" width="15%">Urban: <img src=%%Url%%/assets/Pictures/%%UrbanExmpStatus%% style="height: 15px;width: 15px;vertical-align: top;"></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="1" style="font-family:Arial; text-align: left; font-size: 8pt; padding:3pt;">
				<p>Acreage: N/A</p>
			</td>
			<td style="font-family:Arial; text-align: left; font-size: 8pt; padding:3pt;">
				<p>Good Thru Date: <span style="width:45pt;display:inline-block;">%%ThroughDate%%</span></p>
			</td>
		</tr>
		<tr>
			<td colspan="3" style="font-family:Arial; text-align: left; font-size: 8pt; padding:3pt;">
                <table>
                    <tr align="left" style="font-family: Arial; font-size: 8pt; text-align: left;">
                        <td style="width: 20%; white-space: nowrap; vertical-align: top; font-family: Arial; font-size: 8pt;"><u>Comments: </u></td>
                        <td style="font-size: 8pt; font-family: Arial; font-size: 8pt;">%%TaxComments%% </td>
                    </tr>
                </table>

<!-- 				<p><u>Comments:</u>%%TaxComments%%</p>
				<p style="height: 5pt;"></p> -->
			</td>
		</tr>
	</table>
	</div>
<!-- Tax Ends -->
<!-- Mortgage Starts -->
		<div class="tordermortgages">
		<table cellpadding="2" cellspacing="0" style="border-top:0.01em solid grey;border-bottom:0.01em solid grey;border-left:0.01em solid grey;border-right:0.01em solid grey;page-break-inside: avoid;" width="100%">
		%%MortgageHeading%%
		<tr>
			<td colspan="3">
				<table border="0" cellpadding="2" cellspacing="0" width="100%">
					<tr>
						<td colspan="3" style="font-family:Arial; font-size: 8pt; padding:3pt; width: 130pt" width="35%">
                            <table>
                                <tr align="left" style="font-family: Arial; font-size: 8pt; text-align: left;">
                                    <td style="width: 20%; white-space: nowrap; vertical-align: top; font-family: Arial; font-size: 8pt;">Lien Grantor: </td>
                                    <td style="font-size: 8pt;">%%Mortgagor%% </td>
                                </tr>
                            </table>

							<!-- <p>Lien Grantor:&nbsp;%%Mortgagor%%</p> -->
						</td>
					</tr>
					<tr>
					<td colspan="3" style="font-family:Arial; font-size: 8pt; padding:3pt;" width="35%">
                            <table>
                                <tr align="left" style="font-family: Arial; font-size: 8pt; text-align: left;">
                                    <td style="width: 20%; white-space: nowrap; vertical-align: top; font-family: Arial; font-size: 8pt;">Lien Holder: </td>
                                    <td style="font-size: 8pt;">%%Mortgagee%% </td>
                                </tr>
                            </table>

							
							<!-- <p>Lein Holder:&nbsp;%%Mortgagee%%</p> -->
						</td>
					</tr>
					<tr>
						<td style="font-family:Arial; font-size: 8pt; padding:3pt;" width="30%">
							<p>Trustee:&nbsp;%%Trustee%%</p>
						</td>

					</tr>
					<tr>
					<td style="font-family:Arial; font-size: 8pt; padding:3pt;" width="30%">Document Type:&nbsp;<span style="width:45pt;display:inline-block;"></span>%%MortgageDocumentTypeName%%</td>
						<td style="font-family:Arial; font-size: 8pt; padding:3pt;" width="30%">Document Date:&nbsp;<span style="width:45pt;display:inline-block;"></span>%%MortgageDate%%</td>
						<td style="font-family:Arial; font-size: 8pt; padding:3pt;" width="30%">Recorded Date:&nbsp;%%MortgageRecordedDate%%</td>
						
					</tr>
					<tr>
						<td style="font-family:Arial; font-size: 8pt; padding:3pt;" width="20%">%%MortgageDBVTypeName_1%%:&nbsp;%%Mortgage_DBVTypeValue_1%%</td>
						<td style="font-family:Arial; font-size: 8pt; padding:3pt;" width="20%">%%MortgageDBVTypeName_2%%:&nbsp;%%Mortgage_DBVTypeValue_2%%</td>
						<td style="font-family:Arial; font-size: 8pt; padding:3pt;" width="20%">Instrument #:&nbsp;%%InstrumentNo_1%%</td>
						<td style="font-family:Arial; font-size: 8pt; padding:3pt;" width="20%">Document #:&nbsp;%%Document%%</td>

						
					</tr>
					<tr>
						<td style="font-family:Arial; font-size: 8pt; padding:3pt;">Amount:&nbsp;%%MortAmt%%</td>
						<td style="font-family:Arial; font-size: 8pt; padding:3pt;" width="30%">Open/Closed MTG:&nbsp;%%OpenEnded%% <span style="width:25pt;display:inline-block;"></span></td>
						<td style="font-family:Arial; font-size: 8pt; padding:3pt;" width="30%">Maturity Date:&nbsp;%%MaturityDate%%</td>
					</tr>

				</table>
			</td>
		</tr>
		<tr>
			<td colspan="3" style="font-family:Arial; text-align: left; font-size: 8pt; padding:3pt;">
                <table>
                    <tr align="left" style="font-family: Arial; font-size: 8pt; text-align: left;">
                        <td style="width: 20%; white-space: nowrap; vertical-align: top; font-family: Arial; font-size: 8pt;"><u>Comments: </u></td>
                        <td style="font-size: 8pt; font-family: Arial; font-size: 8pt;">%%MortgageComments%% </td>
                    </tr>
                </table>

<!-- 				<p><u>Comments:</u>%%MortgageComments%%</p>
				<p style="height: 5pt;"></p> -->
			</td>
		</tr>
	</table>
	</div>
<!-- Mortgage Ends -->

<!-- Judgement Starts -->
        <div class="torderjudgments">
		<table cellpadding="2" cellspacing="0" style="border-top:0.01em solid grey;border-bottom:0.01em solid grey;border-left:0.01em solid grey;border-right:0.01em solid grey;page-break-inside: avoid;" width="100%">
			    %%JudgementHeading%%
               <tr>
            <td colspan="3">
                <table border="0" cellpadding="2" cellspacing="0" width="100%">
                    <tr>
                        <td style="font-family:Arial; font-size: 8pt; padding:3pt; width: 130pt" width="35%">
                            <p>Lien Grantor :&nbsp;%%Plaintiff%%</p>
                        </td>
                        <td style="font-family:Arial; font-size: 8pt; padding:3pt;" width="35%">
                            <p>Lien Holder :&nbsp;%%Defendent%%</p>
                        </td>
                    </tr>
                    <tr>
                    <td style="font-family:Arial; font-size: 8pt; padding:3pt;" width="30%">Document Type:<span style="width:45pt;display:inline-block;"></span>&nbsp;%%JudgementDocumentTypeName%%</td>
                        <td style="font-family:Arial; font-size: 8pt; padding:3pt;" width="30%">Document Date:<span style="width:45pt;display:inline-block;"></span>&nbsp;%%JudgeDated%%</td>
                        <td style="font-family:Arial; font-size: 8pt; padding:3pt;" width="30%">Recorded Date:&nbsp;%%JudgeRecorded%%</td>
                    </tr>
                    <tr>
                        <td style="font-family:Arial; font-size: 8pt; padding:3pt;" width="30%">%%JudgementDBVTypeName_1%%:&nbsp;%%Judgement_DBVTypeValue_1%%</td>
                        <td style="font-family:Arial; font-size: 8pt; padding:3pt;" width="30%">%%JudgementDBVTypeName_2%%:&nbsp;%%Judgement_DBVTypeValue_2%%</td>
                        <td style="font-family:Arial; font-size: 8pt; padding:3pt;" width="30%">Instrument #:&nbsp;%%InstrumentNo_1%%</td>
                    </tr>
                    <tr>
                        <td colspan="2" style="font-family:Arial; font-size: 8pt; padding:3pt;">Amount:&nbsp;%%JudgementAmount%%</td>
                        <td style="font-family:Arial; font-size: 8pt; padding:3pt;" width="30%">Case:&nbsp;%%CaseNumber%% <span style="width:25pt;display:inline-block;"></span></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="3" style="font-family:Arial; text-align: left; font-size: 8pt; padding:3pt;">
                <table>
                    <tr align="left" style="font-family: Arial; font-size: 8pt; text-align: left;">
                        <td style="width: 20%; white-space: nowrap; vertical-align: top; font-family: Arial; font-size: 8pt;"><u>Comments:</u> </td>
                        <td style="font-size: 8pt; font-family: Arial; font-size: 8pt;">%%JudgementComments%% </td>
                    </tr>
                </table>

<!--                 <p><u>Comments:&nbsp;</u>%%JudgementComments%%</p>
                <p style="height: 5pt;"></p> -->
            </td>
        </tr>
        </table>
            </div>
<!-- Judgement Ends -->
<!-- Lien Starts -->
		<div class="torderliens">
		<table cellpadding="2" cellspacing="0" style="border-top:0.01em solid grey;border-bottom:0.01em solid grey;border-left:0.01em solid grey;border-right:0.01em solid grey;page-break-inside: avoid;" width="100%">
		%%LienHeading%%
		<tr>
			<td style="font-family:Arial; font-size: 8pt; padding:3pt;">
				<p align="left">Lien Grantor:&nbsp;%%ExecutedBy%%</p>
			</td>
		</tr>
		<tr>
			<td style="font-family:Arial; font-size: 8pt; padding:3pt;">
				<p align="left">Lien Holder:&nbsp;%%LeinHolder%%</p>
			</td>
			<td style="font-family:Arial; font-size: 8pt; padding:3pt;">
				<p align="left">Open Ended:&nbsp;<span style="width:10pt;display:inline-block;">%%MTG%%</span></p>
			</td>
		</tr>
		<tr>
			<td colspan="2" style="font-family:Arial; font-size: 8pt; padding:3pt;">
				<p>Description:&nbsp;<span style="width:45pt;display:inline-block;">%%LeinDocumentTypeName%%</span></p>
			</td>
			<td colspan="2" style="font-family:Arial; font-size: 8pt; padding:3pt; text-align:left;">
				<p>Loan Type:&nbsp;N/A<span style="width:45pt;display:inline-block;">&nbsp;</span></p>
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<table cellpadding="2" cellspacing="0" width="100%">
					<tr>
						<td style="font-family:Arial; font-size: 8pt; padding:3pt;">
							<p>%%LienDBVTypeName_1%%:&nbsp;<span style="width:45pt;display:inline-block;">%%Lien_DBVTypeValue_1%%</span></p>
						</td>
						<td style="font-family:Arial; font-size: 8pt; padding:3pt; text-align:left;">
							<p>%%LienDBVTypeName_2%%:&nbsp;<span style="width:45pt;display:inline-block;">%%Lien_DBVTypeValue_2%%</span></p>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<table cellpadding="2" cellspacing="0" width="100%">
					<tr>
						<td style="font-family:Arial; font-size: 8pt; padding:3pt;">
							<p>INSTRUMENT:&nbsp;<span style="width:45pt;display:inline-block;">%%InstrumentNo_1%%</span></p>
						</td>
						<td style="font-family:Arial; font-size: 8pt; padding:3pt; text-align:left;">
							<p>Date:&nbsp;<span style="width:45pt;display:inline-block;">%%LeinDate%%</span></p>
						</td>
						<td style="font-family:Arial; font-size: 8pt; padding:3pt; text-align:left;">
							<p>Recorded:&nbsp;<span style="width:45pt;display:inline-block;">%%LeinRecord%%</span></p>
						</td>
						<td style="font-family:Arial; font-size: 8pt; padding:3pt; text-align:left;">
							<p>Amount:&nbsp;<span style="width:45pt;display:inline-block;">%%LeinAmt%%</span></p>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="4" style="font-family:Arial; font-size: 8pt; padding:3pt; text-align:left;">
				<p><u>Marginal Assignments / Notes:</u></p>
				<p style="height:45pt;">%%LeinComments%%</p>
			</td>
		</tr>
		</table>
			</div>
<!-- Lein Ends -->
<!-- 	<table border="0">
		<tr>
			<td colspan="3" style="border-bottom: 0px; border-left: 0px; border-right: 0px; font-family: arial; text-align: justify; font-size: 8pt; padding:4pt; line-height: 11pt;">
				<p>This report is NOT title insurance. This report only provides title information contained in the above stated records and does NOT reflect unindexed or misindexed matters or off-record matters that may affect said land. This Company, in issuing this report, assumes no liability on account of any instrument or proceeding in the chain of title to the property which may contain defects that would render such instrument null and void or defective. All instruments in the chain of the title to the property are assumed to be good and valid. This report is NOT a committment to insure and therefore does not contain the requirements and exceptions which would appear in a committment to insure or the exceptions which would appear in a title policy.</p><br>
				<p>This Company's liability for this report is limited to the amount paid for this report and extends only to the party to which it is issued. No other party may rely on this report. This report contains no express or implied opinion, warranty, guarantee, insurance, or similar assurances as to the status of title to the land.</p>
			</td>
		</tr>
	</table>  -->
	<div style="page-break-after: always;"></div>
	<div style="padding-top:10pt;"></div>
 	<table>
        <tr>
            <td colspan="3">
                <p align="left" style="font-family:Arial; font-size: 8pt; padding:3pt;"><b>MAP</b></p>
            </td>
        </tr>
        <tr>
            <td>
                <table style="width: 100%;border: 1px solid grey;">
	                <tr>
						<td class="td-bd" style="height: 100%;width: 100%;">
							<img class="stretch" src=%%Url%%%%MapAddress%% alt="" style="height: 300px;width: 750px;">
						</td>
					</tr>
                </table>
            </td>
        </tr>
    </table>
	<p></p>
	<p></p>
	<p></p>
	<p></p>
	<p></p>
	<p></p>
	<p></p>
	<p></p>
	<p></p>
	<div class="nofooter">
	<div style="page-break-after: auto;">
	<table align="left" border="0" cellpadding="5" width="100%">
		<tr>
			<td colspan="4" style="font-size: 12px; font-family: Courier New; line-height: 30px;text-align: center;">
				<h2 align="center" style="text-align: center;"><b style="text-align: center;">SCHEDULE "A"</b></h2>
			</td>
		</tr>
		<tr>
			<td colspan="4" style="font-size: 12px; font-family: Courier New; line-height: 30px;text-align: left;text-transform:uppercase">
				<p>%%Ordernumber%%</p><br>
				<p>%%LegalDescr%%</p><br>
			</td>
		</tr>
	</table>
	
	</div>
</body>
</html>