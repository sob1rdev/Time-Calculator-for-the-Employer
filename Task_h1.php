<form action="Task_c1.php" method="post">
    Access Time: <input type="datetime-local" name="Access_Time"><br>
    Exit Time: <input type="datetime-local" name="Exit_Time"><br>
    <button type="submit">Send</button>
</form>

<pre>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['Access_Time']) && isset($_POST['Exit_Time'])) {
        $access_time_str = $_POST['Access_Time'];
        $exit_time_str = $_POST['Exit_Time'];

        $access_time = new DateTime($access_time_str);
        $exit_time = new DateTime($exit_time_str);

        $difference = calculate_time($access_time, $exit_time);

        $free_time = free_time($difference);

        echo "Access Time: " . $access_time->format('Y-m-d H:i') . "\n";
        echo "Exit Time: " . $exit_time->format('Y-m-d H:i') . "\n";
        echo "\n";
        echo "Working time: " . $difference->format('%H hours %i minutes') . "\n";
        
        if ($free_time > 0) {
            $overtime_hours_int = floor($free_time);
            $overtime_minutes = round(($free_time - $overtime_hours_int) * 60);
            echo "Overtime: " . $overtime_hours_int . " hours " . $overtime_minutes . " minutes\n";
        } else {
            $deficit_hours_int = floor(-$free_time);
            $deficit_minutes = round((-$free_time - $deficit_hours_int) * 60);
            echo "Deficit: " . $deficit_hours_int . " hours " . $deficit_minutes . " minutes\n";
        }
    } else {
        echo "Access Time and Exit Time must be filled out.\n";
    }
}

function calculate_time($access_time, $exit_time)
{
    $difference = $access_time->diff($exit_time);
    return $difference;
}

function free_time($difference)
{
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
</pre>
# Time-calculator-for-employees
