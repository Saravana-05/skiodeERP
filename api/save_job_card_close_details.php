<?php
/* ================================================================
   save_job_card_close_details.php
   Saves job card closing data including per-item discounts.
   Column names verified against jobcard_master schema.
   ================================================================ */
session_start();
include_once '../connect_db.php';

$result = "Failed";

if(isset($_POST['jobcard_no']) && $_POST['jobcard_no'] != "")
{
	$jobcard_no             = mysqli_real_escape_string($connection, $_POST['jobcard_no']);
	$cust_type              = mysqli_real_escape_string($connection, $_POST['cust_type']);
	$close_job_card_type    = mysqli_real_escape_string($connection, $_POST['close_job_card_type']);
	$is_bal_in_cash         = intval($_POST['is_bal_in_cash']);
	$is_bal_in_gpay         = intval($_POST['is_bal_in_gpay']);
	$balance_cash           = floatval($_POST['balance_cash']);
	$balance_gpay           = floatval($_POST['balance_gpay']);
	$discount               = floatval($_POST['discount']);
	$balance_received_total = floatval($_POST['balance_received_total']);
	$total_item_discount    = floatval($_POST['total_item_discount'] ?? 0);

	/* ── 1. Save per-item discounts into jobcard_details ── */
	if(isset($_POST['item_discount_details']) && $_POST['item_discount_details'] != "")
	{
		$item_discount_details = json_decode($_POST['item_discount_details'], true);

		if(is_array($item_discount_details))
		{
			foreach($item_discount_details as $item)
			{
				$detail_id     = intval($item['jobcard_details_id']);
				$item_discount = floatval($item['item_discount']);

				if($detail_id > 0)
				{
					$sql_item = "UPDATE jobcard_details
					             SET    item_discount = $item_discount
					             WHERE  jobcard_details_id = $detail_id
					               AND  jobcard_no = $jobcard_no";
					mysqli_query($connection, $sql_item);
				}
			}
		}
	}

	/* ── 2. Save machine spillage ── */
	if(isset($_POST['machine_spillage_details']) && $_POST['machine_spillage_details'] != "")
	{
		$machine_spillage_details = json_decode($_POST['machine_spillage_details'], true);

		if(is_array($machine_spillage_details))
		{
			foreach($machine_spillage_details as $machine)
			{
				$m_jobcard_no  = mysqli_real_escape_string($connection, $machine['jobcard_no']);
				$machine_code  = mysqli_real_escape_string($connection, $machine['machine_code']);
				$spillage_count= floatval($machine['spillage_count']);

				if($machine_code != "")
				{
					$sql_mac = "UPDATE job_card_wise_machine_usage
					            SET    spillage_count = $spillage_count
					            WHERE  jobcard_no   = '$m_jobcard_no'
					              AND  machine_code = '$machine_code'";
					mysqli_query($connection, $sql_mac);
				}
			}
		}
	}

	/* ── 3. Update jobcard_master with correct column names ──
	   Verified columns from SHOW COLUMNS:
	     job_card_closed        tinyint  ← marks card as closed
	     job_card_closed_on     datetime ← when closed
	     jobcard_closed_by      varchar  ← who closed
	     is_bal_in_gpay         tinyint
	     is_bal_in_cash         tinyint
	     balance_gpay           decimal
	     balance_cash           decimal
	     balance_received_total decimal
	     discount_amount        decimal  ← whole-card discount
	     item_discount_total    decimal  ← sum of per-item discounts
	   ──────────────────────────────────────────────────────── */
	$closed_by = isset($_SESSION['username'])
	             ? mysqli_real_escape_string($connection, $_SESSION['username'])
	             : 'system';
	$closed_on = date('Y-m-d H:i:s');

	$sql_master = "UPDATE jobcard_master
	               SET
	                 job_card_closed        = 1,
	                 job_card_closed_on     = '$closed_on',
	                 jobcard_closed_by      = '$closed_by',
	                 is_bal_in_cash         = $is_bal_in_cash,
	                 is_bal_in_gpay         = $is_bal_in_gpay,
	                 balance_cash           = $balance_cash,
	                 balance_gpay           = $balance_gpay,
	                 discount_amount        = $discount,
	                 balance_received_total = $balance_received_total,
	                 item_discount_total    = $total_item_discount
	               WHERE jobcard_no         = $jobcard_no";

	if(mysqli_query($connection, $sql_master))
	{
		$result = "Success";
	}
	else
	{
		$result = "Failed: " . mysqli_error($connection);
	}
}

echo $result;
?>
