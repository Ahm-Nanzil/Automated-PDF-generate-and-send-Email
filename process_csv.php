<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$csvFile = '/home/iponeuro/public_html/backuo/invoice.csv'; 

if (!file_exists($csvFile)) {
    die("CSV file not found.");
}

$file = fopen($csvFile, 'r');

if (!$file) {
    die("Unable to open the CSV file.");
}

$headers = fgetcsv($file);

require_once 'generate_pdf.php'; 

while (($row = fgetcsv($file)) !== false) {
    $email = trim($row[0]); 
    $customerName = trim($row[1]);
    $companyName = trim($row[2]);
    $address = trim($row[3]);
    $invoiceNumber = trim($row[4]);
    $customerNumber = trim($row[5]);
    $vatId = trim($row[6]);
    $invoiceDate = trim($row[7]);
    $dueDate = trim($row[8]);
    $itemDescription = trim($row[9]);
    $quantity = trim($row[10]);
    $price = trim($row[11]);
    $totalAmount = trim($row[12]);
    $iban = trim($row[13]);
    $swift = trim($row[14]);
    $bankName = trim($row[15]);
    $paymentTerms = trim($row[16]);

    if (!empty($invoiceNumber)) {
    generateInvoicePdf($invoiceNumber);

        $invoice_to_send = $invoiceNumber; 
        include(__DIR__ . "/../backuo/sendmail.php");


    }
}

fclose($file);

echo "All invoices processed successfully.";

?>
