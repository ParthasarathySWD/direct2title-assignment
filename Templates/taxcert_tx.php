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

                footer: html_MyCustomFooter;
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

        .td-ta-r{
            text-align: right;
        }

    </style>
</head>

<body>

<thead>
<htmlpageheader name="MyCustomHeader" >
        <table width="100%" colspan="0">
            <tr>
                <td style="width: 100%;"><img src="https://www.ordersportal.sourcepointmortgage.com/assets/img/sourcepoint.png" style="height: 50pt;z-index: 9999;margin-left: -10px;"></td>
            </tr>
        </table>

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
                <p style="text-align: right;">%%Ordernumber%%</p>
            </div>
        </div>
    </htmlpagefooter>
</tfoot>
<div class="%%TaxDisclaimerNote%%">
<div class="wrapper">
    <div style="width: 80%; padding: 0pt 50pt;">
        <h2 style="text-align: center;font-size: 14pt;">Tax Certificate</h2>
    </div>
    <p class="blur text-center" style="font-size: 10pt;font-weight: bold;margin-top: 10px;">Order Information</p>
    <div style="padding-top: 10pt;">
        <table style="width: 100%;" cellspacing="0">
            <tbody>
                <tr style="height: 23px;">
                    <td class="td-bd" style="width: 32%; height: 23px;" rowspan="4">%%CustomerName%%
                        <br>%%CustomerAddress1%% %%CustomerAddress2%% %%CustomerCountyName%% %%CustomerCityName%% %%CustomerStateCode%% %%CustomerZipCode%%</td>
                    <td class="td-bd text-center bold" style="width: 15%; height: 23px;">Ordered Date</td>
                    <td class="td-bd text-center" style="width: 20%; height: 23px;">%%OrderDate%%</td>
                    <td class="td-bd text-center bold" style="width: 13%; height: 30px;" rowspan="2">Order #</td>
                    <td class="td-bd text-center" style="width: 13%; height: 23px;" rowspan="2">%%Ordernumber%%</td>
                </tr>
                <tr style="height: 23px;">
                    <td class="td-bd text-center bold" style="width: 15%; height: 23px;">County</td>
                    <td class="td-bd text-center" style="width: 20%; height: 23px;">%%Countyname%% </td>
                </tr>
                <tr style="height: 23px;">
                    <td class="td-bd text-center bold" style="width: 15%; height: 23px;">State</td>
                    <td class="td-bd text-center" style="width: 20%; height: 23px;">%%State_code%%</td>
                    <td class="td-bd text-center bold" style="width: 13%; height: 23px; " rowspan="2">Loan #</td>
                    <td class="td-bd text-center" style="width: 13%; height: 23px;" rowspan="2">%%Loannumber%%</td>
                </tr>
            </tbody>
        </table>
        <p class="blur text-center" style="font-size: 10pt;width:100%; font-weight: bold;margin-top: 5pt;">Property Information</p>
        <div style="padding-top: 10pt;">
            <table style="width: 100%;" cellspacing="0">
                <tr>
                    <td class="td-bd text-center bold" style="width:16.5%;">Block/Lot</td>
                    <td class="td-bd text-center" style="width:17.8%;">%%Lot%%</td>
                    <td class="td-bd text-center bold" style="width:16.5%;">Subdivision</td>
                    <td class="td-bd text-center" colspan="3">%%SubDivisionName%%</td>
                </tr>   
                <tr>
                    <td class="td-bd text-center bold" style="width:16.5%;">Property Tax</td>
                    <td class="td-bd text-center" style="width:17.8%;">%%ReferTaxSection%%</td>
                    <td class="td-bd text-center bold" style="width:16.5%;">Tax Year</td>
                    <td class="td-bd text-center" style="width:17.8%;">%%LatestTaxYear%%</td>
                    <td class="td-bd text-center bold" style="width:16.5%;">Delinquent Tax</td>
                    <td class="td-bd text-center" style="width:17.8%;">%%DeliquentTax%%</td>
                </tr>
                <tr>
                    <td class="td-bd text-center bold" style="width: 14.11%;">Borrower</td>
                    <td class="td-bd text-center" style="width:27.6%;">%%Borrowers%%</td>
                    <td class="td-bd text-center bold" colspan="4">Assessment Information</td>
                </tr>
                <tr>
                    <td class="td-bd text-center bold" style="width:16.5%;">Attention</td>
                    <td class="td-bd text-center bold" style="width:16.5%;">%%AttentionName%%</td>
                    <td class="td-bd text-center bold" style="width:16.5%;">Land Value</td>
                    <td class="td-bd text-center" style="width:17.8%;">%%Land%%</td>
                    <td class="td-bd text-center bold" style="width:11.6%;">Year</td>
                    <td class="td-bd text-center" style="width:11.6%;">%%AssessedYear%%</td>
                </tr>
                <tr>
                    <td class="td-bd text-center bold" style="width: 14.11%;" rowspan="3">Property Address</td>
                    <td class="td-bd text-center" style="width: 27.6%;" rowspan="3">%%Propertyaddress1%% %%Propertyaddress2%% %%Cityname%% %%Statecode%% %%Zip%%</td>
                    <td class="td-bd text-center bold" style="width:16.5%;">Improvement Value</td>
                    <td class="td-bd text-center" style="width:11.6%;">%%Buildings%%</td>
                    <td class="td-bd text-center bold" style="width:23.2%;" colspan="2">Total Assessment</td>
                </tr>
                <tr>
                    <td class="td-bd text-center bold" style="width:16.5%;">Agricultural Value</td>
                    <td class="td-bd text-center" style="width:17.8%;">%%Agricultural%%</td>
                    <td class="td-bd text-center" style="width:23.2%;vertical-align: middle;" colspan="2" rowspan="2">%%TotalValue%%</td>
                </tr>
                <tr>
                    <td class="td-bd text-center bold" style="width:16.5%;">Exemptions</td>
                    <td class="td-bd text-center" style="width:17.8%;">%%Exempt%%</td>
                </tr>
                <tr>
                    <td class="td-bd text-center bold" style="width:16.5%;">Comments</td>
                    <td class="td-bd %%alignment%%" colspan="5" style="">%%AssessmentValue%%</td>
                </tr>
                <tr>
                    <td class="td-bd text-center bold" style="width:16.5%;">Legal Description</td>
                    <td class="td-bd" colspan="5" style="">%%LegalDescription%%</td>
                </tr>                      

            </table>
        </div>

        %%taxcert_html%%

        <div style="padding-top: 8pt;"></div>
        <div class="tordertaxcerts">
            <table style="width: 100%;margin-top: 10pt;border:0px;" cellspacing="0">
                <thead>
                    <tr>
                        <th class="blur text-center" colspan="4">
                            <p style="font-size: 10pt;width:100%; font-weight: bold;margin-top: 5pt;text-align: center;">Tax Information</p>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="">
                        <td width="14.7%" class="td-bd text-center" style="">
                            <p class="bold">Taxing Entity</p>
                        </td>
                        <td width="27.6%" class="td-bd text-center" style="">
                            <p style="font-size: 7pt;text-transform: uppercase;" class="text-center">%%TaxDocumentTypeName%%</p>
                        </td>
                        <td width="14.7%" class="td-bd text-center" style="">
                            <p class="bold">&nbsp;Parcel ID&nbsp;</p>
                        </td>
                        <td width="43.5%" class="td-bd text-center" style="">
                            <p class="text-center">%%ParcelNumber%%</p>
                        </td>
                    </tr>
                    <tr style="">
                        <td width="14.7%" class="td-bd text-center">
                            <p class="bold">Tax Installment</p>
                        </td>
                        <td width="27.6%" class="td-bd text-center">
                            <p class="text-center">%%TaxBasisName%%</p>
                        </td>
                        <td width="14.7%" class="td-bd text-center">
                            <p class="bold">Property Type</p>
                        </td>
                        <td width="43.5%" class="td-bd text-center">
                            <p class="text-center">%%PropertyClassName%%</p>
                        </td>
                    </tr>
                </tbody>
            </table>
            <table style="width: 100%;border: 0.01em solid grey;" cellspacing="0">
                <tr style="">
                    <td width="10.4%" class="td-bd text-center">
                        <p class="bold">Tax Year</p>
                    </td>
                    <td width="18.6%" class="td-bd text-center">
                        <p class="text-center bold">Tax Installments</p>
                    </td>
                    <td width="13.5%" class="td-bd text-center">
                        <p class="bold">Base Amount</p>
                    </td>
                    <td width="14.7%" class="td-bd text-center">
                        <p class="text-center bold">Tax Status</p>
                    </td>
                    <td width="21.8%" class="td-bd text-center">
                        <p class="bold">Paid Amount</p>
                    </td>
                    <td width="21.8%" class="td-bd text-center">
                        <p class="bold">Due/Paid Date</p>
                    </td>
                    <!-- <td class="td-bd text-center" colspan="3" style=""> <p class=" text-center bold">Good Through Date</p></td>-->
                </tr>%%taxinstallment%% </table>
            <table style="width: 100%; margin-top: 10pt;" cellspacing="0">%%TaxExmp%%</table>
            <table style="width: 100%; margin-top: 10pt;" cellspacing="0">
                <tbody>
                    <tr style="">
                        <td class="td-bd bold text-center" style="width: 25%;font-size: 7pt;">Total Delinquent Payoff</td>
                        <td class="td-bd text-center" style="width: 25%;font-size: 7pt;">%%AmountDelinquent%%</td>
                        <td class="td-bd bold text-center" style="width: 25%;font-size: 7pt;">Good Through Date</td>
                        <td class="td-bd text-center" style="width: 25%;font-size: 7pt;">%%GoodThroughDate%%</td>
                    </tr>
                    <tr style=""> </tr>
                    <tr style="">
                        <td class="td-bd bold text-center" style="width: 26.6226%;font-size: 7pt;" colspan="1">Comments</td>
                        <td class="td-bd %%taxalignment%%" style="font-size: 7pt;" colspan="3">
                            %%TaxComments%%
                        </td>
                    </tr>
                </tbody>
            </table>
            <div style="page-break-inside: avoid;">
                <table style="width: 100%;margin-top: 10pt;" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="blur text-center" colspan="14">
                                <p style="font-size: 10pt;width:100%; font-weight: bold;margin-top: 5pt;text-align: center;">Tax Collector Information</p>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="border: 0.01em solid grey;">
                            <td class="td-bd bold text-center" style="width: 26.6226%;font-size: 7pt;">Payable to</td>
                            <td class="td-bd text-center" colspan="13" style="width: 72.3774%;font-size: 7pt;">%%TaxPayable%%</td>
                        </tr>
                        <tr style="border: 0.01em solid grey;">
                            <td class="td-bd bold text-center" style="width: 26.6226%;font-size: 7pt;">Address</td>
                            <td class="td-bd text-center" colspan="13" style="width: 72.3774%;font-size: 7pt;">%%PaymentAddrLine1%% %%PaymentAddrLine2%% %%PaymentCity%% %%PaymentState%% %%PaymentZipCode%%</td>
                        </tr>
                        <tr style="border: 0.01em solid grey;">
                            <td class="td-bd bold text-center" style="width: 26.6226%;font-size: 7pt;">Phone #</td>
                            <td class="td-bd text-center" colspan="4" style="">
                                <p style="font-size: 7pt;" class="text-center">%%CollectorPhoneNumber%%</p>
                            </td>
                            <td class="td-bd text-center" colspan="2" style="">
                                <p style="font-size: 7pt;" class="bold">Web Address</p>
                            </td>
                            <td class="td-bd text-center" colspan="7" style="">
                                <p style="font-size: 7pt;color:#0066ff" class="text-center"><u>%%WebsiteAddr%%</u></p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

       

    </div>
</div>
</div>

</body>
</html>

 
