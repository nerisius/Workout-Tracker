<?php
require_once 'includes/auth.php';
require_once 'includes/config.php';

// Get workout_id from URL
$workout_id = $_GET['workout_id'] ?? null;

if (!$workout_id) {
    header("Location: workouts.php");
    exit;
}

// Fetch workout (only if belongs to current user)
$stmt = $pdo->prepare("SELECT * FROM workouts WHERE workout_id = ?");
$stmt->execute([$workout_id]);
$workout = $stmt->fetch();

if (!$workout) {
    header("Location: workouts.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($workout['program_name']) ?> Details</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        .move-container {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }
        .move-item {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .move-item:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1><?= htmlspecialchars($workout['program_name']) ?></h1>
            <button onclick="toggleEdit()" class="edit-btn">Edit Workout</button>
            <nav>
                <a href="workouts.php">← Back to All Workouts</a>
            </nav>
        </header>

        <div class="workout-details">
            <h2>Program Details</h2>
            <p><strong>Category:</strong> <?= htmlspecialchars($workout['category']) ?></p>
            <p><strong>Sets:</strong> <?= $workout['sets'] ?> × <strong>Reps:</strong> <?= $workout['reps'] ?></p>
            
            <div class="move-container">
                <h3>Workout Moves:</h3>
                <?php
                $moves = explode("\n", $workout['description']);
                
                foreach ($moves as $move) {
                    if (trim($move) !== '') {
                        echo '<div class="move-item">';
                        
                        // Extract link if it exists in parentheses
                        if (preg_match('/\((https?:\/\/[^\s]+)\)/', $move, $matches)) {
                            $link = $matches[1];
                            $move_name = preg_replace('/\s*\(https?:\/\/[^\s]+\)\s*/', '', $move);
                            
                            echo '• ' . htmlspecialchars(trim($move_name));
                            echo '<div class="exercise-link">';
                            echo '<a href="' . htmlspecialchars($link) . '" target="_blank">';
                            echo '<svg class="youtube-icon" viewBox="0 0 24 24"><path d="M10,15l5.19-3L10,9V15M21.56,7.17C21.69,7.64 21.78,8.27 21.84,9.06C21.91,9.85 21.94,10.64 21.94,11.44L22,12C22,14.81 21.84,16.83 21.56,17.83C21.31,18.73 20.73,19.31 19.83,19.56C19.36,19.69 18.5,19.78 17.18,19.84C15.88,19.91 14.69,19.94 13.59,19.94L12,20C7.81,20 5.17,19.84 4.17,19.56C3.27,19.31 2.69,18.73 2.44,17.83C2.31,17.36 2.22,16.73 2.16,15.94C2.09,15.15 2.06,14.36 2.06,13.56L2,12C2,9.19 2.16,7.17 2.44,6.17C2.69,5.27 3.27,4.69 4.17,4.44C4.64,4.31 5.5,4.22 6.82,4.16C8.12,4.09 9.31,4.06 10.41,4.06L12,4C16.19,4 18.83,4.16 19.83,4.44C20.73,4.69 21.31,5.27 21.56,6.17Z"/></svg> Watch Demo';
                            echo '</a>';
                            echo '</div>';
                        } else {
                            // No link found, just display the move
                            echo '• ' . htmlspecialchars(trim($move));
                        }
                        
                        echo '</div>';
                    }
                }
                ?>
            </div>
        </div>
    </div>

    <div class="edit-section">
        <h3>Edit Workout</h3>
        <form action="update_workout.php" method="post">
            <input type="hidden" name="workout_id" value="<?= $workout['workout_id'] ?>">
            
            <label>Program Name:</label>
            <input type="text" name="program_name" value="<?= htmlspecialchars($workout['program_name']) ?>">
            
            <label>Category:</label>
            <select name="category">
                <?php
                $categories = ['Upper Body', 'Lower Body', 'Cardio', 'Core', 'SAQ', 'Full Body'];
                foreach ($categories as $cat) {
                    $selected = ($cat == $workout['category']) ? 'selected' : '';
                    echo "<option value='$cat' $selected>$cat</option>";
                }
                ?>
            </select>
            
            <label>Sets × Reps:</label>
            <div class="set-rep-inputs">
                <input type="number" name="sets" value="<?= $workout['sets'] ?>" min="0">
                <span>×</span>
                <input type="number" name="reps" value="<?= $workout['reps'] ?>" min="0">
            </div>
            
            <label>Workout Moves (put YouTube links in parentheses):</label>
            <textarea name="description" rows="10" class="moves-textarea"><?php
                // This will display exactly as entered (with parentheses)
                echo htmlspecialchars($workout['description']);
            ?></textarea>
            
            <button type="submit" class="save-btn">Save Changes</button>
        </form>
    </div>
    <script>
    function toggleEdit() {
        const editSection = document.querySelector('.edit-section');
        editSection.style.display = editSection.style.display === 'none' ? 'block' : 'none';
        
        // Optional: Change button text
        const btn = document.querySelector('.edit-btn');
        btn.textContent = editSection.style.display === 'none' ? 'Edit Workout' : 'Cancel Editing';
    }
    </script>
</body>
</html>