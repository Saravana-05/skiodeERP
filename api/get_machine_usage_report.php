<?php
session_start();
include_once '../connect_db.php';
$result=""; 
$machine_ary=[];
$machine_cnt_ary=[];
class row_object{
	public $dt;
	public $jc_no;
	public $material;
	public $qty;
	public $machine;
	public $meter;
	public $time;
	public $user;
	public function __construct($dt,$jc_no,$material,$qty,$machine,$meter,$time,$user)
	{
		$this->dt=$dt;
		$this->jc_no=$jc_no;
		$this->material=$material;
		$this->qty=$qty;
		$this->machine=$machine;
		$this->meter=$meter;
		$this->time=$time;
		$this->user=$user;
	}
}
$rows=[];
if(isset($_POST['from']))
{
	$from = date("Y-m-d", strtotime($_POST['from']));
	$to = date("Y-m-d", strtotime($_POST['to']));
	$sql="SELECT a.*,b.machine_name FROM job_card_wise_machine_usage a join machine_master b  on a.machine_code=b.machine_code  
	where jobcard_date >='".$from."' and jobcard_date<='".$to."'";  
	$sql="Select b.*,a.* from job_card_details b inner join job_card_master where a.jobcard_date >='".$from."' and a.jobcard_date<='".$to."'";
	
	$sql="Select a.jobcard_date,a.jobcard_no, b.product_code,b.sheet_count,b.machine_code,a.created_dt_tm,a.created_by from jobcard_details b inner join jobcard_master a on b.jobcard_no=a.jobcard_no where a.jobcard_date  between '".$from."' and '".$to."' and machine_code<>''";
	if($_POST["machine_code_txt"]!="all")
		$sql.=" and machine_code='".$_POST["machine_code_txt"]."'";
	if($_POST["user_name_txt"]!="all")
		$sql.=" and created_by='".$_POST["user_name_txt"]."'";
	
	$sql.=" order by a.jobcard_no, a.created_dt_tm;";
	
	$sql="Select a.jobcard_date,b.jobcard_no,b.usage_count,b.material_code,b.machine_code,a.created_dt_tm,a.created_by from job_card_wise_machine_usage b inner join jobcard_master a on b.jobcard_no=a.jobcard_no where a.jobcard_date between '".$from."' and '".$to."' and machine_code<>'' ";
	if($_POST["machine_code_txt"]!="all")
		$sql.=" and machine_code='".$_POST["machine_code_txt"]."'";
	if($_POST["user_name_txt"]!="all")
		$sql.=" and created_by='".$_POST["user_name_txt"]."'";
	
	$sql.=" order by a.jobcard_no, a.created_dt_tm;";
	
	
	
	//$result.=$sql; 
	//echo $sql;
	
	if($query=mysqli_query($connection,$sql))
	{
		if(mysqli_num_rows($query)>0)
		{ 
			 while($row=mysqli_fetch_array($query)) {
				    $machine_code=$row["machine_code"];
					if(!array_key_exists($machine_code,$machine_ary))
					{
						$cntr_sql="SELECT machine_code, dt,machine_counter_reading FROM `machine_eod_counter_details` where machine_code='".$_POST["machine_code_txt"]."' AND dt<'".$from."' order by dt desc LIMIT 1;";
						
						$machine_ary[$machine_code]=0;
						$machine_cnt_ary[$machine_code]=0;
						if($cntr_qry=mysqli_query($connection,$cntr_sql))
						{
							if($cntr_row=mysqli_fetch_array($cntr_qry))
							{
							$machine_ary[$machine_code]=$cntr_row["machine_counter_reading"];
							
							}
						}
					}
					$machine_ary[$machine_code]+=$row["usage_count"];
					$machine_cnt_ary[$machine_code]+=$row["usage_count"];
/* 					$result=$result.'<tr>';
					$result=$result.'<td>'.$i.'</td>';
					$result=$result.'<td>'.date("d-m-Y", strtotime($row["jobcard_date"])).'</td>';
					
					$result=$result.'<td>'.$row["jobcard_no"].'</td>';
					$result=$result.'<td>'.str_replace("_"," ",$row["material_code"]).'</td>';
					$result=$result.'<td>'.$row["usage_count"].'</td>';
					$result=$result.'<td>'.str_replace("_"," ",$row["machine_code"]).'</td>';
					//$result=$result.'<td>'.$row["machine_code"]."= ".$machine_ary[$machine_code].'</td>';
					
					$result=$result.'<td>'.$machine_ary[$machine_code].'</td>';
					$result=$result.'<td>'.date("h:i:s", strtotime($row["created_dt_tm"])).'</td>';
					$result=$result.'<td>'.$row["created_by"].'</td>'; 
					$result=$result.'</tr>';
					$i++; */
					
					$new_row=new row_object(date("d-m-Y",strtotime($row["jobcard_date"])),$row["jobcard_no"],str_replace("_"," ",$row["material_code"]),$row["usage_count"],str_replace("_","",$row["machine_code"]),$machine_ary[$machine_code],date("h:i:s",strtotime($row["created_dt_tm"])),$row["created_by"]);
					//$new_row=new row_object(1,2,3,4,5,6,7,8);
					$rows[]=$new_row;
			  }
			
		}
	}
	$sql="SELECT a.*,b.created_by,b.created_dt_tm FROM job_card_wise_material_spillage a inner join jobcard_master b on a.jobcard_no=b.jobcard_no where a.jobcard_date between '".$from."' and '".$to."' and machine_code<>''";
	if($_POST["machine_code_txt"]!="all")
		$sql.=" and a.machine_code='".$_POST["machine_code_txt"]."'";
	if($_POST["user_name_txt"]!="all")
		$sql.=" and b.created_by='".$_POST["user_name_txt"]."'";	
	$query=mysqli_query($connection,$sql);
	if($query) while($row=mysqli_fetch_array($query))
	{
		$machine_code=$row["machine_code"];
		if(!array_key_exists($machine_code,$machine_ary))
		{
			$cntr_sql="SELECT machine_code, dt,machine_counter_reading FROM `machine_eod_counter_details` where machine_code='".$_POST["machine_code_txt"]."' AND dt<'".$from."' order by dt desc LIMIT 1;";

			$machine_ary[$machine_code]=0;
			$machine_cnt_ary[$machine_code]=0;
			if($cntr_qry=mysqli_query($connection,$cntr_sql))
			{
				if($cntr_row=mysqli_fetch_array($cntr_qry))
				{
				$machine_ary[$machine_code]=$cntr_row["machine_counter_reading"];

				}
			}
		}
		$machine_ary[$machine_code]+=$row["spillage_count"];
		$machine_cnt_ary[$machine_code]+=$row["spillage_count"];
		$new_row=new row_object(date("d-m-Y",strtotime($row["jobcard_date"])),$row["jobcard_no"]. " Spill",str_replace("_"," ",$row["material_code"]),$row["spillage_count"],str_replace("_","",$row["machine_code"]),$machine_ary[$machine_code],date("h:i:s",strtotime($row["created_dt_tm"])),$row["created_by"]);

					$rows[]=$new_row;
	}
	
	$sql="SELECT * FROM wastage_master where wastage_date between '".$from." 00:00:00' and '".$to." 23:59:59' and machine_code<>''";
	if($_POST["machine_code_txt"]!="all")
		$sql.=" and machine_code='".$_POST["machine_code_txt"]."'";
	if($_POST["user_name_txt"]!="all")
		$sql.=" and created_by='".$_POST["user_name_txt"]."'";	
	$query=mysqli_query($connection,$sql);
	if($query) while($row=mysqli_fetch_array($query))
	{
		$machine_code=$row["machine_code"];
		if(!array_key_exists($machine_code,$machine_ary))
		{
			$cntr_sql="SELECT machine_code, dt,machine_counter_reading FROM `machine_eod_counter_details` where machine_code='".$_POST["machine_code_txt"]."' AND dt<'".$from."' order by dt desc LIMIT 1;";
			
			$machine_ary[$machine_code]=0;
			$machine_cnt_ary[$machine_code]=0;
			if($cntr_qry=mysqli_query($connection,$cntr_sql))
			{
				if($cntr_row=mysqli_fetch_array($cntr_qry))
				{
				$machine_ary[$machine_code]=$cntr_row["machine_counter_reading"];
				
				}
			}
		}		
		$machine_ary[$machine_code]+=$row["qty"];
		$machine_cnt_ary[$machine_code]+=$row["qty"];
		$new_row=new row_object(date("d-m-Y",strtotime($row["wastage_date"])),"W.Entry",str_replace("_"," ",$row["material_code"]),$row["qty"],str_replace("_","",$row["machine_code"]),$machine_ary[$machine_code],date("h:i:s",strtotime($row["wastage_date"])),$row["created_by"]);
					
					$rows[]=$new_row;
	}
	

	
	$result.='<div class="row"><div class="col-10"  id="printable_div">';
	$result.='<table class="table table-bordered table-hover table-condensed table-striped " id="resultDetailTable">';
	$result=$result.'<thead class="table-dark">';
	$result=$result.'<tr> ';
	$result=$result.'<th>SNo</th>';
	$result=$result.'<th>Job Card Date</th>'; 
	$result=$result.'<th>Job Card No</th>'; 
	$result=$result.'<th>Product</th>'; 

	$result=$result.'<th>Qty</th>'; 
	$result=$result.'<th>Machine</th>'; 
	$result=$result.'<th>Meter</th>'; 
	$result=$result.'<th>Time</th>'; 
	$result=$result.'<th>User</th>'; 
	$result=$result.'</tr>';
	$result=$result.'</thead>';
	$result=$result.'<tbody>';
	$i=1;
function cmp( $a, $b )
{ 
  if(  $a->dt ==  $b->dt ){ return 0 ; } 
  return ($a->dt < $b->dt) ? -1 : 1;
} 
usort($rows,'cmp');	
	foreach ( $rows as $r)
	{
	$result=$result.'<tr>';
	$result=$result.'<td>'.$i++.'</td>';
	$result=$result.'<td>'.$r->dt.'</td>';

	$result=$result.'<td>'.$r->jc_no.'</td>';
	$result=$result.'<td>'.$r->material.'</td>';
	$result=$result.'<td>'.$r->qty.'</td>';
	$result=$result.'<td>'.$r->machine.'</td>';
	$result=$result.'<td>'.$r->meter.'</td>';
	$result=$result.'<td>'.$r->time.'</td>';
	$result=$result.'<td>'.$r->user.'</td>';


	$result=$result.'</tr>';

	}

	$result=$result.'</tbody>';
	$result=$result.'</table>';
	$result.='</div>';
	$result.='<div class="col-2 text-bg-light" style="border-left:1px inset #ccc;" >';
	$result.='<div  id="card_1">';
	$result.='<div class="text-center bg-success text-white"><h5 class="mb-0">SUMMARY</h5></div>';
	$result.='<table class="table table-bordered" width="100%" style="bgcolor:white;color:black;font-weight:bold;" border="1">';
	foreach ($machine_cnt_ary as $key => $value) {
	$result.="<tr>";
	$result.="<td>".$key."</td>";
	$result.="<td align='right'>".$value."</td>";

	$result.="</tr>";
	}
	$result.='</table>';
	$result.='</div>';
					$result.='<div class="col-12"  style="border:0px solid #ccc; padding:5px;">';
	$result.='<br> <center><button type="button" class="btn btn-primary btn-sm" onclick="printMachineUsageDiv('."'printable_div'".')">PRINT</button><br><br>';
	$result.='<button type="button" class="btn btn-secondary btn-sm" onclick="fnSideMenu(1)">Close</button></center>';
	$result.='</div>';
	$result.='</div>';

	$result.='</div>';		
		
	 
}
echo $result;
?>