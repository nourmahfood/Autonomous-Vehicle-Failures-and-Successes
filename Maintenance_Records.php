<?php
require 'db.php';

// Add new record functionality
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['vehicle_id'])) {
    $vehicle_id = $_POST['vehicle_id'];
    $maintenance_date = $_POST['maintenance_date'];
    $brief_description = $_POST['brief_description'];
    $the_cost = $_POST['the_cost'];

    // Insert the new record into the database
    $sql = "INSERT INTO maintenance_records (vehicle_id, maintenance_date, brief_description, the_cost)
            VALUES (:vehicle_id, :maintenance_date, :brief_description, :the_cost)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':vehicle_id', $vehicle_id);
    $stmt->bindParam(':maintenance_date', $maintenance_date);
    $stmt->bindParam(':brief_description', $brief_description);
    $stmt->bindParam(':the_cost', $the_cost);

    if ($stmt->execute()) {
        // Get the ID of the newly inserted record
        $maintenance_id = $conn->lastInsertId();

        // Return the new row HTML
        echo "<tr data-id='$maintenance_id'>
                <td>$maintenance_id</td>
                <td>$vehicle_id</td>
                <td>$maintenance_date</td>
                <td>$brief_description</td>
                <td>$the_cost</td>
                <td>
                    <button class='btn btn-update' onclick='editRecord(this)'>Edit</button>
                    <button class='btn btn-delete' onclick='deleteRecord(this)'>Delete</button>
                </td>
            </tr>";
    } else {
        echo "Error: Could not add record.";
    }
    exit;
}

// Fetch records for display
$sql = "SELECT * FROM maintenance_records";
$stmt = $conn->prepare($sql);
$stmt->execute();
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Records</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin-top: 50px;
        }

        h1 {
            color: #333;
        }

        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            cursor: pointer;
        }

        table, th, td {
            border: 1px solid black;
        }

        th, td {
            padding: 10px;
            text-align: center;
        }

        table:hover {
            background-color: #f0f0f0;
        }

        .btn {
            padding: 5px 10px;
            text-decoration: none;
            color: white;
            border-radius: 3px;
            font-size: 14px;
        }

        .btn-update {
            background-color: #007BFF;
        }

        .btn-delete {
            background-color: #DC3545;
        }

        .btn:hover {
            opacity: 0.8;
        }

        .back-button {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }

        .back-button:hover {
            background-color: #45a049;
        }

        #add-form {
            margin-top: 30px;
        }

        input[type="text"], input[type="number"], input[type="date"] {
            padding: 10px;
            margin: 5px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <h1>Maintenance Records</h1>

    <!-- Add new record form -->
    <div id="add-form">
        <h3>Add New Maintenance Record</h3>
        <form id="newRecordForm">
            <input type="text" id="vehicle_id" placeholder="Vehicle ID" required>
            <input type="date" id="maintenance_date" required>
            <input type="text" id="brief_description" placeholder="Brief Description" required>
            <input type="number" id="the_cost" placeholder="Cost" required>
            <button type="submit" class="btn btn-update">Add Record</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Maintenance ID</th>
                <th>Vehicle ID</th>
                <th>Maintenance Date</th>
                <th>Brief Description</th>
                <th>Cost</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="table-body">
        <?php foreach ($records as $row): ?>
            <tr data-id="<?= htmlspecialchars($row['maintenance_id']) ?>">
                <td><?= htmlspecialchars($row['maintenance_id']) ?></td>
                <td class="vehicle_id"><?= htmlspecialchars($row['vehicle_id']) ?></td>
                <td class="maintenance_date"><?= htmlspecialchars($row['maintenance_date']) ?></td>
                <td class="brief_description"><?= htmlspecialchars($row['brief_description']) ?></td>
                <td class="the_cost"><?= htmlspecialchars($row['the_cost']) ?></td>
                <td>
                    <button class="btn btn-update" onclick="editRecord(this)">Edit</button>
                    <button class="btn btn-delete" onclick="deleteRecord(this)">Delete</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <script>
        // Edit record in the table
        function editRecord(button) {
            const row = button.parentElement.parentElement;
            const maintenanceId = row.getAttribute('data-id');
            const vehicleId = row.querySelector('.vehicle_id').textContent;
            const maintenanceDate = row.querySelector('.maintenance_date').textContent;
            const briefDescription = row.querySelector('.brief_description').textContent;
            const cost = row.querySelector('.the_cost').textContent;

            // Populate form with data for editing
            document.getElementById('vehicle_id').value = vehicleId;
            document.getElementById('maintenance_date').value = maintenanceDate;
            document.getElementById('brief_description').value = briefDescription;
            document.getElementById('the_cost').value = cost;

            // Change the form action to update
            document.getElementById('newRecordForm').onsubmit = function(event) {
                event.preventDefault();
                updateRecord(maintenanceId);
            };
        }

        // Update record in database (using AJAX)
        function updateRecord(maintenanceId) {
            const vehicleId = document.getElementById('vehicle_id').value;
            const maintenanceDate = document.getElementById('maintenance_date').value;
            const briefDescription = document.getElementById('brief_description').value;
            const cost = document.getElementById('the_cost').value;

            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'update.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    alert('Record updated successfully!');
                    location.reload();  // Refresh table after update
                }
            };
            xhr.send('maintenance_id=' + maintenanceId + '&vehicle_id=' + vehicleId + '&maintenance_date=' + maintenanceDate + '&brief_description=' + briefDescription + '&the_cost=' + cost);
        }

        // Delete record from database (using AJAX)
        function deleteRecord(button) {
            const row = button.parentElement.parentElement;
            const maintenanceId = row.getAttribute('data-id');

            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'delete.php?id=' + maintenanceId, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    alert('Record deleted successfully!');
                    row.remove();  // Remove the row from the table
                }
            };
            xhr.send();
        }

        // Add new record
        document.getElementById('newRecordForm').onsubmit = function(event) {
            event.preventDefault();
            const vehicleId = document.getElementById('vehicle_id').value;
            const maintenanceDate = document.getElementById('maintenance_date').value;
            const briefDescription = document.getElementById('brief_description').value;
            const cost = document.getElementById('the_cost').value;

            const xhr = new XMLHttpRequest();
            xhr.open('POST', '', true); // POST to the same page
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    const newRow = xhr.responseText;  // The new row HTML returned from the server
                    document.getElementById('table-body').innerHTML += newRow;  // Append new row to the table body
                    alert('Record added successfully!');
                    
                    // Reset the form fields
                    document.getElementById('newRecordForm').reset();
                }
            };

            xhr.send('vehicle_id=' + vehicleId + '&maintenance_date=' + maintenanceDate + '&brief_description=' + briefDescription + '&the_cost=' + cost);
        };
    </script>
</body>
</html>
