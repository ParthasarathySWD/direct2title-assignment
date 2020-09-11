<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <style>
        @page {
             header: html_MyCustomHeader;/* display <htmlpageheader name="MyCustomHeader"> on all pages */
            footer: html_MyCustomFooter;/* display <htmlpagefooter name="MyCustomFooter"> on all pages */
        }

            @page noheaderfooter {
                footer: _blank;
                header: _blank;
            }
            @page header {

                 header: _blank;
            }
            @page footer {

                 footer: _blank;
            }
            @page displayheader{

               header: html_MyCustomHeader;
            }
            @page displayfooter{

                footer: html_MyTaxLegalFooter;
            }
            div.noheaderfooter 
            {
                page: noheaderfooter;
            }
            div.noheader{
                page: header;
            }
            div.nofooter
            {
                page: footer;
            }
            div.displayheader{
                page:displayheader;
            }
            div.displayfooter{
                page:displayfooter;
            }

        
        @page {
            margin-top:2.9cm;
            margin-left: 2cm;
            margin-right: 2cm;
            margin-bottom: 3cm;
        }
        

        table { page-break-inside:avoid;}
        td    { page-break-inside:avoid; page-break-after:auto }
        thead { display:table-header-group }
        tfoot { display:table-footer-group }
        
        * {
            font-family: 'Calibri', Times, serif;
        }
        
        body {
            font-family: 'Calibri', Times, serif;
            font-size: 7pt;
            margin: 10pt 20pt;
        }
        
        hr {
            border: none;
            border-top: 0.01em solid grey;
        }
        
        p {
            margin: 0;
            font-size: 7pt;
            padding: 2px;
        }
        
        footer {
            display: block;
        }


        
        .wrapper {
            width: 100%;
        }
        
        p.blur {
            height: 10pt;
            background: rgba(255, 255, 255, 1);
            background: -moz-linear-gradient(top, rgba(255, 255, 255, 1) 0%, rgba(219, 219, 219, 1) 45%, rgba(209, 209, 209, 1) 50%, rgba(254, 254, 254, 1) 86%, rgba(254, 254, 254, 1) 100%);
            background: -webkit-gradient(left top, left bottom, color-stop(0%, rgba(255, 255, 255, 1)), color-stop(45%, rgba(219, 219, 219, 1)), color-stop(50%, rgba(209, 209, 209, 1)), color-stop(86%, rgba(254, 254, 254, 1)), color-stop(100%, rgba(254, 254, 254, 1)));
            background: -webkit-linear-gradient(top, rgba(255, 255, 255, 1) 0%, rgba(219, 219, 219, 1) 45%, rgba(209, 209, 209, 1) 50%, rgba(254, 254, 254, 1) 86%, rgba(254, 254, 254, 1) 100%);
            background: -o-linear-gradient(top, rgba(255, 255, 255, 1) 0%, rgba(219, 219, 219, 1) 45%, rgba(209, 209, 209, 1) 50%, rgba(254, 254, 254, 1) 86%, rgba(254, 254, 254, 1) 100%);
            background: -ms-linear-gradient(top, rgba(255, 255, 255, 1) 0%, rgba(219, 219, 219, 1) 45%, rgba(209, 209, 209, 1) 50%, rgba(254, 254, 254, 1) 86%, rgba(254, 254, 254, 1) 100%);
            background: linear-gradient(to bottom, rgba(255, 255, 255, 1) 0%, rgba(219, 219, 219, 1) 45%, rgba(209, 209, 209, 1) 50%, rgba(254, 254, 254, 1) 86%, rgba(254, 254, 254, 1) 100%);
            filter: progid: DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#fefefe', GradientType=0);
        }


        td.blur,th.blur{
                        height: 10pt;
            background: rgba(255, 255, 255, 1);
            background: -moz-linear-gradient(top, rgba(255, 255, 255, 1) 0%, rgba(219, 219, 219, 1) 45%, rgba(209, 209, 209, 1) 50%, rgba(254, 254, 254, 1) 86%, rgba(254, 254, 254, 1) 100%);
            background: -webkit-gradient(left top, left bottom, color-stop(0%, rgba(255, 255, 255, 1)), color-stop(45%, rgba(219, 219, 219, 1)), color-stop(50%, rgba(209, 209, 209, 1)), color-stop(86%, rgba(254, 254, 254, 1)), color-stop(100%, rgba(254, 254, 254, 1)));
            background: -webkit-linear-gradient(top, rgba(255, 255, 255, 1) 0%, rgba(219, 219, 219, 1) 45%, rgba(209, 209, 209, 1) 50%, rgba(254, 254, 254, 1) 86%, rgba(254, 254, 254, 1) 100%);
            background: -o-linear-gradient(top, rgba(255, 255, 255, 1) 0%, rgba(219, 219, 219, 1) 45%, rgba(209, 209, 209, 1) 50%, rgba(254, 254, 254, 1) 86%, rgba(254, 254, 254, 1) 100%);
            background: -ms-linear-gradient(top, rgba(255, 255, 255, 1) 0%, rgba(219, 219, 219, 1) 45%, rgba(209, 209, 209, 1) 50%, rgba(254, 254, 254, 1) 86%, rgba(254, 254, 254, 1) 100%);
            background: linear-gradient(to bottom, rgba(255, 255, 255, 1) 0%, rgba(219, 219, 219, 1) 45%, rgba(209, 209, 209, 1) 50%, rgba(254, 254, 254, 1) 86%, rgba(254, 254, 254, 1) 100%);
            filter: progid: DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#fefefe', GradientType=0);
        }
        /*         div.image{
         width: 20%; background-image: url(isgn.png); background-repeat: no-repeat;
         }*/
        
        .pd-20 {
            padding: 0pt 20pt;
        }
        
        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
            text-transform: uppercase;
        }


        
        .td-bd {
            border: 0.01em solid grey;
            

        }
        
        .bold {
            font-weight: bold;
        }
        
        .pdrl-30 {
            padding: 0pt 30pt 30pt 30pt;
        }
        table
        {
            border-collapse: collapse;
        }

        .br-t-trans{
            border-top-style : none !important;
        }
        .br-b-trans{
            border-bottom-style : none !important;
        }
        .br-l-trans{
            border-left-style : none !important;
        }
        .br-r-trans{
            border-right-style : none !important;
        }

        .br-trans{
            border-top-style : none !important;
            border-bottom-style : none !important;
            border-left-style : none !important;
            border-right-style : none !important;
        }

        .br-black{
            border: 0.01em solid grey;
        }

    </style>
