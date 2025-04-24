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
      --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
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
      overflow-x: hidden;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @keyframes pulse {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.05); }
    }

    @keyframes slideInLeft {
      from { transform: translateX(-100%); opacity: 0; }
      to { transform: translateX(0); opacity: 1; }
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
      animation: fadeIn 0.5s ease-out;
    }

    .header h1 {
      font-size: 2rem;
      font-weight: 700;
      letter-spacing: -0.025em;
      animation: pulse 2s infinite;
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
      animation: slideInLeft 0.5s ease-out;
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
      opacity: 0;
      animation: fadeIn 0.5s ease-out forwards;
      animation-delay: calc(0.1s * var(--i));
    }

    .sidebar ul li:nth-child(1) { --i: 1; }
    .sidebar ul li:nth-child(2) { --i: 2; }
    .sidebar ul li:nth-child(3) { --i: 3; }

    .sidebar ul li a {
      display: block;
      padding: 0.75rem 1rem;
      text-decoration: none;
      color: var(--text-light);
      font-weight: 500;
      border-radius: 8px;
      transition: var(--transition);
      position: relative;
      overflow: hidden;
    }

    .sidebar ul li a::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
      transition: 0.5s;
    }

    .sidebar ul li a:hover::before {
      left: 100%;
    }

    .sidebar ul li a:hover {
      background: var(--primary);
      color: white;
      transform: translateX(8px);
      box-shadow: 0 2px 8px rgba(37, 99, 235, 0.3);
    }

    .main-content {
      flex: 1;
      background: var(--card-bg);
      border-radius: 12px;
      padding: 2rem;
      box-shadow: var(--shadow);
      animation: fadeIn 0.5s ease-out;
    }

    .main-content h2 {
      font-size: 1.75rem;
      font-weight: 600;
      margin-bottom: 1.5rem;
      color: var(--text);
      position: relative;
    }

    .main-content h2::after {
      content: '';
      position: absolute;
      bottom: -8px;
      left: 0;
      width: 50px;
      height: 3px;
      background: var(--primary);
      transition: var(--transition);
    }

    .main-content h2:hover::after {
      width: 100px;
    }

    .card {
      background: var(--card-bg);
      border-radius: 12px;
      padding: 1.5rem;
      margin-bottom: 1.5rem;
      box-shadow: var(--shadow);
      transition: var(--transition);
      opacity: 0;
      animation: fadeIn 0.5s ease-out forwards;
    }

    .card:nth-child(1) { animation-delay: 0.2s; }
    .card:nth-child(2) { animation-delay: 0.3s; }
    .card:nth-child(3) { animation-delay: 0.4s; }
    .card:nth-child(4) { animation-delay: 0.5s; }

    .card:hover {
      transform: translateY(-8px);
      box-shadow: var(--shadow-lg);
      border: 1px solid rgba(37, 99, 235, 0.2);
    }

    .card h4 {
      font-size: 1.25rem;
      font-weight: 600;
      margin-bottom: 1rem;
      color: var(--text);
      transition: var(--transition);
    }

    .card h4:hover {
      color: var(--primary);
    }

    .card p {
      color: var(--text-light);
      margin-bottom: 0.75rem;
      transition: var(--transition);
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
      transition: var(--transition);
    }

    .card ul li:hover {
      color: var(--text);
      transform: translateX(4px);
    }

    .card ul li:before {
      content: '•';
      position: absolute;
      left: 0;
      color: var(--primary);
      font-size: 1.25rem;
      transition: var(--transition);
    }

    .card ul li:hover:before {
      color: var(--primary-dark);
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
      transition: var(--transition);
      position: relative;
    }

    input[type="file"]:focus, textarea:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
      transform: scale(1.01);
    }

    textarea {
      resize: vertical;
      min-height: 120px;
      transition: var(--transition);
    }

    textarea:hover {
      border-color: var(--primary);
    }

    button {
      background: var(--primary);
      color: white;
      border: none;
      padding: 0.75rem 1.5rem;
      border-radius: 8px;
      font-weight: 500;
      cursor: pointer;
      transition: var(--transition);
      position: relative;
      overflow: hidden;
    }

    button::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
      transition: 0.5s;
    }

    button:hover::before {
      left: 100%;
    }

    button:hover {
      background: var(--primary-dark);
      transform: translateY(-2px) scale(1.05);
      box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
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
      transition: var(--transition);
      position: relative;
      overflow: hidden;
    }

    .logout::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
      transition: 0.5s;
    }

    .logout:hover::before {
      left: 100%;
    }

    .logout:hover {
      background: var(--danger-dark);
      transform: translateY(-2px) scale(1.05);
      box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
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

      .card {
        animation: none;
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