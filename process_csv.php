<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$baseDir = '/home/iponeuro/public_html/backuo/';
$csvFile = $baseDir . 'invoice.csv';

if (!file_exists($csvFile)) {
    die("CSV file not found.");
}

require_once 'generate_pdf.php';

function getInvoiceData($csvFilePath, $invoiceNumber = null) {
    $csvData = [];

    if (($handle = fopen($csvFilePath, "r")) !== FALSE) {
        $headers = fgetcsv($handle, 1000, ",");

        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $row = array_fill_keys($headers, '');

            for ($i = 0; $i < count($data); $i++) {
                if (isset($headers[$i])) {
                    $row[$headers[$i]] = $data[$i];
                }
            }

            if ($invoiceNumber !== null && isset($row['Invoice Number']) && trim($row['Invoice Number']) === trim($invoiceNumber)) {
                fclose($handle);
                return $row;
            }

            $csvData[] = $row;
        }
        fclose($handle);
    }

    return $invoiceNumber !== null ? [] : $csvData;
}

$companyData = getInvoiceData($baseDir . 'company.csv');
$companyData = !empty($companyData) ? $companyData[0] : [];

$invoices = getInvoiceData($csvFile);
$processedCount = 0;

foreach ($invoices as $invoiceData) {
    $invoiceNumber = trim($invoiceData['Invoice Number'] ?? '');
    
    if (!empty($invoiceNumber)) {
        echo "Processing invoice: $invoiceNumber\n";
        
        generateInvoicePdf($invoiceNumber);
        
        $invoice_to_send = $invoiceNumber;
        $current_invoice_data = $invoiceData;
        $current_company_data = $companyData;
        
        include($baseDir . "sendmail.php");
        
        $processedCount++;
        echo "Completed processing invoice: $invoiceNumber\n";
    }
}

echo "All invoices processed successfully. Total: $processedCount\n";
?>