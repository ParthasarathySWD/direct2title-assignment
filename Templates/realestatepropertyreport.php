<!DOCTYPE html>
<html>
<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type">
    <style type="text/css">
    @page {
        sheet-size: LEGAL;
        border-style: double;
        border:1px solid black;
    }

    @page {
        margin-top:8cm;
    }

    @page {
        /*size: auto;*/
        header: html_MyCustomHeader;
        /* display <htmlpageheader name="MyCustomHeader"> on all pages */
    }

    @page noheader {
        header: _blank;
    }

    div.noheader {
        page-break-before: right;
        page: noheader;
    }

    body {
        margin: 0pt;
        padding: 0pt;
        font-family: Courier New, Times New Roman;
    }

    .text-center {
        text-align: center;
    }

    p {
        margin: 0pt !important;
        padding: 2pt;
        font-size: 10pt;
        
    }



    span {
        text-align: right;
    }
</style>
<title></title>
</head>

<body>
    <htmlpageheader name="MyCustomHeader">
        <table width="100%">

            <tr>
                <td colspan="2" rowspan="2" width="50%">
                    <h2 style="text-align:left; font-family: Times New Roman; font-size: 12pt;"></h2>
                    <p style="font-size: 22pt;font-family: Times New Roman;"><b>REAL ESTATE PROPERTY REPORT</b></p>
                    <p style="font-size: 20px;font-family: Times New Roman;"><b>ISGN FULFILLMENT SERVICES, INC.</b></p>
                    <p style="font-size: 20px;font-family: Times New Roman;"><b>PHONE: (855) 884-8001 FAX: (877) 760-0228</b></p>
                </td>
                <td style="border:1px solid #333;" width="20%">
                    <p style="font-family: Times New Roman; font-size:12pt;"><b>Date of Search:<br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; %%SearchFromDate%%</b></p>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid #333;">
                    <p style="font-family: Times New Roman; font-size:12pt;"><b>Search As Of :<br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; %%SearchAsOfDate%%</b></p>
                </td>
            </tr>
            <tr>
                <td width="">
                    <tr>
                        <td width="60%">
                            <p>&nbsp;</p>
                        </td>
                        <td style="border:1px solid #333;" width="15%">
                            <p style="font-family: Times New Roman; font-size:12pt;"><b>Code: &nbsp; N/A</b></p>
                        </td>
                        <td style="border:1px solid #333;" width="31%">
                            <p style="font-family: Times New Roman; font-size:12pt;"><b>Cost: &nbsp; </b></p>
                        </td>
                    </tr>
                </td>
            </tr>
            <tr>
                <td colspan="4">
                    <table  cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            <td rowspan="4" style=" padding:2pt;">
                                <table cellspacing="0" width="100%">
                                    <tr>
                                        <td style="border:1px solid #333;" width="40%">
                                            <p style="font-family:Arial; font-size:8pt;">%%CustomerName%%</p>
                                            <p style="font-family:Courier New; font-size:8pt;"><b>LOAN NO:&nbsp; %%Loannumber%%</b></p>
                                            <p style="font-family:Arial;font-size:8pt;">Client Address: <br>%%CustomerAddress1%%</p>
                                            <p style="font-family:Arial;font-size:8pt;">%%CustomerAddress2%%</p>
                                            <p style="font-family:Arial; font-size:7pt;"> %%CustomerStateCode%% %%CustomerCountyName%% %%CustomerCityName%% %%CustomerZipCode%%</p>
                                            <p style="font-family:Arial; font-size:7pt;">PHONE: %%CustomerPContactMobileNo%%</p>
                                            <p style="font-family:Arial; font-size:7pt;">FAX: %%CustomerFaxNo%%</p>
                                        </td>
                                    </tr>
                                </table>
                            </td>

                            <td width="2%"></td>
                            <td width="60%" style="border:1px solid #333;">
                                <p style="font-family:Times New Roman; font-size:12pt;"><b>Re</b></p>
                                <p style="font-family:Courier New; font-size:8pt;">%%Borrowers%%</p>
                            </td>
                            <td colspan="2" style="border:1px solid #333; padding:3pt;">
                                <p style="font-family:Times New Roman; font-size:12pt;"><b>Sub No.</b></p>
                                <p style="font-family:Times New Roman; font-size:12pt;"><b>%%CustomerNumber%%</b></p>
                            </td>
                        </tr>
                        <tr>
                            <td width="2%"></td>
                            <td width="40%"  colspan="8" style="border:1px solid #333; padding:3pt;">
                                <p  style="font-family:Courier New; font-size:8pt;">%%Propertyaddress1%% %%Propertyaddress2%% %%Cityname%%  %%Statecode%% %%Countyname%% %%Zip%% &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p>
                            </td>
                        </tr>
                        <tr>
                            <td width="2%"></td>
                            <td width="40%" style="border:1px solid #333; padding:3pt;" width="35%">
                                <p style="font-family:Times New Roman; font-size:12pt;"><b>County</b></p>
                                <p style="font-family:Courier New; font-size:8pt;">%%Countyname%%</p>
                            </td>
                            <td colspan="2" style="border:1px solid #333; padding:3pt;">
                                <p style="font-family:Times New Roman; font-size:12pt;"><b>Order No</b></p>
                                <p style="font-family:Times New Roman; font-size:12pt;"><b>%%Ordernumber%%</b></p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

        </table>
        </htmlpageheader>

        <div style="width: 100%;  display: block;border:1px;">

            <table border="1" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td colspan="6" style="border-top:none;border-left:none;border-right:none;border-bottom:none;padding:3pt;">
                        <p><b>Special Remarks -</b> %%AddressComments%%&nbsp;&nbsp; <br>%%CountyVariance%%</p>
                    </td>
                </tr>
            </table>
            
         
                    
                        <table cellpadding="10" cellspacing="0" width="100%" style="border-top:none;border-left:1px solid #333;border-right:1px solid #333;border-bottom:1px solid #333;">
                            <tr>
                                <td rowspan="2" style="white-space: nowrap; vertical-align: top;">
                                    <p><b>Records Searched: </b> &nbsp;&nbsp;&nbsp;&nbsp;</p>
                                </td>
                                <td>
                                    <p><b>From Date:</b> &nbsp;&nbsp;&nbsp;&nbsp; %%SearchFromDate%%</p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <p><b>Through Date:</b> &nbsp;&nbsp;&nbsp;&nbsp; %%SearchAsOfDate%%</p>
                                </td>
                            </tr>
                                                        <tr>
                                <td colspan="3" style="border-top:1px solid #333; padding:8pt;text-align: center;">
                                    <p><b><u>ASSESSMENT RECORD</u></b></p>
                                </td>
                            </tr>
                            <tr>
                                <td><b>Land:</b></td>
                                <td>
                                    <p align="left">%%Land%%</p>
                                </td>
                            </tr>
                            <tr>
                                <td><b>Building:</b></td>
                                <td>
                                    <p align="left">%%Buildings%%</p>
                                </td>
                            </tr>
                            <tr>
                                <td><b>Total Value:</b></td>
                                <td>
                                    <p align="left">%%TotalValue%%</p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <p>&nbsp;</p>
                                </td>
                                <td>
                                    <p align="left">PARCEL ID: &nbsp;&nbsp; %%APN%%</p>
                                    <p style="height:15pt;"></p>
                                </td>
                            </tr>
                        </table>
               		 
                                <div class="torderdeeds">
                                <table width="100%" style="border-top:0.5px solid #333;border-left:1px solid #333;border-right:1px solid #333;border-bottom:0.5px solid #333;page-break-inside: avoid;">                            
                                        <tr>
                                            <td colspan="4" style=" padding:8pt;text-align: center;">
                                                <p align="center"><b><u>DEED RECORD</u></b></p>
                                            </td>
                                        </tr>
                                <tr>
                                    <td colspan="3">
                                        <!-- <table cellspacing="0" width="100%"> -->
                                            <tr>
                                                <td>
                                                    <p><b>Grantor:</b></p>
                                                </td>
                                                <td>
                                                    <p>%%Grantor%%</p>
                                                </td>
                                                <td>
                                                    <p><b>Grantee:</b></p>
                                                </td>
                                                <td>
                                                    <p>%%Grantee%%</p>
                                                </td>
                                            </tr>

                                            <tr style="margin-top: 20pt;">
                                                <td>
                                                    <p><b>Dated:</b></p>
                                                </td>
                                                <td>
                                                    <p>%%DeedDate%%</p>
                                                </td>
                                                <td>
                                                    <p><b>Recorded:</b></p>
                                                </td>
                                                <td>
                                                    <p>%%RecordedDate%%</p>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>
                                                    <p><b>%%DeedDBVTypeName_1%%:</b></p>
                                                </td>
                                                <td>
                                                    <p>%%Deed_DBVTypeValue_1%%</p>
                                                </td>
                                                <td>
                                                    <p><b>%%DeedDBVTypeName_2%%:</b></p>
                                                </td>
                                                <td>
                                                    <p>%%Deed_DBVTypeValue_2%%</p>
                                                </td>
                                            </tr>
                                            <tr>
                                            <td>
                                                    <p><b>Consideration:</b></p>
                                                </td>
                                                <td>
                                                    <p>%%ConsiderAmount%%</p>
                                                </td>
                                            </tr>
