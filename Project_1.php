<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Time Calculator for Employees</title>
    <script>
        function updateWeekday() {
            const accessTimeInput = document.querySelector('input[name="Access_Time"]');
            const exitTimeInput = document.querySelector('input[name="Exit_Time"]');
            const accessWeekdayInput = document.querySelector('input[name="Access_Week"]');
            const exitWeekdayInput = document.querySelector('input[name="Exit_Week"]');
            
            const weekdays = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];

            const accessDate = new Date(accessTimeInput.value);
            if (!isNaN(accessDate.getDay())) {
                accessWeekdayInput.value = weekdays[accessDate.getDay()];
            }

            const exitDate = new Date(exitTimeInput.value);
            if (!isNaN(exitDate.getDay())) {
                exitWeekdayInput.value = weekdays[exitDate.getDay()];
            }
        }
    </script>
</head>
<body>
    <?php
    session_start();
    ob_start();

    $action = $_POST['action'] ?? '';

    if (!isset($_SESSION['data'])) {
        $_SESSION['data'] = [];
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $access_week = $_POST['Access_Week'];
        $exit_week = $_POST['Exit_Week'];
        $access_time_str = $_POST['Access_Time'];
        $exit_time_str = $_POST['Exit_Time'];

        $access_time = new DateTime($access_time_str);
        $exit_time = new DateTime($exit_time_str);

        $entry = [
            'access_week' => $access_week,
            'exit_week' => $exit_week,
            'access_time' => $access_time,
            'exit_time' => $exit_time
        ];

        if ($action == 'add') {
            $_SESSION['data'][] = $entry;
        } elseif ($action == 'stop') {
            displayTotal();
            session_destroy();
            ob_end_flush();
            exit;
        } elseif ($action == 'clear') {
            $_SESSION['data'] = [];
            ob_end_flush();
            header("Location: ".$_SERVER['PHP_SELF']);
            exit;
        }
    }

    function displayTotal() {
        $total_overtime = 0;
        $total_deficit = 0;

        foreach ($_SESSION['data'] as $entry) {
            $access_time = $entry['access_time'];
            $exit_time = $entry['exit_time'];

            $difference = calculate_time($access_time, $exit_time);
            $free_time = free_time($difference);

            if ($free_time > 0) {
                $total_overtime += $free_time;
            } else {
                $total_deficit -= $free_time;
            }
        }

        echo "<h2>Total Working Time Summary</h2>";

        if ($total_overtime > 0) {
            $overtime_hours_int = floor($total_overtime);
            $overtime_minutes = round(($total_overtime - $overtime_hours_int) * 60);
            echo "Total Overtime: " . $overtime_hours_int . ":" . sprintf("%02d", $overtime_minutes) . "<br>";
        }

        if ($total_deficit > 0) {
            $deficit_hours_int = floor($total_deficit);
            $deficit_minutes = round(($total_deficit - $deficit_hours_int) * 60);
            echo "Total Deficit: " . $deficit_hours_int . ":" . sprintf("%02d", $deficit_minutes) . "<br>";
        }

        $total_time = $total_overtime - $total_deficit;
        $total_hours_int = floor($total_time);
        $total_minutes = round(($total_time - $total_hours_int) * 60);
        echo "<strong>Total Time: " . $total_hours_int . ":" . sprintf("%02d", $total_minutes) . "</strong><br>";
    }

    function displayData() {
        if (!empty($_SESSION['data'])) {
            $weekdays = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
            $data_by_day = [];

            foreach ($_SESSION['data'] as $entry) {
                $access_week = $entry['access_week'];
                $exit_week = $entry['exit_week'];
                $access_time = $entry['access_time'];
                $exit_time = $entry['exit_time'];

                $difference = calculate_time($access_time, $exit_time);
                $free_time = free_time($difference);

                $data_by_day[$access_week][] = [
                    'access_time' => $access_time,
                    'exit_time' => $exit_time,
                    'difference' => $difference,
                    'free_time' => $free_time
                ];
            }

            echo '<table border="1">';
            echo '<tr><th>Weekday</th><th>Access Time</th><th>Exit Time</th><th>Working Time</th><th>Overtime/Deficit</th></tr>';

            foreach ($weekdays as $day) {
                if (isset($data_by_day[$day])) {
                    foreach ($data_by_day[$day] as $entry) {
                        $access_time = $entry['access_time'];
                        $exit_time = $entry['exit_time'];
                        $difference = $entry['difference'];
                        $free_time = $entry['free_time'];

                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($day) . '</td>';
                        echo '<td>' . $access_time->format('Y-m-d H:i') . '</td>';
                        echo '<td>' . $exit_time->format('Y-m-d H:i') . '</td>';
                        echo '<td>' . $difference->format('%H hours %i minutes') . '</td>';

                        if ($free_time > 0) {
                            $overtime_hours_int = floor($free_time);
                            $overtime_minutes = round(($free_time - $overtime_hours_int) * 60);
                            echo '<td>Overtime: ' . $overtime_hours_int . ' hours ' . sprintf("%02d", $overtime_minutes) . ' minutes</td>';
                        } else {
                            $deficit_hours_int = floor(-$free_time);
                            $deficit_minutes = round((-$free_time - $deficit_hours_int) * 60);
                            echo '<td>Deficit: ' . $deficit_hours_int . ' hours ' . sprintf("%02d", $deficit_minutes) . ' minutes</td>';
                        }

                        echo '</tr>';
                    }
                }
            }

            echo '</table>';
        }
    }

    function calculate_time($access_time, $exit_time) {
        return $access_time->diff($exit_time);
    }

    function free_time($difference) {
        $worked_hours = $difference->h + ($difference->i / 60);

        if ($worked_hours > 9) {
            $overtime_hours = $worked_hours - 9;
            return $overtime_hours;
        } else {
            $deficit_hours = 9 - $worked_hours;
            return -$deficit_hours;
        }
    }
    ?>

    <?php if ($action != 'stop'): ?>
        <form action="" method="post">
            Access Weekday: <input type="text" name="Access_Week" readonly><br>
            Access Time: <input type="datetime-local" name="Access_Time" required oninput="updateWeekday()"><br>
            Exit Weekday: <input type="text" name="Exit_Week" readonly><br>
            Exit Time: <input type="datetime-local" name="Exit_Time" required oninput="updateWeekday()"><br>
            <button type="submit" name="action" value="add">Add</button>
            <button type="submit" name="action" value="stop">Stop</button>
            <button type="submit" name="action" value="clear">Clear</button>
        </form>

        <pre>
        <?php
        displayData();
        displayTotal(); 
        ?>
        </pre>
    <?php endif; ?>
</body>
</html>
# Time-Calculator-for-the-Employer
# Time-Calculator-for-the-Employer
