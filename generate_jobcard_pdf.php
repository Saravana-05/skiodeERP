<?php
require_once('TCPDF-main/tcpdf.php'); // Adjust path to TCPDF

// --- DB Connection ---
$mysqli = new mysqli('localhost', 'root', '', 'cp_ssr_db');
if ($mysqli->connect_error) {
    die('DB connection failed: ' . $mysqli->connect_error);
}

// --- Get jobcard by GET param ---
$jc_no = $_GET['jobcard_no'] ?? '1';
if (empty($jc_no)) die('Jobcard number is required.');

$sql = "SELECT jobcard_no, jobcard_date, customer_id, customer_type,
               customer_name, approximate_amount, advance_gpay, advance_cash,
               discount_amount, advance_amount, job_card_quotation_no,
               balance_gpay, balance_cash
        FROM jobcard_master
        WHERE jobcard_no = ?";
$stmt = $mysqli->prepare($sql);
if (!$stmt) die('SQL error: ' . $mysqli->error);
$stmt->bind_param('s', $jc_no);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
if (!$data) die('No record found.');


// --- Instantiate TCPDF ---
$pdf = new TCPDF('P', 'mm', 'A5', true, 'UTF-8', false);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Margins
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(true, 5);
$pdf->AddPage();

$page_width = $pdf->getPageWidth();
$page_height = $pdf->getPageHeight();
$margin = 5; // distance from edge

$pdf->SetDrawColor(0, 0, 0); // black
$pdf->Rect($margin, $margin, $page_width - 2 * $margin, $page_height - 2 * $margin);

// --- Top Header ---
if (file_exists('logo.png')) {
    $pdf->Image('logo.png', 15, 10, 45);
}
$pdf->SetFont('helvetica', '', 10);
$pdf->SetXY(15, 30);
$pdf->MultiCell(80, 0, "No.4,100 feet Road,Vadapalani,\nChennai-600026", 0);

$pdf->RoundedRect(8, 6, 133,38,4,'1111', 'D');

$pdf->SetFont('helvetica', '', 11);
$pdf->SetXY(90, 5);
$pdf->Image('canva-24-hours-phone-support-icon-MAES_7SJCFY.jpg', 85, 7, 5, 5); 
$pdf->Cell(-10, 8, "+91 90030 88363", 0, 1);
$pdf->SetXY(93, 10.5);
$pdf->Cell(-10, 8, "+91 90030 88363", 0, 1);
$pdf->SetXY(93, 16);
$pdf->Cell(-10, 8, "+91 90030 88363", 0, 1);
$pdf->SetXY(90, 21.5);
$pdf->Image('check-icon.jpg', 85, 23, 5, 5); 
$pdf->Cell(-10, 8, " citizenprints@gmail.com", 0, 1);
$pdf->SetXY(90, 27);
$pdf->Cell(-10, 8, "cityzenprints@gmail.com", 0, 1);
$pdf->SetXY(90, 32.5);
$pdf->Cell(-10, 8, "citizenprintpro@gmail.com", 0, 1);
$pdf->Image('360_F_134151149_4qh7m3ir2TR4wZOCeysrpql2xHSQhc5b-removebg-preview.jpg', 85, 38, 5.5, 5.5); 
$pdf->SetXY(90, 37);
$pdf->Cell(-10, 8, "www.citizenprints.com", 0, 1);

// --- Horizontal Line & Title Info ---
$pdf->SetY(40);
$pdf->Line(75, 6, 75,44);
$pdf->Line(77, 6, 77,44);
$pdf->SetY(45);
$pdf->SetFont('helvetica','B',12);
$pdf->Cell(100, 6, "To:", 0, 0);
$pdf->Ln(5);
$pdf->Cell(100, 6, "Walk In", 0, 0);
$pdf->Cell(0, 6, "No: " , 0, 0);


        // ---- LEFT SIDE ----
        $pdf->SetX(-100);
        $pdf->Cell(0, 5, 'Date:', 0, 1, 'L', false, '', 0, false, 'T', 'M');

// --- Detail Table Placeholder ---
$pdf->Ln(2);
$pdf->SetFont('helvetica','B',11);
$pdf->Cell(15, 10, 'S.no', 1, 0, 'C');
$pdf->Cell(55, 10, 'Particulars', 1, 0, 'C');
$pdf->Cell(30, 10, 'Qty', 1, 0, 'C');
$pdf->Cell(30, 10, 'Amount', 1, 1, 'C');
$pdf->SetFont('helvetica','',10);

// For a real system you'd fetch and loop jobcard items.
// Here, placeholder row:
$pdf->Cell(15, 8, '1', 1, 0, 'C');
$pdf->Cell(55, 8, 'Sample work details...', 1, 0);
$pdf->Cell(30, 8, '1', 1, 0, 'C');
$pdf->Cell(30, 8, number_format($data['approximate_amount'],2), 1, 1, 'R');

// --- Calculations Section ---
$pdf->Ln(3);
$pdf->SetXY(70, $pdf->GetY());
$pdf->Cell(70, 6, 'Amount: ' . number_format($data['approximate_amount'],2), 0, 1, 'R');
$pdf->SetX(70);
$pdf->Cell(70, 6, 'Discount: ' . number_format($data['discount_amount'],2), 0, 1, 'R');

$advTotal = $data['advance_gpay'] + $data['advance_cash'];
$pdf->SetX(70);
$pdf->Cell(70, 6, 'Adv Paid (GPay+Cash): ' . number_format($advTotal,2), 0, 1, 'R');

$balance = $data['approximate_amount'] - $data['discount_amount'] - $advTotal;
$pdf->SetX(70);
$pdf->SetFont('helvetica','B',11);
$pdf->Cell(70, 6, 'Bal Paid (GPay+Cash): ' . number_format($balance,2), 0, 1, 'R');

// --- Footer ---
$pdf->SetY(-50);
$pdf->SetFont('helvetica','',9);
$pdf->Cell(60, 20, 'Username:Admin', 0, 0, 'L');
$pdf->Cell(70, 30, '', 0, 0, 'C');

// QR code in TCPDF:
$style = array(
    'border' => 0,
    'vpadding' => 'auto',
    'hpadding' => 'auto',
    'fgcolor' => [0,0,0],
    'bgcolor' => false
);

$pdf->write2DBarcode('https://www.youtube.com/watch?v=k49fxN6jcPg', 'QRCODE,H', 60, $pdf->GetY(), 30, 30, $style, 'C');

$pdf->SetXY(150, $pdf->GetY());
$pdf->Cell(0,20, 'For Citizen Prints', 0, 0, 'R');

// --- Output PDF ---
$pdf->Output('jobcard_' . $data['jobcard_no'] . '.pdf', 'I');

