<?php
require 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Auto-generate Schedule</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    input[type="text"], input[type="number"] { padding: 5px; margin: 5px; background-color: #e6f0ff; }
    button { padding: 5px 10px; margin: 5px; background-color: #007bff; color: white; border: none; cursor: pointer; }
    table { border-collapse: collapse; width: 100%; margin-top: 20px; }
    table, th, td { border: 1px solid #ccc; }
    th, td { padding: 8px; text-align: center; }
    .remove-btn { background-color: red; }
  </style>
</head>
<body>
  <h2>Auto-generate Schedule</h2>
  <form method="POST" action="generate.php">
    <label>Subject: <input type="text" name="subject" required /></label><br>

    <div id="courses">
      <div>
        <label>Course Name: <input type="text" name="course_names[]" required /></label>
        <label>Number of Sections: <input type="number" name="sections[]" required /></label>
      </div>
    </div>
    <button type="button" onclick="addCourse()">Add Course</button><br><br>

    <div id="teachers">
      <div>
        <label>Teacher Name: <input type="text" name="teachers[]" required /></label>
      </div>
    </div>
    <button type="button" onclick="addTeacher()">Add Teacher</button><br><br>

    <button type="submit">Generate Schedule</button>
  </form>

  <table id="scheduleTable">
    <thead>
      <tr>
        <th>Teacher</th><th>Room</th><th>Day</th><th>Time Start</th><th>Time End</th>
        <th>Year</th><th>Block</th><th>Subject</th><th>Course</th><th>Lec</th><th>Lab</th><th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php
        $stmt = $pdo->query("SELECT * FROM schedules");
        while ($row = $stmt->fetch()) {
          echo "<tr>
            <td>{$row['teacher']}</td>
            <td>{$row['room']}</td>
            <td>{$row['day']}</td>
            <td>{$row['time_start']}</td>
            <td>{$row['time_end']}</td>
            <td>{$row['year']}</td>
            <td>{$row['block']}</td>
            <td>{$row['subject']}</td>
            <td>{$row['course']}</td>
            <td>{$row['lec']}</td>
            <td>{$row['lab']}</td>
            <td><button>Edit</button> <button>Delete</button></td>
          </tr>";
        }
      ?>
    </tbody>
  </table>

<script>
  function addCourse() {
    const div = document.createElement("div");
    div.innerHTML = '<label>Course Name: <input type="text" name="course_names[]" required /></label> <label>Number of Sections: <input type="number" name="sections[]" required /></label>';
    document.getElementById("courses").appendChild(div);
  }
  function addTeacher() {
    const div = document.createElement("div");
    div.innerHTML = '<label>Teacher Name: <input type="text" name="teachers[]" required /></label> <button class="remove-btn" onclick="this.parentElement.remove()">Remove</button>';
    document.getElementById("teachers").appendChild(div);
  }
</script>
</body>
</html>
