<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
<!-- SECTION 1 -->
<div class="divTable">
    <div class="column" style="width: 75%">
        <div class="divTableBody">
            <div class="divTableRow">
                <div class="" style="border: none;">
                <p style="font-size:20pt;font-weight:bold;line-height:0.2em;">INVOICE</p>
                <p style="font-size:14pt;font-weight:bold;line-height:0.2em;">SOURCEPOINT FULFILLMENT SERVICES INC.</p>
                <p style="font-size:12pt;font-weight:bold;line-height:0.2em;">2330 COMMERCE DRIVE,SUITE 2 </p>
                <p style="font-size:12pt;font-weight:bold;line-height:0.2em;">PALM BAY FL 32905 </p>
                <p style="font-size:12pt;font-weight:bold;line-height:0.2em;">PHONE: (855) 884-8001 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <span style="font-size:12pt;font-weight:bold;">FAX: (877) 760-0228</span></p>
                </div>
            </div>
        </div>
    </div>
    <div class="column" style="width: 25%;padding-top: 25px;height: 50px;">

        <div class="divTableBody">
            <div class="divTableRow">
                <div class="divTableCell" style="font-size: 12pt;line-height: 2.0em;"><b>Date</b>&nbsp;&nbsp;&nbsp;&nbsp;%%OrderDate%%</div>
            </div>
            <div class="divTableRow">
                <div class="divTableCell" style="font-size: 12pt;line-height: 2.0em;"><b>Invoice</b>&nbsp;&nbsp;&nbsp;&nbsp;# %%OrderNumber%%</div>
            </div>
            <div class="divTableRow">
                <div class="divTableCell" style="font-size: 12pt;line-height: 2.0em;"><b>Sub No.</b>&nbsp;&nbsp;&nbsp;&nbsp;%%SubProductUID%%</div>
            </div>
        </div>
        
    </div>
</div>

<div class="divTable" style="margin-top: 10pt;">
    <div class="column" style="width: 49%;">
        <div class="divTableBody">
            <div class="divTableRow" style="line-height: 10.0em;">
                <div class="divTableCell">
                    <p style="line-height: 2.0em;">%%CustomerAddress1%% %%CustomerAddress2%% %%CustomerStateCode%% %%CustomerCountyName%% %%CustomerCityName%%&nbsp;&nbsp;&nbsp;                 %%CustomerFaxNo%% </p>
                </div>
            </div>
        </div>
        <div class="" style="line-height: 3.0em;padding-top: 5pt;">
            <div class="">
                <div class=""></div>
            </div>
        </div>
    </div>
    <div class="column" style="width: 1%;line-height: 8.0em;border: unset;">
        <div class="" style="">
            <div class="" style="border: 0px;">
                <div class="">&nbsp;</div>
            </div>
        </div>
    </div>
    <div class="column" style="width: 50%;line-height: 8.0em;">
        <div class="divTableBody">
            <div class="divTableRow">
                <div class="divTableCell">
                    <h4 style="font-weight: bold;line-height: 1.0em;">Re</h4>
                    <p style="line-height: 1.0em;">%%BorrowerName%%</p>
                </div>
            </div>
        </div>

        <div class="divTableBody" style="line-height: 3.0em;padding-top: 5pt;">
            <div class="divTableRow">
                <div class="divTableCell">
                     <p style="line-height: 1.0em;">%%PropertyAddress1%% %%PropertyStateCode%% %%PropertyZipcode%%</p>
                </div>
            </div>
        </div>

        <div class="divTableBody" style="line-height: 3.0em;padding-top: 5pt;">
            <div class="divTableRow">
                <div class="divTableCell">
                    <h4 style="font-weight: bold;line-height: 1.0em;">County</h4>
                    <p style="line-height: 1.0em;">%%PropertyCountyName%%</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div style="margin-top: 10pt;">
    <table  style="width: 100%;table-layout: fixed;border-spacing: 10px;" border="1" >
        <thead>
            <tr>
                <th width="16.66%" class = "headingstyle"  >Order<br>Number</th>
                <th width="16.66%" class = "headingstyle"  >Order<br>Date</th>
                <th width="16.66%" class = "headingstyle"  >Deal<br>Number</th>
                <th width="16.66%" class = "headingstyle"  >Invoice<br>Number</th>
                <th width="16.66%" class = "headingstyle"  >Invoice<br>Date</th>
                <th width="16.66%" class = "headingstyle"  >Invoice<br>Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td width="16.66%" class = "bodystyle" >%%OrderNumber%%</td>
                <td width="16.66%" class = "bodystyle" >%%OrderDate%%</td>
                <td width="16.66%" class = "bodystyle" ></td>
                <td width="16.66%" class = "bodystyle" >%%OrderNumber%%</td>
                <td width="16.66%" class = "bodystyle" >%%CurrentDate%%</td>
                <td width="16.66%" class = "bodystyle" >$ %%CustomerAmount%%</td>
            </tr>
        </tbody>
    </table>
