 <?php
$category1="";
if(isset($_GET["category1"]))
{
	$category1=$_GET["category1"];
	
}
?>
	<div class="row"  style="margin-top:-4px !important">
		<center><b><?php echo $category1; ?></b></center>
		<div class = "col-7 p-0 bg_gdarkgreen" >
			<center><p class="p-0 m-0">SERVICE ONLY</p></center>
			<div class="row m-1 ">
				<div class="form-check col-4 pl-0	">
				  <input class="form-check-input media_stock_type_txt" type="radio" name="media_stock_type_txt" value="<?php echo $category1;?>-INHOUSE" id="own_stock_type_papers_txt">
				  <label class="form-check-label" for="own_stock_type_papers_txt">
					Cutting/Scoring
				  </label>
				</div>
				<div class="form-check col-4 pl-0">
				  <input class="form-check-input media_stock_type_txt" type="radio" name="media_stock_type_txt" value="<?php echo $category1;?>-OUTSIDE" id="own_stock_type_stickers_txt">
				  <label class="form-check-label" for="own_stock_type_stickers_txt">
					Outside Works
				  </label>
				</div>
				<div class="form-check col-4 pl-0">
				  <input class="form-check-input media_stock_type_txt" type="radio" name="media_stock_type_txt" value="<?php echo $category1;?>-ADDL" id="own_stock_type_boards_txt">
				  <label class="form-check-label" for="own_stock_type_boards_txt">
					Additional Works
				  </label>
				</div>	
				
				 	
			</div>
		</div>
		<div class = "col-5 p-0 bg_glightgreen">
			<center><p class="p-0 m-0">SERVICE + STOCK</p></center>
			<div class="row m-1">
				<div class="form-check col-5">
				  <input class="form-check-input media_stock_type_txt" type="radio" name="media_stock_type_txt" value="<?php echo $category1;?>-BINDING" id="customer_stock_type_media_txt">
				  <label class="form-check-label" for="customer_stock_type_media_txt">
					Binding
				</div>
				<div class="form-check col">
				  <input class="form-check-input media_stock_type_txt" type="radio" name="media_stock_type_txt" value="<?php echo $category1;?>-LAMINATIONS" id="customer_stock_type_coverss_txt">
				  <label class="form-check-label" for="customer_stock_type_coverss_txt">
					Lamination Pouches
				  </label>
				</div>	
			</div>	
		</div>
	<input type="hidden" name="sales_subtypeTxt" id="sales_subtypeTxt" value="" />
	
	<div id="stock_based_ui_div" class="p-0">
	</div>	
 
<script>
$(document).ready(function() {
	SetUpBasics();
/* 	$( "#own_stock_type_papers_txt" ).trigger('click');
	$( "#own_stock_type_papers_txt" ).focus(); */
});
$('.form-check-input').on('click', function(evt) {
				//alert($(this).val());
				 /*$(".main_tab_btns").removeClass("btn-success");
				$(this).addClass("btn-success");*/
				$("#stock_based_ui_div").load("media_usage_ui.php?category1="+$(this).val(), function() {
				  $("#sales_subtypeTxt").val($('input[name="media_stock_type_txt"]:checked').val());
				  $('#stock_based_ui_div').show(); 
				}); 
			});
</script>