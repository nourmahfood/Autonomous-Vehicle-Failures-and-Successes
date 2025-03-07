<?php
require 'db.php';

// Handle Add new scenario functionality (AJAX)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['brief_description'])) {
    $brief_description = $_POST['brief_description'];
    $the_location = $_POST['the_location'];
    $weather_conditions = $_POST['weather_conditions'];
    $traffic_conditions = $_POST['traffic_conditions'];
    $duration = $_POST['duration'];

    // Insert the new scenario into the database
    $sql = "INSERT INTO test_scenarios (brief_description, the_location, weather_conditions, traffic_conditions, duration) 
            VALUES (:brief_description, :the_location, :weather_conditions, :traffic_conditions, :duration)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':brief_description', $brief_description);
    $stmt->bindParam(':the_location', $the_location);
    $stmt->bindParam(':weather_conditions', $weather_conditions);
    $stmt->bindParam(':traffic_conditions', $traffic_conditions);
    $stmt->bindParam(':duration', $duration);

    if ($stmt->execute()) {
        // Get the ID of the newly inserted scenario
        $scenario_id = $conn->lastInsertId();
        
        // Return the new row HTML
        echo "<tr data-id='$scenario_id'>
                <td>$scenario_id</td>
                <td>$brief_description</td>
                <td>$the_location</td>
                <td>$weather_conditions</td>
                <td>$traffic_conditions</td>
                <td>$duration</td>
                <td>
                    <button class='btn btn-update' onclick='editScenario(this)'>Edit</button>
                    <button class='btn btn-delete' onclick='deleteScenario(this)'>Delete</button>
                </td>
              </tr>";
    } else {
        echo "Error: Could not add scenario.";
    }
    exit;
}

// Handle Delete scenario functionality (AJAX)
if (isset($_GET['delete_id'])) {
    $scenario_id = $_GET['delete_id'];

    // Prepare and execute delete statement
    $sql = "DELETE FROM test_scenarios WHERE scenario_id = :scenario_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':scenario_id', $scenario_id);

    if ($stmt->execute()) {
        echo "Scenario deleted successfully.";
    } else {
        echo "Error: Could not delete scenario.";
    }
    exit;
}

// Fetch all existing test scenarios
$sql = "SELECT * FROM test_scenarios";
$stmt = $conn->prepare($sql);
$stmt->execute();
$test_scenarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Scenarios</title>
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

        input[type="text"], input[type="number"] {
            padding: 10px;
            margin: 5px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <h1>Test Scenarios</h1>

    <!-- Add New Scenario Form -->
    <div id="add-form">
        <h3>Add New Test Scenario</h3>
        <form id="newScenarioForm">
            <input type="text" id="brief_description" placeholder="Brief Description" required>
            <input type="text" id="the_location" placeholder="Location" required>
            <input type="text" id="weather_conditions" placeholder="Weather Conditions" required>
            <input type="text" id="traffic_conditions" placeholder="Traffic Conditions" required>
            <input type="number" id="duration" placeholder="Duration (minutes)" required>
            <button type="submit" class="btn btn-update">Add Scenario</button>
        </form>
    </div>

    <!-- Table to display test scenarios -->
    <table>
        <thead>
            <tr>
                <th>Scenario ID</th>
                <th>Brief Description</th>
                <th>Location</th>
                <th>Weather Conditions</th>
                <th>Traffic Conditions</th>
                <th>Duration (minutes)</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="table-body">
        <?php foreach ($test_scenarios as $row): ?>
            <tr data-id="<?= htmlspecialchars($row['scenario_id']) ?>">
                <td><?= htmlspecialchars($row['scenario_id']) ?></td>
                <td class="brief_description"><?= htmlspecialchars($row['brief_description']) ?></td>
                <td class="location"><?= htmlspecialchars($row['the_location']) ?></td>
                <td class="weather"><?= htmlspecialchars($row['weather_conditions']) ?></td>
                <td class="traffic"><?= htmlspecialchars($row['traffic_conditions']) ?></td>
                <td class="duration"><?= htmlspecialchars($row['duration']) ?></td>
                <td>
                    <button class="btn btn-update" onclick="editScenario(this)">Edit</button>
                    <button class="btn btn-delete" onclick="deleteScenario(this)">Delete</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <script>
        // Edit Scenario (Populate form with existing data)
        function editScenario(button) {
            const row = button.parentElement.parentElement;
            const scenarioId = row.getAttribute('data-id');
            const briefDescription = row.querySelector('.brief_description').textContent;
            const location = row.querySelector('.location').textContent;
            const weather = row.querySelector('.weather').textContent;
            const traffic = row.querySelector('.traffic').textContent;
            const duration = row.querySelector('.duration').textContent;

            // Populate form with existing data
            document.getElementById('brief_description').value = briefDescription;
            document.getElementById('the_location').value = location;
            document.getElementById('weather_conditions').value = weather;
            document.getElementById('traffic_conditions').value = traffic;
            document.getElementById('duration').value = duration;

            // Update form to handle update request instead of adding new
            document.getElementById('newScenarioForm').onsubmit = function(event) {
                event.preventDefault();
                updateScenario(scenarioId);
            };
        }

        // Update Scenario (AJAX)
        function updateScenario(scenarioId) {
            const briefDescription = document.getElementById('brief_description').value;
            const location = document.getElementById('the_location').value;
            const weatherConditions = document.getElementById('weather_conditions').value;
            const trafficConditions = document.getElementById('traffic_conditions').value;
            const duration = document.getElementById('duration').value;

            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'update_scenario.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    alert('Scenario updated successfully!');
                    location.reload();  // Refresh the table after update
                }
            };
            xhr.send(`scenario_id=${scenarioId}&brief_description=${briefDescription}&the_location=${location}&weather_conditions=${weatherConditions}&traffic_conditions=${trafficConditions}&duration=${duration}`);
        }

        // Delete Scenario (AJAX)
        function deleteScenario(button) {
            const row = button.parentElement.parentElement;
            const scenarioId = row.getAttribute('data-id');

            // Confirm deletion
            if (!confirm('Are you sure you want to delete this scenario?')) {
                return;
            }

            const xhr = new XMLHttpRequest();
            xhr.open('GET', '?delete_id=' + scenarioId, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    // After deletion, remove the row from the table
                    alert('Scenario deleted successfully!');
                    row.remove();
                }
            };
            xhr.send();
        }

        // Add New Scenario (AJAX)
        document.getElementById('newScenarioForm').onsubmit = function(event) {
            event.preventDefault();
            const briefDescription = document.getElementById('brief_description').value;
            const location = document.getElementById('the_location').value;
            const weatherConditions = document.getElementById('weather_conditions').value;
            const trafficConditions = document.getElementById('traffic_conditions').value;
            const duration = document.getElementById('duration').value;

            const xhr = new XMLHttpRequest();
            xhr.open('POST', '', true); // POST to the same page
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    const newRow = xhr.responseText;  // The new row HTML returned from the server
                    document.getElementById('table-body').innerHTML += newRow;  // Append new row to the table body
                    alert('Scenario added successfully!');
                    
                    // Reset the form fields
                    document.getElementById('newScenarioForm').reset();
                }
            };

            xhr.send(`brief_description=${briefDescription}&the_location=${location}&weather_conditions=${weatherConditions}&traffic_conditions=${trafficConditions}&duration=${duration}`);
        };
    </script>
</body>
</html>
