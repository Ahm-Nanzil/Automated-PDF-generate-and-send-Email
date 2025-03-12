<?php
$message = ''; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $invoiceNumber = $_POST["invoice_number"];

    if (!empty($invoiceNumber)) {
        require 'generate_pdf.php'; 
        $pdfPath = generateInvoicePdf($invoiceNumber); 
        
        $message = "<pre>PDF generated!</pre>"; 

        $sendMailCommand = "php sendemail2.php " . escapeshellarg($invoiceNumber);


            $output = shell_exec($sendMailCommand);

            // echo "<pre>Output: $output</pre>";

            $message .= "<p>Email sent successfully!</p>";


    } else {
        $message = "<p>Please enter an invoice number.</p>"; 
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Email Sender</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            text-align: center;
            padding: 40px;
            background-color: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            width: 100%;
            max-width: 500px;
        }
        h2 {
            color: #333;
        }
        label {
            font-size: 16px;
            color: #666;
        }
        input[type="text"] {
            padding: 10px;
            width: 100%;
            margin-top: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        button {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .links {
            margin-top: 30px;
        }
        .links a {
            display: inline-block;
            padding: 10px 20px;
            margin: 10px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        .links a:hover {
            background-color: #0056b3;
        }
        .message {
            margin-top: 20px;
            padding: 10px;
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            font-size: 16px;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Send Invoice PDF As Email Body</h2>
        <form method="post">
            <label for="invoice_number">Invoice Number:</label>
            <input type="text" name="invoice_number" required>
            <button type="submit">Send Email</button>
        </form>

        <div class="links">
            <a href="invoiceinput.php">Send Invoice PDF attached Only</a>
            <a href="invoice_input.php">Send Invoice PDF as Email Body</a>
            <a href="csvinput.php">Create New Invoice CSV</a>
                        <a href="pdf.php">Show all Generated PDF</a>

        </div>

        <?php if ($message): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>
    </div>

</body>
</html>
