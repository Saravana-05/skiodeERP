<style>
.mt_1
{
	margin-top:0.25rem !important;
}
/* label:focus-within {
  outline: 2px solid green;
  box-shadow: 0 0 5px rgba(0, 128, 0, 0.5);
}
input[type="checkbox"]:focus {
  outline: 2px solid blue; /* Adds a blue outline */
  box-shadow: 0 0 5px rgba(0, 0, 255, 0.5); /* Adds a subtle blue shadow */
} */
</style>
<div class="row" style="font-faimly:arial;font-size:12px;font-weight:bold;">
<div class="col-8">
<?php
include_once 'connect_db.php';
 
		$mach_sql ="SELECT a.*,b.machine_name FROM `product_machine_detail` a inner join machine_master b on a.machine_code= b.machine_code where product_code='".$_GET["product_code_txt"]."';"; 
		if($mach_query = mysqli_query($connection,$mach_sql))
		{
			if(mysqli_num_rows($mach_query)>0)
			{
				?>
				<div class="row"> 
				<label for="Stock Name" class="col-md-3 col-form-label mt_1">Machine  </label>
				<div class="col-md-5 mt_1">
					
					<select class="form-select" id="machine_code_txt">		
						<option value="">Select</option>
				<?php
				while($mach_row=mysqli_fetch_array($mach_query))
				{
					$mach_code=$mach_row["machine_code"];
					$selected="";
					//if(str_contains($mach_code,"IRIDES")) $selected="selected";
					echo '<option value="'.$mach_row["machine_code"].'" '.$selected.' >'.$mach_row["machine_name"].'</option>';
				}
				?>
				</select>
				</div>
				</div>
				<?php
			}
		}//if($mach_query = mysqli_query($connection,$mach_sql))
