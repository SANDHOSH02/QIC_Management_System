<?php
session_start();
if (!isset($_SESSION['user'])) header("Location: login.html");

$user = $_SESSION['user'];
$role = $_SESSION['role'];

// Database connection
$mysqli = new mysqli('localhost', 'root', '', 'qic_system');

// Fetch staff data
$query = "SELECT * FROM users WHERE username = '$user' LIMIT 1";
$result = $mysqli->query($query);
$staffData = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Staff Dashboard</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary: #2563eb;
      --primary-dark: #1e40af;
      --secondary: #64748b;
      --background: #f8fafc;
      --card-bg: #ffffff;
      --text: #1e293b;
      --text-light: #64748b;
      --danger: #dc2626;
      --danger-dark: #b91c1c;
      --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
      --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', sans-serif;
      background-color: var(--background);
      color: var(--text);
      line-height: 1.5;
    }

    .header {
      background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
      color: white;
      padding: 2rem;
      text-align: center;
      box-shadow: var(--shadow);
      position: sticky;
      top: 0;
      z-index: 1000;
    }

    .header h1 {
      font-size: 2rem;
      font-weight: 700;
      letter-spacing: -0.025em;
    }

    .container {
      display: flex;
      gap: 1.5rem;
      margin: 2rem auto;
      max-width: 1400px;
      padding: 0 1.5rem;
    }

    .sidebar {
      width: 280px;
      background: var(--card-bg);
      border-radius: 12px;
      padding: 1.5rem;
      box-shadow: var(--shadow);
      position: sticky;
      top: 7rem;
      height: fit-content;
    }

    .sidebar h3 {
      font-size: 1.25rem;
      font-weight: 600;
      margin-bottom: 1.5rem;
      color: var(--text);
    }

    .sidebar ul {
      list-style: none;
    }

    .sidebar ul li {
      margin-bottom: 0.75rem;
    }

    .sidebar ul li a {
      display: block;
      padding: 0.75rem 1rem;
      text-decoration: none;
      color: var(--text-light);
      font-weight: 500;
      border-radius: 8px;
      transition: all 0.2s ease;
    }

    .sidebar ul li a:hover {
      background: var(--primary);
      color: white;
      transform: translateX(4px);
    }

    .main-content {
      flex: 1;
      background: var(--card-bg);
      border-radius: 12px;
      padding: 2rem;
      box-shadow: var(--shadow);
    }

    .main-content h2 {
      font-size: 1.75rem;
      font-weight: 600;
      margin-bottom: 1.5rem;
      color: var(--text);
    }

    .card {
      background: var(--card-bg);
      border-radius: 12px;
      padding: 1.5rem;
      margin-bottom: 1.5rem;
      box-shadow: var(--shadow);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
      transform: translateY(-4px);
      box-shadow: var(--shadow-lg);
    }

    .card h4 {
      font-size: 1.25rem;
      font-weight: 600;
      margin-bottom: 1rem;
      color: var(--text);
    }

    .card p {
      color: var(--text-light);
      margin-bottom: 0.75rem;
    }

    .card ul {
      list-style: none;
      padding-left: 1rem;
    }

    .card ul li {
      position: relative;
      margin-bottom: 0.5rem;
      color: var(--text-light);
      padding-left: 1.5rem;
    }

    .card ul li:before {
      content: '•';
      position: absolute;
      left: 0;
      color: var(--primary);
      font-size: 1.25rem;
    }

    form {
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }

    input[type="file"], textarea {
      border: 1px solid #e2e8f0;
      border-radius: 8px;
      padding: 0.75rem;
      font-size: 0.875rem;
      color: var(--text);
      background: #f8fafc;
      transition: border-color 0.2s ease;
    }

    input[type="file"]:focus, textarea:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    textarea {
      resize: vertical;
      min-height: 120px;
    }

    button {
      background: var(--primary);
      color: white;
      border: none;
      padding: 0.75rem 1.5rem;
      border-radius: 8px;
      font-weight: 500;
      cursor: pointer;
      transition: background 0.2s ease, transform 0.2s ease;
    }

    button:hover {
      background: var(--primary-dark);
      transform: translateY(-2px);
    }

    .logout {
      display: inline-flex;
      align-items: center;
      background: var(--danger);
      color: white;
      padding: 0.75rem 1.5rem;
      text-decoration: none;
      border-radius: 8px;
      font-weight: 500;
      transition: background 0.2s ease, transform 0.2s ease;
    }

    .logout:hover {
      background: var(--danger-dark);
      transform: translateY(-2px);
    }

    @media (max-width: 768px) {
      .container {
        flex-direction: column;
        padding: 0 1rem;
      }

      .sidebar {
        width: 100%;
        position: static;
      }

      .header h1 {
        font-size: 1.5rem;
      }

      .main-content h2 {
        font-size: 1.5rem;
      }
    }
  </style>
</head>
<body>

  <!-- Header -->
  <div class="header">
    <h1>Welcome, <?php echo $staffData['username']; ?> (Staff)</h1>
  </div>

  <!-- Main Content -->
  <div class="container">
    <!-- Sidebar -->
    <div class="sidebar">
      <h3>Staff Menu</h3>
      <ul>
        <li><a href="#">My Trainings</a></li>
        <li><a href="#">Upload Certificate</a></li>
        <li><a href="#">Feedback</a></li>
      </ul>
    </div>

    <!-- Main Section -->
    <div class="main-content">
      <h2>Dashboard</h2>
      <div class="card">
        <h4>Staff Information</h4>
        <p><strong>Username:</strong> <?php echo $staffData['username']; ?></p>
        <p><strong>Role:</strong> <?php echo ucfirst($role); ?></p>
        <p><strong>Email:</strong> <?php echo isset($staffData['email']) ? $staffData['email'] : 'N/A'; ?></p>
        <p><strong>Department:</strong> <?php echo isset($staffData['department']) ? $staffData['department'] : 'N/A'; ?></p>
      </div>

      <div class="card">
        <h4>My Trainings</h4>
        <p>Here you can see the trainings assigned to you.</p>
        <ul>
          <li>Training 1: Cyber Security Basics - Completed</li>
          <li>Training 2: Advanced Database Systems - In Progress</li>
        </ul>
      </div>

      <div class="card">
        <h4>Upload Certificate</h4>
        <form action="upload_certificate.php" method="POST" enctype="multipart/form-data">
          <label for="certificate">Select Certificate:</label>
          <input type="file" name="certificate" id="certificate" required><br><br>
          <button type="submit">Upload Certificate</button>
        </form>
      </div>

      <div class="card">
        <h4>Feedback</h4>
        <p>Provide feedback on recent training sessions or the system.</p>
        <form action="submit_feedback.php" method="POST">
          <textarea name="feedback" rows="5" cols="40" placeholder="Enter your feedback..." required></textarea><br><br>
          <button type="submit">Submit Feedback</button>
        </form>
      </div>

      <a href="logout.php" class="logout">Logout</a>
    </div>
  </div>

</body>
</html>