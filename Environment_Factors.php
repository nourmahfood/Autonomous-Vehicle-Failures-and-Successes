<?php
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST["action"];

    if ($action == "add") {
        $test_id = $_POST["test_id"];
        $lighting_conditions = $_POST["lighting_conditions"];
        $road_type = $_POST["road_type"];
        $temperature = $_POST["temperature"];
        $traffic_density = $_POST["traffic_density"];

        $sql = "INSERT INTO environment_factors (test_id, lighting_conditions, road_type, temperature_id, traffic_density) 
                VALUES (:test_id, :lighting_conditions, :road_type, :temperature, :traffic_density)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':test_id', $test_id);
        $stmt->bindParam(':lighting_conditions', $lighting_conditions);
        $stmt->bindParam(':road_type', $road_type);
        $stmt->bindParam(':temperature', $temperature);
        $stmt->bindParam(':traffic_density', $traffic_density);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "new_id" => $conn->lastInsertId()]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error adding record."]);
        }
    } elseif ($action == "update") {
        $environment_id = $_POST["environment_id"];
        $test_id = $_POST["test_id"];
        $lighting_conditions = $_POST["lighting_conditions"];
        $road_type = $_POST["road_type"];
        $temperature = $_POST["temperature"];
        $traffic_density = $_POST["traffic_density"];

        $sql = "UPDATE environment_factors 
                SET test_id = :test_id, lighting_conditions = :lighting_conditions, 
                    road_type = :road_type, temperature_id = :temperature, 
                    traffic_density = :traffic_density 
                WHERE environment_id = :environment_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':environment_id', $environment_id);
        $stmt->bindParam(':test_id', $test_id);
        $stmt->bindParam(':lighting_conditions', $lighting_conditions);
        $stmt->bindParam(':road_type', $road_type);
        $stmt->bindParam(':temperature', $temperature);
        $stmt->bindParam(':traffic_density', $traffic_density);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error updating record."]);
        }
    } elseif ($action == "delete") {
        $environment_id = $_POST["environment_id"];

        $sql = "DELETE FROM environment_factors WHERE environment_id = :environment_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':environment_id', $environment_id);

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
    <title>Environment Factors</title>
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
    <h1>Environment Factors</h1>

    <div class="form-container">
        <h2 id="form-title">Add Environment Factor</h2>
        <form id="environmentForm">
            <input type="hidden" id="environment_id">
            <label>Test ID:</label>
            <input type="text" id="test_id" required>
            <label>Lighting Conditions:</label>
            <input type="text" id="lighting_conditions" required>
            <label>Road Type:</label>
            <input type="text" id="road_type" required>
            <label>Temperature:</label>
            <input type="text" id="temperature" required>
            <label>Traffic Density:</label>
            <input type="text" id="traffic_density" required>
            <button type="submit" id="submitBtn">Add Environment Factor</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Environment ID</th>
                <th>Test ID</th>
                <th>Lighting Conditions</th>
                <th>Road Type</th>
                <th>Temperature</th>
                <th>Traffic Density</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php 
        $stmt = $conn->prepare("SELECT * FROM environment_factors");
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr data-id='{$row["environment_id"]}'>
                <td>{$row['environment_id']}</td>
                <td>{$row['test_id']}</td>
                <td>{$row['lighting_conditions']}</td>
                <td>{$row['road_type']}</td>
                <td>{$row['temperature_id']}</td>
                <td>{$row['traffic_density']}</td>
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
        document.getElementById('environmentForm').addEventListener('submit', function (event) {
            event.preventDefault();

            let environment_id = document.getElementById('environment_id').value;
            let test_id = document.getElementById('test_id').value;
            let lighting_conditions = document.getElementById('lighting_conditions').value;
            let road_type = document.getElementById('road_type').value;
            let temperature = document.getElementById('temperature').value;
            let traffic_density = document.getElementById('traffic_density').value;

            let formData = new FormData();
            formData.append("action", environment_id ? "update" : "add");
            formData.append("environment_id", environment_id);
            formData.append("test_id", test_id);
            formData.append("lighting_conditions", lighting_conditions);
            formData.append("road_type", road_type);
            formData.append("temperature", temperature);
            formData.append("traffic_density", traffic_density);

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
            document.getElementById("environment_id").value = row.dataset.id;
            document.getElementById("test_id").value = row.children[1].textContent;
            document.getElementById("lighting_conditions").value = row.children[2].textContent;
            document.getElementById("road_type").value = row.children[3].textContent;
            document.getElementById("temperature").value = row.children[4].textContent;
            document.getElementById("traffic_density").value = row.children[5].textContent;
        }

        function deleteRecord(button) {
            if (!confirm("Are you sure?")) return;
            let row = button.closest("tr");
            fetch("", { method: "POST", body: new URLSearchParams({ action: "delete", environment_id: row.dataset.id }) })
            .then(() => row.remove());
        }
    </script>
</body>
</html>
