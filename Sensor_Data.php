<?php
require 'db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sensor Data</title>
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

        input[type="text"], input[type="date"], input[type="number"], textarea {
            padding: 10px;
            margin: 5px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <h1>Sensor Data</h1>

    <!-- Add New Sensor Data Form -->
    <div id="add-form">
        <h3>Add New Sensor Data</h3>
        <form id="newSensorDataForm">
            <input type="text" id="test_id" placeholder="Test ID" required>
            <input type="text" id="sensor_id" placeholder="Sensor ID" required>
            <input type="datetime-local" id="sensor_timestamp" placeholder="Timestamp" required>
            <input type="text" id="sensor_type" placeholder="Sensor Type" required>
            <input type="text" id="sensor_unit" placeholder="Unit" required>
            <button type="submit" class="btn btn-update">Add Sensor Data</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Data ID</th>
                <th>Test ID</th>
                <th>Sensor ID</th>
                <th>Timestamp</th>
                <th>Sensor Type</th>
                <th>Unit</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="table-body">
        <?php
        // SQL Query to fetch data
        $sql = "SELECT * FROM sensor_data";
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        // Fetch data and display it in table
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr data-id='" . $row['data_id'] . "'>
                <td>" . htmlspecialchars($row['data_id']) . "</td>
                <td class='test_id'>" . htmlspecialchars($row['test_id']) . "</td>
                <td class='sensor_id'>" . htmlspecialchars($row['sensor_id']) . "</td>
                <td class='sensor_timestamp'>" . htmlspecialchars($row['sensor_timestamp']) . "</td>
                <td class='sensor_type'>" . htmlspecialchars($row['sensor_type']) . "</td>
                <td class='sensor_unit'>" . htmlspecialchars($row['sensor_unit']) . "</td>
                <td>
                    <button class='btn btn-update' onclick='editSensorData(this)'>Edit</button>
                    <button class='btn btn-delete' onclick='deleteSensorData(this)'>Delete</button>
                </td>
            </tr>";
        }
        ?>
        </tbody>
    </table>

    <script>
        // Edit Sensor Data Record
        function editSensorData(button) {
            const row = button.parentElement.parentElement;
            const dataId = row.getAttribute('data-id');
            const testId = row.querySelector('.test_id').textContent;
            const sensorId = row.querySelector('.sensor_id').textContent;
            const sensorTimestamp = row.querySelector('.sensor_timestamp').textContent;
            const sensorType = row.querySelector('.sensor_type').textContent;
            const sensorUnit = row.querySelector('.sensor_unit').textContent;

            // Populate form with existing data
            document.getElementById('test_id').value = testId;
            document.getElementById('sensor_id').value = sensorId;
            document.getElementById('sensor_timestamp').value = sensorTimestamp;
            document.getElementById('sensor_type').value = sensorType;
            document.getElementById('sensor_unit').value = sensorUnit;

            // Change form to update record
            document.getElementById('newSensorDataForm').onsubmit = function(event) {
                event.preventDefault();
                updateSensorData(dataId);
            };
        }

        // Update Sensor Data Record (AJAX)
        function updateSensorData(dataId) {
            const testId = document.getElementById('test_id').value;
            const sensorId = document.getElementById('sensor_id').value;
            const sensorTimestamp = document.getElementById('sensor_timestamp').value;
            const sensorType = document.getElementById('sensor_type').value;
            const sensorUnit = document.getElementById('sensor_unit').value;

            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'update.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    alert('Sensor data updated successfully!');
                    location.reload();  // Refresh the table after update
                }
            };
            xhr.send('data_id=' + dataId + '&test_id=' + testId + '&sensor_id=' + sensorId + '&sensor_timestamp=' + sensorTimestamp + '&sensor_type=' + sensorType + '&sensor_unit=' + sensorUnit);
        }

        // Delete Sensor Data Record (AJAX)
        function deleteSensorData(button) {
            const row = button.parentElement.parentElement;
            const dataId = row.getAttribute('data-id');

            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'delete.php?id=' + dataId, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    alert('Sensor data deleted successfully!');
                    row.remove();  // Remove the row from the table
                }
            };
            xhr.send();
        }

        // Add New Sensor Data Record
        document.getElementById('newSensorDataForm').onsubmit = function(event) {
            event.preventDefault();
            const testId = document.getElementById('test_id').value;
            const sensorId = document.getElementById('sensor_id').value;
            const sensorTimestamp = document.getElementById('sensor_timestamp').value;
            const sensorType = document.getElementById('sensor_type').value;
            const sensorUnit = document.getElementById('sensor_unit').value;

            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'add.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    alert('Sensor data added successfully!');
                    location.reload();  // Refresh the table after adding
                }
            };
            xhr.send('test_id=' + testId + '&sensor_id=' + sensorId + '&sensor_timestamp=' + sensorTimestamp + '&sensor_type=' + sensorType + '&sensor_unit=' + sensorUnit);
        };
    </script>
</body>
</html>
