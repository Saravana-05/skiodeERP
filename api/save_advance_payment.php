<?php
// SARAVANA - START (Advance Payment Feature - new API file)
session_start();
include_once "../connect_db.php";
header('Content-Type: application/json');

// Allow all logged-in users
if (!isset($_SESSION['logged_in'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in.']);
    exit;
}

// Create table if it doesn't exist (includes new columns qr_payment_status, ext_transaction_id, qr_string)
$createSql = "CREATE TABLE IF NOT EXISTS `advance_payment_master` (
  `advance_id`          INT(11)        NOT NULL AUTO_INCREMENT,
  `advance_date`        DATE           NOT NULL,
  `customer_name`       VARCHAR(100)   NOT NULL,
  `customer_mobile`     VARCHAR(20)    NOT NULL,
  `pay_mode`            ENUM('Cash','GPay') NOT NULL DEFAULT 'Cash',
  `amount`              DECIMAL(10,2)  NOT NULL DEFAULT 0.00,
  `remarks`             TEXT,
  `created_by`          VARCHAR(50)    NOT NULL,
  `is_linked`           TINYINT(1)     NOT NULL DEFAULT 0,
  `linked_jobcard_no`   INT(11)        DEFAULT NULL,
  `created_dt_tm`       DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `qr_payment_status`   ENUM('NA','PENDING','PAID') NOT NULL DEFAULT 'NA',
  `ext_transaction_id`  VARCHAR(100)   DEFAULT NULL,
  `qr_string`           LONGTEXT,
  PRIMARY KEY (`advance_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
mysqli_query($connection, $createSql);

// ── Migrate existing tables (add new columns if not present) ──────────────────
$migrate = [
    'qr_payment_status'  => "ALTER TABLE advance_payment_master ADD COLUMN `qr_payment_status` ENUM('NA','PENDING','PAID') NOT NULL DEFAULT 'NA'",
    'ext_transaction_id' => "ALTER TABLE advance_payment_master ADD COLUMN `ext_transaction_id` VARCHAR(100) DEFAULT NULL",
    'qr_string'          => "ALTER TABLE advance_payment_master ADD COLUMN `qr_string` LONGTEXT",
];
foreach ($migrate as $col => $alterSql) {
    $chk = mysqli_query($connection, "SHOW COLUMNS FROM advance_payment_master LIKE '$col'");
    if ($chk && mysqli_num_rows($chk) === 0) {
        mysqli_query($connection, $alterSql);
    }
}

$customer_name   = mysqli_real_escape_string($connection, trim($_POST['customer_name']));
$customer_mobile = mysqli_real_escape_string($connection, trim($_POST['customer_mobile']));
$pay_mode        = $_POST['pay_mode'] === 'GPay' ? 'GPay' : 'Cash';
$amount          = (float)$_POST['amount'];
$remarks         = mysqli_real_escape_string($connection, trim($_POST['remarks'] ?? ''));
$created_by      = $_SESSION['user_name'];
$advance_date    = date('Y-m-d');

// New QR-related fields
$valid_statuses    = ['NA', 'PENDING', 'PAID'];
$qr_payment_status = in_array($_POST['qr_payment_status'] ?? '', $valid_statuses)
                   ? $_POST['qr_payment_status'] : 'NA';
$ext_transaction_id = mysqli_real_escape_string($connection, trim($_POST['ext_transaction_id'] ?? ''));
$qr_string          = mysqli_real_escape_string($connection, trim($_POST['qr_string'] ?? ''));

if ($customer_name === '' || $customer_mobile === '' || $amount <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Customer name, mobile and amount are required.']);
    exit;
}

$ext_val = $ext_transaction_id ? "'$ext_transaction_id'" : 'NULL';
$qr_val  = $qr_string          ? "'$qr_string'"          : 'NULL';

$sql = "INSERT INTO advance_payment_master
        (advance_date, customer_name, customer_mobile, pay_mode, amount, remarks, created_by,
         qr_payment_status, ext_transaction_id, qr_string)
        VALUES
        ('$advance_date','$customer_name','$customer_mobile','$pay_mode',$amount,'$remarks','$created_by',
         '$qr_payment_status',$ext_val,$qr_val)";

if (mysqli_query($connection, $sql)) {
    $new_id = mysqli_insert_id($connection);

    // Post journal entry only when payment is confirmed (not PENDING GPay)
    if ($qr_payment_status !== 'PENDING') {
        if ($pay_mode === 'Cash') {
            post_journal_single_entry($advance_date, 'CASH', 'Advance Cash (no JC) - ' . $customer_name . ' / ' . $customer_mobile, $amount, 'ADV_' . $new_id);
        } else {
            post_journal_single_entry($advance_date, 'SB', 'Advance GPay (no JC) - ' . $customer_name . ' / ' . $customer_mobile, $amount, 'ADV_' . $new_id);
        }
    }

    // SARAVANA - START (Save customer to customer_master as General type if not already present)
    $chk = mysqli_query($connection, "SELECT customer_code FROM customer_master WHERE mobile_no='$customer_mobile' LIMIT 1");
    if (!$chk || mysqli_num_rows($chk) == 0) {
        $gen_code = 'GEN_' . $customer_mobile;
        $chk2 = mysqli_query($connection, "SELECT customer_code FROM customer_master WHERE customer_code='$gen_code' LIMIT 1");
        if (!$chk2 || mysqli_num_rows($chk2) == 0) {
            mysqli_query($connection,
                "INSERT INTO customer_master
                    (customer_code, customer_name, customer_type, customer_addr1, customer_addr2,
                     customer_city, customer_state_code, customer_state, gst_no, mobile_no, customer_email_id)
                 VALUES
                    ('$gen_code', '$customer_name', 'General', '', '', '', '', '', '', '$customer_mobile', '')"
            );
        }
    }
    // SARAVANA - END

    echo json_encode(['status' => 'success', 'advance_id' => $new_id]);
} else {
    echo json_encode(['status' => 'error', 'message' => mysqli_error($connection)]);
}
// SARAVANA - END (Advance Payment Feature - new API file)
?>
