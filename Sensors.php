<?php
require 'db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sensors</title>
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

        #add-form {
            margin-top: 30px;
        }

        input[type="text"], input[type="number"], input[type="text"], textarea {
            padding: 10px;
            margin: 5px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <h1>Sensors</h1>

    <!-- Add New Sensor Form -->
    <div id="add-form">
        <h3>Add New Sensor</h3>
        <form id="newSensorForm">
            <input type="text" id="sensor_type" placeholder="Sensor Type" required>
            <input type="text" id="manufacture" placeholder="Manufacturer" required>
            <input type="text" id="model_name" placeholder="Model Name" required>
            <input type="text" id="sensor_range" placeholder="Sensor Range" required>
            <button type="submit" class="btn btn-update">Add Sensor</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Sensor ID</th>
                <th>Sensor Type</th>
                <th>Manufacturer</th>
                <th>Model Name</th>
                <th>Sensor Range</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="table-body">
        <?php
        // SQL Query to fetch sensor data
        $sql = "SELECT * FROM sensors";
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        // Fetch data and display it in table
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr data-id='" . $row['sensor_id'] . "'>
                <td>" . htmlspecialchars($row['sensor_id']) . "</td>
                <td class='sensor_type'>" . htmlspecialchars($row['sensor_type']) . "</td>
                <td class='manufacture'>" . htmlspecialchars($row['manufacture']) . "</td>
                <td class='model_name'>" . htmlspecialchars($row['model_name']) . "</td>
                <td class='sensor_range'>" . htmlspecialchars($row['sensor_range']) . "</td>
                <td>
                    <button class='btn btn-update' onclick='editSensor(this)'>Edit</button>
                    <button class='btn btn-delete' onclick='deleteSensor(this)'>Delete</button>
                </td>
            </tr>";
        }
        ?>
        </tbody>
    </table>

    <script>
        // Edit Sensor Functionality
        function editSensor(button) {
            const row = button.parentElement.parentElement;
            const sensorId = row.getAttribute('data-id');
            const sensorType = row.querySelector('.sensor_type').textContent;
            const manufacture = row.querySelector('.manufacture').textContent;
            const modelName = row.querySelector('.model_name').textContent;
            const sensorRange = row.querySelector('.sensor_range').textContent;

            // Fill the form with existing data
            document.getElementById('sensor_type').value = sensorType;
            document.getElementById('manufacture').value = manufacture;
            document.getElementById('model_name').value = modelName;
            document.getElementById('sensor_range').value = sensorRange;

            // Update form action to edit sensor data
            document.getElementById('newSensorForm').onsubmit = function(event) {
                event.preventDefault();
                updateSensor(sensorId);
            };
        }

        // Update Sensor Data (AJAX)
        function updateSensor(sensorId) {
            const sensorType = document.getElementById('sensor_type').value;
            const manufacture = document.getElementById('manufacture').value;
            const modelName = document.getElementById('model_name').value;
            const sensorRange = document.getElementById('sensor_range').value;

            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'update.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    alert('Sensor updated successfully!');
                    location.reload();  // Refresh the table after update
                }
            };
            xhr.send('sensor_id=' + sensorId + '&sensor_type=' + sensorType + '&manufacture=' + manufacture + '&model_name=' + modelName + '&sensor_range=' + sensorRange);
        }

        // Delete Sensor Data (AJAX)
        function deleteSensor(button) {
            const row = button.parentElement.parentElement;
            const sensorId = row.getAttribute('data-id');

            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'delete.php?id=' + sensorId, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    alert('Sensor deleted successfully!');
                    row.remove();  // Remove the row from the table
                }
            };
            xhr.send();
        }

        // Add New Sensor (AJAX)
        document.getElementById('newSensorForm').onsubmit = function(event) {
            event.preventDefault();
            const sensorType = document.getElementById('sensor_type').value;
            const manufacture = document.getElementById('manufacture').value;
            const modelName = document.getElementById('model_name').value;
            const sensorRange = document.getElementById('sensor_range').value;

            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'add.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    alert('Sensor added successfully!');
                    location.reload();  // Refresh the table after adding
                }
            };
            xhr.send('sensor_type=' + sensorType + '&manufacture=' + manufacture + '&model_name=' + modelName + '&sensor_range=' + sensorRange);
        };
    </script>
</body>
</html>
