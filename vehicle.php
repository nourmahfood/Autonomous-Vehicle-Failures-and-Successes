<?php
require 'db.php';

// Add new Vehicle functionality (AJAX)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['make'])) {
    $make = $_POST['make'];
    $model_name = $_POST['model_name'];
    $manufacture_year = $_POST['manufacture_year'];
    $software_version_id = $_POST['software_version_id'];
    $autonomy_level = $_POST['autonomy_level'];
    $brief_description = $_POST['brief_description'];

    // Insert the new vehicle record into the database
    $sql = "INSERT INTO vehicles (make, model_name, manufacture_year, software_version_id, autonomy_level, brief_description) 
            VALUES (:make, :model_name, :manufacture_year, :software_version_id, :autonomy_level, :brief_description)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':make', $make);
    $stmt->bindParam(':model_name', $model_name);
    $stmt->bindParam(':manufacture_year', $manufacture_year);
    $stmt->bindParam(':software_version_id', $software_version_id);
    $stmt->bindParam(':autonomy_level', $autonomy_level);
    $stmt->bindParam(':brief_description', $brief_description);

    if ($stmt->execute()) {
        // Get the ID of the newly inserted vehicle
        $vehicle_id = $conn->lastInsertId();

        // Return the new row HTML
        echo "<tr data-id='$vehicle_id'>
                <td>$vehicle_id</td>
                <td>$make</td>
                <td>$model_name</td>
                <td>$manufacture_year</td>
                <td>$software_version_id</td>
                <td>$autonomy_level</td>
                <td>$brief_description</td>
                <td>
                    <button class='btn btn-update' onclick='editVehicle(this)'>Edit</button>
                    <button class='btn btn-delete' onclick='deleteVehicle(this)'>Delete</button>
                </td>
              </tr>";
    } else {
        echo "Error: Could not add vehicle.";
    }
    exit;
}

// Delete Vehicle (AJAX)
if (isset($_GET['delete_id'])) {
    $vehicle_id = $_GET['delete_id'];

    // Prepare the DELETE SQL statement
    $sql = "DELETE FROM vehicles WHERE vehicle_id = :vehicle_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':vehicle_id', $vehicle_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo 'Vehicle deleted successfully!';
    } else {
        echo 'Error: Could not delete vehicle.';
    }
    exit;
}

// Fetch all existing vehicles
$sql = "SELECT * FROM vehicles";
$stmt = $conn->prepare($sql);
$stmt->execute();
$vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicles</title>
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
        input[type="text"], input[type="number"], input[type="date"] {
            padding: 10px;
            margin: 5px;
            border-radius: 5px;
            width: 100%;
            max-width: 300px;
        }
    </style>
</head>
<body>
    <h1>Vehicles</h1>

    <!-- Add New Vehicle Form -->
    <div id="add-form">
        <h3>Add New Vehicle</h3>
        <form id="newVehicleForm">
            <input type="text" id="make" placeholder="Make" required>
            <input type="text" id="model_name" placeholder="Model Name" required>
            <input type="number" id="manufacture_year" placeholder="Manufacture Year" required>
            <input type="number" id="software_version_id" placeholder="Software Version ID" required>
            <input type="number" id="autonomy_level" placeholder="Autonomy Level" required>
            <input type="text" id="brief_description" placeholder="Brief Description" required>
            <button type="submit" class="btn btn-update">Add Vehicle</button>
        </form>
    </div>

    <!-- Table to display vehicle records -->
    <h2>Vehicle List</h2>
    <table>
        <thead>
            <tr>
                <th>Vehicle ID</th>
                <th>Make</th>
                <th>Model Name</th>
                <th>Manufacture Year</th>
                <th>Software Version ID</th>
                <th>Autonomy Level</th>
                <th>Brief Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="vehicle-table-body">
        <?php foreach ($vehicles as $row): ?>
            <tr data-id="<?= htmlspecialchars($row['vehicle_id']) ?>">
                <td><?= htmlspecialchars($row['vehicle_id']) ?></td>
                <td class="make"><?= htmlspecialchars($row['make']) ?></td>
                <td class="model_name"><?= htmlspecialchars($row['model_name']) ?></td>
                <td class="manufacture_year"><?= htmlspecialchars($row['manufacture_year']) ?></td>
                <td class="software_version_id"><?= htmlspecialchars($row['software_version_id']) ?></td>
                <td class="autonomy_level"><?= htmlspecialchars($row['autonomy_level']) ?></td>
                <td class="brief_description"><?= htmlspecialchars($row['brief_description']) ?></td>
                <td>
                    <button class="btn btn-update" onclick="editVehicle(this)">Edit</button>
                    <button class="btn btn-delete" onclick="deleteVehicle(this)">Delete</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <script>
        // Add New Vehicle (AJAX)
        document.getElementById('newVehicleForm').onsubmit = function(event) {
            event.preventDefault();

            const make = document.getElementById('make').value;
            const model_name = document.getElementById('model_name').value;
            const manufacture_year = document.getElementById('manufacture_year').value;
            const software_version_id = document.getElementById('software_version_id').value;
            const autonomy_level = document.getElementById('autonomy_level').value;
            const brief_description = document.getElementById('brief_description').value;

            const xhr = new XMLHttpRequest();
            xhr.open('POST', '', true); // POST to the same page
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    const newRow = xhr.responseText;  // The new row HTML returned from the server
                    document.getElementById('vehicle-table-body').innerHTML += newRow;  // Append new row to the table body
                    alert('Vehicle added successfully!');

                    // Reset the form fields
                    document.getElementById('newVehicleForm').reset();
                }
            };

            xhr.send(`make=${make}&model_name=${model_name}&manufacture_year=${manufacture_year}&software_version_id=${software_version_id}&autonomy_level=${autonomy_level}&brief_description=${brief_description}`);
        };

        // Edit Vehicle (Populate form with existing data)
        function editVehicle(button) {
            const row = button.parentElement.parentElement;
            const vehicleId = row.getAttribute('data-id');
            const make = row.querySelector('.make').textContent;
            const modelName = row.querySelector('.model_name').textContent;
            const manufactureYear = row.querySelector('.manufacture_year').textContent;
            const softwareVersionId = row.querySelector('.software_version_id').textContent;
            const autonomyLevel = row.querySelector('.autonomy_level').textContent;
            const briefDescription = row.querySelector('.brief_description').textContent;

            // Populate form with existing data
            document.getElementById('make').value = make;
            document.getElementById('model_name').value = modelName;
            document.getElementById('manufacture_year').value = manufactureYear;
            document.getElementById('software_version_id').value = softwareVersionId;
            document.getElementById('autonomy_level').value = autonomyLevel;
            document.getElementById('brief_description').value = briefDescription;

            // Update form to handle update request instead of adding new
            document.getElementById('newVehicleForm').onsubmit = function(event) {
                event.preventDefault();
                updateVehicle(vehicleId);
            };
        }

        // Delete Vehicle (AJAX)
        function deleteVehicle(button) {
            const row = button.parentElement.parentElement;
            const vehicleId = row.getAttribute('data-id');

            const xhr = new XMLHttpRequest();
            xhr.open('GET', '?delete_id=' + vehicleId, true); // Send GET request to delete the vehicle
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    // Check the server response
                    const response = xhr.responseText;
                    if (response.includes('Vehicle deleted successfully')) {
                        alert('Vehicle deleted successfully!');
                        row.remove();  // Remove the row from the table
                    } else {
                        alert('Error deleting vehicle: ' + response);
                    }
                }
            };
            xhr.send();
        }
    </script>
</body>
</html>
