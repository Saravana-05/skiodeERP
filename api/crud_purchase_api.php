<?php
include '../connect_db.php';

if(isset($_POST['action']))
{
	$action = $_POST['action'];
}
if ($action == 'read') {

    $sql = "SELECT * FROM purchase_master WHERE 1";

    // Date filtering
    $from_date = isset($_POST['from_date']) && !empty($_POST['from_date']) ? $_POST['from_date'] : '';
    $to_date   = isset($_POST['to_date']) && !empty($_POST['to_date']) ? $_POST['to_date'] : '';

    if ($from_date) {
        $safe_from = mysqli_real_escape_string($connection, date('Y-m-d', strtotime($from_date)));
        $sql .= " AND purchase_date >= '$safe_from'";
    }
    if ($to_date) {
        $safe_to = mysqli_real_escape_string($connection, date('Y-m-d', strtotime($to_date)));
        $sql .= " AND purchase_date <= '$safe_to'";
    }

    $sql .= " ORDER BY purchase_date DESC, purchase_voucher_no DESC";

    if($query=mysqli_query($connection,$sql))
	{
		$result='<table class="table table-hover" id="resultPurchaseTable" style="width:100%;">';
		$result.='<thead>';
		$result.='<tr>';
		$result.='<th>PV No</th>';
		$result.='<th>Date</th>';
		$result.='<th>PO No</th>';
		$result.='<th>Vendor</th>';
		$result.='<th>Invoice No</th>';
		$result.='<th>Invoice Date</th>';
		$result.='<th>Items</th>';
		$result.='<th>Tax</th>';
		$result.='<th>Total</th>';
		$result.='<th>Actions</th>';
		$result.='</tr>';
		$result.='</thead>';
		$result.='<tbody>';
		if(mysqli_num_rows($query)>0)
		{
			 while($row=mysqli_fetch_array($query)) {

				// Get vendor name
				$vendor_name = $row["vendor_code"];
				$vsql = "SELECT customer_name FROM customer_master WHERE customer_code='" . mysqli_real_escape_string($connection, $row["vendor_code"]) . "' LIMIT 1";
				$vq = mysqli_query($connection, $vsql);
				if ($vq && $vr = mysqli_fetch_assoc($vq)) {
					$vendor_name = $vr['customer_name'];
				}

				// Get item summary
				$sql1 = "SELECT pd.*, mm.material_name FROM purchase_details pd LEFT JOIN material_master mm ON mm.material_code = pd.material_code WHERE pd.purchase_voucher_no=" . $row["purchase_voucher_no"];
				$items_html = '';
				$item_count = 0;
				if($query1=mysqli_query($connection,$sql1))
				{
					while($row1=mysqli_fetch_array($query1)) {
						$item_count++;
						$mat_name = $row1["material_name"] ?: $row1["material_code"];
						$items_html .= '<div style="white-space:nowrap;font-size:0.78rem;line-height:1.6;">'
							. '<span style="color:#475569;">' . htmlspecialchars($mat_name) . '</span>'
							. ' <span style="color:#94a3b8;">&times;</span> '
							. '<span style="font-weight:600;">' . $row1["qty"] . '</span>'
							. ' <span style="color:#94a3b8;">@ ₹' . ind_money($row1["purchase_price"], 2) . '</span>'
							. '</div>';
					}
				}

				// Tax summary
				$tax_parts = [];
				if($row["igst_value"]>0) $tax_parts[] = 'IGST: ' . $row["igst_value"] . '%';
				if($row["cgst_value"]>0) $tax_parts[] = 'CGST: ' . $row["cgst_value"] . '%';
				if($row["sgst_value"]>0) $tax_parts[] = 'SGST: ' . $row["sgst_value"] . '%';
				$tax_html = count($tax_parts) > 0 ? implode('<br>', $tax_parts) : '<span style="color:#cbd5e1;">—</span>';

				$result.='<tr data-id="' . $row["purchase_voucher_no"] . '">';
				$result.='<td><span style="font-weight:700;color:#1e40af;">' . $row["purchase_voucher_no"] . '</span></td>';
				$result.='<td style="white-space:nowrap;">' . date("d-m-Y", strtotime($row["purchase_date"])) . '</td>';
				$result.='<td>' . ($row["our_po_no"] ?: '<span style="color:#cbd5e1;">—</span>') . '</td>';
				$result.='<td><span style="font-weight:600;">' . htmlspecialchars($vendor_name) . '</span></td>';
				$result.='<td>' . $row["vendor_invoice_no"] . '</td>';
				$result.='<td style="white-space:nowrap;">' . date("d-m-Y", strtotime($row["vendor_invoice_date"])) . '</td>';
				$result.='<td>' . $items_html . '<span style="font-size:0.7rem;color:#94a3b8;">' . $item_count . ' item(s)</span></td>';
				$result.='<td style="font-size:0.78rem;">' . $tax_html . '</td>';
				$result.='<td><span style="font-weight:700;font-size:0.92rem;">₹' . ind_money($row["total_after_discount_value"], 2) . '</span></td>';
				$result.='<td style="white-space:nowrap;"><button class="btn btn-sm btn-primary me-1" onclick="fnPurchaseView(this)" title="View"><span class="material-icons-round" style="font-size:14px;vertical-align:-2px;">visibility</span></button><button class="btn btn-sm btn-outline-danger deleteBtn" onclick="fnPurchaseDelete(' . $row["purchase_voucher_no"] . ')" title="Delete"><span class="material-icons-round" style="font-size:14px;vertical-align:-2px;">delete</span></button></td>';
				$result.='</tr>';
			  }

		}
		$result.='</tbody>';
		$result.='</table>';
	}
    echo $result;
}
elseif ($action == 'add') {
    $dt=date("Y-m-d",strtotime($_POST["purchase_date"]));
$sql = "INSERT INTO `purchase_master`(`purchase_voucher_no`, `vendor_code`, `vendor_invoice_no`, `purchase_date`, `vendor_invoice_date`, `our_po_no`, `discount_value`, `total_after_discount_value`, `igst_value`, `cgst_value`, `sgst_value`, `net_total_value` ) VALUES ('".$_POST["purchase_voucher_no"]."','".$_POST["vendor_code"]."','".$_POST["vendor_invoice_no"]."','".$dt."','".date("Y-m-d",strtotime($_POST["vendor_invoice_date"]))."','".$_POST["our_po_no"]."','0','".$_POST["total_after_discount_value"]."','".$_POST["igst_value"]."','".$_POST["cgst_value"]."','".$_POST["sgst_value"]."','".$_POST["net_total_value"]."')";
	if(mysqli_query($connection,$sql))
	{
		$purchase_details_array= json_decode($_POST['allTableData'], true);
		for($i=0;$i<count($purchase_details_array);$i++)
		{
			$sql = "INSERT INTO `purchase_details`(`purchase_voucher_no`, `material_code`, `purchase_price`, `qty`, `gst_percentage`, `item_discount`) VALUES ('".$_POST["purchase_voucher_no"]."','".$purchase_details_array[$i]['material_code']."','".$purchase_details_array[$i]['purchase_price']."','".$purchase_details_array[$i]['qty']."','".$purchase_details_array[$i]['gst']."','".$purchase_details_array[$i]['discount']."')";
			mysqli_query($connection,$sql);
		}
//Posting to Journal Starts Here

	$total_after_discount_value_txt=(float) $_POST["total_after_discount_value"];

		post_journal_double_entry($dt,$_POST["vendor_code"],"PURCHASE","Purchase Voucher No:".$_POST["purchase_voucher_no"],$total_after_discount_value_txt,"PU_".$_POST["purchase_voucher_no"]);


//Posting to Journal Ends Here
		echo "Success";
	}
	else
		echo "Failed";
}
elseif ($action == 'delete') {
	$purchase_voucher_no = $_POST['pId'];
	$sql="DELETE FROM  purchase_master where purchase_voucher_no='".$purchase_voucher_no ."'";
	mysqli_query($connection,$sql);
     $sql="DELETE FROM  purchase_details where purchase_voucher_no='".$purchase_voucher_no ."'";
	mysqli_query($connection,$sql);
     $sql="DELETE FROM  journal_details where link_key='PU_".$purchase_voucher_no ."'";
	mysqli_query($connection,$sql);
    echo "Success";
}
elseif ($action =='getNextPVNo')
{
	$sql="SELECT max(purchase_voucher_no) as max_voucher_no FROM purchase_master;";
	$purchase_voucher_no=1;
	if($query=mysqli_query($connection,$sql))
	{
		if(mysqli_num_rows($query)>0)
		{
			if($row=mysqli_fetch_array($query))
			{
				if(!is_null($row["max_voucher_no"]))
				{
					$purchase_voucher_no=$row["max_voucher_no"]+1;
					echo $purchase_voucher_no;
				}
			}
		}
	}
}
?>