</head>

<body>

<thead>
<htmlpageheader name="MyCustomHeader">
        <div>
            <div style="float: left; width: 80%">
                <p><img src=%%ImageUrl%% style="width:115pt;z-index: 9999;margin-left: -10px;"></p>
            </div>
            <div style="float: right; width: 20%">
                 <p style="text-align: right;"></p>
            </div>
        </div>

</htmlpageheader>
</thead>
<tfoot>
  
    <htmlpagefooter name="MyCustomFooter">
        <p style="text-align: justify;font-size: 6pt; margin-top:75px;"><b>USE OF THIS REPORT:  </b>%%DisclaimerNote%%. </p>
        <div>
            <div style="float: left; width: 70%">
                <p> %%DisclaimerStatePhoneNumber%%&nbsp;&nbsp;%%DisclaimerStateWebsite%%</p>
            </div>
            <div style="float: right; width: 30%">
                <p style="text-align: right;">%%Ordernumber%%&nbsp;&nbsp;&nbsp;Page {PAGENO}</p>
            </div>
        </div>
    </htmlpagefooter>

    <htmlpagefooter name="MyTaxLegalFooter">
        <p style="text-align: justify;font-size: 6pt; margin-top:75px;"><b>USE OF THIS REPORT:  </b>%%DisclaimerNote%%. </p>
        <div>
            <div style="float: left; width: 70%">
                <p> %%DisclaimerStatePhoneNumber%%&nbsp;&nbsp;%%DisclaimerStateWebsite%%</p>
            </div>
            <div style="float: right; width: 30%">
                <p style="text-align: right;">%%Ordernumber%%</p>
            </div>
        </div>
    </htmlpagefooter>

</tfoot>

    <div class="wrapper">

             <div style="width: 100%; padding: 0pt 50pt;">
            <h2 style="text-align: center;font-size: 16pt; padding: 5px 0px;">%%ReportHeading%%</h2>
            </div>
    
       <!--  <div class="pdrl-30"> -->

            <!-- <div class="pd-30"> -->
                <p class="blur text-center" style="font-size: 10pt;font-weight: bold;margin-top: 0px;">Order Information</p>

                <table style="width: 100%;margin-top: 10pt;" cellspacing="0" >
                    <tr style="border: 0.01em solid grey;">
                        <td width="15.58%" class="td-bd text-center">
                            <p style="font-size: 7pt;" class="bold">Order Date</p>
                        </td>
                        <td width="11.68%" class="td-bd text-center">
                            <p style="font-size: 7pt;">%%OrderDate%%</p>
                        </td>
                        <td width="7.79%" class="td-bd text-center">
                            <p style="font-size: 7pt;" class="bold">Order #</p>
                        </td>
                        <td width="16.13%" class="td-bd text-center">
                            <p style="font-size: 7pt;">%%Ordernumber%%</p>
                        </td>
                        <td width="15.58%" class="td-bd text-center" style="white-space: nowrap;">
                            <p style="font-size: 7pt;" class="bold">Search Through Date</p>
                        </td>
                        <td width="11.12%" class="td-bd text-center">
                            <p style="font-size: 7pt;">%%SearchFromDate%%</p>
                        </td>
                        <td width="7.24%" class="td-bd text-center">
                            <p style="font-size: 7pt;" class="bold">Loan #</p>
                        </td>
                        <td width="15.98%" class="td-bd text-center" style="white-space: nowrap;">
                            <p style="font-size: 7pt;">%%Loannumber%%</p>
                        </td>
                    </tr>

                    <tr> 
                        <td   class="td-bd text-center" colspan="1">
                            <p style="font-size: 7pt;" class="bold">Requestor</p>
                        </td>
                        <td  class="td-bd" colspan="3">
                            <p style="font-size: 7pt;">%%CustomerName%%</p>
                        </td>
                        <td  class="td-bd text-center" colspan="1">
                            <p style="font-size: 7pt;" class="bold">Effective Date</p>
                        </td>
                        <td  class="td-bd text-center" colspan="1">
                            <p style="font-size: 7pt;">%%SearchAsOfDate%%</p>
                        </td>
                        <td  class="td-bd text-center" colspan="1">
                            <p style="font-size: 7pt;" class="bold">Attention:</p>
                        </td>
                        <td  class="td-bd text-center" colspan="1">
                            <p style="font-size: 7pt;">%%AttentionName%%</p>
                        </td>
                    </tr>
                    <tr>
                        <td class="td-bd text-center" colspan="1">
                            <p style="font-size: 7pt;" class="bold">Applicant Name</p>
                        </td>
                        <td class="td-bd" colspan="7">
                            <p style="font-size: 7pt;">%%Borrowers%%</p>
                        </td>
                    </tr>
                    </table>
                     <table style="width: 100%;table-layout: fixed;" cellspacing="0" >
                    <tr>
                        <td class="td-bd text-center" colspan="2">
                            <p style="font-size: 7pt;" class="bold text-center">Ordered Address</p>
                        </td>
                        <td class="td-bd text-center" colspan="2">
                            <p style="font-size: 7pt;" class="bold text-center">Assessed Address</p>
                        </td>
                        <td class="td-bd text-center" colspan="2">
                            <p style="font-size: 7pt;" class="bold text-center">USPS Address</p>
                        </td>
                    </tr>

                    <tr>
                        <td class="td-bd text-center" colspan="2"  style="text-transform: uppercase;">
                            
                            <p style="font-size: 7pt;" class="text-center">%%PropertyAddress%%</p>
                  
                            <p style="font-size: 7pt;" class="text-center">%%PropertyCityName%% %%PropertyStateCode%% 
                             %%PropertyZip%%</p>
                             <br>
                        </td>
                        <td class="td-bd text-center" colspan="2" style="text-transform: uppercase;"> 
                            
                            <p style="font-size: 7pt;" class="text-center">%%AssessedAddress%%</p>
                  
                            <p style="font-size: 7pt;" class="text-center">%%AssessedCityName%% %%AssessedStateCode%%  %%AssessedZipcode%%</p>
                            <br>
                        </td>

                        <td class="td-bd text-center" colspan="2" style="text-transform: uppercase;">
                            
                            <p style="font-size: 7pt;" class="text-center">%%USPSAddress%%</p>
                        
                            <p style="font-size: 7pt;" class="text-center">%%USPSCityName%% %%USPSStateCode%%  %%USPSZipcode%%</p>
                            <br>
                        </td>
                    </tr>

                    <tr>
                        <!-- <table cellspacing="0" style="width: 100%;"> -->
                           <!--  <tr> -->
                                <td class="td-bd text-center" width="7.79%">
                                    <p style="font-size: 7pt;" class="bold text-center">County</p>
                                </td>
                                <td class="td-bd text-center" width="25.96%">
                                    <p style="font-size: 7pt;" class="text-center">%%PropertyCountyName%%</p>
                                </td>
                                <td class="td-bd text-center" width="7.79%">
                                    <p style="font-size: 7pt;" class="bold text-center">County</p>
                                </td>
                                <td class="td-bd text-center" width="25.96%">
                                    <p style="font-size: 7pt;" class="text-center">%%AssessedCountyName%%</p>
                                </td>
                                <td class="td-bd text-center" width="7.79%">
                                    <p style="font-size: 7pt;" class="bold text-center">County</p>
                                </td>
                                <td class="td-bd text-center" width="25.96%">
                                    <p style="font-size: 7pt;" class="text-center">%%USPSCountyName%%</p>
                                </td>
                           <!--  </tr> -->
                       <!--  </table> -->
                    </tr>
                    </table>
                     <table style="width: 100%;table-layout: fixed;" cellspacing="0" >
                    <tr>
                        <!-- <table cellspacing="0" style="width:100%;">
                            <tr> -->
                                <td class="td-bd text-center" width="15.30%">
                                    <p style="font-size: 7pt;" class="bold text-center">PACER</p>
                                </td>
                                <td class="td-bd text-center" width="15.30%">
                                    <p style="font-size: 7pt;" class="text-center">%%ParcerNo%%</p>
                                </td>
