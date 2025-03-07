<?php
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST["action"];

    if ($action == "insert") {
        $model_name = $_POST["model_name"];
        $brief_description = $_POST["brief_description"];
        $make = $_POST["make"];

        $sql = "INSERT INTO failure_types (model_name, brief_description, make) 
                VALUES (:model_name, :brief_description, :make)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':model_name', $model_name);
        $stmt->bindParam(':brief_description', $brief_description);
        $stmt->bindParam(':make', $make);

        if ($stmt->execute()) {
            $last_id = $conn->lastInsertId();
            echo json_encode([
                "status" => "success",
                "message" => "Record added successfully!",
                "new_record" => [
                    "failure_type_id" => $last_id,
                    "model_name" => $model_name,
                    "brief_description" => $brief_description,
                    "make" => $make
                ]
            ]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error adding record."]);
        }
    } elseif ($action == "update") {
        $failure_type_id = $_POST["failure_type_id"];
        $model_name = $_POST["model_name"];
        $brief_description = $_POST["brief_description"];
        $make = $_POST["make"];

        $sql = "UPDATE failure_types 
                SET model_name = :model_name, brief_description = :brief_description, 
                    make = :make
                WHERE failure_type_id = :failure_type_id";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':failure_type_id', $failure_type_id);
        $stmt->bindParam(':model_name', $model_name);
        $stmt->bindParam(':brief_description', $brief_description);
        $stmt->bindParam(':make', $make);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Record updated successfully!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error updating record."]);
        }
    } elseif ($action == "delete") {
        $failure_type_id = $_POST["failure_type_id"];

        $sql = "DELETE FROM failure_types WHERE failure_type_id = :failure_type_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':failure_type_id', $failure_type_id);

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
    <title>Failure Types</title>
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
        }

        table, th, td {
            border: 1px solid black;
        }

        th, td {
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

        .form-container button {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }

        .form-container button:hover {
            background-color: #45a049;
        }

        .edit-btn {
            background-color: #ffc107;
            color: black;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }

        .edit-btn:hover {
            background-color: #e0a800;
        }

        .delete-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }

        .delete-btn:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <h1>Failure Types</h1>

    <div class="form-container">
        <h2 id="form-title">Add / Update Failure Type</h2>
        <form id="failureForm">
            <input type="hidden" id="failure_type_id">
            <label>Model Name:</label>
            <input type="text" id="model_name" required>

            <label>Brief Description:</label>
            <input type="text" id="brief_description" required>

            <label>Make:</label>
            <input type="text" id="make" required>

            <button type="submit" id="submitBtn">Save</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Failure Type ID</th>
                <th>Model Name</th>
                <th>Brief Description</th>
                <th>Make</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="failureTable">
            <?php 
            $sql = "SELECT * FROM failure_types";
            $stmt = $conn->prepare($sql);
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr data-id='{$row["failure_type_id"]}' data-model='{$row["model_name"]}' data-description='{$row["brief_description"]}' data-make='{$row["make"]}'>
                    <td>" . htmlspecialchars($row['failure_type_id']) . "</td>
                    <td>" . htmlspecialchars($row['model_name']) . "</td>
                    <td>" . htmlspecialchars($row['brief_description']) . "</td>
                    <td>" . htmlspecialchars($row['make']) . "</td>
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
        document.getElementById('failureForm').addEventListener('submit', function (event) {
            event.preventDefault();

            let failure_type_id = document.getElementById('failure_type_id').value;
            let model_name = document.getElementById('model_name').value;
            let brief_description = document.getElementById('brief_description').value;
            let make = document.getElementById('make').value;

            let formData = new FormData();
            formData.append("action", failure_type_id ? "update" : "insert");
            formData.append("failure_type_id", failure_type_id);
            formData.append("model_name", model_name);
            formData.append("brief_description", brief_description);
            formData.append("make", make);

            fetch("", { method: "POST", body: formData })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    location.reload();
                } else {
                    alert(data.message);
                }
            });
        });

        function editRecord(button) {
            let row = button.closest("tr");
            document.getElementById("failure_type_id").value = row.getAttribute("data-id");
            document.getElementById("model_name").value = row.getAttribute("data-model");
            document.getElementById("brief_description").value = row.getAttribute("data-description");
            document.getElementById("make").value = row.getAttribute("data-make");
        }

        function deleteRecord(button) {
            let row = button.closest("tr");
            let failure_type_id = row.getAttribute("data-id");

            fetch("", { method: "POST", body: new URLSearchParams({ action: "delete", failure_type_id }) })
            .then(() => row.remove());
        }
    </script>
</body>
</html>
