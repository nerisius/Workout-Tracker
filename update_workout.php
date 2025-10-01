<?php
require_once 'includes/auth.php';
require_once 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: workouts.php");
    exit;
}

$workout_id = $_POST['workout_id'] ?? null;
$program_name = $_POST['program_name'] ?? '';
$category = $_POST['category'] ?? '';
$sets = $_POST['sets'] ?? 0;
$reps = $_POST['reps'] ?? 0;
$description = $_POST['description'] ?? '';

if (!$workout_id) {
    header("Location: workouts.php");
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE workouts SET 
        program_name = ?,
        category = ?,
        sets = ?,
        reps = ?,
        description = ?
        WHERE workout_id = ?");
    
    $stmt->execute([
        $program_name,
        $category,
        $sets,
        $reps,
        $description,
        $workout_id
    ]);
    
    header("Location: workout_detail.php?workout_id=$workout_id");
    exit;
    
} catch (PDOException $e) {
    error_log("Update failed: " . $e->getMessage());
    header("Location: workout_detail.php?workout_id=$workout_id&error=1");
    exit;
}

// Process the textarea content
$lines = explode("\n", $_POST['description']);
$exercises = [];
$links = [];

foreach ($lines as $line) {
    $parts = explode('|', trim($line));
    $exercises[] = $parts[0];
    $links[] = $parts[1] ?? '';
}

$description = implode("\n", $exercises);
$exercise_links = implode("\n", $links);

// Update your SQL to include exercise_links
$stmt = $pdo->prepare("UPDATE workouts SET 
    program_name = ?,
    category = ?,
    sets = ?,
    reps = ?,
    description = ?,
    exercise_links = ?
    WHERE workout_id = ?");