<!--                                 <td class="td-bd text-center" width="11.68%" style="white-space: nowrap;">
                                    <p style="font-size: 7pt;" class="bold text-center">Patriot Search</p>
                                </td>
                                <td class="td-bd text-center" width="7.79%">
                                    <p style="font-size: 7pt;" class="text-center">CLEAR</p>
                                </td> -->
                                <td class="td-bd text-center" width="18.30%" style="white-space: nowrap;">
                                    <p style="font-size: 7pt;" class="bold text-center">Domestic Partnership</p>
                                </td>
                                <td class="td-bd text-center" width="18.30%">
                                    <p style="font-size: 7pt;" class="text-center">%%DomesticPartnership%%</p>
                                </td>
                                <td class="td-bd text-center" width="15.30%" style="white-space: nowrap;">
                                    <p style="font-size: 7pt;" class="bold text-center">Search Length</p>
                                </td>
                                <td class="td-bd text-center" width="18.30%">
                                    <p style="font-size: 7pt;" class="text-center">%%SearchLengthType%%</p>
                                </td>
                           <!--  </tr>
                        </table> -->
                    </tr>
                    <tr>
                        <td class="td-bd text-center" colspan="1">
                            <p style="font-size: 7pt;" class="bold">Comments</p>
                        </td>
                        <td class="td-bd" colspan="5">
                            <p style="font-size: 7pt;" class="text-left">%%AddressComments%%<br>%%CountyVariance%%</p>
                        </td>
                    </tr>

                </table>
            <!-- </div> -->

            <!-- <div class="pd-30"> -->
                <p class="blur text-center" style="font-size: 10pt;width:100%; font-weight: bold;margin-top: 5pt;">Property Information</p>
                <div class="torderproperty">
                <table cellspacing="0" style="width: 100%;margin-top: 8pt;border: 0.01em solid grey;">
                    <tr>
                        <td class="td-bd text-center">
                            <p style="font-size: 7pt;" class="bold text-center" colspan="1">Current Ownership</p>
                        </td>
                        <td class="td-bd" colspan="7">
                            <p style="font-size: 7pt;">%%OwnerName%% <span style="color:#ff0000; ">%%MaritalStatusName%%</span></p>
                        </td>
                    </tr>
                    <tr>
                        <td class="td-bd text-center">
                            <p style="font-size: 7pt;" class="bold" colspan="1">Manner of Title</p>
                        </td>
                        <td class="td-bd text-center" colspan="4">
                            <p style="font-size: 7pt;" class="text-center">%%MannerofTitle%%</p>
                        </td>
                        <td class="td-bd text-center" colspan="2">
                            <p style="font-size: 7pt;" class="text-center">%%TenancyTypeName%%</p>
                        </td>
                        <td colspan="1" class="td-bd text-center" style="">
                        <a  target="_blank" title="none" href="https://www.google.co.in/maps/place/%%GoogleMapAddress%%"  id="GoogleMap" style="font-size: 7pt;color:#0fc4c4;" class="bold text-center">Google Map</a>