$product_code_txt=$_GET["product_code_txt"]; 
$sql ="select * from product_master where product_code='".$product_code_txt."';";
echo '<input type="text" autocomplete="off" id="product_rates_txt" style="display:none;"/>';
if($query=mysqli_query($connection,$sql))
{
	if($row=mysqli_fetch_array($query))
	{
		?>
		<div class="row">
		<input type="hidden" id="gst_percentage_txt" value="<?php echo $row["gst_percentage"]; ?>"/>
		<?php
		
		if($row["1st_copy_addl_copy_applicable"])
		{?>
			
				<label for="Stock Name" class="col-md-3 col-form-label mt_1">1st Copy </label>
				<div class="col-md-3 mt_1">
					<input type="text" autocomplete="off" id="1st_copy_nos_txt" size="2" name="1st_copy_nos_txt" class="numericOnly"  style="font-size:14px;font-weight:bold;border-radius:3px;padding:2px;" />
					<label>@ </label><input type="text" autocomplete="off" id="1st_copy_rate_txt" size="2" name="1st_copy_rate_txt" disabled />
					<input type="hidden" id="1st_copy_rate_discounted_txt" name="1st_copy_rate_discounted_txt" style="font-size:14px;font-weight:bold;border-radius:3px;padding:2px;"/>
				</div>  
				<label for="Stock Name" class="col-md-2 col-form-label mt_1">Addl Copy </label>
				<div class="col-md-2 mt_1">
					<input type="text" autocomplete="off" id="addl_copy_nos_txt"  size="2" name="addl_copy_nos_txt" class="numericOnly"  style="font-size:14px;font-weight:bold;border-radius:3px;padding:2px;" />
					<label>@ </label><input type="text" autocomplete="off" id="addl_copy_rate_txt"  size="2" name="addl_copy_rate_txt" disabled />
					<input type="hidden" id="addl_copy_rate_discounted_txt" name="addl_copy_rate_discounted_txt" style="font-size:14px;font-weight:bold;border-radius:3px;padding:2px;"/>
				</div>
			 
<?php		}
		else
		{?>
				<?php
				if($row["category1"]=='SERVICES' && ($row["category2"]=='INHOUSE' ||$row["category2"]=='BINDING') )
				{?>
				</div><div class="row">
					<label for="no_of_ups_txt" class="col-md-3 col-form-label mt_1"><?php if($row["category2"]=='INHOUSE') {?>No. Of Ups :<?php } if($row["category2"]=='BINDING') {?>Sheet Count :<?php } ?> </label>
				<div class="col-md-4 mt_1">
					<input type="text" autocomplete="off" id="no_of_ups_txt" name="no_of_ups_txt" size="5" class="numericOnly"  style="font-size:14px;font-weight:bold;border-radius:3px;padding:2px;"/>
				</div>
				</div><div class="row">
				<?php
				}					
				?>
				<label for="Stock Name" class="col-md-3 col-form-label mt_1">Qty </label>
				<div class="col-md-9 mt_1">
					<input type="text" autocomplete="off" id="1st_copy_nos_txt" name="1st_copy_nos_txt" size="5" class="numericOnly"   style="font-size:14px;font-weight:bold;border-radius:3px;padding:2px;" />
					<?php if($row["is_material_based_rate_calculation"]==1) echo '<div style="display:none;">';?>
					<label>@ </label>
					<input type="text" autocomplete="off" id="1st_copy_rate_txt" name="1st_copy_rate_txt" size="5"   style="font-size:14px;font-weight:bold;border-radius:3px;padding:2px;" <?php if($row["is_manual_rate_allowed"]!=1)	{ ?>disabled <?php } ?>/>
					<input type="hidden" id="1st_copy_rate_discounted_txt" name="1st_copy_rate_discounted_txt"/>
					<?php if($row["is_material_based_rate_calculation"]==1) echo '</div>';?>
				</div>
			 
<?php		}
		if($row["is_front_and_back_option_available"])
		{?>
			 <div class="col-md-2 mt_1"></div>
				<div class="col-md-4 mt_1"  style="font-size:14px;font-weight:bold;border-radius:3px;padding:2px;">
					&nbsp;&nbsp;&nbsp;&nbsp;<input class="form-check-input" type="checkbox" id="is_front_and_back_txt"
					<?php
						if(strpos($product_code_txt,"DOUBLE")) echo " checked";
					?>
					>
					<label class="form-check-label" for="flexCheckDefault">
						Front & Back
					  </label>
				</div>
			 	 
<?php		} ?>
		 

		</div>  
	
	<?php
		$matr_sql ="SELECT * FROM `product_material_details` a inner join material_master b on a.material_code= b.material_code where a.product_code='".$_GET["product_code_txt"]."';"; 
		//echo $matr_sql;
		if($matr_query = mysqli_query($connection,$matr_sql))
		{
			if(mysqli_num_rows($matr_query)>0)
			{
				?>
				<div class="row">
				<label for="Stock Name" class="col-md-2 col-form-label mt_1">Material  </label>
				<div class="col-md-10 mt_1">
					<p style='font-size: 0.8rem;'>
				<?php
				$mat_code_qty_txt="";
				$fixed_mat_code_qty_txt="";
				$floating_mat_code_qty_txt="";
				$mat_result='<table width="100%">';
				while($matr_row=mysqli_fetch_array($matr_query))
				{
					if($matr_row["is_alternatives_available"]==1)
					{
						$alternative_caption=$matr_row["alternative_caption"];
						$alternative_key=$matr_row["alternative_key"];
						$alt_sql="select a.*,b.material_name from product_material_details_alternatives a inner join material_master b on a.material_code=b.material_code where a.alternative_key='".$alternative_key."';";
						//echo $alt_sql;
						if($alt_qry=mysqli_query($connection,$alt_sql))
						{
							$mat_result .= "<tr><td>".$alternative_caption."</td><td>";
							$mat_result .=  '<select class="alt_mat" onchange="recalc_mats()" id="alt_'.$alternative_key.'" name="alt_'.$alternative_key.'">';
							$mat_result .= '<option value="'.$matr_row["material_code"].",".$matr_row["multiplication_factor"].'">'.$matr_row["material_name"].'</option>';
							while($alt_row=mysqli_fetch_array($alt_qry))
							{
								$mat_result .= '<option value="'.$alt_row["material_code"].",".$alt_row["multiplication_factor"].'">'.$alt_row["material_name"].'</option>';
							}
							$mat_result .=  '</select></td></tr>';
							if(strlen($mat_code_qty_txt)>0) $mat_code_qty_txt.="~";
							$mat_code_qty_txt.=$matr_row["material_code"].",".$matr_row["multiplication_factor"];
							if(strlen($floating_mat_code_qty_txt)>0) $floating_mat_code_qty_txt.="~";
							$floating_mat_code_qty_txt.=$matr_row["material_code"].",".$matr_row["multiplication_factor"];							
						}//if($alt_qry=mysqli_query($connection,$alt_sql))
						
						
					}//if($matr_row["is_alternatives_available"]==1)
					else
					{
						$mat_result .= '<tr><td colspan="2">'.$matr_row["material_name"].'</td></tr>';
						if(strlen($mat_code_qty_txt)>0) $mat_code_qty_txt.="~";
						$mat_code_qty_txt.=$matr_row["material_code"].",".$matr_row["multiplication_factor"];
						if(strlen($fixed_mat_code_qty_txt)>0) $fixed_mat_code_qty_txt.="~";
						$fixed_mat_code_qty_txt.=$matr_row["material_code"].",".$matr_row["multiplication_factor"];
					}//if($matr_row["is_alternatives_available"]==1)
					
				}//while
				echo $mat_result."</table>";
				echo '</p></div>';
				echo '<div title="prod_mat_qty_div" id="prod_mat_qty_div" style="display:none; border:1px solid red;">'.$mat_code_qty_txt."</div>";
				echo '<div title="fixed_prod_mat_qty_div" id="fixed_prod_mat_qty_div"  style="display:none;">'.$fixed_mat_code_qty_txt."</div>";
				echo '<div title="floating_prod_mat_qty_div" id="floating_prod_mat_qty_div"  style="display:none;">'.$floating_mat_code_qty_txt."</div>";
				echo '</div>';
			}
		}//if($mach_query = mysqli_query($connection,$mach_sql))			
	}//if($row=mysqli_fetch_array($query))
}
?>
</div>
<div class="col-4" style='border-left:1px dotted grey;'>
<center>For Estimation & Enquiry
			<table>
			<tr>
			<td align="right">Total Qty : </td>
			<td><input type="text" autocomplete="off" disabled id="total_qty_txt" name="total_qty_txt" size="6"  style="font-size:14px;font-weight:bold;border-radius:3px;padding:2px;" /></td>
			</tr>
			<tr>
			<td align="right">MOQ : </td>
			<td><input type="text" autocomplete="off" disabled id="moq_txt" name="moq_txt" size="6"  style="font-size:14px;font-weight:bold;border-radius:3px;padding:2px;" /></td>
			</tr>
			<tr>
			<td align="right">MBQ : </td>
			<td><input type="text" autocomplete="off" disabled id="mbq_txt" name="mbq_txt" size="6"  style="font-size:14px;font-weight:bold;border-radius:3px;padding:2px;" /></td>
			</tr>
			<td align="right">Bill.Qty : </td>
			<td><input type="text" autocomplete="off" disabled id="bill_qty_txt" name="bill_qty_txt" size="6"  style="font-size:14px;font-weight:bold;border-radius:3px;padding:2px;" /></td>
			</tr>
			<tr>
			<td align="right">Value : </td>
			<td><input type="text" autocomplete="off" disabled id="value_txt" name="value_txt" size="6"  style="font-size:14px;font-weight:bold;border-radius:3px;padding:2px;" />
				<input type="hidden" id="value_discountable_txt" name="value_discountable_txt" /></td>
