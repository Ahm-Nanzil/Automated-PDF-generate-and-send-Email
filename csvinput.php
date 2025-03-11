<?php
$message = '';
$messageType = '';
$csvFilePath = 'invoice.csv';
$defaultData = 'Email,Customer Name,Company Name,Address,Invoice Number,Customer Number,VAT ID,Invoice Date,Due Date,Item Description,Quantity,Price,Total Amount,IBAN,SWIFT,Bank Name,Payment Terms
eva.kommas@example.com,Eva Kommas,SCRATCH Pharmacovigilance GmbH & Co. KG,"Schlossstraße 25, 35510 BUTZBACH, Germany",2559817,944,DE268771241,04-03-2025,18-03-2025,scratch-pv.com Markenregistrierung Europa Kommerziell 2025 - 2026,1,"€ 863,42","€ 863,42",ES27 0073 0100 5107 8500 3951,OPENESMM,OPEN BANK S.A.,Fällig in 14 Tagen
john.doe@example.com,John Doe,XYZ Pharma Ltd.,"1234 Market Street, Berlin, Germany",2559818,945,DE123456789,05-03-2025,19-03-2025,XYZ Pharma Registration Europe 2025 - 2026,1,"€ 950,00","€ 950,00",ES27 0073 0100 5107 8500 3952,OPENESMM,OPEN BANK S.A.,Fällig in 14 Tagen';

$existingData = '';
if (file_exists($csvFilePath)) {
    $existingData = file_get_contents($csvFilePath);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['csv_data'])) {
        $csvData = trim($_POST['csv_data']);
        
        if (strpos($csvData, 'Email,Customer Name,Company Name,Address,Invoice Number') === 0) {
            if (file_put_contents($csvFilePath, $csvData)) {
                $message = "CSV data successfully saved to $csvFilePath";
                $messageType = "success";
                $existingData = $csvData; 
            } else {
                $message = "Error: Could not save data to $csvFilePath. Please check file permissions.";
                $messageType = "error";
            }
        } else {
            $message = "Error: Invalid CSV format. Please ensure your data has the correct header row.";
            $messageType = "error";
        }
    } else {
        $message = "Error: No CSV data provided.";
        $messageType = "error";
    }
}

if (isset($_POST['add_invoice']) && $_POST['add_invoice'] == 'true') {
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $customerName = isset($_POST['customer_name']) ? $_POST['customer_name'] : '';
    $companyName = isset($_POST['company_name']) ? $_POST['company_name'] : '';
    $address = isset($_POST['address']) ? $_POST['address'] : '';
    $invoiceNumber = isset($_POST['invoice_number']) ? $_POST['invoice_number'] : '';
    $customerNumber = isset($_POST['customer_number']) ? $_POST['customer_number'] : '';
    $vatId = isset($_POST['vat_id']) ? $_POST['vat_id'] : '';
    $invoiceDate = isset($_POST['invoice_date']) ? $_POST['invoice_date'] : '';
    $dueDate = isset($_POST['due_date']) ? $_POST['due_date'] : '';
    $itemDescription = isset($_POST['item_description']) ? $_POST['item_description'] : '';
    $quantity = isset($_POST['quantity']) ? $_POST['quantity'] : '1';
    $price = isset($_POST['price']) ? $_POST['price'] : '';
    $totalAmount = isset($_POST['total_amount']) ? $_POST['total_amount'] : '';
    $iban = isset($_POST['iban']) ? $_POST['iban'] : '';
    $swift = isset($_POST['swift']) ? $_POST['swift'] : '';
    $bankName = isset($_POST['bank_name']) ? $_POST['bank_name'] : '';
    $paymentTerms = isset($_POST['payment_terms']) ? $_POST['payment_terms'] : '';

    $newRow = "\n" . implode(',', [
        $email,
        $customerName,
        $companyName,
        '"' . str_replace('"', '""', $address) . '"', 
        $invoiceNumber,
        $customerNumber,
        $vatId,
        $invoiceDate,
        $dueDate,
        $itemDescription,
        $quantity,
        '"' . $price . '"', 
        '"' . $totalAmount . '"', 
        $swift,
        $bankName,
        $paymentTerms
    ]);

    if (!file_exists($csvFilePath)) {
        $csvContent = "Email,Customer Name,Company Name,Address,Invoice Number,Customer Number,VAT ID,Invoice Date,Due Date,Item Description,Quantity,Price,Total Amount,IBAN,SWIFT,Bank Name,Payment Terms" . $newRow;
        $saveResult = file_put_contents($csvFilePath, $csvContent);
    } else {
        $saveResult = file_put_contents($csvFilePath, $newRow, FILE_APPEND);
    }

    if ($saveResult !== false) {
        $message = "New invoice successfully added to $csvFilePath";
        $messageType = "success";
        $existingData = file_get_contents($csvFilePath);
    } else {
        $message = "Error: Could not add new invoice to $csvFilePath. Please check file permissions.";
        $messageType = "error";
    }
}

