<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "scheduling_system";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['action'])) {
        if ($_GET['action'] == 'get_admin_load') {
            $sql = "SELECT * FROM admin_load";
            if (isset($_GET['id'])) {
                $sql .= " WHERE id = " . $_GET['id'];
                $result = $conn->query($sql);
                echo json_encode($result->fetch_assoc());
            } else {
                if (isset($_GET['teacher'])) {
                    $sql .= " WHERE teacher = '" . $_GET['teacher'] . "'";
                }
                $result = $conn->query($sql);
                $admin_load = array();
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $admin_load[] = $row;
                    }
                }
                echo json_encode($admin_load);
            }
            exit();
        }
        if ($_GET['action'] == 'get_schedule') {
            $sql = "SELECT * FROM schedules WHERE id = " . $_GET['id'];
            $result = $conn->query($sql);
            echo json_encode($result->fetch_assoc());
            exit();
        }
    }
    

    $sql = "SELECT * FROM schedules";

    $where = array();
    if (isset($_GET['teacher'])) {
        $where[] = "teacher = '" . $_GET['teacher'] . "'";
    }
    if (isset($_GET['course'])) {
        $where[] = "course = '" . $_GET['course'] . "'";
    }
    if (isset($_GET['year'])) {
        $where[] = "year = '" . $_GET['year'] . "'";
    }
    if (isset($_GET['block'])) {
        $where[] = "block = '" . $_GET['block'] . "'";
    }

    if (count($where) > 0) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }

    $result = $conn->query($sql);

    $schedules = array();
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $schedules[] = $row;
        }
    }

    echo json_encode($schedules);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'add_admin_load') {
            $teacher = $_POST['teacher'];
            $office = $_POST['office'];
            $load = $_POST['load'];
            $day = $_POST['day'];
            $time = $_POST['time'];
            $hours = $_POST['hours'];

            $sql = "INSERT INTO admin_load (teacher, office, `load`, day, `time`, hours)
            VALUES ('$teacher', '$office', '$load', '$day', '$time', '$hours')";

            if ($conn->query($sql) === TRUE) {
                echo json_encode(array("status" => "success", "message" => "New record created successfully"));
            } else {
                echo json_encode(array("status" => "error", "message" => "Error: " . $sql . "<br>" . $conn->error));
            }
            exit();
        }

        if ($_POST['action'] == 'update_admin_load') {
            $id = $_POST['id'];
            $teacher = $_POST['teacher'];
            $office = $_POST['office'];
            $load = $_POST['load'];
            $day = $_POST['day'];
            $time = $_POST['time'];
            $hours = $_POST['hours'];

            $sql = "UPDATE admin_load SET teacher='$teacher', office='$office', `load`='$load', day='$day', `time`='$time', hours='$hours' WHERE id=$id";

            if ($conn->query($sql) === TRUE) {
                echo json_encode(array("status" => "success", "message" => "Record updated successfully"));
            } else {
                echo json_encode(array("status" => "error", "message" => "Error: " . $sql . "<br>" . $conn->error));
            }
            exit();
        }

        if ($_POST['action'] == 'delete_admin_load') {
            $id = $_POST['id'];

            $sql = "DELETE FROM admin_load WHERE id=$id";

            if ($conn->query($sql) === TRUE) {
                echo json_encode(array("status" => "success", "message" => "Record deleted successfully"));
            } else {
                echo json_encode(array("status" => "error", "message" => "Error: " . $sql . "<br>" . $conn->error));
            }
            exit();
        }

        if ($_POST['action'] == 'add_schedule') {
            $teacher = $_POST['teacher'];
            $room = $_POST['room'];
            $day = $_POST['day'];
            $time_start = $_POST['time_start'];
            $time_end = $_POST['time_end'];
            $year = $_POST['year'];
            $block = $_POST['block'];
            $subject = $_POST['subject'];
            $course = $_POST['course'];
            $lec = $_POST['lec'];
            $lab = $_POST['lab'];

            // Check for conflicts
            $sql = "SELECT * FROM schedules WHERE 
                    (teacher = '$teacher' OR room = '$room') AND 
                    day = '$day' AND 
                    ((time_start <= '$time_start' AND time_end > '$time_start') OR 
                    (time_start < '$time_end' AND time_end >= '$time_end') OR
                    (time_start >= '$time_start' AND time_end <= '$time_end'))";
            
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                echo json_encode(array("status" => "error", "message" => "Conflict detected!"));
            } else {
                $sql = "INSERT INTO schedules (teacher, room, day, time_start, time_end, year, block, subject, course, lec, lab)
                VALUES ('$teacher', '$room', '$day', '$time_start', '$time_end', '$year', '$block', '$subject', '$course', '$lec', '$lab')";

                if ($conn->query($sql) === TRUE) {
                    echo json_encode(array("status" => "success", "message" => "New record created successfully"));
                } else {
                    echo json_encode(array("status" => "error", "message" => "Error: " . $sql . "<br>" . $conn->error));
                }
            }
            exit();
        }

        if ($_POST['action'] == 'update_schedule') {
            $id = $_POST['id'];
            $teacher = $_POST['teacher'];
            $room = $_POST['room'];
            $day = $_POST['day'];
            $time_start = $_POST['time_start'];
            $time_end = $_POST['time_end'];
            $year = $_POST['year'];
            $block = $_POST['block'];
            $subject = $_POST['subject'];
            $course = $_POST['course'];
            $lec = $_POST['lec'];
            $lab = $_POST['lab'];

            $sql = "UPDATE schedules SET teacher='$teacher', room='$room', day='$day', time_start='$time_start', time_end='$time_end', year='$year', block='$block', subject='$subject', course='$course', lec='$lec', lab='$lab' WHERE id=$id";

            if ($conn->query($sql) === TRUE) {
                echo json_encode(array("status" => "success", "message" => "Record updated successfully"));
            } else {
                echo json_encode(array("status" => "error", "message" => "Error: " . $sql . "<br>" . $conn->error));
            }
            exit();
        }

        if ($_POST['action'] == 'delete_schedule') {
            $id = $_POST['id'];

            $sql = "DELETE FROM schedules WHERE id=$id";

            if ($conn->query($sql) === TRUE) {
                echo json_encode(array("status" => "success", "message" => "Record deleted successfully"));
            } else {
                echo json_encode(array("status" => "error", "message" => "Error: " . $sql . "<br>" . $conn->error));
            }
            exit();
        }
    }
}

$conn->close();
?>
