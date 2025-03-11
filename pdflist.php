<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 50px;
        }
        .card {
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        .table {
            border-radius: 10px;
            overflow: hidden;
        }
        .btn-download {
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            padding: 5px 10px;
            text-decoration: none;
            transition: 0.3s;
        }
        .btn-download:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card p-4">
        <h2 class="text-center text-primary">📜 Invoice List</h2>
        <table class="table table-hover mt-3">
            <thead class="table-primary">
                <tr>
                    <th>#</th>
                    <th>Invoice Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $directory = "templates/invoices"; // Path to your PDF folder
                $files = glob($directory . "/invoice_*.pdf");

                if (!$files) {
                    echo "<tr><td colspan='3' class='text-center'>No invoices found.</td></tr>";
                } else {
                    $count = 1;
                    foreach ($files as $file) {
                        $filename = basename($file);
                        echo "<tr>
                                <td>{$count}</td>
                                <td>{$filename}</td>
                                <td><a href='{$file}' target='_blank' class='btn-download'>📂 View</a></td>
                              </tr>";
                        $count++;
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
