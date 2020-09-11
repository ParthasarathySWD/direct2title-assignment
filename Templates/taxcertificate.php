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
            div.noheaderfooter 
            {
                page: noheaderfooter;
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
            font-size: 8pt;
            margin: 10pt 20pt;
        }
        
        hr {
            border: none;
            border-top: 1px solid grey;
        }
        
        p {
            margin: 0;
            font-size: 8pt;
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

        td.blur{
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
            border:1px;
            border-style: solid;
            border-color:#595959;

        }
        
        .bold {
            font-weight: bold;
        }
        
        .pdrl-30 {
            padding: 0pt 30pt 30pt 30pt;
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
            <div style="float: left; width: 80%">
                <p>1-800-884-8002 www.isgnsolutions.com</p>
            </div>
            <div style="float: right; width: 20%">
                <p style="text-align: left;">90-4314567</p>
            </div>
        </div>
    </htmlpagefooter>
</tfoot>

    <div class="wrapper">


                <div style="width: 80%; padding: 0pt 50pt;">
            <h2 style="text-align: center;font-size: 14pt;">Tax Certificate</h2>
            </div>
    
        <!-- <div class="pdrl-30"> -->

            <!-- <div class="pd-30"> -->
                <p class="blur text-center" style="font-size: 10pt;font-weight: bold;margin-top: 10px;">Order Information</p>
                    <div style="padding-top: 10pt;">
                        <table style="width: 100%;" cellspacing="0">
                        <tbody>
                        <tr style="height: 23px;">
                        <td class="td-bd" style="width: 32%; height: 23px;" rowspan="4">%%CustomerName%%<br>%%CustomerAddress1%% %%CustomerAddress2%%</td>
                        <td class="td-bd text-center" style="width: 15%; height: 23px;">Search Date</td>
                        <td class="td-bd text-center" style="width: 20%; height: 23px;">%%SearchFromDate%%</td>
                        <td class="td-bd text-center" style="width: 13%; height: 30px;" rowspan="2">Order #</td>
                        <td class="td-bd text-center" style="width: 13%; height: 23px;" rowspan="2">%%Ordernumber%%</td>
                        </tr>
                        <tr style="height: 23px;">
                        <td class="td-bd text-center" style="width: 15%; height: 23px;">County</td>
                        <td class="td-bd text-center" style="width: 20%; height: 23px;">%%CustomerCountyName%%</td>

                        </tr>
                        <tr style="height: 23px;">
                        <td class="td-bd text-center" style="width: 15%; height: 23px;">State</td>
                        <td class="td-bd text-center" style="width: 20%; height: 23px;">%%CustomerStateCode%%</td>
                         <td class="td-bd text-center" style="width: 13%; height: 23px; " rowspan="2">Loan #</td>
                        <td class="td-bd text-center" style="width: 13%; height: 23px;" rowspan="2">%%Loannumber%%</td>
                        </tr>
                        </tbody>
                        </table>
            <!-- </div> -->

            <!-- <div class="pd-30"> -->
            <p class="blur text-center" style="font-size: 10pt;width:100%; font-weight: bold;margin-top: 5pt;">Property Information</p>
                              <div style="padding-top: 10pt;">
                <!-- <div class="torderproperty"> -->
<table style="width: 100%;" cellspacing="0">

    <tr style="height: 30pt;">
          <td class="td-bd text-center" style="width: 22%;;height: 30pt;font-size: 12pt;" rowspan="2">Borrower</td>
          <td class="td-bd text-center"  style="width: 22%;;height: 30pt;font-size: 12pt;" rowspan="2">%%Borrowers%%</td>
          <td class="td-bd text-center"  style="width: 116%;;height: 30pt;font-size: 12pt;" colspan="4">Assessment Information</td>
    </tr>
    <tr style="height: 30pt;">
          <td class="td-bd text-center" style="width:25%;height: 30pt;font-size: 12pt;">Land Value</td>
          <td class="td-bd text-center" style="width:25%;height: 30pt;font-size: 12pt;">%%Land%%</td>
          <td class="td-bd text-center"  style="width:25%;height: 30pt;font-size: 12pt;">Year</td>
          <td class="td-bd text-center"  style="width:25%;height: 30pt;font-size: 12pt;" >%%AssessedYear%%</td>
    </tr>
    <tr style="height: 30pt;">
          <td class="td-bd text-center" style="width: 22%;height: 30pt;font-size: 12pt;" rowspan="3">Property Address</td>
          <td class="td-bd text-center" style="width: 22%;height: 30pt;font-size: 12pt;" rowspan="3">%%Propertyaddress1%% %%Propertyaddress2%% %%Cityname%% %%Statecode%% %%Zip%%</td>
          <td class="td-bd text-center"  style="width:25%;height: 30pt;font-size: 12pt;">Improvement Value</td>
          <td class="td-bd text-center"  style="width:25%;height: 30pt;font-size: 12pt;">%%Buildings%%</td>
          <td class="td-bd text-center" style="width:25%;height: 30pt;font-size: 12pt;" colspan="2">Total Assessment</td>
    </tr>
    <tr style="height: 30pt;">
          <td class="td-bd text-center"  style="width:25%;height: 30pt;font-size: 12pt;">Agricultural</td>
          <td class="td-bd text-center"  style="width:25%;height: 30pt;font-size: 12pt;">%%Agricultural%%</td>
          <td class="td-bd text-center" style="width:25%;height: 30pt;font-size: 12pt;vertical-align: middle;" colspan="2" rowspan="2">%%TotalValue%%</td>
    </tr>
  <tr style="height: 30pt;">
         <td class="td-bd text-center" style="width:25% height: 30pt;font-size: 12pt;">Exemptions</td>
         <td  class="td-bd text-center" style="width:25% height: 30pt;font-size: 12pt;">%%Exempt%%</td>
  </tr>

</table>

                <!-- </div> -->
            <!-- </div> -->
            
                <div style="padding-top: 10pt;"></div>
                <div class="tordertaxcerts">
                <table style="width: 100%;" cellspacing="0">
                   <p class="blur text-center" style="font-size: 10pt;width:100%; font-weight: bold;margin-top: 5pt;">Tax Information</p>

                    <tr style="border: 1px solid grey;">
                        <td width="20%" class="td-bd text-center" colspan="3" style="">
                            <p style="font-size: 9pt;" class="bold">Tax Entity</p>
                        </td>
                        <td width="30%" class="td-bd text-center" colspan="9" style="">
                            <p style="font-size: 9pt;" class="text-center">%%subDocumentTypeName%%</p>
                        </td>
                        <td width="20%" class="td-bd text-center" colspan="3" style="">
                            <p style="font-size: 9pt;" class="bold">&nbsp;Parcel ID&nbsp;</p>
                        </td>
                        <td width="30%" class="td-bd text-center" colspan="9" style="">
                            <p style="font-size: 9pt;" class="text-center">%%APN%%</p>
                        </td>
                    </tr>
                    <tr style="border: 1px solid grey;">
                        <td width="20%" class="td-bd text-center" colspan="3" style="">
                            <p style="font-size: 9pt;" class="bold">Tax Installment</p>
                        </td>
                        <td width="30%" class="td-bd text-center" colspan="9" style="">
                            <p style="font-size: 9pt;" class="text-center">%%TaxBasisName%%</p>
                        </td>
                        <td width="20%" class="td-bd text-center" colspan="3" style="">
                            <p style="font-size: 9pt;" class="bold">Property Type</p>
                        </td>
                        <td width="30%" class="td-bd text-center" colspan="9" style="">
                            <p style="font-size: 9pt;" class="text-center">%%PropertyClassName%%</p>
                        </td>
                    </tr>
                    <tr style="border: 1px solid grey;">
                        <td class="td-bd text-center" colspan="3" style="">
                            <p style="font-size: 9pt;" class="bold">Tax Year</p>
                        </td>
                        <td class="td-bd text-center" colspan="3" style="">
                            <p style="font-size: 9pt;" class="text-center bold">Tax Installments</p>
                        </td>
                        <td class="td-bd text-center" colspan="3" style="">
                            <p style="font-size: 9pt;" class="bold">Base Amount</p>
                        </td>
                        <td class="td-bd text-center" colspan="4" style="">
                            <p style="font-size: 9pt;" class="text-center bold">Tax Status</p>
                        </td>
                        <td class="td-bd text-center" colspan="3" style="">
                            <p style="font-size: 9pt;" class="bold">Paid Amount</p>
                        </td>
                        <td class="td-bd text-center" colspan="3" style="">
                            <p style="font-size: 9pt;" class=" text-center bold">Paid Date</p>
                        </td>
                          <td class="td-bd text-center" colspan="3" style="">
                            <p style="font-size: 9pt;" class=" text-center bold">Next Due Date</p>
                        </td>
                        <td class="td-bd text-center" colspan="3" style="">
                            <p style="font-size: 9pt;" class=" text-center bold">Good Through Date</p>
                        </td>
                    </tr>

                    <tr style="border: 1px solid grey;">
                        <td class="td-bd text-center" colspan="3" style="">
                            <p style="font-size: 9pt;" class="">%%TaxYear%%</p>
                        </td>
                        <td class="td-bd text-center" colspan="3" style="">
                            <p style="font-size: 9pt;" class="text-center">%%TaxInstallmentName%%</p>
                        </td>
                        <td class="td-bd text-center" colspan="3" style="">
                            <p style="font-size: 9pt;" class="">%%GrossAmount%%</p>
                        </td>
                        <td class="td-bd text-center" colspan="4" style="">
                            <p style="font-size: 9pt;" class="text-center">%%TaxStatusName%%</p>
                        </td>
                        <td class="td-bd text-center" colspan="4" style="">
                            <p style="font-size: 9pt;" class="">%%AmountPaid%%</p>
                        </td>
                        <td class="td-bd text-center" colspan="4" style="">
                            <p style="font-size: 9pt;" class=" text-center">%%PaidDate%%</p>
                        </td>
                        <td class="td-bd text-center" colspan="3" style="">
                            <p style="font-size: 9pt;" class=" text-center">%%ThroughDate%%</p>
                        </td>
                    </tr>
                </table>

                                <!-- Total Delinquent Payoff -->
        
                
                <table style="width: 100%; margin-top: 10pt;" cellspacing="0">
                    <tbody>
                        <tr style="border: 1px solid grey;">
                            <td class="td-bd bold text-center" style="width: 26.6226%;font-size: 9pt;" >Total Delinquent Payoff</td>
                            <td class="td-bd text-center" style="width: 72.3774%;font-size: 9pt;" >%%AmountDelinquent%%</td>
                        </tr>
                        <tr style="border: 1px solid grey;">
                            <td class="td-bd bold text-center" style="width: 26.6226%;font-size: 9pt;" >Comments</td>
                            <td class="td-bd" style="width: 72.3774%;font-size: 9pt;" >%%TaxComments%%</td>
                        </tr>
                    </tbody>
                </table>
                <!-- Total Delinquent Payoff -->


                <!-- Tax Collector Information -->
                <p class="blur text-center" style="font-size: 10pt;font-weight: bold;margin-top: 10px;">Tax Collector Information</p>
                <table style="width: 100%;" cellspacing="0">
                    <tbody>
                        <tr style="border: 1px solid grey;">
                            <td class="td-bd bold text-center"  style="width: 26.6226%;font-size: 9pt;" >Payable to</td>
                            <td class="td-bd text-center"  colspan="10" style="width: 72.3774%;font-size: 9pt;" >%%PaymentAddrLine1%% %%PaymentAddrLine2%% %%PaymentCity%% %%PaymentState%% %%PaymentZipCode%%</td>
                        </tr>
                        <tr style="border: 1px solid grey;">
                            <td class="td-bd bold text-center"  style="width: 26.6226%;font-size: 9pt;" >Address</td>
                            <td class="td-bd text-center" colspan="10" style="width: 72.3774%;font-size: 9pt;" >%%TaxAuthorityAddress1%% %%TaxAuthorityAddress2%% %%TaxAuthorityCity%% %%TaxAuthorityState%% %%TaxAuthorityZipcode%%</td>
                        </tr>
                        <tr style="border: 1px solid grey;">
                        <td class="td-bd bold text-center" style="width: 26.6226%;font-size: 9pt;" >Phone #</td>
                        <td class="td-bd text-center" colspan="3" style="">
                            <p style="font-size: 9pt;" class="text-center">%%CollectorPhone%%</p>
                        </td>
                        <td class="td-bd text-center" colspan="2" style="">
                            <p style="font-size: 9pt;" class="bold">Web Address</p>
                        </td>
                        <td class="td-bd text-center" colspan="5" style="">
                            <p style="font-size: 9pt;" class="text-center">%%WebsiteAddress%%</p>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <!-- Tax Collector Information -->
            </div>

</body>
</html>

 
