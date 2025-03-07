<?php
require 'db.php';

// Add new Vehicle Status functionality (AJAX)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['vehicle_id'])) {
    $vehicle_id = $_POST['vehicle_id'];
    $status_date = $_POST['status_date'];
    $current_status = $_POST['current_status'];
    $notes = $_POST['notes'];

    // Insert the new vehicle status record into the database
    $sql = "INSERT INTO vehicle_status (vehicle_id, status_date, current_status, notes) 
            VALUES (:vehicle_id, :status_date, :current_status, :notes)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':vehicle_id', $vehicle_id);
    $stmt->bindParam(':status_date', $status_date);
    $stmt->bindParam(':current_status', $current_status);
    $stmt->bindParam(':notes', $notes);

    if ($stmt->execute()) {
        // Get the ID of the newly inserted vehicle status record
        $status_id = $conn->lastInsertId();
        
        // Return the new row HTML
        echo "<tr data-id='$status_id'>
                <td>$status_id</td>
                <td>$vehicle_id</td>
                <td>$status_date</td>
                <td>$current_status</td>
                <td>$notes</td>
                <td>
                    <button class='btn btn-update' onclick='editVehicleStatus(this)'>Edit</button>
                    <button class='btn btn-delete' onclick='deleteVehicleStatus(this)'>Delete</button>
                </td>
              </tr>";
    } else {
        echo "Error: Could not add vehicle status.";
    }
    exit;
}

// Delete Vehicle Status (AJAX)
if (isset($_GET['id'])) {
    $status_id = $_GET['id'];

    // Prepare the DELETE SQL statement
    $sql = "DELETE FROM vehicle_status WHERE status_id = :status_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':status_id', $status_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo 'Vehicle Status deleted successfully!';
    } else {
        echo 'Error: Could not delete vehicle status.';
    }
    exit;
}

// Fetch all existing vehicle status records
$sql = "SELECT * FROM vehicle_status";
$stmt = $conn->prepare($sql);
$stmt->execute();
$vehicle_statuses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Status</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin-top: 50px;
        }
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
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
        #add-form {
            margin-top: 30px;
        }
        input[type="text"], input[type="date"] {
            padding: 10px;
            margin: 5px;
            border-radius: 5px;
            width: 100%;
            max-width: 300px;
        }
    </style>
</head>
<body>
    <h1>Vehicle Status</h1>

    <!-- Add New Vehicle Status Form -->
    <div id="add-form">
        <h3>Add New Vehicle Status</h3>
        <form id="newVehicleStatusForm">
            <input type="text" id="vehicle_id" placeholder="Vehicle ID" required>
            <input type="date" id="status_date" placeholder="Status Date" required>
            <input type="text" id="current_status" placeholder="Current Status" required>
            <input type="text" id="notes" placeholder="Notes">
            <button type="submit" class="btn btn-update">Add Vehicle Status</button>
        </form>
    </div>

    <!-- Table to display vehicle status records -->
    <h2>Status</h2>
    <table>
        <thead>
            <tr>
                <th>Status ID</th>
                <th>Vehicle ID</th>
                <th>Status Date</th>
                <th>Current Status</th>
                <th>Notes</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="table-body">
        <?php foreach ($vehicle_statuses as $row): ?>
            <tr data-id="<?= htmlspecialchars($row['status_id']) ?>">
                <td><?= htmlspecialchars($row['status_id']) ?></td>
                <td class="vehicle_id"><?= htmlspecialchars($row['vehicle_id']) ?></td>
                <td class="status_date"><?= htmlspecialchars($row['status_date']) ?></td>
                <td class="current_status"><?= htmlspecialchars($row['current_status']) ?></td>
                <td class="notes"><?= htmlspecialchars($row['notes']) ?></td>
                <td>
                    <button class="btn btn-update" onclick="editVehicleStatus(this)">Edit</button>
                    <button class="btn btn-delete" onclick="deleteVehicleStatus(this)">Delete</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <script>
        // Edit Vehicle Status (Populate form with existing data)
        function editVehicleStatus(button) {
            const row = button.parentElement.parentElement;
            const statusId = row.getAttribute('data-id');
            const vehicleId = row.querySelector('.vehicle_id').textContent;
            const statusDate = row.querySelector('.status_date').textContent;
            const currentStatus = row.querySelector('.current_status').textContent;
            const notes = row.querySelector('.notes').textContent;

            // Populate form with existing data
            document.getElementById('vehicle_id').value = vehicleId;
            document.getElementById('status_date').value = statusDate;
            document.getElementById('current_status').value = currentStatus;
            document.getElementById('notes').value = notes;

            // Update form to handle update request instead of adding new
            document.getElementById('newVehicleStatusForm').onsubmit = function(event) {
                event.preventDefault();
                updateVehicleStatus(statusId);
            };
        }

        // Update Vehicle Status (AJAX)
        function updateVehicleStatus(statusId) {
            const vehicleId = document.getElementById('vehicle_id').value;
            const statusDate = document.getElementById('status_date').value;
            const currentStatus = document.getElementById('current_status').value;
            const notes = document.getElementById('notes').value;

            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'update_vehicle_status.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    alert('Vehicle Status updated successfully!');
                    location.reload();  // Refresh the table after update
                }
            };
            xhr.send(`status_id=${statusId}&vehicle_id=${vehicleId}&status_date=${statusDate}&current_status=${currentStatus}&notes=${notes}`);
        }

        // Delete Vehicle Status (AJAX)
        function deleteVehicleStatus(button) {
            const row = button.parentElement.parentElement;
            const statusId = row.getAttribute('data-id');

            const xhr = new XMLHttpRequest();
            xhr.open('GET', '?id=' + statusId, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    alert('Vehicle Status deleted successfully!');
                    row.remove();  // Remove the row from the table
                }
            };
            xhr.send();
        }

        // Add New Vehicle Status (AJAX)
        document.getElementById('newVehicleStatusForm').onsubmit = function(event) {
            event.preventDefault();
            const vehicleId = document.getElementById('vehicle_id').value;
            const statusDate = document.getElementById('status_date').value;
            const currentStatus = document.getElementById('current_status').value;
            const notes = document.getElementById('notes').value;

            const xhr = new XMLHttpRequest();
            xhr.open('POST', '', true); // POST to the same page
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    const newRow = xhr.responseText;  // The new row HTML returned from the server
                    document.getElementById('table-body').innerHTML += newRow;  // Append new row to the table body
                    alert('Vehicle Status added successfully!');
                    
                    // Reset the form fields
                    document.getElementById('newVehicleStatusForm').reset();
                }
            };

            xhr.send(`vehicle_id=${vehicleId}&status_date=${statusDate}&current_status=${currentStatus}&notes=${notes}`);
        };
    </script>
</body>
</html>