</div>

<div style="margin-top: 10pt;">
    <table style="width: 100%;table-layout: fixed;" border="1" cellspacing="10">
        <thead>
            <tr>
                <th width="33.3%" class = "headingstyle"  >Product</th>
                <th width="33.3%" class = "headingstyle"  >Contact</th>
                <th width="33.3%" class = "headingstyle"  >Loan Number</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td width="33.3%" class = "bodystyle" >%%SubProductName%%</td>
                <td width="33.3%" class = "bodystyle" style="text-transform: uppercase;">%%AttentionName%%</td>
                <td width="33.3%" class = "bodystyle" >%%LoanNumber%%</td>
            </tr>
        </tbody>
    </table>
</div>

<div class="hr"></div>

<div class="divTable">
        <div class="divTableBody" style="text-align: center;">
            <div class="divTableRow">
                <div class="" style="border: none;">
                <p style="font-size:12pt;font-weight:bold;line-height:0.2em;">PLEASE REMIT PAYMENT TO:</p>
                <p style="font-size:12pt;font-weight:bold;line-height:0.2em;">SOURCEPOINT FULFILLMENT SERVICES INC.</p>
                <p style="font-size:12pt;font-weight:bold;line-height:0.2em;">31 INWOOD ROAD</p>
                <p style="font-size:12pt;font-weight:bold;line-height:0.2em;">ROCKY HILL, CT 06067</p>
                </div>
            </div>
        </div>
