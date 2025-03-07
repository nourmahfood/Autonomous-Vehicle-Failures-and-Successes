<?php
require 'db.php';

// Add new Personnel functionality (AJAX)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['test_id'])) {
    $test_id = $_POST['test_id'];
    $personnel_id = $_POST['personnel_id'];
    $assigned_role = $_POST['assigned_role'];

    // Insert the new personnel record into the database
    $sql = "INSERT INTO test_personnel (test_id, personnel_id, assigned_role) 
            VALUES (:test_id, :personnel_id, :assigned_role)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':test_id', $test_id);
    $stmt->bindParam(':personnel_id', $personnel_id);
    $stmt->bindParam(':assigned_role', $assigned_role);

    if ($stmt->execute()) {
        // Get the ID of the newly inserted personnel record
        $test_personnel_id = $conn->lastInsertId();
        
        // Return the new row HTML
        echo "<tr data-id='$test_personnel_id'>
                <td>$test_personnel_id</td>
                <td>$test_id</td>
                <td>$personnel_id</td>
                <td>$assigned_role</td>
                <td>
                    <button class='btn btn-update' onclick='editPersonnel(this)'>Edit</button>
                    <button class='btn btn-delete' onclick='deletePersonnel(this)'>Delete</button>
                </td>
              </tr>";
    } else {
        echo "Error: Could not add personnel.";
    }
    exit;
}

// Fetch all existing personnel records
$sql = "SELECT * FROM test_personnel";
$stmt = $conn->prepare($sql);
$stmt->execute();
$personnel_records = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Delete Personnel functionality
if (isset($_GET['id'])) {
    $test_personnel_id = $_GET['id'];

    // Delete the personnel record from the database
    $sql = "DELETE FROM test_personnel WHERE test_personnel_id = :test_personnel_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':test_personnel_id', $test_personnel_id);

    if ($stmt->execute()) {
        echo "Personnel deleted successfully!";
    } else {
        echo "Error: Could not delete personnel.";
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personnel</title>
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
        input[type="text"] {
            padding: 10px;
            margin: 5px;
            border-radius: 5px;
            width: 100%;
            max-width: 300px;
        }
    </style>
</head>
<body>
    <h1>Personnel Assigned to Vehicle Tests</h1>

    <!-- Add New Personnel Form -->
    <div id="add-form">
        <h3>Add New Personnel</h3>
        <form id="newPersonnelForm">
            <input type="text" id="test_id" placeholder="Test ID" required>
            <input type="text" id="personnel_id" placeholder="Personnel ID" required>
            <input type="text" id="assigned_role" placeholder="Assigned Role" required>
            <button type="submit" class="btn btn-update">Add Personnel</button>
        </form>
    </div>

    <!-- Table to display personnel records -->
    <table>
        <thead>
            <tr>
                <th>Test Personnel ID</th>
                <th>Test ID</th>
                <th>Personnel ID</th>
                <th>Assigned Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="table-body">
        <?php foreach ($personnel_records as $row): ?>
            <tr data-id="<?= htmlspecialchars($row['test_personnel_id']) ?>">
                <td><?= htmlspecialchars($row['test_personnel_id']) ?></td>
                <td class="test_id"><?= htmlspecialchars($row['test_id']) ?></td>
                <td class="personnel_id"><?= htmlspecialchars($row['personnel_id']) ?></td>
                <td class="assigned_role"><?= htmlspecialchars($row['assigned_role']) ?></td>
                <td>
                    <button class="btn btn-update" onclick="editPersonnel(this)">Edit</button>
                    <button class="btn btn-delete" onclick="deletePersonnel(this)">Delete</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <script>
        // Edit Personnel (Populate form with existing data)
        function editPersonnel(button) {
            const row = button.parentElement.parentElement;
            const testPersonnelId = row.getAttribute('data-id');
            const testId = row.querySelector('.test_id').textContent;
            const personnelId = row.querySelector('.personnel_id').textContent;
            const assignedRole = row.querySelector('.assigned_role').textContent;

            // Populate form with existing data
            document.getElementById('test_id').value = testId;
            document.getElementById('personnel_id').value = personnelId;
            document.getElementById('assigned_role').value = assignedRole;

            // Update form to handle update request instead of adding new
            document.getElementById('newPersonnelForm').onsubmit = function(event) {
                event.preventDefault();
                updatePersonnel(testPersonnelId);
            };
        }

        // Update Personnel (AJAX)
        function updatePersonnel(testPersonnelId) {
            const testId = document.getElementById('test_id').value;
            const personnelId = document.getElementById('personnel_id').value;
            const assignedRole = document.getElementById('assigned_role').value;

            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'update_personnel.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    alert('Personnel updated successfully!');
                    location.reload();  // Refresh the table after update
                }
            };
            xhr.send(`test_personnel_id=${testPersonnelId}&test_id=${testId}&personnel_id=${personnelId}&assigned_role=${assignedRole}`);
        }

        // Delete Personnel (AJAX)
        function deletePersonnel(button) {
            const row = button.parentElement.parentElement;
            const testPersonnelId = row.getAttribute('data-id');

            const xhr = new XMLHttpRequest();
            xhr.open('GET', '?id=' + testPersonnelId, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    alert('Personnel deleted successfully!');
                    row.remove();  // Remove the row from the table
                }
            };
            xhr.send();
        }

        // Add New Personnel (AJAX)
        document.getElementById('newPersonnelForm').onsubmit = function(event) {
            event.preventDefault();
            const testId = document.getElementById('test_id').value;
            const personnelId = document.getElementById('personnel_id').value;
            const assignedRole = document.getElementById('assigned_role').value;

            const xhr = new XMLHttpRequest();
            xhr.open('POST', '', true); // POST to the same page
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    const newRow = xhr.responseText;  // The new row HTML returned from the server
                    document.getElementById('table-body').innerHTML += newRow;  // Append new row to the table body
                    alert('Personnel added successfully!');
                    
                    // Reset the form fields
                    document.getElementById('newPersonnelForm').reset();
                }
            };

            xhr.send(`test_id=${testId}&personnel_id=${personnelId}&assigned_role=${assignedRole}`);
        };
    </script>
</body>
</html>
