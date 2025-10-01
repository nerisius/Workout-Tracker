<?php
require_once 'includes/auth.php';
require_once 'includes/config.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Workout Tracker</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Workout Tracker</h1>
            <nav>
                <a href="workouts.php">My Workouts</a>
                <a href="calendar.php">Calendar</a>
                <a href="logout.php">Logout</a>
            </nav>
        </header>
        
        <section class="stats">
            <h2>This Week's Progress</h2>
            <!-- (We'll add charts/logs later) -->
        </section>
    </div>
</body>
</html>