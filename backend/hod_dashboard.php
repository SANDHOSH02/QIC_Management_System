<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'hod') {
    header("Location: login.html");
    exit();
}

$user = $_SESSION['user'];
$role = $_SESSION['role'];

// Database connection
$mysqli = new mysqli('localhost', 'root', '', 'qic_system');

// Fetch HOD data
$query = "SELECT * FROM users WHERE username = '$user' LIMIT 1";
$result = $mysqli->query($query);
$hodData = $result->fetch_assoc();

// Fetch up to 10 staff members
$staffQuery = "SELECT id, username, email, department FROM users WHERE role = 'staff' LIMIT 10";
$staffResult = $mysqli->query($staffQuery);

// Fetch staff details and activities if a staff member is selected
$selectedStaff = null;
$activities = [];
if (isset($_GET['staff_id'])) {
    $staffId = $mysqli->real_escape_string($_GET['staff_id']);
    $staffDetailQuery = "SELECT * FROM users WHERE id = '$staffId' AND role = 'staff' LIMIT 1";
    $staffDetailResult = $mysqli->query($staffDetailQuery);
    $selectedStaff = $staffDetailResult->fetch_assoc();

    // Fetch activities (trainings, certificates, feedback)
    $activityQuery = "SELECT * FROM activities WHERE user_id = '$staffId' ORDER BY created_at DESC";
    $activityResult = $mysqli->query($activityQuery);
    while ($activity = $activityResult->fetch_assoc()) {
        $activities[] = $activity;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>HOD Dashboard</title>
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

    .staff-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 1rem;
    }

    .staff-table th,
    .staff-table td {
      padding: 0.75rem;
      text-align: left;
      border-bottom: 1px solid #e2e8f0;
    }

    .staff-table th {
      background: #f8fafc;
      font-weight: 600;
      color: var(--text);
    }

    .staff-table td {
      color: var(--text-light);
    }

    .staff-table tr:hover {
      background: #f1f5f9;
    }

    .view-details {
      color: var(--primary);
      text-decoration: none;
      font-weight: 500;
      padding: 0.5rem 1rem;
      border-radius: 8px;
      transition: background 0.2s ease, color 0.2s ease;
    }

    .view-details:hover {
      background: var(--primary);
      color: white;
    }

    .activity-list {
      list-style: none;
      padding-left: 1rem;
    }

    .activity-list li {
      position: relative;
      margin-bottom: 0.5rem;
      color: var(--text-light);
      padding-left: 1.5rem;
    }

    .activity-list li:before {
      content: '•';
      position: absolute;
      left: 0;
      color: var(--primary);
      font-size: 1.25rem;
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

      .staff-table {
        font-size: 0.875rem;
      }

      .staff-table th,
      .staff-table td {
        padding: 0.5rem;
      }
    }
  </style>
</head>
<body>

  <!-- Header -->
  <div class="header">
    <h1>Welcome, <?php echo $hodData['username']; ?> (HOD)</h1>
  </div>

  <!-- Main Content -->
  <div class="container">
    <!-- Sidebar -->
    <div class="sidebar">
      <h3>HOD Menu</h3>
      <ul>
        <li><a href="#">Staff Details</a></li>
        <li><a href="#">Training Reports</a></li>
        <li><a href="#">Feedback Overview</a></li>
      </ul>
    </div>

    <!-- Main Section -->
    <div class="main-content">
      <h2>HOD Dashboard</h2>
      <div class="card">
        <h4>HOD Information</h4>
        <p><strong>Username:</strong> <?php echo $hodData['username']; ?></p>
        <p><strong>Role:</strong> <?php echo ucfirst($role); ?></p>
        <p><strong>Email:</strong> <?php echo isset($hodData['email']) ? $hodData['email'] : 'N/A'; ?></p>
        <p><strong>Department:</strong> <?php echo isset($hodData['department']) ? $hodData['department'] : 'N/A'; ?></p>
      </div>

      <div class="card">
        <h4>Staff List (Up to 10)</h4>
        <?php if ($staffResult->num_rows > 0): ?>
          <table class="staff-table">
            <thead>
              <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Department</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($staff = $staffResult->fetch_assoc()): ?>
                <tr>
                  <td><?php echo htmlspecialchars($staff['username']); ?></td>
                  <td><?php echo htmlspecialchars($staff['email'] ?? 'N/A'); ?></td>
                  <td><?php echo htmlspecialchars($staff['department'] ?? 'N/A'); ?></td>
                  <td><a href="?staff_id=<?php echo $staff['id']; ?>" class="view-details">View Details</a></td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        <?php else: ?>
          <p>No staff members found.</p>
        <?php endif; ?>
      </div>

      <?php if ($selectedStaff): ?>
        <div class="card">
          <h4>Staff Details: <?php echo htmlspecialchars($selectedStaff['username']); ?></h4>
          <p><strong>Username:</strong> <?php echo htmlspecialchars($selectedStaff['username']); ?></p>
          <p><strong>Email:</strong> <?php echo htmlspecialchars($selectedStaff['email'] ?? 'N/A'); ?></p>
          <p><strong>Department:</strong> <?php echo htmlspecialchars($selectedStaff['department'] ?? 'N/A'); ?></p>
          <p><strong>Role:</strong> <?php echo ucfirst($selectedStaff['role']); ?></p>
          <h4>Activities</h4>
          <?php if (!empty($activities)): ?>
            <ul class="activity-list">
              <?php foreach ($activities as $activity): ?>
                <li>
                  <?php
                  echo htmlspecialchars($activity['activity_type']) . ": " . 
                       htmlspecialchars($activity['description']) . 
                       " (Date: " . date('Y-m-d', strtotime($activity['created_at'])) . ")";
                  ?>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php else: ?>
            <p>No activities recorded for this staff member.</p>
          <?php endif; ?>
        </div>
      <?php endif; ?>

      <a href="logout.php" class="logout">Logout</a>
    </div>
  </div>

</body>
</html>