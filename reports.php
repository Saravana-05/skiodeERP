<?php
session_start();
include_once "connect_db.php";
?>
<style>
.reportContent
{
	margin-top: 20px !important;
    border: 2px solid #302f2f;
	border-radius: 4px;
	min-height:550px;
}
.reportContent h4
{
	margin-top: -25px; 
    background: white;
} 
div.dt-container div.dt-length select {
    width: 35%; 
}
div.dt-container div.dt-length label {
	width: 55%; 
}
.reportContent .iconImgCls {
    width: 50px;
} 
.reportContent .reportDivCls {
	text-align: left;
	padding: 10px;
}
.reportDivCls
{ 
    margin: 5px;
    padding: 2px;
    font-size: 1rem;
    font-weight: bold;
    text-align: center;
    padding-top: 5px;
    border-radius: 28px;
	cursor: pointer;
	color:#fff;
	border-top-right-radius: 0px;
    border-bottom-left-radius: 0px;
	margin-top:15px;
}
.reportDivCls:hover
{
	 box-shadow: 0 8px 17px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19);
	 text-decoration:underline;
}
.reportDivCls .iconImgCls
{
	margin-left: 7%;
}
</style>
<div class="p-3 m-1 pt-0 reportContent">
	<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-2 mb-1">
		<h4 class="mb-0">Reports</h4> 			
	</div>
	<div class="row " style="margin-left:4.5rem !important;"> 
		<div class="col-md-5 reportDivCls" onclick="fnReportModule(1)" style="margin-right:1.5rem !important;background:#ffaaab;">
			<a > <img loading="lazy" class="iconImgCls ml-2" src="img/report-icon-1.png" alt="JOB CARD" /> JOB CARD REPORT 
			</a>
		</div>
		<div class="col-md-5 reportDivCls" onclick="fnReportModule(2)" style="margin-right:1.5rem !important;background:#76c1d4;">
			<a> <img loading="lazy" class="iconImgCls" src="img/report-icon-8.png" alt="SALE QUOTATION" /> SALE QUOTATION REPORT 
			</a>
		</div>
		<div class="col-md-5 reportDivCls" onclick="fnReportModule(3)" style="background:#d0bdf4;margin-right:1.5rem !important;">
			<a> <img loading="lazy" class="iconImgCls" src="img/report-icon-3.png" alt="SALE INVOICE" /> SALE INVOICE REPORT 
			</a>
		</div>
		<div class="col-md-5 reportDivCls" onclick="fnReportModule(4)" style="margin-right:1.5rem !important;background:#8bf0ba;">
			<a> <img loading="lazy" class="iconImgCls" src="img/report-icon-4.png" alt="JOURNAL" /> JOURNAL REPORT 
			</a>
		</div>
		<div class="col-md-5 reportDivCls" onclick="fnReportModule(5)" style="margin-right:1.5rem !important;background:#68d388;">
			<a> <img loading="lazy" class="iconImgCls" src="img/report-icon-5.png" alt="LEDGER" /> LEDGER REPORT 
			</a>
		</div>
		<div class="col-md-5 reportDivCls" onclick="fnReportModule(6)" style="background:#ffb766;">
			<a> <img loading="lazy" class="iconImgCls" src="img/report-icon-6.png" alt="TRIAL BALANCE" /> TRIAL BALANCE REPORT 
			</a>
		</div>
		<div class="col-md-5 reportDivCls" onclick="fnReportModule(7)" style="margin-right:1.5rem !important;background:#ffde22;"  >
			<a> <img loading="lazy" class="iconImgCls" src="img/report-icon-7.png" alt="MATERIAL USAGE" /> MATERIAL USAGE REPORT 
			</a>
		</div>
		<div class="col-md-5 reportDivCls" onclick="fnReportModule(8)" style="background:#DCC7AA;">
			<a> <img loading="lazy" class="iconImgCls" src="img/report-icon-2.png" alt="MACHINE USAGE" /> MACHINE USAGE REPORT 
			</a>
		</div>
	</div>
</div>
<script>
function fnReportModule(pType)
{
	switch(pType) 
	{   
		case 1:
			$("#rightContentDiv").empty();
			$("#rightContentDiv").load("job_card_report.php");
			break;
		case 2:
			$("#rightContentDiv").empty();
			$("#rightContentDiv").load("sale_quotation_report.php");
			break;
		case 3:
			$("#rightContentDiv").empty();
			$("#rightContentDiv").load("sale_invoice_report.php");
			break;
		case 4:
			$("#rightContentDiv").empty();
			$("#rightContentDiv").load("journal_report.php");
			break;
		case 5:
			$("#rightContentDiv").empty();
			$("#rightContentDiv").load("ledger_report.php");
			break;
		case 6:
			$("#rightContentDiv").empty();
			$("#rightContentDiv").load("trial_balance_report.php");
			break;
		case 7:
			$("#rightContentDiv").empty();
			$("#rightContentDiv").load("material_usage_report.php");
			break;
		case 8:
			$("#rightContentDiv").empty();
			$("#rightContentDiv").load("machine_usage_report.php");
			break;
		case 9:
			$("#rightContentDiv").empty();
			$("#rightContentDiv").load("user_master.php");
			break;
			
	}
}
</script>