<?php
// get_order_details.php

// 1. Clear out any previous buffer layers to guarantee a clean payload delivery channel
if (ob_get_length()) ob_clean();

// 2. Set strict headers to enforce valid Application JSON handling
header('Content-Type: application/json; charset=utf-8');

// 3. DATABASE INITIALIZATION
// NOTE: If /inc/header.php prints standard page layouts (HTML structure/navbars), 
// DO NOT use it here. Instead, instantiate the PDO connection cleanly as done below:
try {
    // If you have a separate standalone database configuration file, uncomment the line below:
    // require_once __DIR__ . '/inc/db_connect.php'; 
    
    // Otherwise, initialize the connection inline or match your setup properties:
    if (!isset($pdo)) {
        $host = 'localhost'; // Update with your actual host if different
        $db   = 'if0_42273705_ticket2';
        $user = 'root';        // Update with your database username
        $pass = '';            // Update with your database password
        $charset = 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $pdo = new PDO($dsn, $user, $pass, $options);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// 4. PARSE EXPECTED GET STRING PARAMETERS 
if (!isset($_GET['order_ids']) || empty(trim($_GET['order_ids']))) {
    echo json_encode(['success' => false, 'message' => 'Missing order identification sequence.']);
    exit;
}

$orderIdsArray = array_filter(array_map('intval', explode(',', $_GET['order_ids'])));

if (empty($orderIdsArray)) {
    echo json_encode(['success' => false, 'message' => 'No valid order numbers parsed.']);
    exit;
}

// 5. QUERY AND PROCESS DATA
try {
    $placeholders = implode(',', array_fill(0, count($orderIdsArray), '?'));
    
    $query = "
        SELECT 
            t.ticket_id,
            t.ticket_name,
            t.section_name,
            t.row_name,
            t.price,
            c.concert_id,
            c.title AS concert_title,
            c.concert_date,
            c.day_time,
            c.venue,
            c.location,
            a.artist_id,
            a.artist_name,
            a.artist_image,
            u.id AS user_id,
            u.full_name,
            u.email,
            u.country
        FROM tickets t
        LEFT JOIN concerts c ON t.concert_id = c.concert_id
        LEFT JOIN artists a ON c.artist_id = a.artist_id
        LEFT JOIN users u ON t.user_id = u.id
        WHERE t.ticket_id IN ($placeholders)
        ORDER BY t.ticket_id ASC
    ";

    $stmt = $pdo->prepare($query);
    $stmt->execute(array_values($orderIdsArray));
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($records)) {
        echo json_encode(['success' => false, 'message' => 'No matching data logs found in database.']);
        exit;
    }

    $userData = [
        'full_name' => $records[0]['full_name'] ?? 'N/A',
        'email'     => $records[0]['email'] ?? 'N/A',
        'country'   => $records[0]['country'] ?? 'N/A'
    ];

    $items = [];
    foreach ($records as $row) {
        $items[] = [
            'ticket_id'    => $row['ticket_id'],
            'ticket_name'  => $row['ticket_name'] ?? 'General Entry',
            'section_name' => $row['section_name'] ?? 'N/A',
            'row_name'     => $row['row_name'] ?? 'N/A',
            'price'        => $row['price'],
            'concert'      => [
                'title'    => $row['concert_title'] ?? 'N/A',
                'date'     => $row['concert_date'] ?? 'N/A',
                'time'     => $row['day_time'] ?? 'N/A',
                'venue'    => $row['venue'] ?? 'N/A',
                'location' => $row['location'] ?? 'N/A'
            ],
            'artist'       => [
                'name'     => $row['artist_name'] ?? 'N/A',
                'image'    => $row['artist_image'] ?? ''
            ]
        ];
    }

    echo json_encode([
        'success' => true,
        'user'    => $userData,
        'items'   => $items
    ]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database exception occurred: ' . $e->getMessage()]);
}
exit;
