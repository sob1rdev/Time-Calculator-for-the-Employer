<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Time Tracker</title>
</head>
<body>
    <form id="employeeForm" action="Task_h1.php" method="post">
        <h1>Employee name: <input type="text" name="name"></h1>
        <h2>Monday</h2>
        Arrived at: <input type="datetime-local" name="arrivedAt[Monday]"> <br>
        Leaved at: <input type="datetime-local" name="leavedAt[Monday]"> <br>

        <h2>Tuesday</h2>
        Arrived at: <input type="datetime-local" name="arrivedAt[Tuesday]"> <br>
        Leaved at: <input type="datetime-local" name="leavedAt[Tuesday]"> <br>

        <h2>Wednesday</h2>
        Arrived at: <input type="datetime-local" name="arrivedAt[Wednesday]"> <br>
        Leaved at: <input type="datetime-local" name="leavedAt[Wednesday]"> <br>

        <h2>Thursday</h2>
        Arrived at: <input type="datetime-local" name="arrivedAt[Thursday]"> <br>
        Leaved at: <input type="datetime-local" name="leavedAt[Thursday]"> <br>

        <h2>Friday</h2>
        Arrived at: <input type="datetime-local" name="arrivedAt[Friday]"> <br>
        Leaved at: <input type="datetime-local" name="leavedAt[Friday]"> <br>

        <button type="submit" name="action" value="send">Send</button>
        <button type="submit" name="action" value="clear">Clear</button>
    </form>

    <div id="resultTable">
        <?php
        function calculate_time($access_time, $exit_time)
        {
            $access_time_dt = new DateTime($access_time);
            $exit_time_dt = new DateTime($exit_time);
            $difference = $access_time_dt->diff($exit_time_dt);
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

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'send') {
            $name = $_POST['name'];
            $arrivedAt = $_POST['arrivedAt'];
            $leavedAt = $_POST['leavedAt'];
            
            $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

            echo "<table border='1'>";
            echo "<tr><th>Employee</th><th>Day</th><th>Arrived At</th><th>Leaved At</th><th>Working Time</th><th>Overtime/Deficit</th></tr>";

            foreach ($days as $day) {
                $arrivedAtDay = $arrivedAt[$day];
                $leavedAtDay = $leavedAt[$day];

                $arrivedAtFormatted = (new DateTime($arrivedAtDay))->format('Y-m-d H:i');
                $leavedAtFormatted = (new DateTime($leavedAtDay))->format('Y-m-d H:i');

                $difference = calculate_time($arrivedAtDay, $leavedAtDay);
                $free_time = free_time($difference);

                $total_hours = floor($difference->h + ($difference->i / 60));
                $total_minutes = $difference->i % 60;

                $working_time = "$total_hours : $total_minutes ";

                if ($free_time > 0) {
                    $overtime_hours_int = floor($free_time);
                    $overtime_minutes = round(($free_time - $overtime_hours_int) * 60);
                    $overtime_deficit = "$overtime_hours_int : $overtime_minut";
                } else {
                    $deficit_hours_int = floor(-$free_time);
                    $deficit_minutes = round((-$free_time - $deficit_hours_int) * 60);
                    $overtime_deficit = "$deficit_hours_int : $deficit_minute";
                }

                echo "<tr>
                        <td>$name</td>
                        <td>$day</td>
                        <td>$arrivedAtFormatted</td>
                        <td>$leavedAtFormatted</td>
                        <td>$working_time</td>
                        <td>$overtime_deficit</td>
                      </tr>";
            }

            echo "</table>";
        }
        ?>
    </div>
</body>
</html>
