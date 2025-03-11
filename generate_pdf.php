<?php 
require 'vendor/autoload.php'; 
use Dompdf\Dompdf;
use Dompdf\Options;

function fetchInvoiceHtml($invoiceNumber) {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    $path = dirname($_SERVER['PHP_SELF']);
    
    $url = $protocol . $host . $path . '/pdfcontent.php?invoice=' . urlencode($invoiceNumber);
    
    $html = file_get_contents($url);
    
    if (!$html) {
        die("Error: Could not fetch invoice content for invoice #$invoiceNumber");
    }
    
    return $html;
}

function generateInvoicePdf($invoiceNumber) {
    if (empty($invoiceNumber)) {
        die("Error: Invoice number is required.");
    }
    
    $options = new Options();
    $options->set('isRemoteEnabled', true);
    
    $dompdf = new Dompdf($options);

    $html = fetchInvoiceHtml($invoiceNumber);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    
    $dompdf->render();
    
    $directory = __DIR__ . "/templates/invoices";
    if (!is_dir($directory)) {
        mkdir($directory, 0755, true);
    }
    
    $pdfPath = "$directory/invoice_$invoiceNumber.pdf";
    file_put_contents($pdfPath, $dompdf->output());

    // $pdfPath = __DIR__ . "/templates/invoices/invoice_$invoiceNumber.pdf";
    // file_put_contents($pdfPath, $dompdf->output());

    return $pdfPath;
}

if (isset($_GET['invoice'])) {
    generateInvoicePdf($_GET['invoice']);
}
?>