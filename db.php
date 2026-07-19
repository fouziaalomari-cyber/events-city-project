<?php
if (class_exists('mysqli')) {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
}

$host = '127.0.0.1';
$user = 'root';
$pass = '';
$dbname = 'events_city';
$port = 3306;

class PdoResultWrapper
{
    private PDOStatement $stmt;

    public function __construct(PDOStatement $stmt)
    {
        $this->stmt = $stmt;
    }

    public function fetch_assoc(): ?array
    {
        $row = $this->stmt->fetch(PDO::FETCH_ASSOC);
        return is_array($row) ? $row : null;
    }
}

class PdoStatementWrapper
{
    private PDOStatement $stmt;

    public function __construct(PDOStatement $stmt)
    {
        $this->stmt = $stmt;
    }

    public function bind_param(string $types, mixed &...$params): bool
    {
        $typeMap = [
            'i' => PDO::PARAM_INT,
            's' => PDO::PARAM_STR,
            'd' => PDO::PARAM_STR,
        ];

        foreach ($params as $index => $param) {
            $pdoType = $typeMap[$types[$index]] ?? PDO::PARAM_STR;
            $this->stmt->bindParam($index + 1, $param, $pdoType);
        }

        return true;
    }

    public function execute(): bool
    {
        return $this->stmt->execute();
    }

    public function get_result(): PdoResultWrapper
    {
        return new PdoResultWrapper($this->stmt);
    }

    public function close(): void
    {
    }
}

class PdoConnectionWrapper
{
    private ?PDO $pdo;

    public function __construct(string $dsn, string $user, string $pass, array $options = [])
    {
        $this->pdo = new PDO($dsn, $user, $pass, $options);
    }

    public function set_charset(string $charset): void
    {
        if ($this->pdo !== null) {
            $this->pdo->exec("SET NAMES '$charset'");
        }
    }

    public function prepare(string $sql): PdoStatementWrapper
    {
        return new PdoStatementWrapper($this->pdo->prepare($sql));
    }

    public function query(string $sql): PdoResultWrapper
    {
        $stmt = $this->pdo->query($sql);
        return new PdoResultWrapper($stmt);
    }

    public function select_db(string $db): void
    {
        if ($this->pdo !== null) {
            $this->pdo->exec("USE `$db`");
        }
    }

    public function close(): void
    {
        $this->pdo = null;
    }
}

function loadDatabaseConfig(): void
{
    global $host, $user, $pass, $dbname, $port;

    $databaseUrl = getenv('DATABASE_URL') ?: getenv('JAWSDB_URL') ?: getenv('CLEARDB_DATABASE_URL');
    if ($databaseUrl) {
        $parts = parse_url($databaseUrl);
        if ($parts !== false) {
            $host = $parts['host'] ?? $host;
            $user = $parts['user'] ?? $user;
            $pass = $parts['pass'] ?? $pass;
            $dbname = isset($parts['path']) ? ltrim($parts['path'], '/') : $dbname;
            $port = isset($parts['port']) ? (int) $parts['port'] : $port;
        }
    }

    $host = getenv('MYSQL_HOST') ?: $host;
    $user = getenv('MYSQL_USER') ?: $user;
    $pass = getenv('MYSQL_PASSWORD') ?: $pass;
    $dbname = getenv('MYSQL_DATABASE') ?: $dbname;
    $port = getenv('MYSQL_PORT') ?: $port;
}

loadDatabaseConfig();

function connectDb(): object
{
    global $host, $user, $pass, $dbname, $port;

    if (class_exists('mysqli')) {
        $conn = new mysqli($host, $user, $pass, $dbname, $port);
        $conn->set_charset('utf8mb4');
        return $conn;
    }

    $dsn = "mysql:host=$host;port=$port;charset=utf8mb4";
    if ($dbname !== '') {
        $dsn .= ";dbname=$dbname";
    }

    $conn = new PdoConnectionWrapper($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    $conn->set_charset('utf8mb4');
    return $conn;
}

function ensureDatabase(): bool
{
    global $host, $user, $pass, $dbname, $port;

    try {
        if (class_exists('mysqli')) {
            $conn = new mysqli($host, $user, $pass, '', $port);
            $conn->set_charset('utf8mb4');
            $conn->query("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $conn->select_db($dbname);
        } else {
            $dsn = "mysql:host=$host;port=$port;charset=utf8mb4";
            $conn = new PdoConnectionWrapper($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
            $conn->query("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $conn->select_db($dbname);
        }

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
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $count = (int) ($row['COUNT(*)'] ?? 0);
        $stmt->close();

        if ($count === 0) {
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
            $result = $existing->get_result();
            $row = $result->fetch_assoc();
            $exists = (int) ($row['COUNT(*)'] ?? 0);
            $existing->close();

            if ($exists === 0) {
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
