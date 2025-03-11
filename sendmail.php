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
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'ahmnanzil33@gmail.com';
    $mail->Password   = 'hpitjdlzhhmnhurc';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom('invoice@ipon-europe.com', 'IPon Europe'); 

    function loadCSVData($filePath, $invoiceNumber = null) {
        $data = [];
        if (($handle = fopen($filePath, "r")) !== false) {
            $header = fgetcsv($handle);
            
            // Process each row
            while (($row = fgetcsv($handle)) !== false) {
                // Make sure the row has the same number of elements as the header
                // If not, pad the shorter array with empty strings
                if (count($header) > count($row)) {
                    $row = array_pad($row, count($header), "");
                } elseif (count($header) < count($row)) {
                    $header = array_pad($header, count($row), "Column" . (count($header) + 1));
                }
                
                if ($invoiceNumber === null || (isset($row[4]) && $row[4] == $invoiceNumber)) {
                    $data = array_combine($header, $row);
                    break;
                }
            }
            fclose($handle);
        }
        return $data;
    }
    

    if ($argc < 2) {
        die("Error: Invoice number is required");
    }
    $invoiceNumber = $argv[1]; 
    
    $invoiceData = loadCSVData("invoice.csv", $invoiceNumber);
    $companyData = loadCSVData("company.csv");

    if (!$invoiceData || !$companyData) {
        die("Error: Missing invoice or company data.");
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
    $mail->addAddress($recipientEmail, "$recipientName - $companyName"); 

    if (empty($recipientEmail)) {
        die("Error: Recipient email address is missing.");
    }

    if (!$emailBody) {
        die("Error: Could not read email template.");
    }

    $emailBody = str_replace(array_keys($placeholders), array_values($placeholders), $emailBody);

    $pdfPath = __DIR__ . "/templates/invoices/invoice_$invoiceNumber.pdf";
    if (file_exists($pdfPath)) {
        $mail->addAttachment($pdfPath, "Invoice_$invoiceNumber.pdf");
    }

    $mail->isHTML(true);
    $mail->Subject = 'Ihre Rechnung - ' . $invoiceNumber;
    $mail->Body    = $emailBody;
    $mail->AltBody = strip_tags($emailBody);

    $mail->send();
    echo 'Email has been sent successfully!';
} catch (Exception $e) {
    echo "Email could not be sent. Error: {$mail->ErrorInfo}";
}