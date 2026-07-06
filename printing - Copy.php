 <style>
 .iconDivCls:hover
{
	 box-shadow: 0 8px 17px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19);
}
 </style>
	<div class="row "  style="margin-top:-4px !important">
		<div class = "col" >
			<center><p class="p-0 m-0">OWN STOCK</p></center>
			<div class="row m-1">
				<div class="form-check col-3">
				  <input class="form-check-input" type="radio" name="media_stock_type_txt" value="PRINTING-PAPERS" id="own_stock_type_papers_txt">
				  <label class="form-check-label" for="own_stock_type_papers_txt">
					Papers
				  </label>
				</div>
				<div class="form-check col-3">
				  <input class="form-check-input" type="radio" name="media_stock_type_txt" value="PRINTING-BOARDS" id="own_stock_type_boards_txt">
				  <label class="form-check-label" for="own_stock_type_boards_txt">
					Boards
				  </label>
				</div>	
				<div class="form-check col-3">
				  <input class="form-check-input" type="radio" name="media_stock_type_txt" value="PRINTING-STICKERS" id="own_stock_type_stickers_txt">
				  <label class="form-check-label" for="own_stock_type_stickers_txt">
					Stickers
				  </label>
				</div>
				<div class="form-check col-3">
				  <input class="form-check-input" type="radio" name="media_stock_type_txt" value="PRINTING-COVERS" id="own_stock_type_covers_txt">
				  <label class="form-check-label" for="own_stock_type_covers_txt">
					Covers
				  </label>
				</div>			
			</div>
		</div>
		<div class = "col" style="background-color:#adb3cb;">
			CUSTOMER MEDIA
			<div class="row m-1">
				<div class="form-check col">
				  <input class="form-check-input" type="radio" name="media_stock_type_txt" value="PRINTING-CMEDIA" id="customer_stock_type_media_txt">
				  <label class="form-check-label" for="customer_stock_type_media_txt">
					Media
				</div>
				<div class="form-check col">
				  <input class="form-check-input" type="radio" name="media_stock_type_txt" value="PRINTING-CCOVERS" id="customer_stock_type_coverss_txt">
				  <label class="form-check-label" for="customer_stock_type_coverss_txt">
					Covers
				  </label>
				</div>	
			</div>	
		</div>
	<input type="hidden" name="sales_subtypeTxt" id="sales_subtypeTxt" value="" />
	<div id="stock_based_ui_div">
	</div>
 
<script>
$('.form-check-input').on('click', function(evt) {
				/*alert($(this).val());
				 $(".main_tab_btns").removeClass("btn-success");
				$(this).addClass("btn-success");*/
				
				$("#stock_based_ui_div").load("media_usage_ui.php?category1="+$(this).val(), function() {
				  $("#sales_subtypeTxt").val($('input[name="media_stock_type_txt"]:checked').val());
				  $('#stock_based_ui_div').show(); 
				}); 
			});
</script>