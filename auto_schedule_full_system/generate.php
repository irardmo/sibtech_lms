<?php
require 'db.php';

$subject = $_POST['subject'];
$course_names = $_POST['course_names'];
$sections = $_POST['sections'];
$teachers = $_POST['teachers'];

$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
$rooms = ['Room 101', 'Room 102', 'ComLab', 'HS101', 'Library'];

$id = 0;
foreach ($teachers as $teacher) {
  foreach ($course_names as $i => $course) {
    for ($s = 0; $s < $sections[$i]; $s++) {
      $stmt = $pdo->prepare("INSERT INTO schedules (teacher, room, day, time_start, time_end, year, block, subject, course, lec, lab) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
      $stmt->execute([
        $teacher,
        $rooms[$id % count($rooms)],
        $days[$id % count($days)],
        sprintf('%02d:00 AM', 7 + ($id % 5)),
        sprintf('%02d:00 AM', 8 + ($id % 5)),
        '1st Year',
        chr(65 + ($id % 3)),
        $subject,
        $course,
        1,
        $id % 3
      ]);
      $id++;
    }
  }
}
header("Location: index.php");
exit;
?>
