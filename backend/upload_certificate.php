<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.html");
    exit;
}

// Handle the file upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['certificate'])) {
    $username = $_SESSION['user'];  // Staff username from session
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["certificate"]["name"]);
    $uploadOk = 1;
    $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if the file is a valid certificate (e.g., PDF, DOC, DOCX)
    if ($fileType != "pdf" && $fileType != "doc" && $fileType != "docx") {
        echo "Sorry, only PDF, DOC, DOCX files are allowed.";
        $uploadOk = 0;
    }

    // Proceed with file upload
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    } else {
        if (move_uploaded_file($_FILES["certificate"]["tmp_name"], $target_file)) {
            echo "The file " . htmlspecialchars(basename($_FILES["certificate"]["name"])) . " has been uploaded.";

            // Now, save the file path into the database
            $mysqli = new mysqli('localhost', 'root', '', 'qic_system');
            if ($mysqli->connect_error) {
                die("Connection failed: " . $mysqli->connect_error);
            }

            // Save the certificate file path in the database
            $stmt = $mysqli->prepare("INSERT INTO certificates (username, certificate_path) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $target_file);

            if ($stmt->execute()) {
                echo "Certificate uploaded and saved in database successfully!";
            } else {
                echo "Error: Could not save certificate in database.";
            }
            $stmt->close();
            $mysqli->close();
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}
?>
