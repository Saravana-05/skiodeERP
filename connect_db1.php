<?php
date_default_timezone_set("Asia/Kolkata");
$server = 'localhost';  
$user="root";
$pass="CPMagesh@2025";
$db = 'cp_db';

$running_on_localhost = strpos($_SERVER["HTTP_HOST"],'localhost')!==false ? true : false;
if($running_on_localhost)
{
	$user="root";
	$pass="";
	$db = 'cp_ssr_new_db';
} 

$avoid_sending_SMS_from_localhost=false;
// Connect to Database 
$connection = mysqli_connect($server, $user, $pass)  or die ("Could not connect to server ... \n" . mysqli_connect_error ()); 
//mysqli_query($connection,'SET character_set_results=utf8');
//mysqli_query($connection,'SET names=utf8');
//mysqli_query($connection,'SET character_set_client=utf8');
//mysqli_query($connection,'SET character_set_connection=utf8');
//mysqli_query($connection,'SET character_set_results=utf8');
//mysqli_query($connection,'SET collation_connection=utf8_general_ci');
mysqli_select_db($connection,$db)  or die ("Could not connect to database ... \n" . mysqli_connect_error ());
mysqli_query($connection,"SET sql_mode = ''");
//executed_sql("SET GLOBAL sql_mode = TRADITIONAL;");

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
    file_put_contents("sqls.txt",$dt_tm." ".$txt.PHP_EOL,FILE_APPEND | LOCK_EX);
}
function executed_sql($sql)
{
    global $connection;
    log_this($sql);
    mysqli_query($connection,$sql);
    if(mysqli_affected_rows($connection)>0)
        return true;
    else
    {
        log_this("Failed");
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
		log_this($sql);
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
		log_this($sql);
		mysqli_query($connection,$sql);	
}
function delete_journal_entries($pLink_key)
{
	global $connection;
	$sql = "delete from journal_details where link_key='".$pLink_key."';";
	log_this($sql);
	mysqli_query($connection,$sql);		
}
?>