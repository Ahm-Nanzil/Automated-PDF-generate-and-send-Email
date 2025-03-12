<?php
if (!isset($_GET['invoice'])) {
    die("Error: Invoice number is required.");
}

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

            if ($invoiceNumber !== null && trim($row['Invoice Number']) === trim($invoiceNumber)) {
                fclose($handle);
                return $row;
            }

            $csvData[] = $row;
        }
        fclose($handle);
    }

    return $invoiceNumber !== null ? [] : $csvData;
}

$requestedInvoice = isset($_GET['invoice']) ? $_GET['invoice'] : null;


$baseDir = "/home/iponeuro/public_html/backuo/";

$invoiceData = getInvoiceData($baseDir . "invoice.csv", $requestedInvoice);


if (empty($invoiceData)) {
    echo "Invoice not found.";
    exit;
}

function formatCurrency($amount) {
    return $amount;  
}

$customerName = $invoiceData['Customer Name'];
$companyName = $invoiceData['Company Name'];
$address = $invoiceData['Address'];
$addressParts = explode(',', $address);

$invoiceNumber = $invoiceData['Invoice Number'];
$customerNumber = $invoiceData['Customer Number'];
$vatId = $invoiceData['VAT ID'];
$invoiceDate = $invoiceData['Invoice Date'];
$dueDate = $invoiceData['Due Date'];
$itemDescription = $invoiceData['Item Description'];
$quantity = $invoiceData['Quantity'];
$price = $invoiceData['Price'];
$totalAmount = $invoiceData['Total Amount'];
$iban = $invoiceData['IBAN'];
$swift = $invoiceData['SWIFT'];
$bankName = $invoiceData['Bank Name'];
$paymentTerms = $invoiceData['Payment Terms'];
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IPON Invoice</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .header {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 30px;
        }
        .eu-map {
            flex: 1 1 100%;
            text-align: left;
            margin-bottom: 20px;
        }
        .eu-map img {
            max-width: 100%;
            height: auto;
        }
        .company-info {
            flex: 1 1 100%;
            text-align: right;
        }
        .company-info p {
            margin: 0;
            line-height: 1.3;
        }
        .customer-info {
            margin-top: 20px;
            margin-bottom: 30px;
        }
        .note {
            margin: 20px 0;
            font-style: italic;
        }
        .invoice-header {
            background-color: #d9f0ff !important;
            color: #0275d8;
        }
        .table-bordered {
            border: 1px solid #dee2e6;
            width: 100%;
        }
        .total-section {
            margin-top: 20px;
            text-align: right;
        }
        .total-box {
            border: 1px solid #dee2e6;
            padding: 5px 10px;
            display: inline-block;
            min-width: 120px;
            text-align: right;
        }
        .bold {
            font-weight: bold;
        }
        .small-text {
            font-size: 0.85em;
        }
        .company-name {
            font-size: 1em;
            font-weight: bold;
        }
        .vat-note {
            text-align: right;
            margin-top: 5px;
        }
        .blue-text {
            color: #0275d8;
        }
        .footer-text {
            margin-top: 20px;
            font-size: 0.9em;
        }
        .footer-highlight {
            color: red;
        }
        @media (min-width: 768px) {
            .header {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
            }
            .eu-map {
                flex: 0 0 40%; 
                text-align: left;
            }
            .company-info {
                flex: 0 0 60%; 
                text-align: right;
            }
        }

        .eu-map img {
            max-width: 100%;
            height: auto;
        }
        
        
        @media (min-width: 1024px) {
            .eu-map img {
                max-width: 60%; 
            }
        }
        
        
        
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            

            <table width="100%" cellspacing="0" cellpadding="0">
                <tr>
                    <td align="left" class="eu-map" width="40%">
                        <img src="https://ipon-europe.com/backuo/resources/images/EU.png" alt="EU Map">
                        <div class="customer-info">
                            <p class="bold"><?php echo htmlspecialchars($companyName); ?><br>
                                <?php echo htmlspecialchars($customerName);?><br>
                                <?php 
                                foreach($addressParts as $part) {
                                    echo htmlspecialchars(trim($part)) . "<br>";
                                }
                                ?>
                            </p>
                        </div>
                    </td>
                    
                    <td align="right" class="company-info" width="60%">
                        <p class="company-name">Intellectual Property Office Netherlands (IPON)</p>
                        <p class="small-text"><i>formerly known as HR Community CertiSphere</i></p>
                        <br>
                        <p class="bold">UST-IdNr - NL856615523B01</p>
                        <p class="bold">Handelsnummer - 66576148</p>
                        <br>
                        <p>Adammium Headquarter</p>
                        <p>Joop Geesinkweg 201</p>
                        <p>1114 AB Amsterdam</p>
                        <br>
                        <p>www.iponederland.nl</p>
                        <p>E-mail: info@iponederland.nl</p>
                        <p>Tel. +31 (0)88 661 58 34</p>
                        <br>
                        <p class="bold">IBAN <?php echo htmlspecialchars($iban); ?></p>
                        <br>
                        <p>SWIFT/BIC: <?php echo htmlspecialchars($swift); ?></p>
                        <p>Bank: <?php echo htmlspecialchars($bankName); ?></p>
                    </td>
                </tr>
            </table>
            
        </div>

        <div class="note">
            <p>Die Phase-1-Registrierung ist ein verpflichtendes Verfahren für kommerzielle Unternehmen innerhalb der EU</p>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th class="blue-text invoice-header">Rechnung</th>
                        <th class="blue-text invoice-header">Kundennummer</th>
                        <th class="blue-text invoice-header">USt-Idnr.</th>
                        <th class="blue-text invoice-header">Datum</th>
                        <th class="blue-text invoice-header">Fälligkeitsdatum</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo htmlspecialchars($invoiceNumber); ?></td>
                        <td><?php echo htmlspecialchars($customerNumber); ?></td>
                        <td><?php echo htmlspecialchars($vatId); ?></td>
                        <td><?php echo htmlspecialchars($invoiceDate); ?></td>
                        <td><?php echo htmlspecialchars($dueDate); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="table-responsive mt-4">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th class="blue-text invoice-header">Anzahl</th>
                        <th class="blue-text invoice-header">Beschreibung</th>
                        <th class="blue-text invoice-header text-end">Preis</th>
                        <th class="blue-text invoice-header text-end">Gesamt</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo htmlspecialchars($quantity); ?></td>
                        <td><?php echo htmlspecialchars($itemDescription); ?></td>
                        <td class="text-end"><?php echo htmlspecialchars($price); ?></td>
                        <td class="text-end"><?php echo htmlspecialchars($totalAmount); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="total-section">
            <div class="d-flex justify-content-end align-items-center">
                <p class="me-3 mb-0">Gesamt</p>
                <div class="total-box"><?php echo htmlspecialchars($totalAmount); ?></div>
            </div>
            <p class="vat-note">Umsatzsteuer verlagert</p>
        </div>

        <div class="footer-text">
            <p>Wir bitten Sie, den <span class="footer-highlight">fälligen Betrag innerhalb von 14 Tagen zu überweisen</span> die Rechnungsnummer auf Kontonummer <?php echo htmlspecialchars($iban); ?> im Namen des IPON.</p>
            <p>Unsere Allgemeinen Geschäftsbedingungen gelten für alle Leistungen. Sie können dies von unserer Website herunterladen.</p>
        </div>
    </div>
</body>
</html>