<!--                             <a onclick="app.launchURL('https://www.google.co.in/maps/place/%%GoogleMapAddress%%', ''); 
 return false" href="javascript:void(0);" style="font-size: 7pt;color:#0fc4c4;text-transform: uppercase;" class="bold text-center">Google Map</a> -->

                        </td>
                    </tr>
                    <tr>
                        <td class="td-bd text-center" style="width: 15.33%;" style="">
                            <p style="font-size: 7pt;" class="bold">Mortgage count</p>
                        </td>
                        <td class="td-bd text-center" style="width: 5.44%;">
                            <p style="font-size: 7pt; padding:0px 3px;" class="text-center">%%MortgageCount%%</p>
                        </td>
                        <td class="td-bd text-center" style="width: 15.63%;">
                            <p style="font-size: 7pt;" class="bold text-center">Judgment Count</p>
                        </td>
                        <td class="td-bd text-center" style="width: 5.45%;">
                            <p style="font-size: 7pt; padding:0px 3px;" class="text-center">%%JudgementCount%%</p>
                        </td>
                        <td class="td-bd text-center" style="width: 15.75%;">
                            <p style="font-size: 7pt;" class="bold text-center">Lien Count</p>
                        </td>
                        <td class="td-bd text-center" style="width: 5.45%;">
                            <p style="font-size: 7pt; padding:0px 3px;" class="text-center">%%LienCount%%</p>
                        </td>
                        <td class="td-bd text-center" style="width: 16.48%;">
                            <p style="font-size: 7pt;" class="bold text-center">Assessor Parcel #</p>
                        </td>
                        <td class="td-bd text-center" style="width: 20.46%;" >
                            <p style="font-size: 7pt;" class="text-center">%%APN%%</p>
                        </td>
                    </tr>
                    <tr>
                        <td class="td-bd text-center" colspan="1">
                            <p style="font-size: 7pt;" class="bold">Block/Lot</p>
                        </td>
                        <td class="td-bd text-center" colspan="2">
                            <p style="font-size: 7pt;" class="text-center">%%Lot%%</p>
                        </td>
                        <td class="td-bd text-center" colspan="2">
                            <p style="font-size: 7pt;" class="bold text-center">Subdivision</p>
                        </td>
                        <td class="td-bd text-center" colspan="3">
                            <p style="font-size: 7pt;" class="text-center">%%SubDivisionName%%</p>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center" style="border:0px; border-style: solid; border-top: 0.01em solid grey;border-left: 0.01em solid grey;border-right: 0.01em solid grey;" colspan="1">
                            <p style="font-size: 7pt;" class="bold">Tax Year</p>
                        </td>
                        <td class="text-center" style="border:0px; border-style: solid; border-top: 0.01em solid grey;border-right: 0.01em solid grey;" colspan="3">
                            <p style="font-size: 7pt;" class="text-center">%%LatestTaxYear%%</p>
                        </td>
                        <td class="text-center" style="border:0px; border-style: solid; border-top: 0.01em solid grey;border-right: 0.01em solid grey;" colspan="2">
                            <p class="bold text-center" style="font-size: 7pt;">Property Tax</p>
                        </td>
                        <td class="text-center" style="border:0px; border-style: solid; border-top: 0.01em solid grey;border-right: 0.01em solid grey;" colspan="2">
                            <p style="font-size: 7pt;" class="text-center">%%ReferTaxSection%%</p>
                        </td>
                    </tr>
                    <tr>
                        <td class="td-bd text-center" colspan="1">
                            <p style="font-size: 7pt;" class="bold">Assessed Year</p>
                        </td>
                        <td class="td-bd text-center" colspan="3">
                            <p style="font-size: 7pt;" class="text-center">%%AssessedYear%%</p>
                        </td>
                        <td class="td-bd text-center" colspan="2">
                            <p class="bold text-center" style="font-size: 7pt;">Total Assessed Value</p>
                        </td>
                        <td class="td-bd text-center" colspan="2">
                            <p style="font-size: 7pt;" class="text-center">%%TotalValue%%</p>
                        </td>
                    </tr>
                    <tr>
                        <td class="td-bd text-center"  style="white-space: nowrap;" colspan="1">
                            <p style="font-size: 7pt;" class="bold">Delinquent Tax</p>
                        </td>
                        <td class="td-bd text-center" colspan="3">
                            <p style="font-size: 7pt;" class="text-center">%%DeliquentTax%%</p>
                        </td>
                        <td class="td-bd text-center" colspan="2">
                            <p class="bold text-center" style="font-size: 7pt;">Land Value</p>
                        </td>
                        <td class="td-bd text-center" colspan="2">
                            <p style="font-size: 7pt;" class="text-center">%%Land%%</p>
                        </td>
                    </tr>
                    <tr>
                        <td class="td-bd text-center" colspan="1">
                            <p style="font-size: 7pt;" class="bold">Exemption</p>
                        </td>
                        <td class="td-bd text-center" colspan="3">
                            <p style="font-size: 7pt;" class="text-center">%%Exempt%%</p>
                        </td>
                        <td class="td-bd text-center" colspan="2">
                            <p class="bold text-center" style="font-size: 7pt;">Improvement Value</p>
                        </td>
                        <td class="td-bd text-center" colspan="2">
                            <p style="font-size: 7pt;" class="text-center">%%Buildings%%</p>
                        </td>
                    </tr>
