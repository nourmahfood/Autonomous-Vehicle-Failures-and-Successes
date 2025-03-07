<?php
require 'db.php';

// Add new personnel functionality
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['model_name'])) {
    $model_name = $_POST['model_name'];
    $the_role = $_POST['the_role'];
    $contact_info = $_POST['contact_info'];

    // Insert the new personnel into the database
    $sql = "INSERT INTO personnel (model_name, the_role, contact_info)
            VALUES (:model_name, :the_role, :contact_info)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':model_name', $model_name);
    $stmt->bindParam(':the_role', $the_role);
    $stmt->bindParam(':contact_info', $contact_info);

    if ($stmt->execute()) {
        // Get the ID of the newly inserted personnel
        $personnel_id = $conn->lastInsertId();

        // Return the new row HTML
        echo "<tr data-id='$personnel_id'>
                <td>$personnel_id</td>
                <td>$model_name</td>
                <td>$the_role</td>
                <td>$contact_info</td>
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

// Fetch personnel records for display
$sql = "SELECT * FROM personnel";
$stmt = $conn->prepare($sql);
$stmt->execute();
$personnel = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Delete personnel functionality
if (isset($_GET['id'])) {
    $personnel_id = $_GET['id'];

    // SQL query to delete the personnel record
    $sql = "DELETE FROM personnel WHERE personnel_id = :personnel_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':personnel_id', $personnel_id);

    if ($stmt->execute()) {
        echo "Success";
    } else {
        echo "Error: Could not delete the record.";
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

        input[type="text"], input[type="email"] {
            padding: 10px;
            margin: 5px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <h2>Personnel List</h2>

    <!-- Add New Personnel Form -->
    <div id="add-form">
        <h3>Add New Personnel</h3>
        <form id="newPersonnelForm">
            <input type="text" id="model_name" placeholder="Model Name" required>
            <input type="text" id="the_role" placeholder="Role" required>
            <input type="email" id="contact_info" placeholder="Contact Info" required>
            <button type="submit" class="btn btn-update">Add Personnel</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Personnel ID</th>
                <th>Model Name</th>
                <th>Role</th>
                <th>Contact Info</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="table-body">
        <?php foreach ($personnel as $row): ?>
            <tr data-id="<?= htmlspecialchars($row['personnel_id']) ?>">
                <td><?= htmlspecialchars($row['personnel_id']) ?></td>
                <td class="model_name"><?= htmlspecialchars($row['model_name']) ?></td>
                <td class="the_role"><?= htmlspecialchars($row['the_role']) ?></td>
                <td class="contact_info"><?= htmlspecialchars($row['contact_info']) ?></td>
                <td>
                    <button class="btn btn-update" onclick="editPersonnel(this)">Edit</button>
                    <button class="btn btn-delete" onclick="deletePersonnel(this)">Delete</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <script>
        // Edit Personnel Record
        function editPersonnel(button) {
            const row = button.parentElement.parentElement;
            const personnelId = row.getAttribute('data-id');
            const modelName = row.querySelector('.model_name').textContent;
            const role = row.querySelector('.the_role').textContent;
            const contactInfo = row.querySelector('.contact_info').textContent;

            // Populate form with existing data
            document.getElementById('model_name').value = modelName;
            document.getElementById('the_role').value = role;
            document.getElementById('contact_info').value = contactInfo;

            // Change form to update record
            document.getElementById('newPersonnelForm').onsubmit = function(event) {
                event.preventDefault();
                updatePersonnel(personnelId);
            };
        }

        // Update Personnel Record (AJAX)
        function updatePersonnel(personnelId) {
            const modelName = document.getElementById('model_name').value;
            const role = document.getElementById('the_role').value;
            const contactInfo = document.getElementById('contact_info').value;

            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'update_personnel.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    alert('Personnel record updated successfully!');
                    location.reload();  // Refresh the table after update
                }
            };
            xhr.send('personnel_id=' + personnelId + '&model_name=' + modelName + '&the_role=' + role + '&contact_info=' + contactInfo);
        }

        // Delete Personnel Record (AJAX)
        function deletePersonnel(button) {
            const row = button.parentElement.parentElement;
            const personnelId = row.getAttribute('data-id');

            const xhr = new XMLHttpRequest();
            xhr.open('GET', '?id=' + personnelId, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    if (xhr.responseText === "Success") {
                        alert('Personnel record deleted successfully!');
                        row.remove();  // Remove the row from the table
                    } else {
                        alert('Error: Could not delete the record.');
                    }
                }
            };
            xhr.send();
        }

        // Add New Personnel Record
        document.getElementById('newPersonnelForm').onsubmit = function(event) {
            event.preventDefault();
            const modelName = document.getElementById('model_name').value;
            const role = document.getElementById('the_role').value;
            const contactInfo = document.getElementById('contact_info').value;

            const xhr = new XMLHttpRequest();
            xhr.open('POST', '', true); // POST to the same page
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    const newRow = xhr.responseText;  // The new row HTML returned from the server
                    document.getElementById('table-body').innerHTML += newRow;  // Append new row to the table body
                    alert('Personnel record added successfully!');
                    
                    // Reset the form fields
                    document.getElementById('newPersonnelForm').reset();
                }
            };

            xhr.send('model_name=' + modelName + '&the_role=' + role + '&contact_info=' + contactInfo);
        };
    </script>
</body>
</html>
