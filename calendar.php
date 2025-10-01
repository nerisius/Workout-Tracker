<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

</head>
<body>
    <div class="container">
        <!-- Add this navigation link at the top -->
        <div class="page-navigation">
            <a href="index.php" class="nav-button">Back to Dashboard</a>
        </div>
        
        
        <!-- Rest of your existing calendar code... -->
<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

// Handle workout deletion DELETION*************************************************
// Handle workout deletion
// Handle workout deletion
// Handle workout deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_id'])) {
    $deleteId = (int)$_POST['delete_id'];
    $currentMonth = isset($_GET['month']) ? (int)$_GET['month'] : date('n');
    $currentYear = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
    
    $stmt = $pdo->prepare("DELETE FROM workout_logs WHERE id = ?");
    $stmt->execute([$deleteId]);
    
    // Redirect back to current view
    header("Location: calendar.php?month=$currentMonth&year=$currentYear");
    exit();
}
/************************************************************************************** */

// Get current month and year
$month = isset($_GET['month']) ? (int)$_GET['month'] : date('n');
$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');

// Handle month navigation
if (isset($_GET['action'])) {
    if ($_GET['action'] == 'prev') {
        $month--;
        if ($month < 1) {
            $month = 12;
            $year--;
        }
    } elseif ($_GET['action'] == 'next') {
        $month++;
        if ($month > 12) {
            $month = 1;
            $year++;
        }
    }
}

// Get first day of the month and total days in month
$firstDay = mktime(0, 0, 0, $month, 1, $year);
$totalDays = date('t', $firstDay);

// Get the name of the month
$monthName = date('F', $firstDay);

// Get the day of the week for the first day
$dayOfWeek = date('D', $firstDay);

// Create array mapping days of week to 0-6
$dayMap = ['Mon' => 0, 'Tue' => 1, 'Wed' => 2, 'Thu' => 3, 'Fri' => 4, 'Sat' => 5, 'Sun' => 6];
$blankDays = $dayMap[$dayOfWeek];

// Handle workout submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['date'])) {
    $date = $_POST['date'];
    $notes = $_POST['notes'] ?? '';
    $entryType = $_POST['entry_type'] ?? 'workout';
    
    if ($entryType === 'workout' && !empty($_POST['workout_type'])) {
        $workoutType = $_POST['workout_type'];
        $colors = [
            'HIGH' => '#8c1f22',
            'LOW' => '#33FF57',
            'TS' => '#3357FF',
            'MAC' => '#F033FF'
        ];
        
        $color = $colors[$workoutType];
        $stmt = $pdo->prepare("INSERT INTO workout_logs (date, workout_type, color, notes) VALUES (?, ?, ?, ?)");
        $stmt->execute([$date, $workoutType, $color, $notes]);
    } elseif (!empty($notes)) {
        // Insert note-only entry (with NULL workout_type)
        $stmt = $pdo->prepare("INSERT INTO workout_logs (date, notes) VALUES (?, ?)");
        $stmt->execute([$date, $notes]);
    }
    
    header("Location: calendar.php?month=$month&year=$year");
    exit();
}

