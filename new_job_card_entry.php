		<div class="container-fluid">
			<div class="btn-group" role="group" aria-label="main_tabs">
				<button class="main_tab_btns btn btn-primary" page="printing">Printing</button>
				<button class="main_tab_btns btn btn-primary" page="services">Services</button>
				<button class="main_tab_btns btn btn-primary" page='materials'>Materials</button>
				<button class="main_tab_btns btn btn-primary" page='idcards'>ID Cards</button>
				<button class="main_tab_btns btn btn-primary" page='products'>Products</button>
				<input type="hidden" name="sales_typeTxt" id="sales_typeTxt" value="" />
			</div>
			<div class="container-fluid p-3" id="main_tab_content_div">
			
			</div>
		</div>
		<script>
			$('.main_tab_btns').on('click', function(evt) {
				
				$(".main_tab_btns").removeClass("btn-success");
				$(this).addClass("btn-success");
				$("#sales_typeTxt").val($('.main_tab_btns').text());
				$("#main_tab_content_div").load($(this).attr("page")+".php");
			});

		</script>		