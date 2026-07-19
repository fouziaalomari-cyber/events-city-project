<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host = '127.0.0.1';
$user = 'root';
$pass = '';
$dbname = 'events_city';

function connectDb(): mysqli
{
    global $host, $user, $pass, $dbname;

    $conn = new mysqli($host, $user, $pass, $dbname);
    $conn->set_charset('utf8mb4');
    return $conn;
}

function ensureDatabase(): bool
{
    global $host, $user, $pass, $dbname;

    try {
        $conn = new mysqli($host, $user, $pass);
        $conn->set_charset('utf8mb4');
        $conn->query("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $conn->select_db($dbname);

        $conn->query("CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $conn->query("CREATE TABLE IF NOT EXISTS events (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT NOT NULL,
            category VARCHAR(100) NOT NULL,
            location VARCHAR(255) NOT NULL,
            event_date DATE NOT NULL,
            image VARCHAR(255) NOT NULL DEFAULT 'assets/img/placeholder.svg'
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $stmt = $conn->prepare('SELECT COUNT(*) FROM users WHERE username = ?');
        $stmt->bind_param('s', $username);
        $username = 'admin';
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ((int) $count === 0) {
            $hash = password_hash('admin123', PASSWORD_DEFAULT);
            $insert = $conn->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
            $insert->bind_param('ss', $username, $hash);
            $insert->execute();
            $insert->close();
        }

        $sampleEvents = [
            [
                'عنوان فعالية الثقافة الرقمية',
                'ورشة تفاعلية حول مستقبل التقنية والثقافة في المجتمع.',
                'ثقافة',
                'قاعة الهندسة',
                '2026-08-10',
                'assets/img/culture.svg',
            ],
            [
                'مهرجان الرياضة المجتمعية',
                'يوم رياضي مفتوح يجمع الطلاب والموظفين في منافسات متنوعة.',
                'رياضة',
                'الميدان المركزي',
                '2026-08-14',
                'assets/img/sports.svg',
            ],
            [
                'ليلة موسيقى عربية',
                'حفلة موسيقية حية مع فرق طلابية وموسيقى تراثية.',
                'موسيقى',
                'مسرح الجامعة',
                '2026-08-20',
                'assets/img/music.svg',
            ],
            [
                'أمسية عائلية',
                'فعالية ممتعة للعائلات مع أنشطة تعليمية وترفيهية.',
                'عائلي',
                'الحديقة العامة',
                '2026-08-25',
                'assets/img/family.svg',
            ],
        ];

        foreach ($sampleEvents as $event) {
            $existing = $conn->prepare('SELECT COUNT(*) FROM events WHERE title = ?');
            $existing->bind_param('s', $event[0]);
            $existing->execute();
            $existing->bind_result($exists);
            $existing->fetch();
            $existing->close();

            if ((int) $exists === 0) {
                $insertEvent = $conn->prepare('INSERT INTO events (title, description, category, location, event_date, image) VALUES (?, ?, ?, ?, ?, ?)');
                $insertEvent->bind_param('ssssss', $event[0], $event[1], $event[2], $event[3], $event[4], $event[5]);
                $insertEvent->execute();
                $insertEvent->close();
            }
        }

        return true;
    } catch (Throwable $e) {
        return false;
    }
}

function getEventsList(int $limit = 0): array
{
    $conn = connectDb();
    $sql = 'SELECT * FROM events ORDER BY event_date DESC';
    if ($limit > 0) {
        $sql .= ' LIMIT ' . (int) $limit;
    }

    $result = $conn->query($sql);
    $events = [];
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
    $conn->close();
    return $events;
}

function getEventById(int $id): ?array
{
    $conn = connectDb();
    $stmt = $conn->prepare('SELECT * FROM events WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $event = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
    return $event ?: null;
}