<!--                                             <tr>
                                                <td>
                                                    <p><b>Comments :</b></p>
                                                </td>
                                                <td colspan="8">
                                                    <p>%%DeedComments%%</p>
                                                </td>
                                            </tr> -->
                                            <tr>
                                                <td colspan="4" style="text-align: left;">
                                                    <p style="padding:5pt;">%%DeedComments%%</p>
                                                    <p style="height:25pt;">&nbsp;</p>
                                                </td>
                                            </tr>
                                        <!-- </table> -->
                                    </td>
                                </tr>
                            </table>
                            </div>
                            
                            <div class="tordermortgages">
                        <table width="100%" style="border-top:0.5px solid #333;border-left:1px solid #333;border-right:1px solid #333;border-bottom:0.5px solid #333;page-break-inside: avoid;">
                                <tr>
                                    <td colspan="4" style="text-align: center;padding-top:20px;">
                                        <p align="center" style="padding: 8pt;"><b>*** %%mortgage_increment%%  MORTGAGE/DEED OF TRUST CONTAINED IN THIS REPORT ***</b></p>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" style="padding-top:20px;">
                                        <p><b><u>%%NumberToWords%% MORTGAGE/%%MortgageDocumentTypeName%%</u></b></p>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" style="padding-top: 10px;">
                                        <table cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td width="20%">
                                                    <p><b>Holder:</b></p>
                                                </td>
                                                <td colspan="2">
                                                    <p>&nbsp;%%Mortgagee%%</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <p><b>Mortgagor:</b></p>
                                                </td>
                                                <td colspan="2">
                                                    <p>&nbsp;%%Mortgagor%%</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="white-space: nowrap;" valign="top">
                                                    <p><b>%%MortgageDBVTypeName_1%%:</b></p>
                                                </td>
                                                <td colspan="2" height="25" valign="top">
                                                    <p>&nbsp;%%Mortgage_DBVTypeValue_1%%</p>
                                                </td>
                                                <td style="white-space: nowrap;" valign="top">
                                                    <p><b>%%MortgageDBVTypeName_2%%:</b></p>
                                                </td>
                                                <td colspan="2" height="25" valign="top">
                                                    <p>&nbsp;%%Mortgage_DBVTypeValue_2%%</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <p><b>Dated:</b></p>
                                                </td>
                                                <td colspan="4">
                                                    <p>&nbsp;%%MortgageDate%%</p>
                                                </td>
                                                <td colspan="4">
                                                    <p><b>Recorded:</b></p>
                                                </td>
                                                <td>
                                                    <p>&nbsp;%%MortgageRecordedDate%%</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <p><b>Amount:</b></p>
                                                </td>
                                                <td colspan="4">
                                                    <p>&nbsp;%%MortgageAmount%%</p>
                                                </td>
                                                <td colspan="4">
                                                    <p><b>Future Advance:</b></p>
                                                </td>
                                                <td>
                                                    <img src=%%Url%%assets/Pictures/%%MTG%% style="height: 20px;width: 20px;">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <p></p>
                                                </td>
                                                <td colspan="8">
                                                    <p>%%MortgageComments%%</p>
                                                </td><br>
                                            </tr>
                                        </table>
                                        <p style="height: 25pt;"></p>
                                    </td>
                                </tr>
                        </table>
                            </div>
                            <table width="100%" style="border-top:0.5px solid #333;border-left:1px solid #333;border-right:1px solid #333;border-bottom:0.5px solid #333;page-break-inside: avoid;">
                            <tr>
                            <td>
                                 <p>LEGAL DESCRIPTION</p>
                            </td>
                            </tr>
                                 <tr>
                                    <td colspan="8">
                                        <p>THE FOLLOWING DESCRIBED PROPERTY SITUATED IN THE COUNTY OF %%Countyname%%</p>
                                        <p>AND STATE OF %%Statecode%%, TO WIT:-</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="8">
                                        <p>%%LegalDescr%%</p>
                                    </td>
                                </tr>
                            </table>
                            
                            <div class="tordertaxcerts">
                        <table width="100%" style="border-top:0.5px solid #333;border-left:1px solid #333;border-right:1px solid #333;border-bottom:0.5px solid #333;page-break-inside: avoid;">
                                <tr>
                                    <td colspan="4" style="padding: 8pt;text-align: center; &gt; &lt;p align=">
                                        <b><u>TAX RECORD</u></b>
                                        <p>
                                    </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" style="border-bottom: 0px solid #333;">
                                            <table cellpadding="0" cellspacing="0" width="100%">
                                                <tr>
                                                    <td width="20%">
                                                        <p>PARCEL ID&nbsp;:&nbsp;%%ParcelNumber%%</p>
                                                    </td>
                        
                                                </tr>
                                                %%taxinstallment%%
                                                <tr>
                                                    <td colspan="3">
                                                        <p>%%TaxBasisName%%</p>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="8">
                                                        <p>Comments&nbsp;:&nbsp;%%TaxComments%%</p>
                                                    </td>
                                                </tr>
                                            </table>
                                            <p style="height: 25pt;"></p>
                                        </td>
                                    </tr>
                            </table>
                                </div>
                            <br>
                        <!-- </td> -->
                    </tr>
                <!-- </table> -->
            <div style="page-break-after: auto;"></div>
                <table style="page-break-inside: avoid;border:1px solid #333" width="100%">
                    <tr>
                        <td colspan="3" style="padding: 8pt; font-family:Courier New; font-size:10pt;text-align: center;">
                            <p><b><u>JUDGMENTS</u></b></p>
                        </td>
                    </tr>
                    <div class="torderjudgments">
                        <tr>
                            <td colspan="3" style="padding: 8pt;">
                                <p style=" font-family: Arial; font-size: 8pt;"><span style="text-transform: uppercase;">%%JudgementDocumentTypeName%%</span> against %%Defendent%%, in favor of &nbsp;%%Plaintiff%%, in the amount of %%JudgementAmount%%, plus penalties, interest and costs, if applicable, dated 
                                %%JudgeDated%%, filed %%JudgeFiled%%, %%JudgementDBVTypeName_1%% %%Judgement_DBVTypeValue_1%% %%JudgementDBVTypeName_2%% %%Judgement_DBVTypeValue_2%% Comments: %%JudgementComments%%</p>
                            </td>
                            
                        </tr>
                    </div>
                    <hr>
                     <tr>
                        <td colspan="3" style="padding: 8pt; font-family:Courier New; font-size:10pt;text-align: center;">
                            <p><b><u>LIENS</u></b></p>
                        </td>
                    </tr>

                    <div class="torderliens">
                    <tr>
                        <td colspan="3" style="padding: 8pt;">
                            <p style=" font-family: Arial; font-size: 8pt;"><span style="text-transform: uppercase;">%%LeinDocumentTypeName%%</span> against %%ExecutedBy%%,in favour of %%Holder%% in the amount of %%LeinAmt%%,plus penalties,interest and costs,if applicable ,dated %%LeinDate%%, filed %%LeinFiled%% ,%%LienDBVTypeName_1%% %%Lien_DBVTypeValue_1%% %%LienDBVTypeName_2%% %%Lien_DBVTypeValue_2%% Comments:%%LeinComments%%</p>
                        </td>
                    </tr>
                    </div>
                        <!-- <hr> -->
