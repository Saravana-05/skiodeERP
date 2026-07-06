<?php
session_start();
include_once "connect_db.php";

$dt = date("Y-m-d");
$prev_dt=$dt;

function get_software_reading($mcode)
{
	global $connection,$dt;
	$use_cnt=0;
	$spill_cnt=0;
	$spill_cnt1=0;
	$sql="SELECT sum(usage_count) as use_cnt FROM `job_card_wise_machine_usage` WHERE machine_code='".$mcode."' and jobcard_date='".$dt."';";
	
	if($qry=mysqli_query($connection,$sql))
	{
		if(mysqli_num_rows($qry)>0)
		{
			if($row=mysqli_fetch_array($qry))
			{
				$use_cnt=is_null($row["use_cnt"])?0:$row["use_cnt"];
			}
		}
	}
	$sql="SELECT sum(spillage_count) as spill_cnt FROM `job_card_wise_machine_spillage` WHERE machine_code='".$mcode."' and jobcard_date='".$dt."';";
	
	if($qry=mysqli_query($connection,$sql))
	{
		if(mysqli_num_rows($qry)>0)
		{
			if($row=mysqli_fetch_array($qry))
			{
				$spill_cnt=is_null($row["spill_cnt"])?0:$row["spill_cnt"];
			}
		}
	}
	$sql="SELECT sum(qty) as spill_cnt FROM `wastage_master` WHERE machine_code='".$mcode."' and wastage_date between '".$dt." 00:00:00' and  '".$dt." 23:59:59';";
	
	if($qry=mysqli_query($connection,$sql))
	{
		if(mysqli_num_rows($qry)>0)
		{
			if($row=mysqli_fetch_array($qry))
			{
				$spill_cnt1=is_null($row["spill_cnt"])?0:$row["spill_cnt"];
			}
		}
	}
return $use_cnt+$spill_cnt+$spill_cnt1;	
}
function get_machine_reading($mcode)
{
	global $connection,$dt;
	$use_cnt=0;

	$sql="SELECT machine_counter_reading FROM `machine_eod_counter_details` WHERE machine_code='".$mcode."' and dt='".$dt."';";
	
	if($qry=mysqli_query($connection,$sql))
	{
		if(mysqli_num_rows($qry)>0)
		{
			if($row=mysqli_fetch_array($qry))
			{
				$use_cnt=is_null($row["machine_counter_reading"])?0:$row["machine_counter_reading"];
			}
		}
	}

return $use_cnt;	
}
?>
<style>
.dataentryContent>.headerDiv
{
	background-color:#3a3a3a;border-radius:0px;color:#fff;margin-bottom:10px;
}
</style>
<div class="bg_aliceblue p-1 m-1 pt-1 dataentryContent" >
	<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center p-1 headerDiv">
		<h5 class="mb-0 ">End-Of-Day Process</h5> 			
	</div>
	<form action="api/eod_save.php" method="post" id="eod_form">
	<?php
		$eod_completed_already=0;
		$sql="select dt from machine_eod_counter_details where dt='".$dt."' limit 1";
		if($qry=mysqli_query($connection,$sql))
		{
			if(mysqli_num_rows($qry)>0)
			{
				if($row=mysqli_fetch_array($qry))
				{
					if($_SESSION["user_type"]!="ADMIN")
						$eod_completed_already=1;
				}
			}
		}
		
		$sql="select dt from machine_eod_counter_details where dt<'".$dt."' order by dt desc limit 1";
		if($qry=mysqli_query($connection,$sql))
		{
			if(mysqli_num_rows($qry)>0)
			{
				if($row=mysqli_fetch_array($qry))
				{
					$prev_dt=date("Y-m-d", strtotime($row["dt"]));
				}
			}
		}
		
		$sql="SELECT a.machine_code,a.machine_name,b.dt,a.counter_reading as mac_rd,b.machine_counter_reading FROM `machine_master` a left join (select * from machine_eod_counter_details where dt='".$prev_dt."') b on a.machine_code=b.machine_code;";
		
		if($qry=mysqli_query($connection,$sql))
		{
			?>
			<table class="table table-striped table-bordered">
			<thead>
			<tr>
				<th><center>Machine</th>
				<th><center>Prev.Reading</th>
				<th><center>Current Reading</th>
				<th><center>Software Reading</th>
				<th><center>Difference</th>
			</tr>
			</thead>
			<?php
			while($row=mysqli_fetch_array($qry))
			{
				echo "<tr>";
				echo "<td>".$row["machine_name"]."</td>";
				$mac_rd=0;
				if(is_null($row["machine_counter_reading"]))
					$mac_rd=$row["mac_rd"];
				else
					$mac_rd=$row["machine_counter_reading"];
				echo "<td ><center>".$mac_rd."</td>";
				$machine_reading_today=0;
				$machine_reading_today=get_machine_reading($row["machine_code"]);
				if(!$eod_completed_already)				
				{
				echo "<td>".'<input type="hidden" class="form-control text-center macs_txt" size="10" name="macs_txt"  value="'.$row["machine_code"].'" /><input type="text" class="form-control numericOnly machine_reading_today text-center" size="10" name="'.$row["machine_code"].'_txt" id="'.$row["machine_code"].'_txt" mcode="'.$row["machine_code"].'" value="'.$machine_reading_today.'"/>'."</td>";
				}
				else
				{
					
					echo "<td><center>$machine_reading_today</center></td>";
				}
				$software_cnt=get_software_reading($row["machine_code"]);
				echo "<td><center>".'<span id="'.$row["machine_code"].'_software_lbl" title="'.$software_cnt.'">'.$mac_rd+$software_cnt.'</span'."</td>";
				$software_cnt=$mac_rd+$software_cnt;
				$diff_cnt= $machine_reading_today - $software_cnt;
				$diff_str="";
				if($diff_cnt>0) $diff_str=" Exess Printed";
				if($diff_cnt<0) $diff_str=" Exess Billed";
				echo "<td><center>".'<span id="'.$row["machine_code"].'_diff_lbl">'.$diff_cnt.'</span><span id="'.$row["machine_code"].'_diff_str">'.$diff_str.'</span>'."</td>";
			}
			?>
			</table>
			<?php if(!$eod_completed_already ) { ?>
			<center><button type="submit" class="btn btn-primary">Close the Day</button></center>
			<?php } else { ?>
			<center>EOD Completed. So, u can just see...</center>
			<?php } ?>
			</form>
			<?php
		}
		
	?>
