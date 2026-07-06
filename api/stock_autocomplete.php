<?php
session_start();
include_once "../connect_db.php";
$q = isset($_GET['q']) ? $_GET['q'] : '';

if ($q !== '') {
	$category1_txt=$_GET["category1"];
	  $category2_txt="";
	  $pos = strpos($category1_txt,"-");
	  if($pos)
	  {
		  $category2_txt = substr($category1_txt,$pos+1);
		  $category1_txt = substr($category1_txt,0,$pos);
		  
	  }
	  //echo $category1_txt."<hr>".$category2_txt."<hr>";
	  
	  $sql="select * from product_master where category1='".$category1_txt."'".($category2_txt==""?"":" and category2='".$category2_txt."'")." and product_name LIKE '%$q%' LIMIT 20";
	  
    
    $result = $connection->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='stock_autocomplete_item' product_code='".$row['product_code']."'>" . htmlspecialchars($row['product_name']) . "</div>";
        }
    } else {
        echo "<div class='stock_autocomplete_item'>No match found</div>";
    }
}
?>