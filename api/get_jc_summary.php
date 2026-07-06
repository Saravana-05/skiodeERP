<?php
error_reporting(0);
session_start();
include_once '../connect_db.php';
header('Content-Type: application/json');

$month = isset($_GET['month']) ? intval($_GET['month']) : intval(date('m'));
$year  = isset($_GET['year'])  ? intval($_GET['year'])  : intval(date('Y'));

$month_start = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-01';
$month_end   = date('Y-m-t', strtotime($month_start));

$result = array('total' => 0, 'in_progress' => 0, 'completed' => 0, 'void' => 0);

// Total = all job cards in the month
$sql = "SELECT COUNT(*) as cnt FROM jobcard_master
        WHERE jobcard_date BETWEEN '$month_start' AND '$month_end'";
if ($qry = mysqli_query($connection, $sql))
    if ($row = mysqli_fetch_array($qry))
        $result['total'] = (int)$row['cnt'];

// In Progress = not closed AND not voided (open jobs still being worked on)
$sql = "SELECT COUNT(*) as cnt FROM jobcard_master
        WHERE jobcard_date BETWEEN '$month_start' AND '$month_end'
        AND job_card_closed = 0
        AND (void_job_card IS NULL OR void_job_card = 0)";
if ($qry = mysqli_query($connection, $sql))
    if ($row = mysqli_fetch_array($qry))
        $result['in_progress'] = (int)$row['cnt'];

// Completed = closed AND not voided
$sql = "SELECT COUNT(*) as cnt FROM jobcard_master
        WHERE jobcard_date BETWEEN '$month_start' AND '$month_end'
        AND job_card_closed = 1
        AND (void_job_card IS NULL OR void_job_card = 0)";
if ($qry = mysqli_query($connection, $sql))
    if ($row = mysqli_fetch_array($qry))
        $result['completed'] = (int)$row['cnt'];

// Void = all voided jobs regardless of closed status
$sql = "SELECT COUNT(*) as cnt FROM jobcard_master
        WHERE jobcard_date BETWEEN '$month_start' AND '$month_end'
        AND void_job_card = 1";
if ($qry = mysqli_query($connection, $sql))
    if ($row = mysqli_fetch_array($qry))
        $result['void'] = (int)$row['cnt'];

echo json_encode($result);
?>
