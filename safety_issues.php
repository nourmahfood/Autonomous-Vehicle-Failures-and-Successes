<?php
require 'db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Safety Issues</title>
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

        input[type="text"], input[type="date"], textarea {
            padding: 10px;
            margin: 5px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <h1>Safety Issues</h1>

    <!-- Add New Safety Issue Form -->
    <div id="add-form">
        <h3>Add New Safety Issue</h3>
        <form id="newSafetyIssueForm">
            <input type="text" id="vehicle_id" placeholder="Vehicle ID" required>
            <input type="date" id="issue_date" placeholder="Issue Date" required>
            <textarea id="issue_description" placeholder="Issue Description" required></textarea>
            <input type="text" id="severity_level" placeholder="Severity Level" required>
            <input type="text" id="status" placeholder="Status" required>
            <button type="submit" class="btn btn-update">Add Safety Issue</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Vehicle ID</th>
                <th>Issue Date</th>
                <th>Issue Description</th>
                <th>Severity Level</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="table-body">
        <?php
        // SQL Query to fetch data
        $sql = "SELECT * FROM safety_issues";
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        // Fetch data and display it in table
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr data-id='" . $row['vehicle_id'] . "'>
                <td>" . htmlspecialchars($row['vehicle_id']) . "</td>
                <td class='issue_date'>" . htmlspecialchars($row['issue_date']) . "</td>
                <td class='issue_description'>" . htmlspecialchars($row['issue_description']) . "</td>
                <td class='severity_level'>" . htmlspecialchars($row['severity_level']) . "</td>
                <td class='status'>" . htmlspecialchars($row['status']) . "</td>
                <td>
                    <button class='btn btn-update' onclick='editSafetyIssue(this)'>Edit</button>
                    <button class='btn btn-delete' onclick='deleteSafetyIssue(this)'>Delete</button>
                </td>
            </tr>";
        }
        ?>
        </tbody>
    </table>

    <script>
        // Edit Safety Issue Record
        function editSafetyIssue(button) {
            const row = button.parentElement.parentElement;
            const vehicleId = row.getAttribute('data-id');
            const issueDate = row.querySelector('.issue_date').textContent;
            const issueDescription = row.querySelector('.issue_description').textContent;
            const severityLevel = row.querySelector('.severity_level').textContent;
            const status = row.querySelector('.status').textContent;

            // Populate form with existing data
            document.getElementById('vehicle_id').value = vehicleId;
            document.getElementById('issue_date').value = issueDate;
            document.getElementById('issue_description').value = issueDescription;
            document.getElementById('severity_level').value = severityLevel;
            document.getElementById('status').value = status;

            // Change form to update record
            document.getElementById('newSafetyIssueForm').onsubmit = function(event) {
                event.preventDefault();
                updateSafetyIssue(vehicleId);
            };
        }

        // Update Safety Issue Record (AJAX)
        function updateSafetyIssue(vehicleId) {
            const issueDate = document.getElementById('issue_date').value;
            const issueDescription = document.getElementById('issue_description').value;
            const severityLevel = document.getElementById('severity_level').value;
            const status = document.getElementById('status').value;

            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'update.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    alert('Safety issue updated successfully!');
                    location.reload();  // Refresh the table after update
                }
            };
            xhr.send('vehicle_id=' + vehicleId + '&issue_date=' + issueDate + '&issue_description=' + issueDescription + '&severity_level=' + severityLevel + '&status=' + status);
        }

        // Delete Safety Issue Record (AJAX)
        function deleteSafetyIssue(button) {
            const row = button.parentElement.parentElement;
            const vehicleId = row.getAttribute('data-id');

            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'delete.php?id=' + vehicleId, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    alert('Safety issue deleted successfully!');
                    row.remove();  // Remove the row from the table
                }
            };
            xhr.send();
        }

        // Add New Safety Issue Record
        document.getElementById('newSafetyIssueForm').onsubmit = function(event) {
            event.preventDefault();
            const vehicleId = document.getElementById('vehicle_id').value;
            const issueDate = document.getElementById('issue_date').value;
            const issueDescription = document.getElementById('issue_description').value;
            const severityLevel = document.getElementById('severity_level').value;
            const status = document.getElementById('status').value;

            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'add.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    alert('Safety issue added successfully!');
                    location.reload();  // Refresh the table after adding
                }
            };
            xhr.send('vehicle_id=' + vehicleId + '&issue_date=' + issueDate + '&issue_description=' + issueDescription + '&severity_level=' + severityLevel + '&status=' + status);
        };
    </script>
</body>
</html>
