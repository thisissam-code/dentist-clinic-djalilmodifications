<?php

$servername = "127.0.0.1";
$username = "root";  // 
$password = "";      
$dbname = "dental_clinic_management"; // use the db provided by raouf also (add values to the tables)


$conn = new mysqli($servername, $username, $password, $dbname, 3307); // Port 3307 added
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to get appointments along with the patient name
$sql = "
    SELECT 
        a.appointment_id, 
        a.doctor_id, 
        a.patient_id, 
        a.date, 
        a.time, 
        p.name AS patient_name
    FROM appointments a
    JOIN patients p ON a.patient_id = p.patient_id
    WHERE a.date IS NOT NULL AND a.time IS NOT NULL
";

// Execute the query
$result = $conn->query($sql);

// Check if query execution was successful
if ($result === false) {
    die("Error executing query: " . $conn->error);
}

// Initialize an array to store appointments
$appointments = [];
if ($result->num_rows > 0) {
    // Fetch the results
    while($row = $result->fetch_assoc()) {
        // Format the appointment data for FullCalendar
        $start_time = $row['date'] . 'T' . $row['time']; // Combine date and time into start field
        $end_time = $row['date'] . 'T' . date('H:i:s', strtotime('+1 hour', strtotime($row['time']))); // Add an hour for the end time
        
        // Add the patient name in the event title
        $appointments[] = [
            'title' => 'Appointment with ' . $row['patient_name'], // Use patient's name here
            'start' => $start_time, // Start time for FullCalendar
            'end' => $end_time, // End time for FullCalendar
            'backgroundColor' => '#4caf50', // Custom background color for the event
            'borderColor' => '#388e3c', // Darker green border
            'textColor' => '#fff', // White text color for better contrast
            'description' => 'Click for more details' // Custom description
        ];
    }
} else {
    echo "No appointments found.";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Doctor's Appointment Calendar</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css">
  <style>
    /* styles.css content */
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background-color: #f7f7f7;
    }

    header {
      background-color: #4caf50;
      color: white;
      padding: 1rem;
      text-align: center;
      font-size: 1.8rem;
      font-weight: bold;
    }

    .container {
      display: flex;
      flex-wrap: nowrap;
      min-height: 100vh;
    }

    .sidebar {
      background-color: #a1d6a7;
      width: 250px;
      padding: 1rem;
      box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
    }

    .content {
      flex: 1;
      padding: 1.5rem;
      background-color: white;
    }

    footer {
      background-color: #4caf50;
      color: white;
      text-align: center;
      padding: 1rem;
      position: relative;
    }

    #calendar {
      max-width: 100%;
      margin: 0 auto;
      border-radius: 8px;
      background-color: white;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .fc-toolbar {
      background-color: #4caf50;
      color: white;
      padding: 10px;
    }

    .fc-daygrid-day-number {
      font-weight: bold;
    }

    .fc-event {
      background-color: #4caf50;
      border: 2px solid #388e3c;
      color: white;
      border-radius: 12px;
      padding: 12px 20px;
      font-size: 16px;
      box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.15);
      font-weight: bold;
      transition: background-color 0.3s ease, transform 0.2s ease;
      min-height: 50px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .fc-event:hover {
      background-color: #388e3c;
      transform: scale(1.1);
    }

    .fc-event-title {
      font-size: 14px;
    }

    .fc-toolbar-title {
      font-size: 1.2rem;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <header>
    Doctor's Appointment Calendar
  </header>
  <div class="container">
    <aside class="sidebar">
      <h3>Sidebar</h3>
      <ul>
        <li><a href="#">Link 1</a></li>
        <li><a href="#">Link 2</a></li>
        <li><a href="#">Link 3</a></li>
      </ul>
    </aside>
    <main class="content">
      <h1>Welcome, Doctor!</h1>
      <p>Below is the calendar displaying your appointments with patients:</p>
      <div id="calendar"></div>
    </main>
  </div>
  <footer>
    &copy; 2024 Dental Clinic. All rights reserved.
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      var calendarEl = document.getElementById('calendar');
      
      var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        events: <?php echo json_encode($appointments); ?>, // PHP variable injected here
        eventColor: '#4caf50', // Custom event color
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        eventClick: function(info) {
          alert(info.event.title); // Show event title in an alert
        },
      });

      calendar.render();
    });
  </script>
</body>
</html>
