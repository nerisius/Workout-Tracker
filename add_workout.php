<?php
require_once 'includes/auth.php';
require_once 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $program_name = $_POST['program_name'];
    $category = $_POST['category'];
    $sets = $_POST['sets'];
    $reps = $_POST['reps'];
    $description = $_POST['description'];
    
    $stmt = $pdo->prepare("INSERT INTO workouts (program_name, category, sets, reps, description) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$program_name, $category, $sets, $reps, $description]);
    
    header("Location: workouts.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add New Workout</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <h1>Add New Workout</h1>
        
        <form method="POST">
            <label>Program Name:</label>
            <input type="text" name="program_name" required>
            
            <label>Category:</label>
            <select name="category" required>
                <option value="Upper Body">Upper Body</option>
                <option value="Lower Body">Lower Body</option>
                <option value="Cardio">Cardio</option>
                <option value="Core">Core</option>
                <option value="Full Body">Full Body</option>
                <option value="SAQ">SAQ</option>
            </select>
            
            <label>Sets:</label>
            <input type="number" name="sets" value="3">
            
            <label>Reps:</label>
            <input type="number" name="reps" value="12">
            
            <label>Workout Moves (one per line):</label>
            <textarea name="description" rows="10"></textarea>
            
            <button type="submit">Save Workout</button>
        </form>
    </div>
</body>
</html>