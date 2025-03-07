<?php
require 'db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Software Updates</title>
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

        input[type="text"], input[type="date"], textarea {
            padding: 10px;
            margin: 5px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <h1>Software Updates</h1>

    <!-- Add New Software Update Form -->
    <div id="add-form">
        <h3>Add New Software Update</h3>
        <form id="newSoftwareUpdateForm">
            <input type="text" id="software_version_id" placeholder="Software Version ID" required>
            <input type="date" id="update_date" required>
            <textarea id="changelog" placeholder="Changelog" required></textarea>
            <button type="submit" class="btn btn-update">Add Update</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Update ID</th>
                <th>Software Version ID</th>
                <th>Update Date</th>
                <th>Changelog</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="table-body">
        <?php
        // SQL Query to fetch software update data
        $sql = "SELECT * FROM software_updates";
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        // Fetch data and display it in the table
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr data-id='" . $row['update_id'] . "'>
                <td>" . htmlspecialchars($row['update_id']) . "</td>
                <td class='software_version_id'>" . htmlspecialchars($row['software_version_id']) . "</td>
                <td class='update_date'>" . htmlspecialchars($row['update_date']) . "</td>
                <td class='changelog'>" . htmlspecialchars($row['changelog']) . "</td>
                <td>
                    <button class='btn btn-update' onclick='editSoftwareUpdate(this)'>Edit</button>
                    <button class='btn btn-delete' onclick='deleteSoftwareUpdate(this)'>Delete</button>
                </td>
            </tr>";
        }
        ?>
        </tbody>
    </table>

    <script>
        // Edit Software Update Functionality
        function editSoftwareUpdate(button) {
            const row = button.parentElement.parentElement;
            const updateId = row.getAttribute('data-id');
            const softwareVersionId = row.querySelector('.software_version_id').textContent;
            const updateDate = row.querySelector('.update_date').textContent;
            const changelog = row.querySelector('.changelog').textContent;

            // Fill the form with existing data
            document.getElementById('software_version_id').value = softwareVersionId;
            document.getElementById('update_date').value = updateDate;
            document.getElementById('changelog').value = changelog;

            // Update form action to edit software update
            document.getElementById('newSoftwareUpdateForm').onsubmit = function(event) {
                event.preventDefault();
                updateSoftwareUpdate(updateId);
            };
        }

        // Update Software Update Data (AJAX)
        function updateSoftwareUpdate(updateId) {
            const softwareVersionId = document.getElementById('software_version_id').value;
            const updateDate = document.getElementById('update_date').value;
            const changelog = document.getElementById('changelog').value;

            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'update.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    alert('Software Update updated successfully!');
                    location.reload();  // Refresh the table after update
                }
            };
            xhr.send('update_id=' + updateId + '&software_version_id=' + softwareVersionId + '&update_date=' + updateDate + '&changelog=' + changelog);
        }

        // Delete Software Update Data (AJAX)
        function deleteSoftwareUpdate(button) {
            const row = button.parentElement.parentElement;
            const updateId = row.getAttribute('data-id');

            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'delete.php?id=' + updateId, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    alert('Software Update deleted successfully!');
                    row.remove();  // Remove the row from the table
                }
            };
            xhr.send();
        }

        // Add New Software Update (AJAX)
        document.getElementById('newSoftwareUpdateForm').onsubmit = function(event) {
            event.preventDefault();
            const softwareVersionId = document.getElementById('software_version_id').value;
            const updateDate = document.getElementById('update_date').value;
            const changelog = document.getElementById('changelog').value;

            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'add.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    alert('Software Update added successfully!');
                    location.reload();  // Refresh the table after adding
                }
            };
            xhr.send('software_version_id=' + softwareVersionId + '&update_date=' + updateDate + '&changelog=' + changelog);
        };
    </script>
</body>
</html>
