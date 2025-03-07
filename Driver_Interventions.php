<?php
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST["action"];

    if ($action == "add") {
        $test_id = $_POST["test_id"];
        $timestamp = $_POST["timestamp"];
        $reason = $_POST["reason"];
        $duration = $_POST["duration"];

        $sql = "INSERT INTO driver_interventions (test_id, intervention_timestamp, reason, duration_id) 
                VALUES (:test_id, :timestamp, :reason, :duration)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':test_id', $test_id);
        $stmt->bindParam(':timestamp', $timestamp);
        $stmt->bindParam(':reason', $reason);
        $stmt->bindParam(':duration', $duration);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "new_id" => $conn->lastInsertId()]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error adding record."]);
        }
    } elseif ($action == "update") {
        $intervention_id = $_POST["intervention_id"];
        $test_id = $_POST["test_id"];
        $timestamp = $_POST["timestamp"];
        $reason = $_POST["reason"];
        $duration = $_POST["duration"];

        $sql = "UPDATE driver_interventions 
                SET test_id = :test_id, intervention_timestamp = :timestamp, 
                    reason = :reason, duration_id = :duration 
                WHERE intervention_id = :intervention_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':intervention_id', $intervention_id);
        $stmt->bindParam(':test_id', $test_id);
        $stmt->bindParam(':timestamp', $timestamp);
        $stmt->bindParam(':reason', $reason);
        $stmt->bindParam(':duration', $duration);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error updating record."]);
        }
    } elseif ($action == "delete") {
        $intervention_id = $_POST["intervention_id"];

        $sql = "DELETE FROM driver_interventions WHERE intervention_id = :intervention_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':intervention_id', $intervention_id);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success"]);
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
    <title>Driver Interventions</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; margin-top: 50px; }
        table { width: 80%; margin: 20px auto; border-collapse: collapse; }
        th, td { border: 1px solid black; padding: 10px; text-align: center; }
        .form-container { width: 50%; margin: 20px auto; padding: 20px; border: 1px solid #ccc; background-color: #f9f9f9; }
        .form-container input, .form-container button { display: block; width: 100%; margin-top: 10px; padding: 8px; }
        .form-container button { background-color: #4CAF50; color: white; border: none; cursor: pointer; }
        .edit-btn { background-color: #ffc107; color: black; padding: 5px 10px; cursor: pointer; }
        .delete-btn { background-color: #dc3545; color: white; padding: 5px 10px; cursor: pointer; }
    </style>
</head>
<body>
    <h1>Driver Interventions</h1>

    <div class="form-container">
        <h2 id="form-title">Add Intervention</h2>
        <form id="interventionForm">
            <input type="hidden" id="intervention_id">
            <label>Test ID:</label>
            <input type="text" id="test_id" required>
            <label>Timestamp:</label>
            <input type="text" id="timestamp" required>
            <label>Reason:</label>
            <input type="text" id="reason" required>
            <label>Duration (minutes):</label>
            <input type="text" id="duration" required>
            <button type="submit" id="submitBtn">Add Intervention</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Intervention ID</th>
                <th>Test ID</th>
                <th>Timestamp</th>
                <th>Reason</th>
                <th>Duration (minutes)</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php 
        $stmt = $conn->prepare("SELECT * FROM driver_interventions");
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr data-id='{$row["intervention_id"]}'>
                <td>{$row['intervention_id']}</td>
                <td>{$row['test_id']}</td>
                <td>{$row['intervention_timestamp']}</td>
                <td>{$row['reason']}</td>
                <td>{$row['duration_id']}</td>
                <td>
                    <button class='edit-btn' onclick='editRecord(this)'>Edit</button>
                    <button class='delete-btn' onclick='deleteRecord(this)'>Delete</button>
                </td>
            </tr>";
        }
        ?>
        </tbody>
    </table>

    <script>
        document.getElementById('interventionForm').addEventListener('submit', function (event) {
            event.preventDefault();

            let intervention_id = document.getElementById('intervention_id').value;
            let test_id = document.getElementById('test_id').value;
            let timestamp = document.getElementById('timestamp').value;
            let reason = document.getElementById('reason').value;
            let duration = document.getElementById('duration').value;

            let formData = new FormData();
            formData.append("action", intervention_id ? "update" : "add");
            formData.append("intervention_id", intervention_id);
            formData.append("test_id", test_id);
            formData.append("timestamp", timestamp);
            formData.append("reason", reason);
            formData.append("duration", duration);

            fetch("", { method: "POST", body: formData })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    location.reload();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => console.error("Error:", error));
        });

        function editRecord(button) {
            let row = button.closest("tr");
            document.getElementById("intervention_id").value = row.dataset.id;
            document.getElementById("test_id").value = row.children[1].textContent;
            document.getElementById("timestamp").value = row.children[2].textContent;
            document.getElementById("reason").value = row.children[3].textContent;
            document.getElementById("duration").value = row.children[4].textContent;

            document.getElementById("form-title").textContent = "Update Intervention";
            document.getElementById("submitBtn").textContent = "Update";
        }

        function deleteRecord(button) {
            if (!confirm("Are you sure?")) return;
            let row = button.closest("tr");
            fetch("", { method: "POST", body: new URLSearchParams({ action: "delete", intervention_id: row.dataset.id }) })
            .then(() => row.remove());
        }
    </script>
</body>
</html>
