<?php
require 'db.php';

// Add new Vehicle Sensor functionality (AJAX)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['vehicle_id'])) {
    $vehicle_id = $_POST['vehicle_id'];
    $sensor_id = $_POST['sensor_id'];
    $installation_date = $_POST['installation_date'];

    // Insert the new vehicle sensor record into the database
    $sql = "INSERT INTO vehicle_sensors (vehicle_id, sensor_id, installation_date) 
            VALUES (:vehicle_id, :sensor_id, :installation_date)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':vehicle_id', $vehicle_id);
    $stmt->bindParam(':sensor_id', $sensor_id);
    $stmt->bindParam(':installation_date', $installation_date);

    if ($stmt->execute()) {
        // Get the ID of the newly inserted vehicle sensor record
        $vehicle_sensor_id = $conn->lastInsertId();
        
        // Return the new row HTML
        echo "<tr data-id='$vehicle_sensor_id'>
                <td>$vehicle_sensor_id</td>
                <td>$vehicle_id</td>
                <td>$sensor_id</td>
                <td>$installation_date</td>
                <td>
                    <button class='btn btn-update' onclick='editVehicleSensor(this)'>Edit</button>
                    <button class='btn btn-delete' onclick='deleteVehicleSensor(this)'>Delete</button>
                </td>
              </tr>";
    } else {
        echo "Error: Could not add vehicle sensor.";
    }
    exit;
}

// Fetch all existing vehicle sensor records
$sql = "SELECT * FROM vehicle_sensors";
$stmt = $conn->prepare($sql);
$stmt->execute();
$vehicle_sensors = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Delete Vehicle Sensor functionality (AJAX)
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
    $vehicle_sensor_id = $_GET['id'];

    $sql = "DELETE FROM vehicle_sensors WHERE vehicle_sensor_id = :vehicle_sensor_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':vehicle_sensor_id', $vehicle_sensor_id);

    if ($stmt->execute()) {
        echo "Vehicle sensor deleted successfully!";
    } else {
        echo "Error: Could not delete vehicle sensor.";
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Sensors</title>
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
    <h1>Vehicle Sensors</h1>

    <!-- Add New Vehicle Sensor Form -->
    <div id="add-form">
        <h3>Add New Vehicle Sensor</h3>
        <form id="newVehicleSensorForm">
            <input type="text" id="vehicle_id" placeholder="Vehicle ID" required>
            <input type="text" id="sensor_id" placeholder="Sensor ID" required>
            <input type="date" id="installation_date" placeholder="Installation Date" required>
            <button type="submit" class="btn btn-update">Add Vehicle Sensor</button>
        </form>
    </div>

    <!-- Table to display vehicle sensor records -->
    <table>
        <thead>
            <tr>
                <th>Vehicle Sensor ID</th>
                <th>Vehicle ID</th>
                <th>Sensor ID</th>
                <th>Installation Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="table-body">
        <?php foreach ($vehicle_sensors as $row): ?>
            <tr data-id="<?= htmlspecialchars($row['vehicle_sensor_id']) ?>">
                <td><?= htmlspecialchars($row['vehicle_sensor_id']) ?></td>
                <td class="vehicle_id"><?= htmlspecialchars($row['vehicle_id']) ?></td>
                <td class="sensor_id"><?= htmlspecialchars($row['sensor_id']) ?></td>
                <td class="installation_date"><?= htmlspecialchars($row['installation_date']) ?></td>
                <td>
                    <button class="btn btn-update" onclick="editVehicleSensor(this)">Edit</button>
                    <button class="btn btn-delete" onclick="deleteVehicleSensor(this)">Delete</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <script>
        // Edit Vehicle Sensor (Populate form with existing data)
        function editVehicleSensor(button) {
            const row = button.parentElement.parentElement;
            const vehicleSensorId = row.getAttribute('data-id');
            const vehicleId = row.querySelector('.vehicle_id').textContent;
            const sensorId = row.querySelector('.sensor_id').textContent;
            const installationDate = row.querySelector('.installation_date').textContent;

            // Populate form with existing data
            document.getElementById('vehicle_id').value = vehicleId;
            document.getElementById('sensor_id').value = sensorId;
            document.getElementById('installation_date').value = installationDate;

            // Update form to handle update request instead of adding new
            document.getElementById('newVehicleSensorForm').onsubmit = function(event) {
                event.preventDefault();
                updateVehicleSensor(vehicleSensorId);
            };
        }

        // Update Vehicle Sensor (AJAX)
        function updateVehicleSensor(vehicleSensorId) {
            const vehicleId = document.getElementById('vehicle_id').value;
            const sensorId = document.getElementById('sensor_id').value;
            const installationDate = document.getElementById('installation_date').value;

            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'update_vehicle_sensor.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    alert('Vehicle Sensor updated successfully!');
                    location.reload();  // Refresh the table after update
                }
            };
            xhr.send(`vehicle_sensor_id=${vehicleSensorId}&vehicle_id=${vehicleId}&sensor_id=${sensorId}&installation_date=${installationDate}`);
        }

        // Delete Vehicle Sensor (AJAX)
        function deleteVehicleSensor(button) {
            const row = button.parentElement.parentElement;
            const vehicleSensorId = row.getAttribute('data-id');

            const xhr = new XMLHttpRequest();
            xhr.open('GET', '?id=' + vehicleSensorId, true); // GET request to same page for deletion
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    alert('Vehicle Sensor deleted successfully!');
                    row.remove();  // Remove the row from the table
                }
            };
            xhr.send();
        }

        // Add New Vehicle Sensor (AJAX)
        document.getElementById('newVehicleSensorForm').onsubmit = function(event) {
            event.preventDefault();
            const vehicleId = document.getElementById('vehicle_id').value;
            const sensorId = document.getElementById('sensor_id').value;
            const installationDate = document.getElementById('installation_date').value;

            const xhr = new XMLHttpRequest();
            xhr.open('POST', '', true); // POST to the same page
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    const newRow = xhr.responseText;  // The new row HTML returned from the server
                    document.getElementById('table-body').innerHTML += newRow;  // Append new row to the table body
                    alert('Vehicle Sensor added successfully!');
                    
                    // Reset the form fields
                    document.getElementById('newVehicleSensorForm').reset();
                }
            };

            xhr.send(`vehicle_id=${vehicleId}&sensor_id=${sensorId}&installation_date=${installationDate}`);
        };
    </script>
</body>
</html>
