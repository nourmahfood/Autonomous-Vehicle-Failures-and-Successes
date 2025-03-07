<?php
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST["action"];

    if ($action == "insert") {
        $test_id = $_POST["test_id"];
        $failure_type_id = $_POST["failure_type_id"];
        $failure_event_timestamp = $_POST["failure_event_timestamp"];
        $severity = $_POST["severity"];

        $sql = "INSERT INTO failure_events (test_id, failure_type_id, failure_event_timestamp, severity) 
                VALUES (:test_id, :failure_type_id, :failure_event_timestamp, :severity)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':test_id', $test_id);
        $stmt->bindParam(':failure_type_id', $failure_type_id);
        $stmt->bindParam(':failure_event_timestamp', $failure_event_timestamp);
        $stmt->bindParam(':severity', $severity);

        if ($stmt->execute()) {
            $last_id = $conn->lastInsertId();
            echo json_encode([
                "status" => "success",
                "message" => "Record added successfully!",
                "new_record" => [
                    "failure_event_id" => $last_id,
                    "test_id" => $test_id,
                    "failure_type_id" => $failure_type_id,
                    "failure_event_timestamp" => $failure_event_timestamp,
                    "severity" => $severity
                ]
            ]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error adding record."]);
        }
    } elseif ($action == "update") {
        $failure_event_id = $_POST["failure_event_id"];
        $test_id = $_POST["test_id"];
        $failure_type_id = $_POST["failure_type_id"];
        $failure_event_timestamp = $_POST["failure_event_timestamp"];
        $severity = $_POST["severity"];

        $sql = "UPDATE failure_events 
                SET test_id = :test_id, failure_type_id = :failure_type_id, 
                    failure_event_timestamp = :failure_event_timestamp, severity = :severity
                WHERE failure_event_id = :failure_event_id";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':failure_event_id', $failure_event_id);
        $stmt->bindParam(':test_id', $test_id);
        $stmt->bindParam(':failure_type_id', $failure_type_id);
        $stmt->bindParam(':failure_event_timestamp', $failure_event_timestamp);
        $stmt->bindParam(':severity', $severity);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Record updated successfully!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error updating record."]);
        }
    } elseif ($action == "delete") {
        $failure_event_id = $_POST["failure_event_id"];

        $sql = "DELETE FROM failure_events WHERE failure_event_id = :failure_event_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':failure_event_id', $failure_event_id);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Record deleted successfully!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error deleting record."]);
        }
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Failure Events</title>
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
        th, td {
            border: 1px solid black;
            padding: 10px;
            text-align: center;
        }
        .form-container {
            width: 50%;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ccc;
            background-color: #f9f9f9;
        }
        .form-container input, .form-container button {
            display: block;
            width: 100%;
            margin-top: 10px;
            padding: 8px;
        }
    </style>
</head>
<body>
    <h1>Failure Events</h1>

    <div class="form-container">
        <h2 id="form-title">Add / Update Failure Event</h2>
        <form id="failureForm">
            <input type="hidden" id="failure_event_id">
            <label>Test ID:</label>
            <input type="text" id="test_id" required>

            <label>Failure Type ID:</label>
            <input type="text" id="failure_type_id" required>

            <label>Timestamp:</label>
            <input type="text" id="failure_event_timestamp" required>

            <label>Severity:</label>
            <input type="text" id="severity" required>

            <button type="submit" id="submitBtn">Save</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Failure Event ID</th>
                <th>Test ID</th>
                <th>Failure Type ID</th>
                <th>Timestamp</th>
                <th>Severity</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="failureTable">
            <?php 
            $sql = "SELECT * FROM failure_events";
            $stmt = $conn->prepare($sql);
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr data-id='{$row["failure_event_id"]}' data-test_id='{$row["test_id"]}' 
                      data-failure_type_id='{$row["failure_type_id"]}' 
                      data-failure_event_timestamp='{$row["failure_event_timestamp"]}'
                      data-severity='{$row["severity"]}'>
                    <td>{$row['failure_event_id']}</td>
                    <td>{$row['test_id']}</td>
                    <td>{$row['failure_type_id']}</td>
                    <td>{$row['failure_event_timestamp']}</td>
                    <td>{$row['severity']}</td>
                    <td>
                        <button onclick='editRecord(this)'>Edit</button>
                        <button onclick='deleteRecord(this)'>Delete</button>
                    </td>
                </tr>";
            }
            ?>
        </tbody>
    </table>

    <script>
        document.getElementById('failureForm').addEventListener('submit', function (event) {
            event.preventDefault();

            let failure_event_id = document.getElementById('failure_event_id').value;
            let test_id = document.getElementById('test_id').value;
            let failure_type_id = document.getElementById('failure_type_id').value;
            let failure_event_timestamp = document.getElementById('failure_event_timestamp').value;
            let severity = document.getElementById('severity').value;

            let formData = new FormData();
            formData.append("action", failure_event_id ? "update" : "insert");
            formData.append("failure_event_id", failure_event_id);
            formData.append("test_id", test_id);
            formData.append("failure_type_id", failure_type_id);
            formData.append("failure_event_timestamp", failure_event_timestamp);
            formData.append("severity", severity);

            fetch("", { method: "POST", body: formData })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    let newRow = `
                        <tr>
                            <td>${data.new_record.failure_event_id}</td>
                            <td>${data.new_record.test_id}</td>
                            <td>${data.new_record.failure_type_id}</td>
                            <td>${data.new_record.failure_event_timestamp}</td>
                            <td>${data.new_record.severity}</td>
                            <td>
                                <button onclick='editRecord(this)'>Edit</button>
                                <button onclick='deleteRecord(this)'>Delete</button>
                            </td>
                        </tr>`;
                    document.getElementById("failureTable").insertAdjacentHTML("beforeend", newRow);
                } else {
                    alert(data.message);
                }
            });
        });

        function deleteRecord(button) {
            let row = button.closest("tr");
            let failure_event_id = row.getAttribute("data-id");

            let formData = new FormData();
            formData.append("action", "delete");
            formData.append("failure_event_id", failure_event_id);

            fetch("", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    row.remove();
                    alert(data.message); // Optional: display success message
                } else {
                    alert(data.message); // Display error message if deletion failed
                }
            });
        }

        function editRecord(button) {
            let row = button.closest("tr");
            let failure_event_id = row.getAttribute("data-id");

            // Populate form with the data for editing
            document.getElementById('failure_event_id').value = failure_event_id;
            document.getElementById('test_id').value = row.getAttribute('data-test_id');
            document.getElementById('failure_type_id').value = row.getAttribute('data-failure_type_id');
            document.getElementById('failure_event_timestamp').value = row.getAttribute('data-failure_event_timestamp');
            document.getElementById('severity').value = row.getAttribute('data-severity');
        }
    </script>

</body>
</html>
