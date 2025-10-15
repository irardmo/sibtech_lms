CREATE TABLE `schedules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `teacher` varchar(255) NOT NULL,
  `room` varchar(255) NOT NULL,
  `day` varchar(255) NOT NULL,
  `time_start` time NOT NULL,
  `time_end` time NOT NULL,
  `year` varchar(255) NOT NULL,
  `block` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `course` varchar(255) NOT NULL,
  `lec` INT DEFAULT NULL,
  `lab` INT DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `admin_load` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `teacher` varchar(255) NOT NULL,
  `office` varchar(255) NOT NULL,
  `load` varchar(255) NOT NULL,
  `day` varchar(255) NOT NULL,
  `time` varchar(255) NOT NULL,
  `hours` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;