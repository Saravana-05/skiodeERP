<?php
date_default_timezone_set("Asia/Kolkata");
$server = 'localhost';  
$user="root";
$pass="";
$db = 'cp_test_new_db';

$running_on_localhost = strpos($_SERVER["HTTP_HOST"],'localhost')!==false ? true : false;
if($running_on_localhost)
{
	$user="root";
	$pass="";
	$db = 'cp_test_new_db';
} 

$avoid_sending_SMS_from_localhost=false;
// Connect to Database 
$connection = @mysqli_connect($server, $user, $pass);
if (!$connection) {
    error_log("DB connect failed: " . mysqli_connect_error());
    http_response_code(503);
    die("Service temporarily unavailable. Please reload.");
}
if (!@mysqli_select_db($connection, $db)) {
    error_log("DB select failed: " . mysqli_error($connection));
    http_response_code(503);
    die("Service temporarily unavailable. Please reload.");
}
mysqli_query($connection,"SET time_zone = '+05:30'");
mysqli_query($connection,"SET sql_mode = ''");
//executed_sql("SET GLOBAL sql_mode = TRADITIONAL;");

function ind_money($num, $decimals = 2, $rupee = true) {
	$num = floatval($num);
	$neg = $num < 0;
	$num = abs($num);
	$decimal_part = number_format($num - floor($num), $decimals, '.', '');
	$decimal_part = substr($decimal_part, 1);
	$int_part = strval(intval(floor($num)));
	if (strlen($int_part) > 3) {
		$last3 = substr($int_part, -3);
		$rest = substr($int_part, 0, -3);
		$rest = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $rest);
		$formatted = $rest . ',' . $last3;
	} else {
		$formatted = $int_part;
	}
	$result = ($neg ? '-' : '') . ($rupee ? '₹' : '') . $formatted . $decimal_part;
	return $result;
}

function get_customer_balance($pAchead_txt)
{
	global $connection;
	$result=0.0;
	$sql="select sum(amount) as bal from journal_details where achead='".$pAchead_txt."';";

	if($qry=mysqli_query($connection,$sql))		
	{ 
		if($row=mysqli_fetch_array($qry))
		{
			$result=$row["bal"]*-1;	
		}
	}
	return $result;
}
function get_scalar_date($pSql,$pFieldName)
{
	global $connection;
	$result="";
	if($qry=mysqli_query($connection,$pSql))		
	{ 
		if($row=mysqli_fetch_array($qry))
		{
			$result=$row[$pFieldName];	
		}
	}
	return $result;
}
function fnWriteLog($pMessage)
{
	$dt_tm = date("Y-m-d H:i:s");
    $myfile = fopen("theLog1.txt", "a");
    $txt = $dt_tm;
    $txt = $pMessage. "\n";
    fwrite($myfile, $txt);
    fclose($myfile);   
}
 
function log_this($txt)
{
    $dt_tm = date("Y-m-d H:i:s");
    $log_file = __DIR__ . "/sqls.txt";
    if (is_writable($log_file) || (!file_exists($log_file) && is_writable(__DIR__))) {
        file_put_contents($log_file, $dt_tm." ".$txt.PHP_EOL, FILE_APPEND | LOCK_EX);
    }
}
function executed_sql($sql)
{
    global $connection;
    
    mysqli_query($connection,$sql);
    if(mysqli_affected_rows($connection)>0)
        return true;
    else
    {
        
        return false;
    }
}
function is_duplicate_data($tbl_name,$fld_name,$fld_value)
{
    global $connection;
    $sql = "select ".$fld_name." from ".$tbl_name." where ".$fld_name."='".$fld_value."';";  
    if($result=mysqli_query($connection,$sql))
	{
        if(mysqli_num_rows($result)>0)
        {
            return true;
        }
    }
    return false;
} 

//Accounts Rule   -(Minus) means Debit +(Plus) means Credit
//Credit the Giver, Debit the Receiver
//Sales invoice    :: SALES Credit, Customer Debit
//Paying In CASH   :: Customer Credit, CASH Debit
//Paying In BANK   :: Customer Credit, BANK Debit
//Purchase Invoice :: VENDOR Credit, PURCHASE Debit
//GST Sales        :: SALES+GST(s) Credit, Customer Debit
  