<!--                     <tr>
                        <td width="12.5%" class="td-bd text-center" colspan="1">
                            <p style="font-size: 7pt;" class="bold">Other</p>
                        </td>
                        <td width="35.0%" class="td-bd text-center" >
                            <p style="font-size: 7pt;" class="text-center"></p>
                        </td>
                        <td width="21.8%" class="td-bd text-center" >
                            <p class="bold text-center" style="padding:0pt 20pt;font-size: 7pt;">% Improved</p>
                        </td>
                        <td width="21.8%" class="td-bd text-center" >
                            <p class="text-center" style="font-size:8pt;">N/A</p>
                        </td>
                    </tr> -->
                    <tr>
                        <td class="td-bd text-center" colspan="1">
                            <p style="font-size: 7pt;" class="bold">Comments</p>
                        </td>
                        <td class="td-bd %%alignment%%" colspan="7">
                            <p style="font-size: 7pt;">%%AssessmentValue%%</p>
                        </td>
                    </tr>
                </table>
                </div>
            <!-- </div> -->
            
            <!-- <div class="pd-30" style="page-break-after: initial;"> -->
                %%DeedHeading%%
                <div class="torderdeeds">
                <table style="width: 100%;margin-top: 10pt;page-break-inside:avoid;" cellspacing="0">
                    <tr>
                        <td class="td-bd text-center" colspan="3">
                            <p style="font-size: 7pt;" class="bold text-center">Deed-%%deed_increment%%</p>
                        </td>
                        <td class="td-bd text-center" colspan="1">
                            <p style="font-size: 7pt;" class="bold text-center">Document Type</p>
                        </td>
                        <td class="td-bd text-center" colspan="2">
                            <p class="text-center" style="padding:0pt 20pt;font-size: 7pt;text-transform: uppercase;">%%DeedDocumentTypeName%%</p>
                        </td>
                    </tr>
                    <tr>
                        <td class="td-bd text-center" colspan="1">
                            <p style="font-size: 7pt;" class="bold">Grantee</p>
                        </td>
                        <td class="td-bd" colspan="5">
                            <p class="" style="padding: 0pt 20pt;font-size: 7pt;text-transform: uppercase;">%%Grantee%% %%MaritalStatusName%%</p>
                        </td>
                    </tr>
                    <tr>
                        <td class="td-bd text-center" colspan="1">
                            <p style="font-size: 7pt;" class="bold">Grantor</p>
                        </td>
                        <td class="td-bd" colspan="5">
                            <p class="" style="padding: 0pt 20pt;font-size: 7pt;text-transform: uppercase;">%%Grantor%%</p>
                        </td>
                    </tr>
                    <tr>
                        <td width="15.30%" class="td-bd text-center">
                            <p style="font-size: 7pt;" class="bold">Document Dated</p>
                        </td>
                        <td width="18.30%" class="td-bd text-center">
                            <p style="font-size: 7pt;" class="text-center">%%DeedDate%%</p>
                        </td>
                        <td width="15.30%" class="td-bd text-center">
                            <p class="bold text-center" style="font-size: 7pt;">Recorded Date</p>
                        </td>
                        <td width="18.30%" class="td-bd text-center">
                            <p style="font-size: 7pt;" class="text-center">%%RecordedDate%%</p>
                        </td>
                        <td width="15.30%" class="td-bd text-center">
                            <p style="font-size: 7pt;" class="bold">Consideration</p>
                        </td>
                        <td width="18.30%" class="td-bd text-center">
                            <p style="font-size: 7pt;" class="text-center">%%ConsiderAmount%%</p>
                        </td>
                    </tr>
                    <tr>
                        <td class="td-bd text-center" colspan="1" style="white-space: nowrap">
                            <p style="font-size: 7pt;" class="bold">%%DBVTypeName_1%%</p>
                        </td>
                        <td class="td-bd text-center" colspan="1">
                            <p style="font-size: 7pt;" class="text-center">%%Deed_DBVTypeValue_1%%</p>
                        </td>
                        <td class="td-bd text-center" colspan="1">
                            <p class="bold text-center" style="padding:0pt 20pt;font-size: 7pt;">%%DBVTypeName_2%%</p>
                        </td>
                        <td class="td-bd text-center" colspan="1">
                            <p style="font-size: 7pt;" class="text-center">%%Deed_DBVTypeValue_2%%</p>
                        </td>
                        <td class="td-bd text-center" colspan="1">
                            <p style="font-size: 7pt;" class="text-center bold">Certificate</p>
                        </td>
                        <td class="td-bd text-center" colspan="1">
                            <p style="font-size: 7pt;" class="text-center">%%CertificateNo%%</p>
                        </td>
                    </tr>
                    <tr>
                         <td class="td-bd text-center" colspan="1">
                            <p style="font-size: 7pt;" class="bold">Village/Township/City</p>
                        </td>
                        <td class="td-bd" colspan="5">
                            <p class="" style="text-align: justify;font-size: 7pt;white-space: normal;">%%DeedTownship%%</p>
                        </td>
                    </tr>
                    <tr>
                        <td class="td-bd text-center" colspan="1">
                            <p style="font-size: 7pt;" class="bold">Comments</p>
                        </td>
                        <td class="td-bd %%deedalignment%%" colspan="5">
                            <p class="" style="text-align: justify;font-size: 7pt;white-space: normal;">%%DeedComments%%</p>
                        </td>
                    </tr>
                </table>
            </div>
            <!-- </div> -->
            
           <!-- <div style="page-break-inside: auto;"></div> -->
          <!--  <div style="padding-top: 20pt;"></div> -->
            <!-- <div class="pd-30"> -->

                <div style="page-break-inside: avoid;">
            <div class="tordermortgages">
                <table style="width: 100%;margin-top: 10pt;page-break-inside:avoid;" cellspacing="0">
                <thead style="">
                    %%MortgageHeading%%
                 </thead>
                
                 <tbody style="margin-top: 10pt;">
                    <tr style="">
                        <td class="td-bd text-center" colspan="2">
                            <p style="font-size: 7pt;" class="bold text-center">Mortgage-%%mortgage_increment%%</p>
                        </td>
                        <td class="td-bd text-center" colspan="1">
                            <p style="font-size: 7pt;" class="bold text-center">Document Type</p>
                        </td>
                        <td class="td-bd text-center" colspan="1">
                            <p class="text-center" style="font-size: 7pt;text-transform: uppercase;">%%MortgageDocumentTypeName%%</p>
                        </td>
                    </tr>
                    <tr>
                        <td class="td-bd text-center" colspan="1">
                            <p class="bold" style="font-size: 7pt;">Mortgagee</p>
                        </td>
                        <td class="td-bd" colspan="3">
                            <p class="" style="font-size: 7pt;text-transform: uppercase;">%%Mortgagee%%</p>
                        </td>
                    </tr>
                    <tr>
                        <td class="td-bd text-center" colspan="1">
                            <p class="bold" style="font-size: 7pt;">Mortgagor</p>
                        </td>
                        <td class="td-bd" colspan="3">
                            <p class="" style="font-size: 7pt;text-transform: uppercase;">%%Mortgagor%%</p>
                        </td>
                    </tr>
                    <tr>
                        <td class="td-bd text-center" colspan="1">
                            <p class="bold" style="font-size: 7pt;">Trustee</p>
                        </td>
                        <td class="td-bd" colspan="3">
                            <p class="" style="font-size: 7pt;text-transform: uppercase;">%%Trustee%%</p>
                        </td>
                    </tr>
                    <tr>
                        <td width="15.10%" class="td-bd text-center">
                            <p style="font-size: 7pt;" class="bold">Document Dated</p>
                        </td>
                        <td width="35.16%" class="td-bd text-center">
                            <p style="font-size: 7pt;" class="text-center">%%MortgageDate%%</p>
                        </td>
                        <td width="15.10%" class="td-bd text-center">
                            <p class="bold text-center" style="padding:0pt 20pt;font-size: 7pt;">Recorded</p>
                        </td>
                        <td width="35.16%" class="td-bd text-center">
                            <p style="font-size: 7pt;" class="text-center">%%MortgageRecordedDate%%</p>
                        </td>
                    </tr>
                    <tr>
                        <td class="td-bd text-center" >
                            <p style="font-size: 7pt;" class="bold">%%DBVTypeName_1%%</p>
                        </td>
                        <td class="td-bd text-center" >
                            <p style="font-size: 7pt;" class="text-center">%%Mortgage_DBVTypeValue_1%%</p>
                        </td>
                        <td class="td-bd text-center" >
                            <p class="bold text-center" style="padding:0pt 20pt;font-size: 7pt;">%%DBVTypeName_2%%</p>
                        </td>
                        <td class="td-bd text-center" >
                            <p style="font-size: 7pt;" class="text-center">%%Mortgage_DBVTypeValue_2%%</p>
                        </td>
                    </tr>
                    <tr>
                        <td class="td-bd text-center" >
                            <p style="font-size: 7pt;" class="bold">Amount</p>
                        </td>
                        <td class="td-bd text-center" >
                            <p style="font-size: 7pt;" class="text-center">%%LoanAmt%%</p>
                        </td>
                        <td class="td-bd text-center" >
                            <p class="bold text-center" style="padding:0pt 20pt;font-size: 7pt;">Open ended</p>
                        </td>
                        <td class="td-bd text-center" >
                            <p style="font-size: 7pt;" class="text-center">%%OpenEnded%%</p>
                        </td>
                    </tr>
                    <tr>
                        <td class="td-bd text-center" >
                            <p style="font-size: 7pt;" class="bold">Maturity Date</p>
                        </td>
                        <td class="td-bd text-center" >
                            <p style="font-size: 7pt;" class="text-center">%%MaturityDate%%</p>
                        </td>
                        <td class="td-bd text-center" >
                            <p class="bold text-center" style="padding:0pt 20pt;font-size: 7pt;">Additional Info</p>
                        </td>
                        <td class="td-bd text-center" >
                            <p style="font-size: 7pt;" class="text-center">%%AdditionalInfo%%</p>
                        </td>
                    </tr>
                    <tr>
                        <td class="td-bd text-center" colspan="1">
                            <p style="font-size: 7pt;" class="bold">Comments</p>
                        </td>
                        <td class="td-bd %%mortgagealignment%%" colspan="3">
                            <p class="" style="text-align: justify;font-size: 7pt;">%%MortgageComments%%</p>
                        </td>
                    </tr>
                </tbody>
                </table>
                   %%submortgage%%
            </div>
