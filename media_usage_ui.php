<?php
include_once 'connect_db.php';
?>
<style>
.stockDiv
{
	/*background-color:#edc889;*/
	border: 1px solid #9d9191;
		border-radius:0px;
}
.stockDiv .col-form-label
{
	text-align: center;
}
.machineDiv
{
	border: 0px solid #ffd505;
	border-radius:0px;
}
.machineDiv label
{
	font-weight:bold; 
}

#paymentDetailsDiv
{
	background-color:#fdff9f;
	border: 1px solid #9d9191;
	border-radius:5px;
	margin: 0rem !important;
	padding: 0px !important;
}
.stockDiv label {
    font-weight: bold;
}
/*
#autocomplete_stock_list
{
	border: 1px solid #ccc;
	border-top: none;
	position: absolute;
	z-index: 1000;
	width: 63%;
	background: white;
	color:#000000; 
	overflow-y:scroll;
}

.stock_autocomplete_item{
	padding: 1px;
    cursor: pointer;
    //font-size: 0.8rem;
    border-bottom: 1px solid #f1eded;
}

.stock_autocomplete_item:hover {
	background-color: #000000;
	color: white;
}
*/
#search_product_txt {
	width: inherit; 
}

.autocomplete-items { 
  position:absolute;
  border: 1px solid #d4d4d4;
  border-bottom: none;
  border-top: none;
  z-index: 99;
  /*position the autocomplete items to be the same width as the container:*/
  top: 100%;
  left: 0;
  right: 0;
  height:200px;
  overflow-y:scroll;
  background-color:white;
}

.autocomplete-items div {
  padding: 1px;
  cursor: pointer;
  background-color: #fff; 
  border-bottom: 1px solid #d4d4d4; 
}

/*when hovering an item:*/
.autocomplete-items div:hover {
  background-color: #e9e9e9; 
}

/*when navigating through the items using the arrow keys:*/
.autocomplete-active {
  background-color: DodgerBlue !important; 
  color: #ffffff; 
}


</style>

 
<div class="stockDiv bg_babypink">
	<div class="row p-2 ">
		 <label for="search_product_txt" class="control-label col-2">Stock Name</label>
			<div class="col-md-10"> 
				<input type="hidden" id="get_category_txt" name="get_category_txt" value="<?php echo $_GET["category1"];?>" /> 
				<div class="autocomplete" style="width:100%; position:relative;">
				<input type="text" autocomplete="off" name="search_product_txt" class="form-control" id="search_product_txt" placeholder="Start typing..." />
			  </div>
				
				<input type="hidden" id="product_code_txt" value="" />
				<div id="autocomplete_stock_list"></div>
			 </div> 
	</div> 
	<div class="machineDiv bg_excelblue mt-2 p-2" id="product_based_qty_div" style="min-height:175px;">
		here product based input boxes are shown
	</div>
</div> 
 
