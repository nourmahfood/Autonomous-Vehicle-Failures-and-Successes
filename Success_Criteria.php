<?php
require 'db.php';

// Add new criteria functionality (AJAX)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['brief_description'])) {
    $brief_description = $_POST['brief_description'];

    // Insert the new criteria into the database
    $sql = "INSERT INTO success_criteria (brief_description) VALUES (:brief_description)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':brief_description', $brief_description);

    if ($stmt->execute()) {
        // Get the ID of the newly inserted criteria
        $criteria_id = $conn->lastInsertId();
        
        // Return the new row HTML
        echo "<tr data-id='$criteria_id'>
                <td>$criteria_id</td>
                <td>$brief_description</td>
                <td>
                    <button class='btn btn-update' onclick='editCriteria(this)'>Edit</button>
                    <button class='btn btn-delete' onclick='deleteCriteria(this)'>Delete</button>
                </td>
              </tr>";
    } else {
        echo "Error: Could not add criteria.";
    }
    exit;
}

// Delete criteria functionality (AJAX)
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
    $criteria_id = $_GET['id'];

    // Delete the criteria from the database
    $sql = "DELETE FROM success_criteria WHERE criteria_id = :criteria_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':criteria_id', $criteria_id);

    if ($stmt->execute()) {
        echo "Criteria deleted successfully!";
    } else {
        echo "Error: Could not delete criteria.";
    }
    exit;
}

// Fetch all existing criteria
$sql = "SELECT * FROM success_criteria";
$stmt = $conn->prepare($sql);
$stmt->execute();
$success_criteria = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Success Criteria</title>
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

        .back-button {
            display: inline-block;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .back-button:hover {
            background-color: #0056b3;
        }

        #add-form {
            margin-top: 30px;
        }

        input[type="text"] {
            padding: 10px;
            margin: 5px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <h1>Success Criteria</h1>

    <!-- Add New Criteria Form -->
    <div id="add-form">
        <h3>Add New Criteria</h3>
        <form id="newCriteriaForm">
            <input type="text" id="brief_description" placeholder="Brief Description" required>
            <button type="submit" class="btn btn-update">Add Criteria</button>
        </form>
    </div>

    <!-- Table to display success criteria -->
    <table>
        <thead>
            <tr>
                <th>Criteria ID</th>
                <th>Brief Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="table-body">
        <?php foreach ($success_criteria as $row): ?>
            <tr data-id="<?= htmlspecialchars($row['criteria_id']) ?>">
                <td><?= htmlspecialchars($row['criteria_id']) ?></td>
                <td class="brief_description"><?= htmlspecialchars($row['brief_description']) ?></td>
                <td>
                    <button class="btn btn-update" onclick="editCriteria(this)">Edit</button>
                    <button class="btn btn-delete" onclick="deleteCriteria(this)">Delete</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <script>
        // Edit Criteria (Populate form with existing data)
        function editCriteria(button) {
            const row = button.parentElement.parentElement;
            const criteriaId = row.getAttribute('data-id');
            const briefDescription = row.querySelector('.brief_description').textContent;

            // Populate form with existing data
            document.getElementById('brief_description').value = briefDescription;

            // Update form to handle update request instead of adding new
            document.getElementById('newCriteriaForm').onsubmit = function(event) {
                event.preventDefault();
                updateCriteria(criteriaId);
            };
        }

        // Update Criteria (AJAX)
        function updateCriteria(criteriaId) {
            const briefDescription = document.getElementById('brief_description').value;

            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'update_criteria.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    alert('Criteria updated successfully!');
                    location.reload();  // Refresh the table after update
                }
            };
            xhr.send('criteria_id=' + criteriaId + '&brief_description=' + briefDescription);
        }

        // Delete Criteria (AJAX)
        function deleteCriteria(button) {
            const row = button.parentElement.parentElement;
            const criteriaId = row.getAttribute('data-id');

            const xhr = new XMLHttpRequest();
            xhr.open('GET', '?id=' + criteriaId, true);  // Delete request to the same page
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    alert('Criteria deleted successfully!');
                    row.remove();  // Remove the row from the table
                }
            };
            xhr.send();
        }

        // Add New Criteria (AJAX)
        document.getElementById('newCriteriaForm').onsubmit = function(event) {
            event.preventDefault();
            const briefDescription = document.getElementById('brief_description').value;

            const xhr = new XMLHttpRequest();
            xhr.open('POST', '', true); // POST to the same page
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    const newRow = xhr.responseText;  // The new row HTML returned from the server
                    document.getElementById('table-body').innerHTML += newRow;  // Append new row to the table body
                    alert('Criteria added successfully!');
                    
                    // Reset the form fields
                    document.getElementById('newCriteriaForm').reset();
                }
            };

            xhr.send('brief_description=' + briefDescription);
        };
    </script>
</body>
</html>