</div>
                
            <!-- </div> -->
        <!-- </div> -->

        <!--          <footer>
            <p style="text-align: justify;font-size: 6pt; margin-top:75px;"><b>USE OF THIS REPORT:  </b>All of the reports and related schedules furnished by ISGN Fulfillment Services, Inc., (“ISGN”) contain information obtained from public land records. ISGN makes no representation or warranty concerning the accuracy or completeness of these public records and the information contained therein other than as specifically set forth below.  THIS REPORT IS NOT ABSTRACT OR OPINION OF TITLE, TITLE BINDER, TITLE COMMITMENT OR GUARANTEE, OR TITLE INSURANCE POLICY. This report is not title insurance, does not reflect un-indexed or improperly indexed matters of record or off-record matters affecting title to the subject property and ISGN shall have no liability for such matters. </p>
         <div>
            <div style="float: left; width: 80%">
               <p>1-800-884-8002     www.isgnsolutions.com</p>
            </div>
            <div style="float: right; width: 20%">
               <p style="text-align: left;">90-4314567</p>
            </div>
         </div>
          </footer> -->
        <!--  -->

<!--         <table width="100%" colspan="0" style="margin-top: 200px;">
            <tr>
                <td style=""><img src="D:\Profile Backup\venkatachalamn\Desktop\isgnlogo.png" style="width:100px;height: 50pt;z-index: 9999;margin-left: 30px;"></td>
            </tr>
        </table> -->
        <!--      <div style="width: 80%; padding: 0pt 50pt;">
            <h2 style="text-align: center;">Property Report</h2>
            </div>
            -->
        <!-- <div class="pdrl-30" > -->
            <div style="padding-top: 10pt;">
            <div class="torderjudgments">
                <table style="width: 100%;margin-top: 10pt;" cellspacing="0" >
                    <tr style="border: 0.01em solid grey;">
                        <td class="td-bd text-center" colspan="2">
                            <p style="font-size: 7pt;" class="bold text-center">Judgment-%%judgement_increment%%</p>
                        </td>
                        <td class="td-bd text-center" colspan="1">
                            <p style="font-size: 7pt;" class="bold">Document Type</p>
                        </td>
                        <td class="td-bd text-center" colspan="1">
                            <p style="font-size: 7pt;text-transform: uppercase;" class="">%%JudgementDocumentTypeName%%</p>
                        </td>
                    </tr>
                    <tr>
                        <td class="td-bd text-center" colspan="1">
                            <p style="font-size: 7pt;" class="bold">Creditor</p>
                        </td>
                        <td class="td-bd" colspan="3">
                            <p style="font-size: 7pt;text-transform: uppercase;">%%Plaintiff%%</p>
                        </td>
                    </tr>
                    <tr>
                        <td class="td-bd text-center" colspan="1">
                            <p style="font-size: 7pt;" class="bold">Debtor</p>
                        </td>
                        <td class="td-bd" colspan="3">
                            <p style="font-size: 7pt;text-transform: uppercase;">%%Defendent%%<span style="color:#ff0000;">&nbsp;%%MaritalStatusName%%</span></p>
                        </td>
                    </tr>
                    <tr>
                        <td class="td-bd text-center" width="15.30%">
                            <p style="font-size: 7pt;" class="bold">Document Date</p>
                        </td>
                        <td class="td-bd text-center" width="35.36%">
                            <p style="font-size: 7pt;" class="text-center">%%JudgeDated%%</p>
                        </td>
                        <td class="td-bd text-center" width="15.30%">
                            <p style="font-size: 7pt;" class="bold">Recorded Date</p>
                        </td>
                        <td class="td-bd text-center" width="35.36%">
                            <p style="font-size: 7pt;" class="text-center">%%JudgeRecorded%%</p>
                        </td>
                    </tr>
                    <tr>
                        <td class="td-bd text-center" colspan="1">
                            <p style="font-size: 7pt;" class="bold">%%JudgementDBVTypeName_1%%</p>
                        </td>
                        <td class="td-bd text-center" colspan="1">
                            <p style="font-size: 7pt;" class="text-center">%%Judgement_DBVTypeValue_1%%</p>
                        </td>
                        <td class="td-bd text-center" colspan="1">
                            <p style="font-size: 7pt;" class="bold">%%JudgementDBVTypeName_2%%</p>
                        </td>
                        <td class="td-bd text-center" colspan="1">
                            <p style="font-size: 7pt;" class="text-center">%%Judgement_DBVTypeValue_2%%</p>
                        </td>
                    </tr>
                    <tr>
                        <td class="td-bd text-center" colspan="1">
                            <p style="font-size: 7pt;" class="bold">Amount</p>
                        </td>
                        <td class="td-bd text-center" colspan="1">
                            <p style="font-size: 7pt;" class="text-center">%%JudgementAmount%%</p>
                        </td>
                        <td class="td-bd text-center" colspan="1">
                            <p style="font-size: 7pt;" class="bold">Case #</p>
                        </td>
                        <td class="td-bd text-center" colspan="1">
                            <p style="font-size: 7pt;" class="text-center">%%CaseNumber%%</p>
                        </td>
                    </tr>
                    <tr>
                        <td class="td-bd text-center" colspan="1">
                            <p style="font-size: 7pt;" class="bold">Comments</p>
                        </td>
                        <td class="td-bd %%judalignment%%" colspan="3">
                            <p class="" style="text-align: justify;font-size: 7pt;">%%JudgementComments%%</p>
                        </td>
                    </tr>
                </table>
            </div>

            <div style="padding-top: 10pt;">
                <div class="torderliens">
                <table style="width: 100%;margin-top: 10pt;" cellspacing="0">
                    <tr>
                        <td  class="td-bd text-center" colspan="2">
                            <p style="font-size: 7pt;" class="bold text-center">Lien-%%lien_increment%%</p>
                        </td>
                         <td class="td-bd text-center" colspan="1">
                            <p style="font-size: 7pt;" class="bold text-center">Document Type</p>
                        </td>
                         <td  class="td-bd text-center" colspan="1">
                            <p class="text-center" style="padding:0pt 20pt;font-size: 7pt;text-transform: uppercase;">%%LeinDocumentTypeName%%</p>
                        </td>
                    </tr>
                    <tr>
                         <td class="td-bd text-center" colspan="1">
                            <p class="bold" style="font-size: 7pt;">Lien Grantor</p>
                        </td>
                         <td class="td-bd" colspan="3">
                            <p class="" style="font-size: 7pt;">%%ExecutedBy%%</p>
                        </td>
                    </tr>
                    <tr>
                        <td class="td-bd text-center" colspan="1">
                            <p class="bold" style="font-size: 7pt;">Lien Holder</p>
                        </td>
                         <td class="td-bd" colspan="3">
                            <p class="" style="font-size: 7pt;">%%LeinHolder%%</p>
                        </td>
                    </tr>

                    <tr>
                        <td class="td-bd text-center" width="15.30%">
                            <p style="font-size: 7pt;" class="bold">Document Date</p>
                        </td>
                        <td class="td-bd text-center" width="35.36%">
                            <p style="font-size: 7pt;" class="text-center">%%LeinDate%%</p>
                        </td>
                        <td class="td-bd text-center" width="15.30%">
                            <p class="bold text-center" style="padding:0pt 20pt;font-size: 7pt;">Recorded Date</p>
                        </td>
                        <td class="td-bd text-center" width="35.36%">
                            <p style="font-size: 7pt;" class="text-center">%%leinRecord%%</p>
                        </td>
                    </tr>
                    <tr>
                        <td class="td-bd text-center" colspan="1">
                            <p style="font-size: 7pt;" class="bold">%%DBVTypeName_1%%</p>
                        </td>
                        <td class="td-bd text-center" colspan="1">
                            <p style="font-size: 7pt;" class="text-center">%%Lien_DBVTypeValue_1%%</p>
                        </td>
                        <td class="td-bd text-center" colspan="1">
                            <p class="bold text-center" style="padding:0pt 20pt;font-size: 7pt;">%%DBVTypeName_2%%</p>
                        </td>
                        <td class="td-bd text-center" colspan="1">
                            <p style="font-size: 7pt;" class="text-center">%%Lien_DBVTypeValue_2%%</p>
                        </td>
                    </tr>
                    <tr>
                        <td class="td-bd text-center"  colspan="1">
                            <p style="font-size: 7pt;" class="bold">Amount</p>
                        </td>
                        <td class="td-bd text-center"  colspan="1">
                            <p style="font-size: 7pt;" class="text-center">%%LeinAmt%%</p>
                        </td>
                        <td class="td-bd text-center"  colspan="1">
                            <p class="bold text-center" style="padding:0pt 20pt;font-size: 7pt;">Case #</p>
                        </td>
                        <td class="td-bd text-center"  colspan="1">
                            <p style="font-size: 7pt;" class="text-center">%%CaseNumber%%</p>
                        </td>
                    </tr>
                    <tr>
                        <td class="td-bd text-center" colspan="1">
                            <p style="font-size: 7pt;" class="bold">Comments</p>
                        </td>
                        <td class="td-bd %%leinalignment%%" colspan="3">
                            <p class="" style="text-align: justify;font-size: 7pt;">%%LeinComments%%</p>
                        </td>
                    </tr>
                    </table>
                    </div>
        <!-- <div style="page-break-after: always;"></div> -->

            <!-- <div class="pd-30"> -->
                

                <div style="padding-top: 10pt;"></div>
                %%TaxSection%%

                %%Miscellaneous%%

             
                <div style="padding-top: 10pt;">
                <table style="width: 100%;" cellspacing="0">
                <tr>
                    <td class="blur text-center">
                        <p style="font-size: 10pt;width:100%; font-weight: bold;text-align: center;">Map</p>
                    </td>
                </tr>
                    <tr style="border: 0.01em solid grey;">
                        <td class="td-bd" style="height: 60pt;">
                        <img src=%%Url%%%%MapAddress%% alt="" 
                        class="stretch" style="width:750px;height:300px;">
                        <!-- <img src=http:\\localhost\Direct2Title%%MapAddress%% alt="" 
                        class="stretch" style="width:750px;height:300px;"> -->
                        </td>
                    </tr>
                </table>
      <!--       </div>
        </div> -->

        <!--          <footer>
            <p style="text-align: justify;font-size: 6pt;margin-top:500px;font-size: 8px;"><b>USE OF THIS REPORT:  </b>All of the reports and related schedules furnished by ISGN Fulfillment Services, Inc., (“ISGN”) contain information obtained from public land records. ISGN makes no representation or warranty concerning the accuracy or completeness of these public records and the information contained therein other than as specifically set forth below.  THIS REPORT IS NOT ABSTRACT OR OPINION OF TITLE, TITLE BINDER, TITLE COMMITMENT OR GUARANTEE, OR TITLE INSURANCE POLICY. This report is not title insurance, does not reflect un-indexed or improperly indexed matters of record or off-record matters affecting title to the subject property and ISGN shall have no liability for such matters. </p>

         <div>
            <div style="float: left; width: 80%">
               <p>1-800-884-8002     www.isgnsolutions.com</p>
            </div>
            <div style="float: right; width: 20%">
               <p style="text-align: left;">90-4314567</p>
            </div>
         </div>
        </footer> -->
        <!-- Third Page Starts -->

