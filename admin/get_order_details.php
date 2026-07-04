<?php
// get_order_details.php

// 1. Clear out any previous buffer layers to guarantee a clean payload delivery channel
if (ob_get_length()) ob_clean();

// 2. Set strict headers to enforce valid Application JSON handling
header('Content-Type: application/json; charset=utf-8');

// 3. DATABASE INITIALIZATION
try {
    // Dynamically include your centralized database core configurations file
    require_once __DIR__ . '/../config/db.php'; 
    
    // Safety check: Validate if the $pdo variable exists post inclusion
    if (!isset($pdo) || !($pdo instanceof PDO)) {
        echo json_encode(['success' => false, 'message' => 'Database connection state instance missing ($pdo variable not defined in db.php).']);
        exit;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Failed loading configuration architecture: ' . $e->getMessage()]);
    exit;
}

// 4. PARSE EXPECTED GET STRING PARAMETERS 
if (!isset($_GET['order_ids']) || empty(trim($_GET['order_ids']))) {
    echo json_encode(['success' => false, 'message' => 'Missing order identification sequence.']);
    exit;
}

// Extract and normalize array keys safely
$orderIdsArray = array_filter(array_map('intval', explode(',', $_GET['order_ids'])));

if (empty($orderIdsArray)) {
    echo json_encode(['success' => false, 'message' => 'No valid order numbers parsed.']);
    exit;
}

// 5. QUERY AND PROCESS RELATIONAL DATA
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
        echo json_encode(['success' => false, 'message' => 'No matching active order maps found in database logs.']);
        exit;
    }

    // Capture User Info profile metrics from first indexed trace row 
    $userData = [
        'full_name' => $records[0]['full_name'] ?? 'N/A',
        'email'     => $records[0]['email'] ?? 'N/A',
        'country'   => $records[0]['country'] ?? 'N/A'
    ];

    // Reassemble standard items dictionary list grouping elements independently
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
    echo json_encode(['success' => false, 'message' => 'Database SQL execution exception: ' . $e->getMessage()]);
}
exit;
