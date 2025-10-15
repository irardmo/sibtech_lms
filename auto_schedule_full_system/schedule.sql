
CREATE TABLE IF NOT EXISTS schedules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    teacher VARCHAR(100),
    room VARCHAR(100),
    day VARCHAR(20),
    start_time VARCHAR(20),
    end_time VARCHAR(20),
    year VARCHAR(50),
    block VARCHAR(10),
    subject VARCHAR(100),
    course VARCHAR(100),
    lec INT,
    lab INT
);
