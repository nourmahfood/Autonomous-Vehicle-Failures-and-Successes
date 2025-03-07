<?php
require 'db.php';

// Add new Vehicle Test functionality (AJAX)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['vehicle_id'])) {
    $vehicle_id = $_POST['vehicle_id'];
    $scenario_id = $_POST['scenario_id'];
    $test_date = $_POST['test_date'];
    $outcome = $_POST['outcome'];
    $duration = $_POST['duration'];

    // Insert the new vehicle test record into the database
    $sql = "INSERT INTO vehicle_tests (vehicle_id, scenario_id, test_date, outcome, duration) 
            VALUES (:vehicle_id, :scenario_id, :test_date, :outcome, :duration)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':vehicle_id', $vehicle_id);
    $stmt->bindParam(':scenario_id', $scenario_id);
    $stmt->bindParam(':test_date', $test_date);
    $stmt->bindParam(':outcome', $outcome);
    $stmt->bindParam(':duration', $duration);

    if ($stmt->execute()) {
        // Get the ID of the newly inserted vehicle test record
        $test_id = $conn->lastInsertId();
        
        // Return the new row HTML
        echo "<tr data-id='$test_id'>
                <td>$test_id</td>
                <td>$vehicle_id</td>
                <td>$scenario_id</td>
                <td>$test_date</td>
                <td>$outcome</td>
                <td>$duration</td>
                <td>
                    <button class='btn btn-update' onclick='editVehicleTest(this)'>Edit</button>
                    <button class='btn btn-delete' onclick='deleteVehicleTest(this)'>Delete</button>
                </td>
              </tr>";
    } else {
        echo "Error: Could not add vehicle test.";
    }
    exit;
}

// Fetch all existing vehicle tests
$sql = "SELECT * FROM vehicle_tests";
$stmt = $conn->prepare($sql);
$stmt->execute();
$vehicle_tests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Test</title>
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
        input[type="text"], input[type="date"], input[type="number"] {
            padding: 10px;
            margin: 5px;
            border-radius: 5px;
            width: 100%;
            max-width: 300px;
        }
    </style>
</head>
<body>
    <h1>Vehicle Test</h1>

    <!-- Add New Vehicle Test Form -->
    <div id="add-form">
        <h3>Add New Vehicle Test</h3>
        <form id="newVehicleTestForm">
            <input type="text" id="vehicle_id" placeholder="Vehicle ID" required>
            <input type="text" id="scenario_id" placeholder="Scenario ID" required>
            <input type="date" id="test_date" placeholder="Test Date" required>
            <input type="text" id="outcome" placeholder="Outcome" required>
            <input type="number" id="duration" placeholder="Duration (minutes)" required>
            <button type="submit" class="btn btn-update">Add Vehicle Test</button>
        </form>
    </div>

    <!-- Table to display vehicle test records -->
    <h2>Status</h2>
    <table>
        <thead>
            <tr>
                <th>Test ID</th>
                <th>Vehicle ID</th>
                <th>Scenario ID</th>
                <th>Test Date</th>
                <th>Outcome</th>
                <th>Duration (minutes)</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="table-body">
        <?php foreach ($vehicle_tests as $row): ?>
            <tr data-id="<?= htmlspecialchars($row['test_id']) ?>">
                <td><?= htmlspecialchars($row['test_id']) ?></td>
                <td class="vehicle_id"><?= htmlspecialchars($row['vehicle_id']) ?></td>
                <td class="scenario_id"><?= htmlspecialchars($row['scenario_id']) ?></td>
                <td class="test_date"><?= htmlspecialchars($row['test_date']) ?></td>
                <td class="outcome"><?= htmlspecialchars($row['outcome']) ?></td>
                <td class="duration"><?= htmlspecialchars($row['duration']) ?></td>
                <td>
                    <button class="btn btn-update" onclick="editVehicleTest(this)">Edit</button>
                    <button class="btn btn-delete" onclick="deleteVehicleTest(this)">Delete</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <script>
        // Edit Vehicle Test (Populate form with existing data)
        function editVehicleTest(button) {
            const row = button.parentElement.parentElement;
            const testId = row.getAttribute('data-id');
            const vehicleId = row.querySelector('.vehicle_id').textContent;
            const scenarioId = row.querySelector('.scenario_id').textContent;
            const testDate = row.querySelector('.test_date').textContent;
            const outcome = row.querySelector('.outcome').textContent;
            const duration = row.querySelector('.duration').textContent;

            // Populate form with existing data
            document.getElementById('vehicle_id').value = vehicleId;
            document.getElementById('scenario_id').value = scenarioId;
            document.getElementById('test_date').value = testDate;
            document.getElementById('outcome').value = outcome;
            document.getElementById('duration').value = duration;

            // Update form to handle update request instead of adding new
            document.getElementById('newVehicleTestForm').onsubmit = function(event) {
                event.preventDefault();
                updateVehicleTest(testId);
            };
        }

        // Update Vehicle Test (AJAX)
        function updateVehicleTest(testId) {
            const vehicleId = document.getElementById('vehicle_id').value;
            const scenarioId = document.getElementById('scenario_id').value;
            const testDate = document.getElementById('test_date').value;
            const outcome = document.getElementById('outcome').value;
            const duration = document.getElementById('duration').value;

            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'update_vehicle_test.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    alert('Vehicle Test updated successfully!');
                    location.reload();  // Refresh the table after update
                }
            };
            xhr.send(`test_id=${testId}&vehicle_id=${vehicleId}&scenario_id=${scenarioId}&test_date=${testDate}&outcome=${outcome}&duration=${duration}`);
        }

        // Delete Vehicle Test (AJAX)
        function deleteVehicleTest(button) {
            const row = button.parentElement.parentElement;
            const testId = row.getAttribute('data-id');

            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'delete_vehicle_test.php?id=' + testId, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    alert('Vehicle Test deleted successfully!');
                    row.remove();  // Remove the row from the table
                }
            };
            xhr.send();
        }

        // Add New Vehicle Test (AJAX)
        document.getElementById('newVehicleTestForm').onsubmit = function(event) {
            event.preventDefault();
            const vehicleId = document.getElementById('vehicle_id').value;
            const scenarioId = document.getElementById('scenario_id').value;
            const testDate = document.getElementById('test_date').value;
            const outcome = document.getElementById('outcome').value;
            const duration = document.getElementById('duration').value;

            const xhr = new XMLHttpRequest();
            xhr.open('POST', '', true); // POST to the same page
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    const newRow = xhr.responseText;  // The new row HTML returned from the server
                    document.getElementById('table-body').innerHTML += newRow;  // Append new row to the table body
                    alert('Vehicle Test added successfully!');
                    
                    // Reset the form fields
                    document.getElementById('newVehicleTestForm').reset();
                }
            };

            xhr.send(`vehicle_id=${vehicleId}&scenario_id=${scenarioId}&test_date=${testDate}&outcome=${outcome}&duration=${duration}`);
        };
    </script>
</body>
</html>