<!--         <table width="100%" colspan="0" style="margin-top: 700px;">
            <tr>
                <td style=""><img src="D:\Profile Backup\venkatachalamn\Desktop\isgnlogo.png" style="width:100px;height: 50pt;z-index: 9999;margin-left: 30px;"></td>
            </tr>
        </table> -->
        <!--      <div style="width: 80%; padding: 0pt 50pt;">
            <h2 style="text-align: center;">Property Report</h2>
            </div>
            -->
        <!-- <div class="pdrl-30">
            <div class="pd-30"> -->
           <div class="%%LegalDisclaimerNote%%">
      
             %%pagebreak%%
             <!-- <div style="page-break-after: auto;"></div> -->

            <div style="padding-top: 20pt;"></div>

            <p style="font-size: 10pt;">%%Ordernumber%%</p>
                <p class="" style="font-size: 12pt;width:100%; font-weight: bold;text-align: center;">SCHEDULE "A"</p>

               <!--  <table style="width: 100%;" cellspacing="0">
                    <tr style="border: 0.01em solid grey; padding: 30pt 0pt;">
                        <td class="td-bd text-center" style="padding: 80pt 0pt;"> -->
                            <p class="text-left" style="font-size: 10pt;">%%LegalDescr%%</p>
<!--                         </td>
                    </tr>
                </table> -->
            <!-- </div> -->
            <!--             <div class="pd-20" style="page-break-before: always; padding-top: 100px;">
               <p class="blur text-center" style="font-size: 10pt;width:1159px; font-weight: bold;">Map</p>
               <table style="width: 1163px;" cellspacing="0">
                  <tr style="border: 1px; solid grey;">
                     <td class="td-bd" style="height: 60pt;"><img src = http:\\localhost\Direct2Title%%MapAddress%% alt="" style="width:545px;height:228px;"></td>
                  </tr>
               </table>
            </div> -->

            <!-- <div class="pd-20" style="page-break-before: always; padding-top: 100px;"> -->
