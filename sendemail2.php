<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$invoiceNumber = isset($argv[1]) ? $argv[1] : null;

if (!$invoiceNumber) {
    die("Error: Invoice number is required in sendmail2.");
}

$csvFile = 'invoice.csv';
$recipientEmail = '';
$recipientName = '';
$companyName = '';

if (($handle = fopen($csvFile, "r")) !== FALSE) {
    fgetcsv($handle, 1000, ",");
    
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        if (isset($data[4]) && trim($data[4]) == $invoiceNumber) {
            $recipientEmail = $data[0]; 
            $recipientName = $data[1];  
            $companyName = $data[2];    
            break;
        }
    }
    fclose($handle);
}

if (empty($recipientEmail)) {
    die("Error: No recipient found for invoice number $invoiceNumber in the CSV file.");
}

$mail = new PHPMailer(true);

try {
    $mail->SMTPDebug = 2;                      
    $mail->isSMTP();                           
    $mail->Host       = 'smtp.gmail.com';      
    $mail->SMTPAuth   = true;                  
    $mail->Username   = 'ahmnanzil33@gmail.com'; 
    $mail->Password   = 'hpitjdlzhhmnhurc'; 
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
    $mail->Port       = 587;                   

    $mail->setFrom('invoice@ipon-europe.com', 'IPon Europe'); 
    $mail->addAddress($recipientEmail, "$recipientName - $companyName"); 

    $_GET['invoice'] = $invoiceNumber; 

    ob_start();
    include 'pdfcontent.php'; 
    $emailBody = ob_get_clean();

    $mail->isHTML(true);                       
    $mail->Subject = 'Invoice #' . $invoiceNumber; 
    $mail->Body    = $emailBody;
    $mail->AltBody = 'Please view this email in an HTML compatible email client to see your invoice.';

    $pdfPath = __DIR__ . "/templates/invoices/invoice_$invoiceNumber.pdf";
    if (file_exists($pdfPath)) {
        $mail->addAttachment($pdfPath, "Invoice_$invoiceNumber.pdf");
    } else {
        echo "Error: PDF not found at $pdfPath";
    }
    $mail->send();
    echo "Email has been sent successfully to $recipientEmail for invoice #$invoiceNumber!";
} catch (Exception $e) {
    echo "Email could not be sent. Error: {$mail->ErrorInfo}";
}