</center>			
</div>
</div>
<script>
$(document).ready(function() {
	SetUpBasics();
			$('#is_front_and_back_txt').change(function(e) {
				//e.preventDefault();
				if(this.checked) {
					fc_cnt=parseInt($("#1st_copy_nos_txt").val());
					addl_cnt=parseInt($("#addl_copy_nos_txt").val());
					if(isNaN(fc_cnt)) fc_cnt=1;
					if(isNaN(addl_cnt)) addl_cnt=0;
					if(fc_cnt%2!=0 || addl_cnt%2!=0)
					{						
						toastr.error("ODD No FB Now Allowed");						
						this.checked=false;
					}
				}
			});
			$('input,select').off("keydown");
	        $('input,select').on("keydown",function(e) {
            if (e.which === 13) { // Check if the Enter key was pressed
				
				e.stopPropagation();
                e.preventDefault();
				
				
		let focusableElements = $(':focusable').not(':disabled, [tabindex="-1"]');
        let currentFocusIndex = focusableElements.index(document.activeElement);
		
        let nextFocusIndex;
		nextFocusIndex = (currentFocusIndex + 1) % focusableElements.length;
		focusableElements.eq(nextFocusIndex).focus();
		
/* 			const $currentFocused = $(':focus');
			const $tabbables = $('input, select, textarea, button, [tabindex]:not([tabindex="-1"])').filter(':visible');
			const currentIndex = $tabbables.index($currentFocused);
			
			$tabbables.eq(nextIndex).focus();				
			console.log(nextIndex); */
				//$(this).next().find('input').focus();
				//$.tabNext();
/*  				$(this).trigger({
					type: 'keyup',
					which: 9
				});  */
				//$('input, select')[$('input,select').index(this)+1].focus();
			}
			});
			
$('input, select, button').focus(function() {
            $(this).addClass('highlighted-focus');
			//$(this).effect("highlight", {color: "#ffffcc"}, 1000); // Highlight with yellow, duration 1 second
        });

         // Remove highlighting when an element loses focus
        $('input, select, button').blur(function() {
            $(this).removeClass('highlighted-focus');
        }); 	

	 
});
function recalc_mats()
{
	
	floating_mat_code_qty_txt="";
	$(".alt_mat").each(function() {
    var value = $(this).val();
    
	if(floating_mat_code_qty_txt!="")floating_mat_code_qty_txt=floating_mat_code_qty_txt+"~";
	floating_mat_code_qty_txt=floating_mat_code_qty_txt+value;
});
$("#floating_prod_mat_qty_div").text(floating_mat_code_qty_txt);
prod_mat_qty_div=floating_mat_code_qty_txt;
value=$("#fixed_prod_mat_qty_div").text();
if(prod_mat_qty_div!="" && value!="")prod_mat_qty_div=prod_mat_qty_div+"~";
prod_mat_qty_div=prod_mat_qty_div+value;
$("#prod_mat_qty_div").text(prod_mat_qty_div);

}
function isNumber(value) {
	retval=false;
	if(value!="")
	{
		if(!isNaN(value))
		{
			if(parseInt(value)>0)
				retval=true;
		}
	}
  return retval;
}
function calc_qty()
{
	var first_copy_count=0,addl_copy_count=0;
	first_val=$("#1st_copy_nos_txt").val();
	if(isNumber(first_val)){
		first_copy_count=parseInt(first_val);
		}
	if ($('#addl_copy_nos_txt').length) {
		addl_val=$("#addl_copy_nos_txt").val();
		if(isNumber(addl_val))
		{
			addl_copy_count=parseInt(addl_val);
		}
	}
	var qty=first_copy_count+addl_copy_count;
	$("#total_qty_txt").val(qty);
	fetch_rates();
}

