<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.html");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['feedback'])) {
    $feedback = $_POST['feedback'];
    $username = $_SESSION['user'];  // Staff username from session

    // Save the feedback into the database
    $mysqli = new mysqli('localhost', 'root', '', 'qic_system');
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    $stmt = $mysqli->prepare("INSERT INTO feedback (username, feedback) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $feedback);

    if ($stmt->execute()) {
        echo "Feedback submitted successfully.";
    } else {
        echo "Error: Could not submit feedback.";
    }

    $stmt->close();
    $mysqli->close();
}
?>
