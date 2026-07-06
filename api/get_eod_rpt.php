<?php
session_start();
include_once '../connect_db.php';
$result=""; 
$dt=date("Y-m-d");
	$sql="SELECT * FROM machine_eod_counter_details where dt between '".date("Y-m-d",strtotime($_POST["fromdt"]))."' and '".date("Y-m-d",strtotime($_POST["todt"]))."' order by dt;";
	
	$result.='<div class="row"><div class="col-10"  id="printable_div">';
	if($query=mysqli_query($connection,$sql))
	{
		$result.='<table class="table table-bordered table-hover table-condensed table-striped " id="resultDetailTable">';
			$result=$result.'<thead class="table-dark">';
			$result=$result.'<tr> ';	
			
			$result=$result.'<th>Date</th>';
			$result=$result.'<th>Machine</th>';
			$result=$result.'<th>Counter</th>';
			$result=$result.'<th>Software</th>';
			$result=$result.'<th>Difference</th>';
			$result=$result.'</tr>';
			$result=$result.'</thead>';
			$result=$result.'<tbody>';
			$i=1;
			$tot_counter=0;
		if(mysqli_num_rows($query)>0)
		{ 
			 $prev_dt="";
			 
			 while($row=mysqli_fetch_array($query)) {
			
					if($prev_dt!=$row["dt"])
					{
						if($prev_dt!="")
						$result=$result.'<tr><td colspan="5"><center>-::-</center></td></tr>';
						$prev_dt=$row["dt"];
						
					}
					$result=$result.'<tr>';

					$result=$result.'<td>'.date("d-m-Y",strtotime($row["dt"])).'</td>';
					$result=$result.'<td>'.$row["machine_code"].'</td>';
					$result=$result.'<td>'.$row["machine_counter_reading"].'</td>';	
					$result=$result.'<td>'.$row["software_counter_reading"].'</td>';	
					$result=$result.'<td>'.$row["difference_in_reading"].'</td>'; 
					$result=$result.'</tr>';
					$i++;
					$tot_counter=$tot_counter+$row["machine_counter_reading"] ;
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
	$result.='<tr><td align="right" width="50%">'."Total Counter : ".'</td><td><div style="border:1px solid black;text-color:black;background-color:white;text-align:right;padding:2px;">'.ind_money($tot_counter,2)."</div></td></tr>";
	$result.='</table>';
	$result.='</div>';
			$result.='<div class="col-12"  style="border:0px solid #ccc; padding:5px;">';
	$result.='<br> <center><button type="button" class="btn btn-primary btn-sm" onclick="printEodDiv('."'printable_div'".')">PRINT</button><br><br>';
	$result.='<button type="button" class="btn btn-secondary btn-sm" onclick="fnSideMenu(1)">Close</button></center>';
	$result.='</div>';
	$result.='</div>';

	$result.='</div>';
echo $result;
?>