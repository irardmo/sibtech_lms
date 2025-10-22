<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['action'])) {
        if ($_GET['action'] == 'get_admin_load') {
            $sql = "SELECT * FROM admin_load";
            $params = [];
            if (isset($_GET['id'])) {
                $sql .= " WHERE id = ?";
                $params[] = $_GET['id'];
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                echo json_encode($stmt->fetch());
            } else {
                if (isset($_GET['teacher'])) {
                    $sql .= " WHERE teacher = ?";
                    $params[] = $_GET['teacher'];
                }
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                echo json_encode($stmt->fetchAll());
            }
            exit();
        }
        if ($_GET['action'] == 'get_schedule') {
            $sql = "SELECT * FROM schedules WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$_GET['id']]);
            echo json_encode($stmt->fetch());
            exit();
        }
    }

    $sql = "SELECT * FROM schedules";
    $where = [];
    $params = [];
    if (isset($_GET['teacher'])) {
        $where[] = "teacher = ?";
        $params[] = $_GET['teacher'];
    }
    if (isset($_GET['course'])) {
        $where[] = "course = ?";
        $params[] = $_GET['course'];
    }
    if (isset($_GET['year'])) {
        $where[] = "year = ?";
        $params[] = $_GET['year'];
    }
    if (isset($_GET['block'])) {
        $where[] = "block = ?";
        $params[] = $_GET['block'];
    }

    if (count($where) > 0) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    echo json_encode($stmt->fetchAll());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        try {
            if ($_POST['action'] == 'add_admin_load') {
                $sql = "INSERT INTO admin_load (teacher, office, `load`, day, `time`, hours) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$_POST['teacher'], $_POST['office'], $_POST['load'], $_POST['day'], $_POST['time'], $_POST['hours']]);
                echo json_encode(["status" => "success", "message" => "New record created successfully"]);
                exit();
            }

            if ($_POST['action'] == 'update_admin_load') {
                $sql = "UPDATE admin_load SET teacher=?, office=?, `load`=?, day=?, `time`=?, hours=? WHERE id=?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$_POST['teacher'], $_POST['office'], $_POST['load'], $_POST['day'], $_POST['time'], $_POST['hours'], $_POST['id']]);
                echo json_encode(["status" => "success", "message" => "Record updated successfully"]);
                exit();
            }

            if ($_POST['action'] == 'delete_admin_load') {
                $sql = "DELETE FROM admin_load WHERE id=?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$_POST['id']]);
                echo json_encode(["status" => "success", "message" => "Record deleted successfully"]);
                exit();
            }

            if ($_POST['action'] == 'add_schedule') {
                $teacher = $_POST['teacher'];
                $room = $_POST['room'];
                $day = $_POST['day'];
                $time_start = $_POST['time_start'];
                $time_end = $_POST['time_end'];

                // Check for conflicts
                $sql = "SELECT * FROM schedules WHERE
                        (teacher = ? OR room = ?) AND
                        day = ? AND
                        ((time_start <= ? AND time_end > ?) OR
                        (time_start < ? AND time_end >= ?) OR
                        (time_start >= ? AND time_end <= ?))";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$teacher, $room, $day, $time_start, $time_start, $time_end, $time_end, $time_start, $time_end]);

                if ($stmt->rowCount() > 0) {
                    echo json_encode(["status" => "error", "message" => "Conflict detected!"]);
                } else {
                    $sql = "INSERT INTO schedules (teacher, room, day, time_start, time_end, year, block, subject, course, lec, lab)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$teacher, $room, $day, $time_start, $time_end, $_POST['year'], $_POST['block'], $_POST['subject'], $_POST['course'], $_POST['lec'], $_POST['lab']]);
                    echo json_encode(["status" => "success", "message" => "New record created successfully"]);
                }
                exit();
            }

            if ($_POST['action'] == 'update_schedule') {
                $sql = "UPDATE schedules SET teacher=?, room=?, day=?, time_start=?, time_end=?, year=?, block=?, subject=?, course=?, lec=?, lab=? WHERE id=?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$_POST['teacher'], $_POST['room'], $_POST['day'], $_POST['time_start'], $_POST['time_end'], $_POST['year'], $_POST['block'], $_POST['subject'], $_POST['course'], $_POST['lec'], $_POST['lab'], $_POST['id']]);
                echo json_encode(["status" => "success", "message" => "Record updated successfully"]);
                exit();
            }

            if ($_POST['action'] == 'delete_schedule') {
                $sql = "DELETE FROM schedules WHERE id=?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$_POST['id']]);
                echo json_encode(["status" => "success", "message" => "Record deleted successfully"]);
                exit();
            }
        } catch (PDOException $e) {
            echo json_encode(["status" => "error", "message" => "Error: " . $e->getMessage()]);
            exit();
        }
    }
}
?>