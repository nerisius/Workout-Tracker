<?php
require_once 'includes/auth.php';
require_once 'includes/config.php';

// Fetch all workouts
$stmt = $pdo->query("SELECT * FROM workouts");
$workouts = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Workouts</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        .workout-list { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }
        .workout-card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .workout-card h3 { margin-top: 0; color: #2c3e50; }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Workout Tracker</h1>
            <nav>
                <a href="index.php">Dashboard</a>
                <a href="workouts.php">My Workouts</a>
                <a href="calendar.php">Calendar</a>
                <a href="logout.php">Logout</a>
            </nav>
        </header>
        
        <h2>My Workout Programs</h2>
        
        <div class="workout-list">
            <?php foreach ($workouts as $workout): ?>
            <div class="workout-card">
                <h3><?= htmlspecialchars($workout['program_name']) ?></h3>
                <p><?= htmlspecialchars($workout['category']) ?></p>
                <a href="workout_detail.php?workout_id=<?= $workout['workout_id'] ?>">View Details</a>
            </div>
            <?php endforeach; ?>
        </div>
        
        <a href="add_workout.php" class="button">Add New Workout</a>
    </div>
</body>
</html>