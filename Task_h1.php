<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Time Tracker</title>
    <script>
        function clearFormAndTable() {
            document.getElementById("employeeForm").reset();
            document.getElementById("resultTable").innerHTML = "";
        }
    </script>
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

        <button type="submit">Send</button>
        <button type="button" onclick="clearFormAndTable()">Clear</button>
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

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $arrivedAt = $_POST['arrivedAt'];
            $leavedAt = $_POST['leavedAt'];
            
            $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

            $total_worked_hours = 0;
            $total_worked_minutes = 0;
            $total_overtime_deficit = 0;

            echo "<table border='1'>";
            echo "<tr><th>Employee</th><th>Day</th><th>Arrived At</th><th>Leaved At</th><th>Working Time</th><th>Overtime/Deficit</th></tr>";

            foreach ($days as $day) {
                $arrivedAtDay = $arrivedAt[$day];
                $leavedAtDay = $leavedAt[$day];

                $arrivedAtFormatted = (new DateTime($arrivedAtDay))->format('Y-m-d H:i');
                $leavedAtFormatted = (new DateTime($leavedAtDay))->format('Y-m-d H:i');

                $difference = calculate_time($arrivedAtDay, $leavedAtDay);
                $free_time = free_time($difference);

                $total_hours = $difference->h + ($difference->i / 60);
                $total_minutes = $difference->i % 60;

                $total_worked_hours += $difference->h;
                $total_worked_minutes += $difference->i;

                $working_time = "$total_hours : $total_minutes";

                if ($free_time > 0) {
                    $overtime_hours_int = floor($free_time);
                    $overtime_minutes = round(($free_time - $overtime_hours_int) * 60);
                    $overtime_deficit = "$overtime_hours_int : $overtime_minutes";
                } else {
                    $deficit_hours_int = floor(-$free_time);
                    $deficit_minutes = round((-$free_time - $deficit_hours_int) * 60);
                    $overtime_deficit = "$deficit_hours_int : $deficit_minutes";
                }

                $total_overtime_deficit += $free_time;

                echo "<tr>
                        <td>$name</td>
                        <td>$day</td>
                        <td>$arrivedAtFormatted</td>
                        <td>$leavedAtFormatted</td>
                        <td>$working_time</td>
                        <td>$overtime_deficit</td>
                      </tr>";
            }

            $total_worked_hours += floor($total_worked_minutes / 60);
            $total_worked_minutes = $total_worked_minutes % 60;

            if ($total_overtime_deficit > 0) {
                $total_overtime_hours = floor($total_overtime_deficit);
                $total_overtime_minutes = round(($total_overtime_deficit - $total_overtime_hours) * 60);
                $total_overtime_deficit_formatted = "$total_overtime_hours : $total_overtime_minutes";
            } else {
                $total_deficit_hours = floor(-$total_overtime_deficit);
                $total_deficit_minutes = round((-$total_overtime_deficit - $total_deficit_hours) * 60);
                $total_overtime_deficit_formatted = "$total_deficit_hours : $total_deficit_minutes";
            }

            echo "<tr>
                    <td colspan='4'><strong>Total</strong></td>
                    <td><strong>$total_worked_hours hours $total_worked_minutes minutes</strong></td>
                    <td><strong>$total_overtime_deficit_formatted</strong></td>
                  </tr>";

            echo "</table>";
        }
        ?>
    </div>
</body>
</html>