<!--             <p class="blur text-center" style="font-size: 10pt;width:100%; font-weight: bold;">Map</p>
            <table style="width: 100%;" cellspacing="0">
                <tr style="border: 0.01em solid grey;">
                    <td class="td-bd" style="height: 60pt;"><img src=http:\\localhost\Direct2Title%%MapAddress%% alt="" style="width:545px;height:228px;"></td>
                </tr>
            </table> -->
            <!-- </div> -->

        </div>
        
        <!--         <footer>
            <p style="text-align: justify; font-size: 6pt;margin-top: 400px;"><b>USE OF THIS REPORT:  </b>All of the reports and related schedules furnished by ISGN Fulfillment Services, Inc., (“ISGN”) contain information obtained from public land records. ISGN makes no representation or warranty concerning the accuracy or completeness of these public records and the information contained therein other than as specifically set forth below.  THIS REPORT IS NOT ABSTRACT OR OPINION OF TITLE, TITLE BINDER, TITLE COMMITMENT OR GUARANTEE, OR TITLE INSURANCE POLICY. This report is not title insurance, does not reflect un-indexed or improperly indexed matters of record or off-record matters affecting title to the subject property and ISGN shall have no liability for such matters. </p>

         <div>
            <div style="float: left; width: 80%">
               <p>1-800-884-8002     www.isgnsolutions.com</p>
            </div>
            <div style="float: right; width: 20%">
               <p style="text-align: left;">90-4314567</p>
            </div>
         </div>
         </footer> -->
    <!-- </div> -->
    
    
        %%taxpagebreak%%
        <!-- <div style="page-break-after: auto;"></div> -->
        <div class="%%TaxDisclaimerNote%%">
          
                %%TaxCert%%

        </div>


</body>
</html>

 
