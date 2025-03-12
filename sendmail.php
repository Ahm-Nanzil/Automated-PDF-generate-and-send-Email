<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$mail = new PHPMailer(true);
$recipientEmail = '';
$recipientName = '';
$companyName = '';

try {
    $mail->isSMTP();
    $mail->Host = 'mail.ipon-europe.com'; 
    $mail->SMTPAuth = true;
    $mail->Username = 'invoice@ipon-europe.com'; 
    $mail->Password = 'igNgC8ADW&9M';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; 
    $mail->Port = 465;

    $mail->setFrom('invoice@ipon-europe.com', 'IPon Europe');

    if (isset($invoice_to_send) && !empty($invoice_to_send)) {
        $invoiceNumber = $invoice_to_send;
    } else {
        echo "No invoice number provided!\n";
        exit;
    }

    if (isset($current_invoice_data) && !empty($current_invoice_data)) {
        $invoiceData = $current_invoice_data;
    } else {
        echo "No invoice data provided!\n";
        exit;
    }

    if (isset($current_company_data) && !empty($current_company_data)) {
        $companyData = $current_company_data;
    } else {
        echo "No company data provided!\n";
        exit;
    }



    $placeholders = [
        '{{company}}' => $invoiceData['Company Name'] ?? '',
        '{{street}}' => $invoiceData['Address'] ?? '',
        '{{zip}}' => $invoiceData['Zip'] ?? '',
        '{{city}}' => $invoiceData['City'] ?? '',
        '{{date}}' => $invoiceData['Invoice Date'] ?? '',
        '{{contact}}' => $invoiceData['Customer Name'] ?? '',
        '{{itemDescription}}' => $invoiceData['Item Description'] ?? '',
        '{{invoiceNumber}}' => $invoiceNumber,
        '{{totalAmount}}' => $invoiceData['Total Amount'] ?? '',
        '{{dueDate}}' => $invoiceData['Due Date'] ?? '',
        '{{paymentTerms}}' => $invoiceData['Payment Terms'] ?? '',
        '{{myCompany}}' => $companyData['Company Name'] ?? '',
        '{{myStreet}}' => $companyData['Street'] ?? '',
        '{{myZip}}' => $companyData['Zip'] ?? '',
        '{{myCity}}' => $companyData['City'] ?? '',
        '{{myCountry}}' => $companyData['Country'] ?? '',
        '{{myPhone}}' => $companyData['Phone'] ?? '',
        '{{myEmail}}' => $companyData['Email'] ?? '',
        '{{myWebsite}}' => $companyData['Website'] ?? '',
        '{{myVat}}' => $companyData['VAT ID'] ?? '',
        '{{myCoc}}' => $companyData['Chamber of Commerce'] ?? '',
    ];

    $emailTemplatePath = __DIR__ . '/emailbody.html';
    $emailBody = file_get_contents($emailTemplatePath);

    $recipientEmail = $invoiceData['Email'] ?? '';
    $recipientName = $invoiceData['Customer Name'] ?? '';
    $companyName = $invoiceData['Company Name'] ?? '';
    
    if (empty($recipientEmail)) {
        echo "Error: Recipient email address is missing for invoice $invoiceNumber.\n";
        return;
    }

    $mail->addAddress($recipientEmail, "$recipientName - $companyName");

    if (!$emailBody) {
        echo "Error: Could not read email template.\n";
        return;
    }

    $emailBody = str_replace(array_keys($placeholders), array_values($placeholders), $emailBody);

    $pdfPath = __DIR__ . "/templates/invoices/invoice_$invoiceNumber.pdf";
    if (file_exists($pdfPath)) {
        $mail->addAttachment($pdfPath, "Invoice_$invoiceNumber.pdf");
    } else {
        echo "Warning: PDF file not found at $pdfPath\n";
    }

    $mail->isHTML(true);
    $mail->Subject = 'Ihre Rechnung - ' . $invoiceNumber;
    $mail->Body = $emailBody;
    $mail->AltBody = strip_tags($emailBody);

    $mail->send();
    echo "Email has been sent successfully for invoice $invoiceNumber!\n";
} catch (Exception $e) {
    echo "Email could not be sent for invoice $invoiceNumber. Error: {$mail->ErrorInfo}\n";
}
?>