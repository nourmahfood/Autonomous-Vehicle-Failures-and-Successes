<?php
require 'db.php';

// Handle Add, Edit, and Delete functionality

// Add new Test Success functionality (AJAX)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['test_id'])) {
    if (isset($_POST['test_success_id'])) {
        // Handle the Update functionality
        $test_success_id = $_POST['test_success_id'];
        $test_id = $_POST['test_id'];
        $criteria_id = $_POST['criteria_id'];
        $criteria_timestamp = $_POST['criteria_timestamp'];
        $details = $_POST['details'];

        // Update the test success record in the database
        $sql = "UPDATE test_success SET test_id = :test_id, criteria_id = :criteria_id, criteria_timestamp = :criteria_timestamp, details = :details WHERE test_success_id = :test_success_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':test_success_id', $test_success_id);
        $stmt->bindParam(':test_id', $test_id);
        $stmt->bindParam(':criteria_id', $criteria_id);
        $stmt->bindParam(':criteria_timestamp', $criteria_timestamp);
        $stmt->bindParam(':details', $details);

        if ($stmt->execute()) {
            echo "Test Success updated successfully!";
        } else {
            echo "Error: Could not update test success.";
        }
    } else {
        // Handle Add functionality
        $test_id = $_POST['test_id'];
        $criteria_id = $_POST['criteria_id'];
        $criteria_timestamp = $_POST['criteria_timestamp'];
        $details = $_POST['details'];

        // Insert the new test success record into the database
        $sql = "INSERT INTO test_success (test_id, criteria_id, criteria_timestamp, details) 
                VALUES (:test_id, :criteria_id, :criteria_timestamp, :details)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':test_id', $test_id);
        $stmt->bindParam(':criteria_id', $criteria_id);
        $stmt->bindParam(':criteria_timestamp', $criteria_timestamp);
        $stmt->bindParam(':details', $details);

        if ($stmt->execute()) {
            // Get the ID of the newly inserted test success record
            $test_success_id = $conn->lastInsertId();
            
            // Return the new row HTML
            echo "<tr data-id='$test_success_id'>
                    <td>$test_success_id</td>
                    <td>$test_id</td>
                    <td>$criteria_id</td>
                    <td>$criteria_timestamp</td>
                    <td>$details</td>
                    <td>
                        <button class='btn btn-update' onclick='editTestSuccess(this)'>Edit</button>
                        <button class='btn btn-delete' onclick='deleteTestSuccess(this)'>Delete</button>
                    </td>
                  </tr>";
        } else {
            echo "Error: Could not add test success.";
        }
    }
    exit;
}

// Delete Test Success functionality
if (isset($_GET['delete_id'])) {
    $test_success_id = $_GET['delete_id'];

    // Delete the test success record from the database
    $sql = "DELETE FROM test_success WHERE test_success_id = :test_success_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':test_success_id', $test_success_id);

    if ($stmt->execute()) {
        echo "Test Success deleted successfully!";
    } else {
        echo "Error: Could not delete test success.";
    }
    exit;
}

// Fetch all existing test success records
$sql = "SELECT * FROM test_success";
$stmt = $conn->prepare($sql);
$stmt->execute();
$test_success_records = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Success</title>
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
        input[type="text"], input[type="datetime-local"], textarea {
            padding: 10px;
            margin: 5px;
            border-radius: 5px;
            width: 100%;
            max-width: 300px;
        }
    </style>