<!--                     <tr>
                        <td colspan="3" style="padding: 8pt; border:1px solid #333;border-top:none; border-bottom:none; font-family:Courier New; font-size:10pt;text-align: center;">
                            <p><b><u>MISC EXCEPTION</u></b></p>
                        </td>
                    </tr>

                    <div class="miscexception">
                    <tr>
                        <td colspan="3" style="padding: 8pt; border:1px solid #333;border-top:0;border-bottom: none;text-align: center;">
                            <p style=" font-family: Arial; font-size: 8pt;text-align: left;">%%LeinsubDocumentTypeName%% against %%ExecutedBy%%,in favour of %%Holder%% in the amount of %%LeinAmount%%,plus penalties,interest and costs,if applicable ,dated %%LeinDated%%, filed %%LeinFiled%% ,BOOK %%LienBook%% PAGE %%LienPage%%.Comments:%%LeinComments%%</p>
                           
                        </td>
                    </tr> -->
                    </div>
                            <hr>
                    <tr>
                        <td colspan="3" style="padding: 8pt;">
                            <p>ABSTRACTOR:  STAFF</p>
                            <p>CERTIFYING ATTORNEY:  </p>
                            <p>THANK YOU, ISGN FULFILLMENT SERVICES, INC., %%UserID%% %%Ordernumber%%  %%ReportGeneratedDateTime%% </p>
                        </td>
                    </tr>
