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
    $mail->SMTPAuth   = true;
    $mail->Username = 'invoice@ipon-europe.com'; 
    $mail->Password   = 'igNgC8ADW&9M';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; 
    $mail->Port = 465;

    $mail->setFrom('invoice@ipon-europe.com', 'IPon Europe'); 

function loadCSVData($filePath, $invoiceNumber = null) {
    
    
    if (!preg_match('~^/~', $filePath)) {
        $filePath = __DIR__ . '/' . $filePath;
    }
    
    if (!file_exists($filePath)) {
        echo "Error: File $filePath not found.\n";
        return [];
    }
    
    $data = [];
    
    if (($handle = fopen($filePath, "r")) !== false) {
        $headers = fgetcsv($handle);
        
        if ($headers === false) {
            echo "Error: Unable to read headers from $filePath\n";
            fclose($handle);
            return [];
        }
        
        while (($row = fgetcsv($handle)) !== false) {
            
            $rowData = array_fill_keys($headers, '');
            
            
            for ($i = 0; $i < count($row); $i++) {
                if (isset($headers[$i])) {
                    $rowData[$headers[$i]] = $row[$i];
                }
            }
            
            
            if ($invoiceNumber === null) {
                $data[] = $rowData;
            } else {
               
                if ((isset($rowData['Invoice Number']) && trim($rowData['Invoice Number']) === trim($invoiceNumber)) || 
                    (isset($row[4]) && trim($row[4]) === trim($invoiceNumber))) {
                    fclose($handle);
                    return $rowData;
                }
            }
        }
        fclose($handle);
    } else {
        echo "Error: Unable to open file $filePath\n";
    }
    
    if ($invoiceNumber !== null && empty($data)) {
        echo "Warning: Invoice number $invoiceNumber not found in $filePath\n";
        return [];
    }
    
    
    return $invoiceNumber === null ? $data : [];
}

    


if (isset($invoice_to_send) && !empty($invoice_to_send)) {
    $invoiceNumber = $invoice_to_send;
} else {
    echo "No invoice number provided!\n";
}
    $baseDir = "/home/iponeuro/public_html/backuo/";

$invoiceData = loadCSVData($baseDir . "invoice.csv", $invoiceNumber);
$companyData = loadCSVData($baseDir . "company.csv");


    if (!$invoiceData || !$companyData) {
                echo "Error: Missing invoice or company data.\n";

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