</head>
<body>
    <h1>Test Success</h1>

    <!-- Add New Test Success Form -->
    <div id="add-form">
        <h3>Add New Test Success</h3>
        <form id="newTestSuccessForm">
            <input type="text" id="test_id" placeholder="Test ID" required>
            <input type="text" id="criteria_id" placeholder="Criteria ID" required>
            <input type="datetime-local" id="criteria_timestamp" placeholder="Timestamp" required>
            <textarea id="details" placeholder="Details" required></textarea>
            <button type="submit" class="btn btn-update">Add Test Success</button>
        </form>
    </div>

    <!-- Table to display test success records -->
    <table>
        <thead>
            <tr>
                <th>Test Success ID</th>
                <th>Test ID</th>
                <th>Criteria ID</th>
                <th>Timestamp</th>
                <th>Details</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="table-body">
        <?php foreach ($test_success_records as $row): ?>
            <tr data-id="<?= htmlspecialchars($row['test_success_id']) ?>">
                <td><?= htmlspecialchars($row['test_success_id']) ?></td>
                <td class="test_id"><?= htmlspecialchars($row['test_id']) ?></td>
                <td class="criteria_id"><?= htmlspecialchars($row['criteria_id']) ?></td>
                <td class="timestamp"><?= htmlspecialchars($row['criteria_timestamp']) ?></td>
                <td class="details"><?= htmlspecialchars($row['details']) ?></td>
                <td>
                    <button class="btn btn-update" onclick="editTestSuccess(this)">Edit</button>
                    <button class="btn btn-delete" onclick="deleteTestSuccess(this)">Delete</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <script>
        // Edit Test Success (Populate form with existing data)
        function editTestSuccess(button) {
            const row = button.parentElement.parentElement;
            const testSuccessId = row.getAttribute('data-id');
            const testId = row.querySelector('.test_id').textContent;
            const criteriaId = row.querySelector('.criteria_id').textContent;
            const timestamp = row.querySelector('.timestamp').textContent;
            const details = row.querySelector('.details').textContent;

            // Populate form with existing data
            document.getElementById('test_id').value = testId;
            document.getElementById('criteria_id').value = criteriaId;
            document.getElementById('criteria_timestamp').value = timestamp;
            document.getElementById('details').value = details;

            // Update form to handle update request instead of adding new
            document.getElementById('newTestSuccessForm').onsubmit = function(event) {
                event.preventDefault();
                updateTestSuccess(testSuccessId);
            };
        }

        // Update Test Success (AJAX)
        function updateTestSuccess(testSuccessId) {
            const testId = document.getElementById('test_id').value;
            const criteriaId = document.getElementById('criteria_id').value;
            const timestamp = document.getElementById('criteria_timestamp').value;
            const details = document.getElementById('details').value;

            const xhr = new XMLHttpRequest();
            xhr.open('POST', '', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    alert('Test Success updated successfully!');
                    location.reload();  // Refresh the table after update
                }
            };
            xhr.send(`test_success_id=${testSuccessId}&test_id=${testId}&criteria_id=${criteriaId}&criteria_timestamp=${timestamp}&details=${details}`);
        }

        // Delete Test Success (AJAX)
        function deleteTestSuccess(button) {
            const row = button.parentElement.parentElement;
            const testSuccessId = row.getAttribute('data-id');

            const xhr = new XMLHttpRequest();
            xhr.open('GET', '?delete_id=' + testSuccessId, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    const response = xhr.responseText;
                    if (response.includes("deleted successfully")) {
                        alert('Test Success deleted successfully!');
                        row.remove();  // Remove the row from the table on the client-side
                    } else {
                        alert('Error: Could not delete Test Success.');
                    }
                }
            };
            xhr.send();
        }

        // Add New Test Success (AJAX)
        document.getElementById('newTestSuccessForm').onsubmit = function(event) {
            event.preventDefault();
            const testId = document.getElementById('test_id').value;
            const criteriaId = document.getElementById('criteria_id').value;
            const timestamp = document.getElementById('criteria_timestamp').value;
            const details = document.getElementById('details').value;

            const xhr = new XMLHttpRequest();
            xhr.open('POST', '', true); // POST to the same page
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    const newRow = xhr.responseText;  // The new row HTML returned from the server
                    document.getElementById('table-body').innerHTML += newRow;  // Append new row to the table body
                    alert('Test Success added successfully!');
                    
                    // Reset the form fields
                    document.getElementById('newTestSuccessForm').reset();
                }
            };

            xhr.send(`test_id=${testId}&criteria_id=${criteriaId}&criteria_timestamp=${timestamp}&details=${details}`);
        };
    </script>
</body>
</html>