<!--                     <tr>
                        <td colspan="3" style="padding: 8pt; border:1px solid #333;border-top:none;border-bottom: none;">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" style="padding: 8pt; border:1px solid #333;border-top:none;">
                        </td>
                    <tr> -->
                

                </table>

                 <div style="page-break-after: always;"></div>

                <table style="width: 100%;border:1px solid #333;" cellspacing="0"  style="">
                <tr>
                    <td style="text-align: center;">
                        <p><b><u>MAP</u></b></p>
                    </td>
                </tr>


                <tr>
                    <td class="td-bd " style="">

                    <img src=%%Url%%%%MapAddress%% class="stretch" alt="" style="height: 300px;width: 750px;">
                    <!-- <img src=http:\\localhost\Direct2Title%%MapAddress%% alt="" 
                        class="stretch" style="width:750px;height:300px;"> -->
                    </td>
                </tr>
                </table>
            </div>


       <!--  <div style="page-break-after: always;"></div>
        <div style="width: 100%;  display: block;border:1px;border-style: double;">
        <table  width="100%">
        <tr>

            <td colspan="4" style="padding:8pt;text-align: center;">
                <p align="center"><b><u>RECORDING INFORMATION</u></b></p>
            </td>
        </tr>

        <tr>
            <td><b>IL Lake County</b></td>
            <td style="padding-left: 175pt;"><p><b>id:</b>850-IL054-17</p></td>          
        </tr>

        </table>
        <table  width="100%" >
        <tr>
            <td colspan="4" style="padding:8pt;text-align: center;">
            </td>
        </tr>
        <hr>
        </table>


         <table  width="100%" >
            <tr>
                <td><b>Address:</b></td>
                <td></td>
            </tr>

            <tr>
                <td><b>US Postal Service Delivery</b></td>
                <td><b>Courier Delivery</b></td>
            </tr>
            <tr>
                <td>Attn: Real Estate Recording<br>Lake County Recorder<br>18 North County Street<br>Courthouse - 2nd Floor<br>Waukegan, IL 60085-4358</td>
                 <td>Attn: Real Estate Recording<br>Lake County Recorder<br>18 North County Street<br>Courthouse - 2nd Floor<br>Waukegan, IL 60085</td>
            </tr>
            <tr>
                <td><b>Phone:</b> (CST) <b>847-377-2575</b></td>
                <td><b>Fax:</b>847-625-7200</td>
            </tr>
            <tr>
                <td><b>Make Checks Payable To:</b> </td>
                <td>Lake County Recorder</td>
            </tr>

            <tr>
                <td><b>Courthouse Hours:</b> 8:30AM-5PM</td>
                <td></td>
            </tr>

         </table>

        <table  width="100%" >
        <tr>
            <td colspan="4" style="padding:8pt;text-align: center;">
            </td>
        </tr>
        <hr>
        </table>
        <table>
            <tr>
                <td><b>Basic Recording Fees:</b></td>
               
            </tr>
            <tr>
                <td>Deed/Mortgage $39.00 for the first 4 pages. Included is $10 for the statewide surcharge fee <br>Amendment/Modification $39.00 for the first 4 pages. Included is $10 for the statewide surcharge fee <br>Assignment $39.00 for the first 4 pages. Included is $10 for the statewide surcharge fee <br>Release $39.00 for the first 4 pages. Included is $10 for the statewide surcharge fee</td>
            </tr>
            <br><br>
            <tr>
                <td><b>Additional Recording Fees:</b></td>
            </tr>
            <tr>
                <td>Additional pages and attachments $1.00 per additional page<br>Multiple instrument number references $1.00 per reference after first <br>Reference to property description by document # $1.00 per reference <br>Non-standard document see fee notes below Multiple assignments $7.00 per assignment after first</td>
            </tr>
            <br><br>
            <tr>
                <td><b>Transfer and Financing Fees/Taxes:</b></td>
            </tr>
            <tr>
                <td>State/County Combined Real Estate Transfer Tax 0.75 per $500 or fraction thereof of consideration</td>
            </tr>
            <br><br>
            <tr>
                <td><b>Searches, Copies and Certification:</b></td>
            </tr>
            <
            <tr>
                <td>Certification $0.00 Same as recording fee; includes copies</td>
            </tr>
        </table>
        </table>
        </div>