</div>	
<script>
$(document).ready(function() {
	SetUpBasics();
	$("#eod_form").submit(function(e){
		e.preventDefault();
		if (confirm('Are you sure you want to save these and close the day?')) {
			// Save it!
			

			
 			var allTableData = new Array();
			$(".macs_txt").each(function()
			{
				id=$(this).val();
				mac_id="#"+id+"_txt";
				soft_id="#"+id+"_software_lbl";
				diff_id="#"+id+"_diff_lbl";
				var tempArray= {}
				tempArray['machine_code']=id;
				tempArray['machine_counter_reading']=$(mac_id).val();
				tempArray['software_counter_reading']=$(soft_id).text();
				tempArray['difference_in_reading']=$(diff_id).text();
				arrCnt=0;
				if(allTableData.length>=1)
					arrCnt=allTableData.length;
				allTableData[arrCnt]=tempArray;	 	
			});
			
		var form = $(this);
			var actionUrl = form.attr('action');
			
			$.ajax({
				type: "POST",
				url: actionUrl,
				data: {"allTableData":JSON.stringify(allTableData)}, // serializes the form's elements.
				success: function(data)
				{
				  alert(data); // show response from the php script.
				  if(data=="Success") fnSideMenu(1);
				}
			});			
		}
	});

  $(".machine_reading_today").change( function(e) {
	  id=$(this).attr('mcode');
	  soft_id=id+"_software_lbl";
	  diff_id=id+"_diff_lbl";
	  diff_str_id=id+"_diff_str";
	  mcnt=$(this).val();
	  
	  mcnt=parseInt(mcnt);
	  if(isNaN(mcnt)) mcnt=0;
	  soft_cnt=parseInt($("#"+soft_id).text());
	  if(isNaN(soft_cnt)) soft_cnt=0;
	  diff_cnt=mcnt-soft_cnt;
	  diff_str="";
	  if(diff_cnt>0) diff_str=" Exess Printed";
	  if(diff_cnt<0) diff_str=" Exess Billed";
	  
	  $("#"+diff_id).text(diff_cnt);
	  $("#"+diff_str_id).text(diff_str);
	  
  });
}); 
</script>

