<?php
session_start();
include_once '../connect_db.php';
$result=""; 
if(isset($_GET['from']))
{
	$from = date("Y-m-d", strtotime($_GET['from']));
	$to = date("Y-m-d", strtotime($_GET['to'])); 
	$actype_txt=$_GET['actype_txt'];
	$total_amount=0;
	//$sql="SELECT dt,achead, sum(amount) as amount FROM  journal_details where dt >='".$from."' and dt<='".$to."' group by achead ;";  
	$sql="SELECT a.achead,b.acname, sum(a.amount) as amount,b.actype FROM journal_details a inner join achead_master b on a.achead=b.achead group by a.achead having b.actype='sundry' and amount<>0;";  
	if($actype_txt!="ALL")
		$sql="select c.*,d.customer_type from (SELECT a.achead,b.acname, sum(a.amount) as amount,b.actype FROM journal_details a inner join achead_master b on a.achead=b.achead group by a.achead having b.actype='sundry' and amount<>0) c inner join customer_master d on c.achead=d.customer_code where d.customer_type like '%".$actype_txt."%';";
		
	$result_hdr='<div class="row"><div class="col-12"  id="printable_div"><center><h4>'. ($actype_txt!="ALL"?$actype_txt:"").' TRIAL BALANCE</h4></center>'; 
	if($query=mysqli_query($connection,$sql))
	{
		$result.='<table class="table table-bordered table-hover table-condensed table-striped " id="resultDetailTable">';
			$result=$result.'<thead class="table-dark">';
			$result=$result.'<tr> ';
			$result=$result.'<th>SNo</th>';
			//$result=$result.'<th>Date</th>';
			$result=$result.'<th>AC Name</th>';
			$result=$result.'<th>AMOUNT</th>'; 
			$result=$result.'</tr>';
			$result=$result.'</thead>';
			$result=$result.'<tbody>';
			$i=1;
		if(mysqli_num_rows($query)>0)
		{ 
			 while($row=mysqli_fetch_array($query)) {
					$result=$result.'<tr>';
					$result=$result.'<td>'.$i.'</td>';
					//$result=$result.'<td>'.date("d-m-Y", strtotime($row["dt"])).'</td>';
					$result=$result.'<td>'.$row["acname"].'</td>'; 
					if($row["amount"] >0)
					{
						$result=$result.'<td align="right">'.$row["amount"].' Cr</td>'; 
					}
					else
					{
						$result=$result.'<td align="right">'.abs($row["amount"]).' Dr</td>'; 
					} 
					$result=$result.'</tr>';
					$total_amount=$total_amount+$row["amount"];
					$i++;
			  }
		}

		$result=$result.'</tbody>';
		$result=$result.'<tfoot><tr style="background:#1e293b;color:#fff;font-weight:700;"><td></td><td align="center">T O T A L</td><td align="right">'.ind_money(abs($total_amount),2).($total_amount>0?" Cr":" Dr").'</td></tr></tfoot>';
		$result=$result.'</table>';
	} 
		//$result.="<center><h3>Total Outstanding ".ind_money(abs($total_amount),2).($total_amount>0?" Cr":" Dr")."</h3></center>";
		$result.='</div>';
/* 	$result.='<div class="col-2 text-bg-light" style="border-left:1px inset #ccc;" >';
	$result.='<div class="text-center bg-success text-white"><h5 class="mb-0">SUMMARY</h5></div>';
	$result.='<div  id="card_1">';
	$result.='<table width="100%">';
	$result.='<tr><td align="right" width="50%">'."Amount : ".'</td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.ind_money(abs($total_amount),2).($total_amount>0?" Cr":" Dr")."</div></td></tr>"; 
	$result.='</table>';
	$result.='</div>';
			$result.='<div class="col-12"  style="border:0px solid #ccc; padding:5px;">';
	$result.='<br> <center><button type="button" class="btn btn-primary btn-sm" onclick="printTrialBalanceDiv('."'printable_div'".')">PRINT</button><br><br>';
	$result.='<button type="button" class="btn btn-secondary btn-sm" onclick="fnSideMenu(1)">Close</button></center>';
	$result.='</div>'; */
	$result='<div class="row"><div class="col-12"  id="printable_div"><center><h4>'. ($actype_txt!="ALL"?$actype_txt:"").' TRIAL BALANCE [Outstanding '.ind_money(abs($total_amount),2).($total_amount>0?" Cr":" Dr").']</h4></center>'.$result.'</div>';
}
echo $result;
?>