<div style="page-break-after: always;"></div>
<div style="width: 100%;  display: block;border:1px;border-style: double;">
<table>
    <tr>
        <td><b>Document/Recording Notes:</b></td>
    </tr>
    <tr>
        <td>Blanket assignments are accepted. Blanket releases are accepted.<br>
            Document numbering system: Document #. Example:<br>
            Original document is not returned.
        </td>
    </tr>
    <tr>
        <td>A self addressed stamped return envelope is not required<br>
            Add $45 for first four if the document format is not as required by statute (see
            page IL-1). Standard forms are: 8 1/2" x 11" pg with a 3" x 5" blank space at the
            top of pg (1). Anything else is a non_standard form & must be chrged the
            non-standard fee.
        </td>
    </tr>

    <tr><td>
    Single Sheet 20lb paper with black type or print of at least 10 point.<br>Also must have a 2" Margin on top and bottom of both the 1st page and the last page. If record is on Legal Sized Paper, add $12.<br>If one or both of these pages do not, recorder will add a page...costing an
    additional $2, you will also still be charged $1 non-conforming fee for each fee for each non-standard page.<br>Effective Aug 1, 2005, the State of Illinois will be charging an additional $10.00 for Rental Housing Surcharge which was passed by the State of IL by the general Assembly.</td>
    </tr>
    <br>
    <tr>
        <td>All deeds must include: statement of exemption or transfer tax declaration;<br>preparer's name/address; name/address to receive tax statements; notary date/seal/signature; complete legal description, including parcel identification;return-to address.</td>
    </tr>
    <tr>
        <td>$10 of the recording fee goes to a statewide surcharge, mandated by statute, to be collected by all Illinois Recorders / Clerk-Recorders.Legal description required with all instruments to be recorded.In subsequent related documents, include references to original document</td>
    </tr>
    <tr>
    <td>GAC has made every effort to insure the accuracy of this recording information.<br>However, due to the frequency with which courthouses revise their fees and other specifications, GAC cannot assume liability for any discrepancy in recording fees or taxes. Please call the courthouse at the above number to verify amounts prior to closing. In the event that amounts have changed, please notify GAC so that we
        may update our records.</td>
    </tr><br><br>
    <tr>
        <td style="text-align: justify;">This report is NOT title insurance. This report only provides title information contained in the above stated records and does NOT reflect unindexed or misindexed matters or off-record matters that may affect said land. This Company, in issuing this report, assumes no liability on account of any instrument or proceeding in the chain of title to the property which may contain defects that would render such
        instrument null and void or defective. All instruments in the chain of the title to the property are assumed to be good and valid. This report is NOT a committment to insure and therefore does not contain the requirements and exceptions which would appear in a committment to insure or the exceptions which would appear in a title policy.</td>
    </tr>
    <br><br>
    <tr>
        <td style="text-align: justify;">This Company's liability for this report is limited to the amount paid for this report and extends only to the party to which it is issued. No other party may rely on this report. This report contains no express or implied opinion, warranty, guarantee, insurance, or similar assurances as to the status of title to the land.</td>
    </tr>
</table>

</div> -->



<!-- 
            <div class="noheader">
                <div style="text-align: center">
                    <h4>SCHEDULE "A"</h4>
                </div>
                <div style="text-align:left;margin-left: 100px;">
                    <p>%%Ordernumber%%</p>
                    <p>THE FOLLOWING DESCRIBED PROPERTY SITUATED IN THE COUNTY OF %%Countyname%%</p>
                    <p>AND STATE OF %%Statecode%%, TO WIT:-</p>
                    <p>%%LegalDescr%%</p>
                    <p>PARCEL NO. %%APN%%</p>
                </div>
            </div> -->

        </body>

        </html>