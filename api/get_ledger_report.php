<?php
session_start();
include_once '../connect_db.php';
$result=""; 
if(isset($_GET['from']))
{
	$from = date("Y-m-d", strtotime($_GET['from']));
	$to = date("Y-m-d", strtotime($_GET['to']));
	$achead =  $_GET['achead']  ;
	$total_amount=0;
	$prevBalance=0;
	$sql="SELECT sum(amount) as prevBalance FROM journal_details where dt <'".$from."' and achead='".$achead."'";  
	$prev_balance=0;
	if($query=mysqli_query($connection,$sql))
	{
		if($row=mysqli_fetch_array($query))
		{
			$prev_balance=$row["prevBalance"];
		}
	}
	$sql="SELECT * FROM journal_details where dt between '".$from."' and '".$to."' and achead='".$achead."' and amount<>0 order by dt";  
	$result='<div class="row"><div class="col-10"  id="printable_div">'; 
	if($query=mysqli_query($connection,$sql))
	{
		$result.='<table class="table table-bordered table-hover table-condensed table-striped " id="resultDetailTable">';
			$result=$result.'<thead class="table-dark">';
			$result=$result.'<tr> ';
			$result=$result.'<th>SNo</th>';
			$result=$result.'<th>DATE</th>'; 
			$result=$result.'<th>ACHEAD</th>';
			$result=$result.'<th>DESCRIPTION</th>';
			$result=$result.'<th>Dr</th>';
			$result=$result.'<th>Cr</th>';
			
			$result=$result.'</tr>';
			$result=$result.'</thead>';
			$result=$result.'<tbody>';
			$i=1;
			$total_cr=0.0;
			$total_dr=0.0;
			if($prev_balance!=0)
			{
				$result=$result.'<tr>';
					$result=$result.'<td>'.$i++.'</td>';
					$result=$result.'<td>&nbsp;</td>';
					$result=$result.'<td>Prev Balance</td>';
					if($prev_balance >0)
					{
						$result=$result.'<td>'.$prev_balance.' Cr</td>'; 
						$total_cr+=$prevBalance;
					}
					else
					{
						$result=$result.'<td>'.abs($prev_balance).' Dr</td>'; 
						$total_dr+=abs($prevBalance);
					}	
					$desc="Previous Balance";
					$result=$result.'<td>'.$desc.'</td>'; 
					$result=$result.'</tr>';	
					$total_amount+=$prev_balance;
			}
		if(mysqli_num_rows($query)>0)
		{ 
			 while($row=mysqli_fetch_array($query)) {
					$result=$result.'<tr>';
					$result=$result.'<td>'.$i.'</td>';
					$result=$result.'<td>'.date("d-m-Y", strtotime($row["dt"])).'</td>';
					$result=$result.'<td>'.$row["contra_achead"].'</td>';
					

					$desc=$row["description"];
					$QPos=strpos($desc,"Quote :");
					$Sq_No="";
					$JC_NOS="";
					if($QPos!==False)
					{
						$Sq_No=substr($desc,$QPos+7);
						if(!str_contains($Sq_No,","))
							$desc = substr($desc,0,$QPos+7).$Sq_No.'&nbsp;<a class="m-1 noPrint" target="_blank" href="bill_receipt.php?sq='.$Sq_No.'">Print</i></a>';
						$sql="select * from sales_quotation_master where quotation_no in (".$Sq_No.");";
						//echo "<hr>".$sql."<hr>";
						if($sq_qry=mysqli_query($connection,$sql))
						{
							while($sq_row=mysqli_fetch_array($sq_qry))
							{
								if($JC_NOS!="")$JC_NOS.=",";
								$JC_NOS.=$sq_row["jobcard_nos"];
							}
						}
						$desc .= '&nbsp;<a class="m-1 noPrint"  href="javascript:void()" onclick="showJobCard('."'".$JC_NOS."'".','."'".$Sq_No."'".')">Show</i></a>';
						
					}
					
					$SI_Pos=strpos($desc,"Sales Invoice :");
					if($SI_Pos!==False)
					{
						$Si_No=substr($desc,$SI_Pos+15);
						$Si_No=substr($Si_No,0,strpos($Si_No," "));
						//$desc.=" >".$Si_No."<";
						$desc=substr($desc,0,$SI_Pos+15+strlen($Si_No)).'&nbsp;<a class="noprint" href="sale_invoice_print.php?si='.$Si_No.'" target="_blank">Print</a>&nbsp;'. '&nbsp;<a class="m-1 noPrint"  href="javascript:void()" onclick="showJobCard('."'".$JC_NOS."'".','.$Sq_No.','.$Si_No.')">Show</i></a>'.substr($desc,$SI_Pos+15+strlen($Si_No));;
					}
					
					$result=$result.'<td>'.$desc.'</td>'; 
					if($row["amount"] >0)
					{
						$result=$result.'<td></td>'; 
						$result=$result.'<td>'.$row["amount"].'</td>'; 
						
						$total_cr+=$row["amount"];
					}
					else
					{
						
						$result=$result.'<td>'.abs($row["amount"]).'</td>'; 
						$result=$result.'<td></td>'; 
						$total_dr+=abs($row["amount"]);
					}					
					$result=$result.'</tr>';
					$total_amount=$total_amount+$row["amount"];
					$i++;
			  }
			
		}
		 
		$result=$result.'</tbody>';
		$result=$result.'</table>';
	} 
	$result.='</div>';
	$result.='<div class="col-2 text-bg-light" style="border-left:1px inset #ccc;" >';
	$result.='<div class="text-center bg-success text-white"><h5 class="mb-0">SUMMARY</h5></div>';
	$result.='<div  id="card_1">';
	$result.='<table width="100%">';
	$result.='<tr><td align="right" width="50%">'."Total Dr   : ".'</td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.ind_money(abs($total_dr),2)." Dr"."</div></td></tr>"; 
	$result.='<tr><td align="right" width="50%">'."Total Cr   : ".'</td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.ind_money(abs($total_cr),2)." Cr"."</div></td></tr>"; 
	$result.='<tr><td align="right" width="50%">'."Amount : ".'</td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.ind_money(abs($total_amount),2).($total_amount>0?" Cr":" Dr")."</div></td></tr>"; 
	$result.='</table>';
	$result.='</div>';
			$result.='<div class="col-12"  style="border:0px solid #ccc; padding:5px;">';
	$result.='<br> <center><button type="button" class="btn btn-primary btn-sm" onclick="printLedgerDiv('."'printable_div'".')">PRINT</button><br><br>';
	$result.='<button type="button" class="btn btn-secondary btn-sm" onclick="fnSideMenu(1)">Close</button></center>';
	$result.='</div>';
	$result.='</div>';
}
echo $result;
?>