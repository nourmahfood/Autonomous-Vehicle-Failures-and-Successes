<?php
require 'db.php'; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST["action"];

    if ($action == "add") {
        // Insert new record
        $model_name = $_POST['model_name'];
        $brief_description = $_POST['brief_description'];

        $sql = "INSERT INTO attack_type (model_name, brief_description) VALUES (:model_name, :brief_description)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':model_name', $model_name);
        $stmt->bindParam(':brief_description', $brief_description);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Record added successfully!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error adding record."]);
        }
    } elseif ($action == "update") {
        // Update existing record
        $attack_type_id = $_POST['attack_type_id'];
        $model_name = $_POST['model_name'];
        $brief_description = $_POST['brief_description'];

        $sql = "UPDATE attack_type SET model_name = :model_name, brief_description = :brief_description WHERE attack_type_id = :attack_type_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':attack_type_id', $attack_type_id);
        $stmt->bindParam(':model_name', $model_name);
        $stmt->bindParam(':brief_description', $brief_description);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Record updated successfully!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error updating record."]);
        }
    } elseif ($action == "delete") {
        // Delete record
        $attack_type_id = $_POST['attack_type_id'];

        $sql = "DELETE FROM attack_type WHERE attack_type_id = :attack_type_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':attack_type_id', $attack_type_id);

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
    <title>Attack Type Tables</title>
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

        .form-container {
            width: 50%;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ccc;
            background-color: #f9f9f9;
            text-align: left;
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
            font-size: 16px;
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
    <h1>Attack Type Tables</h1>

    <!-- Form to Add/Update Record -->
    <div class="form-container">
        <h2 id="form-title">Add New Attack Type</h2>
        <form id="attackForm">
            <input type="hidden" id="attack_type_id">
            <label>Model Name:</label>
            <input type="text" id="model_name" required>

            <label>Brief Description:</label>
            <input type="text" id="brief_description" required>

            <button type="submit" id="submitBtn">Add Attack Type</button>
        </form>
    </div>

    <!-- Table 1 -->
    <h2>Cyber Attacks</h2>
    <table>
        <thead>
            <tr>
                <th>Attack Type ID</th>
                <th>Model Name</th>
                <th>Brief Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="attackTable">
            <?php
            // Fetch and display records
            $sql = "SELECT * FROM attack_type";
            $stmt = $conn->prepare($sql);
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr data-id='{$row["attack_type_id"]}' data-model='{$row["model_name"]}' data-desc='{$row["brief_description"]}'>
                    <td>" . htmlspecialchars($row["attack_type_id"]) . "</td>
                    <td>" . htmlspecialchars($row["model_name"]) . "</td>
                    <td>" . htmlspecialchars($row["brief_description"]) . "</td>
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
        document.getElementById('attackForm').addEventListener('submit', function (event) {
            event.preventDefault();

            let attack_type_id = document.getElementById('attack_type_id').value;
            let model_name = document.getElementById('model_name').value;
            let brief_description = document.getElementById('brief_description').value;
            let action = attack_type_id ? "update" : "add"; // Determine action

            let formData = new FormData();
            formData.append("action", action);
            formData.append("attack_type_id", attack_type_id);
            formData.append("model_name", model_name);
            formData.append("brief_description", brief_description);

            fetch("", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    location.reload(); // Refresh page to show new data
                } else {
                    alert(data.message);
                }
            })
            .catch(error => console.error("Error:", error));
        });

        function editRecord(button) {
            let row = button.closest("tr");
            let attack_type_id = row.getAttribute("data-id");
            let model_name = row.getAttribute("data-model");
            let brief_description = row.getAttribute("data-desc");

            document.getElementById("attack_type_id").value = attack_type_id;
            document.getElementById("model_name").value = model_name;
            document.getElementById("brief_description").value = brief_description;
            document.getElementById("form-title").textContent = "Update Attack Type";
            document.getElementById("submitBtn").textContent = "Update";
        }

        function deleteRecord(button) {
            if (!confirm("Are you sure you want to delete this record?")) return;

            let row = button.closest("tr");
            let attack_type_id = row.getAttribute("data-id");

            fetch("", {
                method: "POST",
                body: new URLSearchParams({ action: "delete", attack_type_id })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    row.remove();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => console.error("Error:", error));
        }
    </script>

</body>
</html>