</div>
<!-- SECTION 1 -->
<!-- SECTION 2 -->
<div class="divTable" style="margin-top: 10pt;">
    <div class="column" style="width: 49%;">
        <div class="divTableBody">
            <div class="divTableRow" style="line-height: 10.0em;">
                <div class="divTableCell">
                     <p style="line-height: 2.0em;">%%CustomerName%%<br>%%CustomerAddress1%% %%CustomerAddress2%% %%CustomerStateCode%% %%CustomerCountyName%% %%CustomerCityName%%&nbsp;&nbsp;&nbsp;%%CustomerFaxNo%%</p>
                </div>
            </div>
        </div>
        <div class="" style="line-height: 3.0em;padding-top: 5pt;">
            <div class="">
                <div class=""></div>
            </div>
        </div>
    </div>
    <div class="column" style="width: 1%;line-height: 8.0em;border: unset;">
        <div class="" style="">
            <div class="" style="border: 0px;">
                <div class="">&nbsp;</div>
            </div>
        </div>
    </div>
    <div class="column" style="width: 50%;line-height: 8.0em;">
        <div class="divTableBody">
            <div class="divTableRow">
                <div class="divTableCell">
                    <h4 style="font-weight: bold;line-height: 1.0em;">Re</h4>
                   <p style="line-height: 1.0em;">%%BorrowerName%%</p>
                </div>
            </div>
        </div>

        <div class="divTableBody" style="line-height: 3.0em;padding-top: 5pt;">
            <div class="divTableRow">
                <div class="divTableCell">
                     <p style="line-height: 1.0em;">%%PropertyAddress1%% %%PropertyStateCode%% %%PropertyZipcode%%</p>
                </div>
            </div>
        </div>

        <div class="divTableBody" style="line-height: 3.0em;padding-top: 5pt;">
            <div class="divTableRow">
                <div class="divTableCell">
                     <h4 style="font-weight: bold;line-height: 1.0em;">County</h4>
                    <p style="line-height: 1.0em;">%%PropertyCountyName%%</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div style="margin-top: 10pt;">
    <table style="width: 100%;table-layout: fixed;">
        <thead>
            <tr>
                <td width="16.66%" class = "headingstyle"  >Order<br>Number</td>
                <td width="16.66%" class = "headingstyle"  >Order<br>Date</td>
                <td width="16.66%" class = "headingstyle"  >Deal<br>Number</td>
                <td width="16.66%" class = "headingstyle"  >Invoice<br>Number</td>
                <td width="16.66%" class = "headingstyle"  >Invoice<br>Date</td>
                <td width="16.66%" class = "headingstyle"  >Invoice<br>Amount</td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td width="16.66%" class = "bodystyle" >%%OrderNumber%%</td>
                <td width="16.66%" class = "bodystyle" >%%OrderDate%%</td>
                <td width="16.66%" class = "bodystyle" ></td>
                <td width="16.66%" class = "bodystyle" >%%OrderNumber%%</td>
                <td width="16.66%" class = "bodystyle" >%%CurrentDate%%</td>
                <td width="16.66%" class = "bodystyle" >$ %%CustomerAmount%%</td>
            </tr>
        </tbody>
    </table>
</div>

<div style="margin-top: 10pt;">
    <table style="width: 100%;table-layout: fixed;" border="1">
        <thead>
            <tr>
                <td width="33.3%" class = "headingstyle"  >Product</td>
                <td width="33.3%" class = "headingstyle"  >Contact</td>
                <td width="33.3%" class = "headingstyle"  >Loan Number</td>
                
            </tr>
        </thead>
        <tbody>
            <tr>
               <td width="33.3%" class = "bodystyle" >%%SubProductName%%</td>
                <td width="33.3%" class = "bodystyle" style="text-transform: uppercase;">%%AttentionName%%</td>
                <td width="33.3%" class = "bodystyle" >%%LoanNumber%%</td>
            </tr>
        </tbody>
    </table>
</div>      		 
<!-- SECTION 2 -->
</body>
</html>
<style type="text/css">
body 
	{
		font-family: "Times New Roman", Times, serif;
	}
table { 
           border-collapse: collapse;
}

           @page {
               sheet-size: LEGAL;
           }

 
.divTable{
    display: table;
    width: 100%;
}
.divTableRow {
    display: table-row;
}
.divTableHeading {
    background-color: #EEE;
    display: table-header-group;
}
.divTableCell, .divTableHead {
    border: 1px solid #999999;
    display: table-cell;
    padding: 3px 10px;
}
.divTableHeading {
    background-color: #EEE;
    display: table-header-group;
    font-weight: bold;
}
.divTableFoot {
    background-color: #EEE;
    display: table-footer-group;
    font-weight: bold;
}
.divTableBody {
    display: table-row-group;
}

.column {
    float: left;
    width: 50%;
}
.headingstyle{
    text-align: center;
    font-weight: bold;
    background-color: black;
    color: #fff;
    border: 10px solid white;
}
.bodystyle{
    text-align: center;
    color: #000;
    border: 10px solid white;
}
.hr{
    margin-top: 25pt;
  border-bottom: 1px dashed ;
  width: 1px;
} 
</style>