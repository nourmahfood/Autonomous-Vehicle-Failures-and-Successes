<?php
require 'db.php';

if (isset($_GET['delete_id'])) {
    // Handle delete request
    $versionId = $_GET['delete_id'];
    $sql = "DELETE FROM software_versions WHERE software_version_id = :version_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':version_id', $versionId);
    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'error';
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Software Versions</title>
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
    <h1>Software Versions</h1>

    <!-- Add New Software Version Form -->
    <div id="add-form">
        <h3>Add New Software Version</h3>
        <form id="newSoftwareVersionForm">
            <input type="text" id="version_number" placeholder="Version Number" required>
            <input type="date" id="release_date" required>
            <textarea id="changelog" placeholder="Changelog" required></textarea>
            <button type="submit" class="btn btn-update">Add Version</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Version ID</th>
                <th>Version Number</th>
                <th>Release Date</th>
                <th>Changelog</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="table-body">
        <?php
        // SQL Query to fetch software version data
        $sql = "SELECT * FROM software_versions";
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        // Fetch data and display it in the table
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr data-id='" . $row['software_version_id'] . "'>
                <td>" . htmlspecialchars($row['software_version_id']) . "</td>
                <td class='version_number'>" . htmlspecialchars($row['version_number']) . "</td>
                <td class='release_date'>" . htmlspecialchars($row['release_date']) . "</td>
                <td class='changelog'>" . htmlspecialchars($row['changelog']) . "</td>
                <td>
                    <button class='btn btn-update' onclick='editSoftwareVersion(this)'>Edit</button>
                    <button class='btn btn-delete' onclick='deleteSoftwareVersion(this)'>Delete</button>
                </td>
            </tr>";
        }
        ?>
        </tbody>
    </table>

    <script>
        // Edit Software Version Functionality
        function editSoftwareVersion(button) {
            const row = button.parentElement.parentElement;
            const versionId = row.getAttribute('data-id');
            const versionNumber = row.querySelector('.version_number').textContent;
            const releaseDate = row.querySelector('.release_date').textContent;
            const changelog = row.querySelector('.changelog').textContent;

            // Fill the form with existing data
            document.getElementById('version_number').value = versionNumber;
            document.getElementById('release_date').value = releaseDate;
            document.getElementById('changelog').value = changelog;

            // Update form action to edit software version
            document.getElementById('newSoftwareVersionForm').onsubmit = function(event) {
                event.preventDefault();
                updateSoftwareVersion(versionId);
            };
        }

        // Update Software Version Data (AJAX)
        function updateSoftwareVersion(versionId) {
            const versionNumber = document.getElementById('version_number').value;
            const releaseDate = document.getElementById('release_date').value;
            const changelog = document.getElementById('changelog').value;

            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'update.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    alert('Software Version updated successfully!');
                    location.reload();  // Refresh the table after update
                }
            };
            xhr.send('version_id=' + versionId + '&version_number=' + versionNumber + '&release_date=' + releaseDate + '&changelog=' + changelog);
        }

        // Delete Software Version Data (AJAX)
        function deleteSoftwareVersion(button) {
            const row = button.parentElement.parentElement;
            const versionId = row.getAttribute('data-id');

            const xhr = new XMLHttpRequest();
            xhr.open('GET', '?delete_id=' + versionId, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    if (xhr.responseText === 'success') {
                        alert('Software Version deleted successfully!');
                        row.remove();  // Remove the row from the table
                    } else {
                        alert('Error deleting software version.');
                    }
                }
            };
            xhr.send();
        }

        // Add New Software Version (AJAX)
        document.getElementById('newSoftwareVersionForm').onsubmit = function(event) {
            event.preventDefault();
            const versionNumber = document.getElementById('version_number').value;
            const releaseDate = document.getElementById('release_date').value;
            const changelog = document.getElementById('changelog').value;

            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'add.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    alert('Software Version added successfully!');
                    location.reload();  // Refresh the table after adding
                }
            };
            xhr.send('version_number=' + versionNumber + '&release_date=' + releaseDate + '&changelog=' + changelog);
        };
    </script>
</body>
</html>