<script>
var stock_details_arr =[];
var stock_details_code_arr = [];
$(document).ready(function () {
	SetUpBasics();
$('input, select, textarea, button').focus(function() {
            $(this).addClass('highlighted-focus');
			//$(this).effect("highlight", {color: "#ffffcc"}, 1000); // Highlight with yellow, duration 1 second
        });

         // Remove highlighting when an element loses focus
        $('input, select, textarea, button').blur(function() {
            $(this).removeClass('highlighted-focus');
        }); 	
	/*$('#search_product_txt').on('input', function () {
		const query = $(this).val();

		if (query.length > 0) {
			$.ajax({
				url: 'api/stock_autocomplete.php',
				method: 'GET',
				data: { q: query,category1:$('#get_category_txt').val() },
				success: function (data) {
					$('#autocomplete_stock_list').html(data);
					$('#autocomplete_stock_list').css('height','200px');
				}
			});
		} else {
			$('#autocomplete_stock_list').empty();
			$('#autocomplete_stock_list').css('height','');
		}
	});
	$(document).on('click', '.stock_autocomplete_item', function (e) {
		e.stopPropagation();
		
		$('#search_product_txt').val($(this).text());
		
		$('#product_code_txt').val($(this).attr("product_code"));
		product_code_txt_changed();
		//$("#product_code_txt").trigger("change"); 
		$('#autocomplete_stock_list').empty();
		$('#autocomplete_stock_list').css('height','');
		$(document).off('click');
		return false;
	});*/
	
	<?php
			  $category1_txt=$_GET["category1"];
			  $category2_txt="";
			  $pos = strpos($category1_txt,"-");
			  if($pos)
			  {
				  $category2_txt = substr($category1_txt,$pos+1);
				  $category1_txt = substr($category1_txt,0,$pos);
				  
			  }
			  //echo $category1_txt."<hr>".$category2_txt."<hr>";
			  
			  $sql="select * from product_master where category1='".$category1_txt."'".($category2_txt==""?"":" and category2='".$category2_txt."'").";";
			  $stock_code="";
			  $stock_name="";
			  if($row=mysqli_query($connection,$sql))		
				{ 
					while($data=mysqli_fetch_array($row))
					{
						$stock_code=$stock_code.'"'.$data["product_code"].'",';
						$stock_name=$stock_name.'"'.$data["product_name"].'",';
						//echo '<option value="'.$data["product_code"].'">'.$data["product_name"].'</option>';           
					}
				}
				$stock_code= trim($stock_code, ",");
				$stock_name= trim($stock_name, ",");
			  ?>
	  stock_details_arr = [<?php echo $stock_name; ?> ];
	  stock_details_code_arr = [<?php echo $stock_code; ?>];
/*initiate the autocomplete function on the "myInput" element, and pass along the countries array as possible autocomplete values:*/
	autocomplete(document.getElementById("search_product_txt"),document.getElementById("product_code_txt"), stock_details_arr,stock_details_code_arr);
});
function autocomplete(inp,code_inp, arr,pcode) {
  /*the autocomplete function takes two arguments,
  the text field element and an array of possible autocompleted values:*/
  var currentFocus;
  /*execute a function when someone writes in the text field:*/

  inp.addEventListener("input", function(e) {
      var a, b, i, val = this.value;
      /*close any already open lists of autocompleted values*/
	  
      closeAllLists();
	  
      //if (!val) { return false;}
	  
      currentFocus = -1;
	  val=val.trim();
      /*create a DIV element that will contain the items (values):*/
      a = document.createElement("DIV");
      a.setAttribute("id", this.id + "autocomplete-list");
      a.setAttribute("class", "autocomplete-items");
	  
      /*append the DIV element as a child of the autocomplete container:*/
      this.parentNode.appendChild(a);
      /*for each item in the array...*/
      for (i = 0; i < arr.length; i++) {
        /*check if the item starts with the same letters as the text field value:*/
        match_pos=arr[i].toUpperCase().indexOf(val.toUpperCase());
		
		if(val.length==0) match_pos=1000;
        if (match_pos>-1) {
          /*create a DIV element for each matching element:*/
          b = document.createElement("DIV");
          /*make the matching letters bold:*/
		  if(val.length>0)
		  {
          b.innerHTML = arr[i].substr(0,match_pos);
          b.innerHTML += "<strong>" + arr[i].substr(match_pos, val.length) + "</strong>";
          b.innerHTML += arr[i].substr(match_pos+val.length);
          /*insert a input field that will hold the current array item's value:*/
          b.innerHTML += "<input type='hidden' value='" + arr[i] + "'  code_value='" + pcode[i] + "' >";
		  }
		  else
		  {
			 b.innerHTML = arr[i];
			 b.innerHTML += "<input type='hidden' value='" + arr[i] + "'  code_value='" + pcode[i] + "' >";
		  }
          /*execute a function when someone clicks on the item value (DIV element):*/
		  
          b.addEventListener("click", function(e) {
              /*insert the value for the autocomplete text field:*/
              inp.value = this.getElementsByTagName("input")[0].value;
			  code_inp.value = this.getElementsByTagName("input")[0].getAttribute('code_value'); 	
			  
              /*close the list of autocompleted values,
              (or any other open lists of autocompleted values:*/
			  
              closeAllLists(b);
			  product_code_txt_changed();
          });
          a.appendChild(b);
        }
      }
  });
  inp.addEventListener("focus",function(e){
	  e.stopPropagation();
	  e.preventDefault();
	  inp.dispatchEvent(new Event('input', { bubbles: false }));

  }); 
  /*execute a function presses a key on the keyboard:*/
  inp.addEventListener("keydown", function(e) {
      var x = document.getElementById(this.id + "autocomplete-list");
      if (x) x = x.getElementsByTagName("div");
      if (e.keyCode == 40) {
        e.preventDefault();
		e.stopPropagation();		  
        /*If the arrow DOWN key is pressed,
        increase the currentFocus variable:*/
        currentFocus++;
        /*and and make the current item more visible:*/
        addActive(x);
      } else if (e.keyCode == 38) { //up
        e.preventDefault();
		e.stopPropagation();	  
        /*If the arrow UP key is pressed,
        decrease the currentFocus variable:*/
        currentFocus--;
        /*and and make the current item more visible:*/
        addActive(x);
      } else if (e.keyCode == 13) {	//enter
        /*If the ENTER key is pressed, prevent the form from being submitted,*/
        e.preventDefault();
		e.stopPropagation();
		
        if (currentFocus > -1) {
          /*and simulate a click on the "active" item:*/
          if (x) x[currentFocus].click();
        }
      }
  });
  function addActive(x) {
    /*a function to classify an item as "active":*/
    if (!x || x.length==0) return false;
    /*start by removing the "active" class on all items:*/
    removeActive(x);
    if (currentFocus >= x.length) currentFocus = 0;
    if (currentFocus < 0) currentFocus = (x.length - 1);
    /*add class "autocomplete-active":*/
	x[currentFocus].scrollIntoView();
    x[currentFocus].classList.add("autocomplete-active");
	
  }
  function removeActive(x) {
    /*a function to remove the "active" class from all autocomplete items:*/
    for (var i = 0; i < x.length; i++) {
      x[i].classList.remove("autocomplete-active");
    }
  }
  function closeAllLists(elmnt) {
    /*close all autocomplete lists in the document,
    except the one passed as an argument:*/

    var x = document.getElementsByClassName("autocomplete-items");
	
	if(x.length>0){					
		for (var i = 0; i < x.length; i++) {
		  if (elmnt != x[i] && elmnt != inp) { // && $(elmnt).attr('id') !="search_product_txtautocomplete-list"
				
			x[i].parentNode.removeChild(x[i]);
		  }

		}
	}
  }
  /*execute a function when someone clicks in the document:*/
  
  document.addEventListener("click", handleClick);

	function handleClick(e)
	{
		  e.stopPropagation();
		  //e.preventDefault();	  
		  
		  closeAllLists(e.target);	
		  document.removeEventListener('click', handleClick);
		
	}
}
function set_focus_nxt_elemt_tmr()
{
	
if($("#no_of_ups_txt").length) { 
$("#no_of_ups_txt").focus();
 }else
if($("#machine_code_txt").length) { 
$("#machine_code_txt").focus();
 }else
if($("#1st_copy_nos_txt").length) { 
$("#1st_copy_nos_txt").focus();
 }

}
function product_code_txt_changed() {	
	
	$.ajax({
		url: 'get_product_based_qty_ui.php', // Sample API endpoint
		method: 'GET',
		aysnc:false,
		data : {'product_code_txt':$('#product_code_txt').val()},
		success: function(result) {
			// Update the content on success
			$("#product_based_qty_div").html(result);
/* 						let focusableElements = $(':focusable').not(':disabled, [tabindex="-1"]');
						let currentFocusIndex = focusableElements.index(document.activeElement);
						
						let nextFocusIndex;
						nextFocusIndex = (currentFocusIndex + 1) % focusableElements.length;
						
						focusableElements.eq(nextFocusIndex).focus();
						 */

			setTimeout(set_focus_nxt_elemt_tmr(),100);
			
		},
		error: function(error) {
			// Handle errors
			console.error('Error:', error);
		}
});
}
//$("#product_code_txt").trigger("change");
</script>