 <?php
$category1="";
if(isset($_GET["category1"]))
{
	$category1=$_GET["category1"];
	
}
?> 
	<div class="row"  style="margin-top:-4px !important">
		<center><b><?php echo $category1; ?></b></center>
		<div class = "col p-0  bg_gdarkgreen" >
			<center><p class="p-0 m-0"> </p></center>
			<div class="row m-1 ">
				<div class="form-check col-6 pl-0	">
				  <input class="form-check-input media_stock_type_txt" type="radio" name="media_stock_type_txt" value="<?php echo $category1;?>-LAMINATEDIDCARD" id="own_stock_type_papers_txt">
				  <label class="form-check-label" for="own_stock_type_papers_txt">
					Laminated ID Cards
				  </label>
				</div>
				<div class="form-check col-6 pl-0	">
				  <input class="form-check-input media_stock_type_txt" type="radio" name="media_stock_type_txt" value="<?php echo $category1;?>-FUSINGCARDS" id="own_stock_type_papers_txt">
				  <label class="form-check-label" for="own_stock_type_papers_txt">
					Fusing ID Cards
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
	/* $( "#own_stock_type_papers_txt" ).trigger('click');
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