$viewLink = 'csvinput.php'; 
$backLink = 'invoiceinput.php'; 

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice CSV Manager</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1, h2 {
            color: #333;
        }
        .message {
            padding: 10px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        textarea {
            width: 100%;
            min-height: 300px;
            margin-bottom: 20px;
            padding: 10px;
            box-sizing: border-box;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: monospace;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        .tabs {
            overflow: hidden;
            border: 1px solid #ccc;
            background-color: #f1f1f1;
            margin-bottom: 20px;
        }
        .tabs button {
            background-color: inherit;
            float: left;
            border: none;
            outline: none;
            cursor: pointer;
            padding: 14px 16px;
            transition: 0.3s;
            font-size: 16px;
        }
        .tabs button:hover {
            background-color: #ddd;
        }
        .tabs button.active {
            background-color: #ccc;
        }
        .tabcontent {
            display: none;
            padding: 20px;
            border: 1px solid #ccc;
            border-top: none;
        }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            min-height: initial;
        }
        .actions {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
        }
        .view-link {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 20px;
        }
        .view-link:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Invoice CSV Manager</h1>
        
        <?php if (!empty($message)): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <div class="tabs">
            <button class="tablinks active" onclick="openTab(event, 'csvEditor')">CSV Editor</button>
            <button class="tablinks" onclick="openTab(event, 'addInvoice')">Add New Invoice</button>
        </div>
        
        <div id="csvEditor" class="tabcontent" style="display: block;">
            <h2>Edit CSV Data</h2>
            <p>Edit the CSV data below and click Save to update the invoice.csv file:</p>
            
            <form method="post" action="">
                <textarea name="csv_data" placeholder="Paste your CSV data here..."><?php echo htmlspecialchars($existingData ?: $defaultData); ?></textarea>
                <input type="submit" value="Save CSV Data">
            </form>
        </div>
        
        <div id="addInvoice" class="tabcontent">
            <h2>Add New Invoice</h2>
            <p>Fill in the form below to add a new invoice entry:</p>
            
            <form method="post" action="">
                <input type="hidden" name="add_invoice" value="true">
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="customer_name">Customer Name:</label>
                        <input type="text" id="customer_name" name="customer_name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="company_name">Company Name:</label>
                        <input type="text" id="company_name" name="company_name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="address">Address:</label>
                        <textarea id="address" name="address" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="invoice_number">Invoice Number:</label>
                        <input type="text" id="invoice_number" name="invoice_number" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="customer_number">Customer Number:</label>
                        <input type="text" id="customer_number" name="customer_number" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="vat_id">VAT ID:</label>
                        <input type="text" id="vat_id" name="vat_id" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="invoice_date">Invoice Date (DD-MM-YYYY):</label>
                        <input type="text" id="invoice_date" name="invoice_date" placeholder="DD-MM-YYYY" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="due_date">Due Date (DD-MM-YYYY):</label>
                        <input type="text" id="due_date" name="due_date" placeholder="DD-MM-YYYY" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="item_description">Item Description:</label>
                        <input type="text" id="item_description" name="item_description" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="quantity">Quantity:</label>
                        <input type="number" id="quantity" name="quantity" value="1" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="price">Price (with currency symbol):</label>
                        <input type="text" id="price" name="price" placeholder="€ 0,00" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="total_amount">Total Amount (with currency symbol):</label>
                        <input type="text" id="total_amount" name="total_amount" placeholder="€ 0,00" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="iban">IBAN:</label>
                        <input type="text" id="iban" name="iban" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="swift">SWIFT:</label>
                        <input type="text" id="swift" name="swift" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="bank_name">Bank Name:</label>
                        <input type="text" id="bank_name" name="bank_name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="payment_terms">Payment Terms:</label>
                        <input type="text" id="payment_terms" name="payment_terms" value="Fällig in 14 Tagen" required>
                    </div>
                </div>
                
                <input type="submit" value="Add New Invoice">
            </form>
        </div>
        
        <div class="actions">
            <a href="<?php echo $viewLink; ?>" class="view-link">View Invoices</a>
            <a href="<?php echo $backLink; ?>" class="view-link">Back</a>

        </div>
        
    </div>

    <script>
        function openTab(evt, tabName) {
            var i, tabcontent, tablinks;
            
            tabcontent = document.getElementsByClassName("tabcontent");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
            }
            
            tablinks = document.getElementsByClassName("tablinks");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" active", "");
            }
            
            document.getElementById(tabName).style.display = "block";
            evt.currentTarget.className += " active";
        }
        
        document.getElementById('price').addEventListener('input', updateTotal);
        document.getElementById('quantity').addEventListener('input', updateTotal);
        
        function updateTotal() {
            var price = document.getElementById('price').value;
            var quantity = document.getElementById('quantity').value;
            
            var numericPrice = price.replace(/[^0-9,]/g, '').replace(',', '.');
            
            if (numericPrice && quantity) {
                var total = parseFloat(numericPrice) * parseInt(quantity);
                var formattedTotal = '€ ' + total.toFixed(2).replace('.', ',');
                document.getElementById('total_amount').value = formattedTotal;
            }
        }
    </script>
</body>
</html>