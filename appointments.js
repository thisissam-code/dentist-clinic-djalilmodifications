// scripts.js
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
  