function fetch_rates()
{
	var first_copy_count=0,addl_copy_count=0,qty=0,machine_code="",no_of_ups_txt="";
	first_val=$("#1st_copy_nos_txt").val();
	if(isNumber(first_val))
	{
		first_copy_count=parseInt(first_val);
	}
	if ($('#addl_copy_nos_txt').length) 
	{
		addl_val=$("#addl_copy_nos_txt").val();
		if(isNumber(addl_val))
		{
			addl_copy_count=parseInt(addl_val);
		}
	}
	qty = first_copy_count+addl_copy_count;
	prod_mat_qty_txt ="";
	if ($('#prod_mat_qty_div').length) 
	{
	prod_mat_qty_txt = $("#prod_mat_qty_div").text();
	}
	if ($("#1st_copy_rate_txt").prop("disabled")) 
	{
		product_code_txt = $('#product_code_txt').val();
		if ($('#machine_code_txt').length) 
		{
			machine_code=$("#machine_code_txt").val();

		}
		if ($('#no_of_ups_txt').length) 
		{
			no_of_ups_txt=$("#no_of_ups_txt").val();

		}	
		$.ajax({
			url: 'get_product_qty_based_rates.php', // Sample API endpoint
			method: 'GET',
			data : {'product_code_txt':product_code_txt,'first_copy_count':first_copy_count,'addl_copy_count':addl_copy_count,'machine_code':machine_code,'no_of_ups_txt':no_of_ups_txt, 'prod_mat_qty_txt':prod_mat_qty_txt},
			success: function(result) {
				// Update the content on success
				$("#product_rates_txt").val(result);
				$("#bill_qty_txt").val(first_copy_count);
				value=0;
				value_discountable_txt=0;
				rate_array = result.split(",");
				moq=parseInt(rate_array[5]);
				mbq=parseInt(rate_array[6]);
				$("#moq_txt").val(moq);
				$("#mbq_txt").val(mbq);
				if(rate_array[2]=="PRODUCT_SLAB")
				{
					if ($('#addl_copy_nos_txt').length)
					{
						$("#1st_copy_rate_txt").val(rate_array[0]);
						$("#1st_copy_rate_discounted_txt").val(rate_array[3]);
						if(rate_array.length>2)
						{
							$("#addl_copy_rate_txt").val(rate_array[1]);
							$("#addl_copy_rate_discounted_txt").val(rate_array[4]);
							value=parseFloat(rate_array[0])* first_copy_count+parseFloat(rate_array[1])* addl_copy_count;
							value_discountable_txt=value-(parseFloat(rate_array[3])* first_copy_count+parseFloat(rate_array[4])* addl_copy_count);
							
						}
						else
						{
							$("#addl_copy_rate_txt").val("");
							$("#addl_copy_rate_discounted_txt").val("");


							value=parseFloat(rate_array[0])* first_copy_count;
							value_discountable_txt=value-(parseFloat(rate_array[3])* first_copy_count);

						}
					}
					else
					{
						
						$("#1st_copy_rate_txt").val(rate_array[0]);
						$("#1st_copy_rate_discounted_txt").val(rate_array[3]);
						if(moq>1 || mbq>1)
						{
							
							if(first_copy_count<moq) first_copy_count=moq;
							if(first_copy_count>moq)
							{
							rndto=first_copy_count % mbq;
							if(rndto>0)
							{
								rndto=mbq-rndto;
								first_copy_count=first_copy_count+rndto;
							}
							}
							$("#bill_qty_txt").val(first_copy_count);
						}
						
						value=parseFloat(rate_array[0])* first_copy_count;
						value_discountable_txt=value-(parseFloat(rate_array[3])* first_copy_count);
					}
				}
				else if(rate_array[2]=="SERVICE_SLAB")
				{
					
					if(parseFloat(rate_array[0])>0)
					{
					$("#1st_copy_rate_txt").val(rate_array[0]);
					$("#1st_copy_rate_discounted_txt").val(rate_array[3]);
					$("#addl_copy_rate_txt").val(rate_array[1]);
					$("#addl_copy_rate_discounted_txt").val(rate_array[4]);
					value=parseFloat(rate_array[0]) +parseFloat(rate_array[1])* qty;
					value_discountable_txt=value-(parseFloat(rate_array[3]) +parseFloat(rate_array[4])* qty);
					}
					else
					{
						//for soft binding scenario
					$("#1st_copy_rate_txt").val(rate_array[1]);
					$("#1st_copy_rate_discounted_txt").val(rate_array[4]);
					$("#addl_copy_rate_txt").val("");
					$("#addl_copy_rate_discounted_txt").val("");
					value=parseFloat(parseFloat(rate_array[1])* qty);
					value_discountable_txt=value-(parseFloat(rate_array[4])* qty);
						
					}
				}
				else if(rate_array[2]=="SERVICE_FLAT")
				{
					$("#1st_copy_rate_txt").val(rate_array[0]);
					$("#1st_copy_rate_discounted_txt").val(rate_array[3]);
					$("#addl_copy_rate_txt").val(rate_array[1]);
					$("#addl_copy_rate_discounted_txt").val(rate_array[4]); 
					value=parseFloat(rate_array[0]) ;
					value_discountable_txt=value;

				}
				else if(rate_array[2]=="MAT_BASED_VALUE")
				{	
					$("#1st_copy_rate_txt").val("");
					$("#addl_copy_rate_txt").val("");
					value=parseFloat(rate_array[0]);
					value_discountable_txt=value-parseFloat(rate_array[3]);
				}			
				$("#value_txt").val(value.toFixed(2));
				$("#value_discountable_txt").val(value_discountable_txt.toFixed(2));
			},
			error: function(error) {
				// Handle errors
				console.error('Error:', error);
			}
		});
	}//if ($("#1st_copy_rate_txt").prop("disabled"))
	else
	{
		
		rate_txt=$("#1st_copy_rate_txt").val();
		
		if(isNumber(rate_txt))
			{
				rate_txt=parseFloat(rate_txt);
			}
		else{
			rate_txt=0.0;
			
		}
		value=parseFloat(rate_txt* qty);
		$("#value_txt").val(value.toFixed(2));
		$("#value_discountable_txt").val("0");
	}

}
$("#1st_copy_rate_txt").on("blur", function(e) {	
	e.stopPropagation();
	calc_qty();
	});
$("#1st_copy_rate_txt").on("keyup", function(e) {
	e.stopPropagation();
	calc_qty();
	});
$("#1st_copy_nos_txt").on("keyup", function(e) {
	e.stopPropagation();
	if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
	}
	else
	{
	calc_qty();
	}
});
$("#addl_copy_nos_txt").on("keyup", function(e) {
	e.stopPropagation();
	if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
	}
	else
	{
	calc_qty();
	}
});
$("#no_of_ups_txt").on("keyup", function(e) {
	e.stopPropagation();
	calc_qty();
	});
$("#1st_copy_nos_txt").on("change", function(e) {
	e.stopPropagation();
	calc_qty();
});
$("#addl_copy_nos_txt").on("change", function(e) {
	e.stopPropagation();
	calc_qty();
	});
$("#no_of_ups_txt").on("change", function(e) {
	e.stopPropagation();
	calc_qty();
	});
$("#machine_code_txt").on("change", function(e) {
	e.stopPropagation();
	calc_qty();
	});
</script>