<?php
include("config.php");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch visit dates and statuses
$sql = "SELECT VisitDate, Status FROM accreditationvisits";
$result = $conn->query($sql);

$visits = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $visits[] = $row;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assessor Schedule</title>
    <style>
        .calendar {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 5px;
            padding: 10px;
        }
        .day {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        .day.scheduled {
            background-color: red;
            color: white;
        }
        .day.in-progress {
            background-color: green;
            color: white;
        }
        .day.completed {
            background-color: blue;
            color: white;
        }
        .navigation {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .navigation button {
            padding: 10px;
        }
    </style>
</head>
<body>
    <h1>Assessor Schedule</h1>
    <div class="navigation">
        <button id="prevMonth">Previous</button>
        <div id="currentMonthYear"></div>
        <button id="nextMonth">Next</button>
    </div>
    <div class="calendar" id="calendar">
        <!-- Calendar will be generated here -->
    </div>
    <script>
        const visits = <?php echo json_encode($visits); ?>;
        let currentMonth = new Date().getMonth();
        let currentYear = new Date().getFullYear();

        function daysInMonth(month, year) {
            return new Date(year, month + 1, 0).getDate();
        }

        function generateCalendar(month, year) {
            const calendar = document.getElementById('calendar');
            const currentMonthYear = document.getElementById('currentMonthYear');
            calendar.innerHTML = '';
            currentMonthYear.innerText = new Date(year, month).toLocaleString('default', { month: 'long', year: 'numeric' });
            const firstDay = new Date(year, month, 1).getDay();
            const totalDays = daysInMonth(month, year);

            for (let i = 0; i < firstDay; i++) {
                const emptyDay = document.createElement('div');
                emptyDay.classList.add('day');
                calendar.appendChild(emptyDay);
            }

            for (let day = 1; day <= totalDays; day++) {
                const date = new Date(year, month, day);
                const formattedDate = date.toISOString().split('T')[0];
                const visit = visits.find(v => v.VisitDate === formattedDate);

                const dayDiv = document.createElement('div');
                dayDiv.classList.add('day');
                dayDiv.innerText = day;

                if (visit) {
                    if (visit.Status === 'Scheduled') {
                        dayDiv.classList.add('scheduled');
                    } else if (visit.Status === 'In Progress') {
                        dayDiv.classList.add('in-progress');
                    } else if (visit.Status === 'Completed') {
                        dayDiv.classList.add('completed');
                    }
                }

                calendar.appendChild(dayDiv);
            }
        }

        document.getElementById('prevMonth').addEventListener('click', () => {
            if (currentMonth === 0) {
                currentMonth = 11;
                currentYear--;
            } else {
                currentMonth--;
            }
            generateCalendar(currentMonth, currentYear);
        });

        document.getElementById('nextMonth').addEventListener('click', () => {
            if (currentMonth === 11) {
                currentMonth = 0;
                currentYear++;
            } else {
                currentMonth++;
            }
            generateCalendar(currentMonth, currentYear);
        });

        generateCalendar(currentMonth, currentYear);
    </script>
</body>
</html>