function post_journal_single_entry($pDt,$pAcHead,$pDesc,$pAmt,$pLink_key,$pContra="NULL")
{
		global $connection;
		
		
		$sql="INSERT INTO `journal_details`( `dt`, `achead`, `description`, `amount`, `link_key`,contra_achead) VALUES ";
		$val_sql="";
		$val_sql.="('".$pDt."','".$pAcHead."','".$pDesc."',".$pAmt.",'".$pLink_key."',".($pContra!="NULL"?"'".$pContra."'":$pContra).")";
		$sql.=$val_sql;
		
		mysqli_query($connection,$sql);	
}	
function post_journal_double_entry($pDt,$pCrAcHead,$pDrAcHead,$pDesc,$pAmt,$pLink_key)
{
		global $connection;
		delete_journal_entries($pLink_key);
		$sql="INSERT INTO `journal_details`( `dt`, `achead`, `description`, `amount`, `link_key`,contra_achead) VALUES ";
		$val_sql="";
		$val_sql.="('".$pDt."','".$pCrAcHead."','".$pDesc."',".$pAmt.",'".$pLink_key."','".$pDrAcHead."')";	
		$val_sql.=",";
		$val_sql.="('".$pDt."','".$pDrAcHead."','".$pDesc."',-".$pAmt.",'".$pLink_key."','".$pCrAcHead."')";
		
		$sql.=$val_sql;
		
		mysqli_query($connection,$sql);	
}
function fn_ensure_pending_qr_table($connection) {
    $sql = "CREATE TABLE IF NOT EXISTS `pending_qr_closures` (
        `id`                 INT AUTO_INCREMENT PRIMARY KEY,
        `jobcard_no`         INT NOT NULL,
        `ext_transaction_id` VARCHAR(100) DEFAULT NULL,
        `approximate_amount` DECIMAL(10,2) DEFAULT 0,
        `balance_gpay`       DECIMAL(10,2) DEFAULT 0,
        `balance_cash`       DECIMAL(10,2) DEFAULT 0,
        `balance_amount`     DECIMAL(10,2) DEFAULT 0,
        `discount_amount`    DECIMAL(10,2) DEFAULT 0,
        `is_bal_in_cash`     TINYINT DEFAULT 0,
        `is_bal_in_gpay`     TINYINT DEFAULT 1,
        `is_gst_extra`       TINYINT DEFAULT 0,
        `gst_tax_amount`     DECIMAL(10,2) DEFAULT 0,
        `created_by`         VARCHAR(100) DEFAULT NULL,
        `created_at`         DATETIME NOT NULL,
        `updated_at`         DATETIME DEFAULT NULL,
        `status`             VARCHAR(20) DEFAULT 'PENDING',
        `converted_sq_no`    INT DEFAULT NULL,
        `converted_at`       DATETIME DEFAULT NULL,
        `cancel_reason`      VARCHAR(500) DEFAULT NULL,
        UNIQUE KEY `uq_jc` (`jobcard_no`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    mysqli_query($connection, $sql);

    // Add ext_transaction_id column if table existed before this column was added.
    // This column is used to link a parked JC closure to the specific QR transaction
    // and is read by get_jobcard_list.php — missing it breaks the whole JC list query.
    $eti_chk = mysqli_query($connection,
        "SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
          WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME   = 'pending_qr_closures'
            AND COLUMN_NAME  = 'ext_transaction_id' LIMIT 1");
    if ($eti_chk && mysqli_num_rows($eti_chk) === 0) {
        mysqli_query($connection,
            "ALTER TABLE `pending_qr_closures`
             ADD COLUMN `ext_transaction_id` VARCHAR(100) DEFAULT NULL AFTER `jobcard_no`");
    }

    // Add cancel_reason column if table existed before this column was added
    $cr_chk = mysqli_query($connection,
        "SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
          WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME   = 'pending_qr_closures'
            AND COLUMN_NAME  = 'cancel_reason' LIMIT 1");
    if ($cr_chk && mysqli_num_rows($cr_chk) === 0) {
        mysqli_query($connection,
            "ALTER TABLE `pending_qr_closures`
             ADD COLUMN `cancel_reason` VARCHAR(500) DEFAULT NULL");
    }

    // Add jobcard_nos column to store comma-separated JC numbers for multi-JC close.
    // jobcard_no (INT) stays as the primary/unique key using the first JC number;
    // jobcard_nos (VARCHAR) holds the full CSV string used by fn_convert_jc_to_sq.
    $jcnos_chk = mysqli_query($connection,
        "SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
          WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME   = 'pending_qr_closures'
            AND COLUMN_NAME  = 'jobcard_nos' LIMIT 1");
    if ($jcnos_chk && mysqli_num_rows($jcnos_chk) === 0) {
        mysqli_query($connection,
            "ALTER TABLE `pending_qr_closures`
             ADD COLUMN `jobcard_nos` VARCHAR(200) DEFAULT NULL AFTER `jobcard_no`");
    }
}

function fn_convert_jc_to_sq($connection, $params, $user_name) {
    $jcnos          = $params['jcnos'];
    $approx_amount  = floatval($params['approximate_amount']);
    $balance_cash   = floatval($params['balance_cash']);
    $balance_gpay   = floatval($params['balance_gpay']);
    $balance_amount = floatval($params['balance_amount']);
    $discount       = floatval($params['discount']);
    $is_bal_in_cash = intval($params['is_bal_in_cash']);
    $is_bal_in_gpay = intval($params['is_bal_in_gpay']);
    $is_gst_extra   = intval($params['is_gst_extra']);
    $gst_tax_amount = floatval($params['gst_tax_amount']);
    $skip_journal   = intval($params['skip_journal'] ?? 0);
    $dt             = date('Y-m-d H:i:s');

    // Always use the original JC creator — never the admin/whoever triggered the conversion.
    // This single lookup covers ALL paths: direct close, QR callback, force-mark-paid, etc.
    $safe_jcnos_lu = implode(',', array_filter(array_map('intval', explode(',', $jcnos))));
    if (!empty($safe_jcnos_lu)) {
        $orig_qry = mysqli_query($connection,
            "SELECT created_by FROM jobcard_master WHERE jobcard_no IN ($safe_jcnos_lu) AND created_by != '' LIMIT 1");
        if ($orig_qry && $orig_row = mysqli_fetch_assoc($orig_qry)) {
            if (!empty($orig_row['created_by'])) $user_name = $orig_row['created_by'];
        }
    }

    $safe_user      = mysqli_real_escape_string($connection, $user_name);

    $check = mysqli_query($connection,
        "SELECT jobcard_no FROM jobcard_master WHERE job_card_closed=1 AND jobcard_no IN ($jcnos)");
    if ($check && mysqli_num_rows($check) > 0) return 'Job Card already Closed';

    $max_sqno = 1;
    $r = $connection->query("SELECT MAX(quotation_no) AS m FROM sales_quotation_master");
    if ($r && $row = $r->fetch_assoc()) $max_sqno = intval($row['m']) + 1;

    $sql  = "INSERT INTO `sales_quotation_master`"
          . "(`quotation_no`,`jobcard_nos`,`quotation_dt_tm`,`discount`,`is_bal_in_gpay`,"
          . "`balance_gpay`,`is_bal_in_cash`,`balance_cash`,`balance_received_total`,"
          . "`approximate_amount`,`created_by`,`is_gst_extra`,`gst_tax_amount`) VALUES ("
          . "$max_sqno,'".$jcnos."','".$dt."',$discount,$is_bal_in_gpay,"
          . "$balance_gpay,$is_bal_in_cash,$balance_cash,$balance_amount,"
          . "$approx_amount,'$safe_user',$is_gst_extra,$gst_tax_amount)";
    log_this($sql);

    $result = 'Failed';
    if ($connection->query($sql) && $connection->affected_rows > 0) $result = $max_sqno;

    if ($result === 'Failed') return $result;

    $close_sql = "UPDATE jobcard_master SET job_card_closed=1, job_closed_dt_tm='$dt',"
               . " job_card_quotation_no='$result', jobcard_closed_by='$safe_user'"
               . " WHERE jobcard_no IN ($jcnos)";
    log_this($close_sql);
    $connection->query($close_sql);

    $balance_total = $balance_cash + $balance_gpay;
    if ($balance_total > 0 && !$skip_journal) {
        if ($balance_cash > 0)
            post_journal_single_entry($dt,'CASH',"Balance Cash for SQ:$max_sqno JC: $jcnos",$balance_cash,"SQ_$max_sqno");
        if ($balance_gpay > 0) {
            $achead = ($balance_gpay < 0) ? 'CA' : 'SB';
            post_journal_single_entry($dt,$achead,"Balance GPAY for SQ:$max_sqno JC: $jcnos",$balance_gpay,"SQ_$max_sqno");
        }
    } else {
        $cq = mysqli_query($connection,
            "SELECT customer_code FROM jobcard_master WHERE jobcard_no IN ($jcnos) LIMIT 1");
        $customer_code = '';
        if ($cq && $cr = mysqli_fetch_array($cq)) $customer_code = $cr['customer_code'];
        if ($customer_code !== '') {
            $prev_balance = get_customer_balance($customer_code);
            if ($prev_balance != 0)
                $connection->query("UPDATE sales_quotation_master SET prev_balance=$prev_balance WHERE quotation_no=$max_sqno");
            $net = $approx_amount - $discount;
            if ($net > 0)
                post_journal_double_entry($dt,'SALES',$customer_code,"Quote :$max_sqno",$net,"SQ_$max_sqno");
        }
    }
    log_this("fn_convert_jc_to_sq result: $result");
    return $result;
}

function delete_journal_entries($pLink_key)
{
	global $connection;
	$sql = "delete from journal_details where link_key='".$pLink_key."';";
	
	mysqli_query($connection,$sql);		
}
function calc_gst_jc($pJCNOS,$pDISCOUNT)
{
	global $connection;
	$jc_no=$pJCNOS;
	$discount=$pDISCOUNT;
	$is_this_igst_invoice=1;
	$total_tax=0;
	$net_total=0;
	$sub_total=0;
	$inclusive_gst=0;
	
	//Get the first jobcard data or gst no or state
	$customer_code="";
	$sql = "select customer_code,customer_gst_no,customer_state_code,customer_state from jobcard_master where jobcard_no in (".$jc_no.");";
	if($query=mysqli_query($connection,$sql))
	{
		if(mysqli_num_rows($query)>0)
		{
			if($row=mysqli_fetch_array($query))
			{					  
//compare states and find out whether it belongs to igst or cgst,sgst category
				$customer_code = $row["customer_code"];
				$customer_state_code = $row["customer_state_code"];
				$customer_state = $row["customer_state"];
				$customer_gst_no = $row["customer_gst_no"];
				if($customer_state_code=="") $customer_state_code=33;
				if($customer_state_code!=33)// hard coded for TN should be improved later
				{
					$is_this_igst_invoice=1;
				}					 
			}
		}
	}	

	//get sum of value,qty of materials used in the jcs grouped by hsnno(or %)
	$sql="SELECT a.jobcard_no,a.product_code,sum(a.total_qty) as sum_total_qty,sum(a.value_amount) as sum_value_amount,b.product_name,b.hsn_code,b.gst_percentage FROM `jobcard_details` a inner join product_master b on a.product_code=b.product_code group by b.gst_percentage having a.jobcard_no in (".$jc_no.");";
	$sql="SELECT a.product_code,sum(a.total_qty) as sum_total_qty,sum(a.value_amount) as sum_value_amount,b.product_name,b.gst_percentage FROM ( select * from `jobcard_details` where jobcard_no in (".$jc_no.")) a inner join product_master b on a.product_code=b.product_code group by b.gst_percentage order by gst_percentage;";
	
	if($query=mysqli_query($connection,$sql))
	{
		$diff_gst_percentage_count=mysqli_num_rows($query);
		$per_gst_percentage_discount=0.00;
		
		if($discount>0)
		{
			$per_gst_percentage_discount=$discount/$diff_gst_percentage_count;
		}
		if($diff_gst_percentage_count>0)
		{
			while($row=mysqli_fetch_array($query))
			{	
				$hsn_codes="";
				$gst_percentage=$row["gst_percentage"];
				$hsn_sql="SELECT DISTINCT b.hsn_code,b.gst_percentage FROM ( select * from `jobcard_details` where jobcard_no in (".$jc_no.")) a inner join product_master b on a.product_code=b.product_code where gst_percentage=".$gst_percentage.";";
				
				if($hsn_query=mysqli_query($connection,$hsn_sql))
				{
					while($hsn_row=mysqli_fetch_array($hsn_query))
					{
						if($hsn_codes!="") $hsn_codes.=",";
						$hsn_codes .=$hsn_row["hsn_code"];						
					}
				}
				$before_gst_value=$row["sum_value_amount"];
				
				$sub_total+=$before_gst_value;
				
				$before_gst_value-=$per_gst_percentage_discount;
				
				$igst=0;
				$cgst=0;
				$sgst=0;				
				if($inclusive_gst==1)
				{
					$igst=round($before_gst_value-($before_gst_value/((1.0+($gst_percentage/100)))),2);
					$total_tax+=$igst;
					$before_gst_value-=$igst;
					if(!$is_this_igst_invoice)
					{
						$cgst=round($igst/2,2);
						$sgst=round($igst/2,2);						
						$igst=0;
					}					
				}
				else
				{
					$igst=round($before_gst_value*($gst_percentage/100),2);
					$total_tax+=$igst;
					if(!$is_this_igst_invoice)
					{
						$cgst=round($igst/2,2);
						$sgst=round($igst/2,2);		
						$total_tax=$cgst+$sgst;						
						$igst=0;
					}
				}					
				
				
				
				return $total_tax;
			}
		}
	}
	return 0;
}

?>