// Get workouts for the current month
$startDate = "$year-$month-01";
$endDate = "$year-$month-$totalDays";
$stmt = $pdo->prepare("SELECT * FROM workout_logs WHERE date BETWEEN ? AND ? ORDER BY date");
$stmt->execute([$startDate, $endDate]);
$workouts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group workouts by date
$workoutsByDate = [];
foreach ($workouts as $workout) {
    $date = $workout['date'];
    if (!isset($workoutsByDate[$date])) {
        $workoutsByDate[$date] = [];
    }
    $workoutsByDate[$date][] = $workout;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workout Calendar</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <h1>Workout Calendar</h1>
        
        <div class="calendar-nav">
            <a href="calendar.php?action=prev&month=<?= $month ?>&year=<?= $year ?>" class="nav-button">Previous</a>
            <h2><?= $monthName ?> <?= $year ?></h2>
            <a href="calendar.php?action=next&month=<?= $month ?>&year=<?= $year ?>" class="nav-button">Next</a>
        </div>


        <table class="calendar">
            <thead>
                <tr>
                    <th>Mon</th>
                    <th>Tue</th>
                    <th>Wed</th>
                    <th>Thu</th>
                    <th>Fri</th>
                    <th>Sat</th>
                    <th>Sun</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $dayCount = 1;
                echo '<tr>';
                
                // Create blank cells for days before the first day of the month
                for ($i = 0; $i < $blankDays; $i++) {
                    echo '<td class="empty"></td>';
                }
                
                // Create cells for each day of the month
                while ($dayCount <= $totalDays) {
                    if (($dayCount + $blankDays - 1) % 7 == 0 && $dayCount != 1) {
                        echo '</tr><tr>';
                    }
                    
                    $currentDate = date('Y-m-d', mktime(0, 0, 0, $month, $dayCount, $year));
                    $isToday = ($currentDate == date('Y-m-d')) ? 'today' : '';
                    $dayWorkouts = $workoutsByDate[$currentDate] ?? [];
                    
                    echo '<td class="day ' . $isToday . '" data-date="' . $currentDate . '">';
                    echo '<div class="day-number">' . $dayCount . '</div>';
                    
                    // Display workout indicators

                    echo '<div class="workout-indicators">';
                    foreach ($dayWorkouts as $workout) {
                        // Convert workout types to abbreviations
                        $abbreviations = [
                            'HIGH' => 'HI',
                            'LOW' => 'LI',
                            'TS' => 'TS',
                            'MAC' => 'MA'
                        ];

                        $abbr = $abbreviations[$workout['workout_type']];
                        
                        echo '<div class="workout-indicator" style="background-color: ' . $workout['color'] . '" data-type="' . $workout['workout_type'] . '" title="' . htmlspecialchars($workout['workout_type'] . ': ' . $workout['notes']) . '">';
                        echo $abbr;
                        echo '<form method="POST" class="delete-form">';
                        echo '<input type="hidden" name="delete_id" value="' . $workout['id'] . '">';
                        echo '<input type="hidden" name="month" value="' . $month . '">';
                        echo '<input type="hidden" name="year" value="' . $year . '">';
                        echo '<button type="submit" class="delete-btn" onclick="return confirm(\'Delete this workout?\')">×</button>';
                        echo '</form>';
                        echo '</div>';
                        // After displaying workout indicators
                        if (!empty($workout['notes'])) {
                            echo '<div class="notes-container">' . htmlspecialchars($workout['notes']) . '</div>';
                        }
                    }
                    echo '</div>';
                    
                    echo '</td>';
                    
                    $dayCount++;
                }
                
                // Fill remaining cells with empty cells if needed
                while (($dayCount + $blankDays - 1) % 7 != 0) {
                    echo '<td class="empty"></td>';
                    $dayCount++;
                }
                
                echo '</tr>';
                ?>
            </tbody>
        </table>
        
        <!-- Workout Form (hidden by default) -->
        <div id="workout-form-container" style="display: none;">
            <div class="workout-form">
                <h3>Log Entry for <span id="workout-date"></span></h3>
                <form id="workout-form" method="POST">
                    <input type="hidden" name="date" id="form-date">
                    <div class="form-group">
                        <label>Add:</label>
                        <div class="form-options">
                            <input type="radio" id="option-workout" name="entry_type" value="workout" checked>
                            <label for="option-workout">Workout</label>
                            <input type="radio" id="option-note" name="entry_type" value="note">
                            <label for="option-note">Note Only</label>
                        </div>
                    </div>
                    <div id="workout-type-container" class="form-group">
                        <label for="workout_type">Workout Type:</label>
                        <select name="workout_type" id="workout_type">
                            <option value="">-- Select --</option>
                            <option value="HIGH">High Intensity</option>
                            <option value="LOW">Low Intensity</option>
                            <option value="TS">TS</option>
                            <option value="MAC">Maç</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="notes">Notes:</label>
                        <textarea name="notes" id="notes" rows="2"></textarea>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn">Save Entry</button>
                        <button type="button" id="cancel-workout" class="btn cancel">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const days = document.querySelectorAll('.day');
            const formContainer = document.getElementById('workout-form-container');
            const workoutDateSpan = document.getElementById('workout-date');
            const formDateInput = document.getElementById('form-date');
            const cancelBtn = document.getElementById('cancel-workout');
            
            days.forEach(day => {
                day.addEventListener('click', function() {
                    const date = this.getAttribute('data-date');
                    const dateObj = new Date(date);
                    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                    const formattedDate = dateObj.toLocaleDateString('en-US', options);
                    
                    workoutDateSpan.textContent = formattedDate;
                    formDateInput.value = date;
                    
                    // Position the form near the clicked day
                    const rect = this.getBoundingClientRect();
                    formContainer.style.position = 'absolute';
                    formContainer.style.top = `${rect.bottom + window.scrollY + 10}px`;
                    formContainer.style.left = `${rect.left + window.scrollX}px`;
                    
                    formContainer.style.display = 'block';
                });
            });
            
            cancelBtn.addEventListener('click', function() {
                formContainer.style.display = 'none';
            });
        });
    </script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Existing click handlers...
        
        // Add this new functionality
        const workoutTypeContainer = document.getElementById('workout-type-container');
        const entryTypeRadios = document.querySelectorAll('input[name="entry_type"]');
        
        entryTypeRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                workoutTypeContainer.style.display = this.value === 'workout' ? 'block' : 'none';
                if (this.value === 'note') {
                    document.getElementById('workout_type').value = '';
                }
            });
        });
    });
    </script>
</body>
</html>