<!DOCTYPE html>
<html>
<head>
    <title>Esedékességi idő kalkulátor</title>

    <script>

        var initialDate = "<?php echo isset($_POST['date']) ? $_POST['date'] : ''; ?>";
        var initialTime = "<?php echo isset($_POST['time']) ? $_POST['time'] : ''; ?>";
        var initialHours = "<?php echo isset($_POST['hours']) ? $_POST['hours'] : ''; ?>";

        function resetForm() {
            document.getElementById("output").innerHTML = "";
            document.getElementById("error").innerHTML = "";
            document.getElementById("date").value = initialDate;
            document.getElementById("time").value = initialTime;
            document.getElementById("hours").value = initialHours;
        }

        function resetOnLoadForm() {
            document.getElementById("output").innerHTML = "";
            document.getElementById("error").innerHTML = "";
            document.getElementById("date").value = "";
            document.getElementById("time").value = "";
            document.getElementById("hours").value = "";
        }

        function validateForm() {
            var date = document.getElementById("date").value;
            var time = document.getElementById("time").value;
            var hours = document.getElementById("hours").value;

            var errorMessages = [];

            if (!date) {
                errorMessages.push("A benyujtasi datumot meg kell adni.");
            }

            if (!time) {
                errorMessages.push("A benyujtasi idopontot meg kell adni.");
            }

            if (!hours || isNaN(hours) || hours <= 0) {
                errorMessages.push("Az atfutasi ido teljes pozitiv ora kell legyen.");
            }

            if (errorMessages.length > 0) {
                document.getElementById("error").innerHTML = errorMessages.join("<br>");
                return false;
            }

            return true;
        }
    </script>
</head>
<body>

<h2>Esedékességi idő kalkulátor</h2>

<form method="post" action="" onsubmit="return validateForm();">
    <label for="date">Benyújtási dátum :</label>
    <input type="date" id="date" name="date"
           ><br><br>
    <label for="time">Benyújtási időpont:</label>
    <input type="time" id="time" name="time"
           ><br><br>
    <br><br>
    <label for="hours">Atfutási idő :</label>
    <input type="text" id="hours" name="hours" placeholder="Orak szama"
           ><br><br>

    <input type="submit" value="Submit">
    <input type="reset" value="Reset" onclick="resetForm()">
</form>

<div id="output">
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $date = (isset($_POST["date"]) && $_POST["date"] != "") ? $_POST["date"] : '';
        $time = (isset($_POST["time"]) && $_POST["time"] != "") ? $_POST["time"] : '';
        $hours = (isset($_POST["hours"]) && $_POST["hours"] != "") ? $_POST["hours"] : '';

        if ($date && $time && $hours) {
            $dueDate = CalculateDueDate($date." ".$time, $hours);
            echo $dueDate;
        }
    }
    ?>
</div>
<div id="error" style="color: red;"></div>

<?php
function CalculateDueDate($startDateTime, $hours)
{

    $startDateTime = new DateTime($startDateTime);


    if ($startDateTime->format('N') >= 6) {
        return "Benyujtasi datum nem lehet hetvegen.";
    }

    if ($startDateTime->format('H') < 9 || $startDateTime->format('H') >= 17) {
        return "Benyujtasi ido 09:00 es 17:00 kozott kell legyen.";
    }

    if($hours < 1 )
        return "Atfutasi ido nagyobb kell legyen 0-nal";

    // Calculate the number of days and hours
    $days = floor($hours / 8);
    $remainingHours = $hours % 8;

    // Add days and hours to the start date and time
    $dueDateTime = $startDateTime;
    $dueDateTime->modify("+$days weekdays");

    // Calculate the remaining working hours
    $remainingWorkingHours = 8 - (int)$startDateTime->format('H');
    $dueDateTime->modify("+$remainingWorkingHours hours");

    // Adjust the due date and time within working hours
    while ($remainingHours >= 8) {
        $dueDateTime->modify('+1 weekdays');
        $remainingHours -= 8;
    }

    $dueDateTime->modify("+$remainingHours hours");

    // Ensure the due time is between 09:00 AM and 05:00 PM
    if ($dueDateTime->format('H') < 9) {
        $dueDateTime->setTime(9, 0);
    } elseif ($dueDateTime->format('H') >= 17) {
        $dueDateTime->modify('+1 weekdays')->setTime(9, 0);
    }

    return "Task will be finished on : ".$dueDateTime->format('Y-m-d H:i A');
}


?>
</div>

</body>
</html>




