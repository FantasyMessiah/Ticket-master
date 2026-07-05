<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Corrected path traversal reference to your database file
require_once 'config/db.php';

$pdo = null;
try {
    if (class_exists('Database')) {
        $dbInstance = new Database();
        $pdo = $dbInstance->connect(); 
    }
} catch (Exception $e) {
    // Graceful containment fallback
}

// Combine keyword and location inputs into a single smart search context
$search_query = isset($_GET['q']) ? trim($_GET['q']) : '';
$location_query = isset($_GET['location']) ? trim($_GET['location']) : '';

// If keyword is blank but location is filled, use location as the primary keyword reference
if (empty($search_query) && !empty($location_query)) {
    $search_query = $location_query;
}

$matched_artists = [];
$matched_events = [];

if (!empty($search_query) && $pdo !== null) {
    // Case-insensitivity conversion setup
    $lowercase_query = mb_strtolower($search_query, 'UTF-8');
    $wildcard_param = "%" . $lowercase_query . "%";
    
    // Phonetic breakdown calculation to ignore typos (e.g., "Teylor" -> "Taylor")
    $soundex_query = soundex($search_query);

    try {
        // Query 1: Smart Artist Matching
        $artist_sql = "SELECT *
                       FROM artists
                       WHERE LOWER(artist_name) LIKE ?
                          OR SOUNDEX(artist_name) = SOUNDEX(?)
                       ORDER BY artist_name
                       LIMIT 10";
        $artist_stmt = $pdo->prepare($artist_sql);
        $artist_stmt->execute([$wildcard_param, $search_query]);
        $matched_artists = $artist_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Query 2: Smart Event Matching across Titles, Venues, Locations, and Dates
        $event_sql = "
        SELECT
            c.*,
            a.artist_name,
            a.artist_image
        FROM concerts c
        INNER JOIN artists a
            ON c.artist_id = a.artist_id
        WHERE (
             LOWER(a.artist_name) LIKE ?
          OR LOWER(c.title) LIKE ?
          OR LOWER(c.venue) LIKE ?
          OR LOWER(c.location) LIKE ?
          OR SOUNDEX(a.artist_name) = SOUNDEX(?)
          OR SOUNDEX(c.title) = SOUNDEX(?)
          OR SOUNDEX(c.venue) = SOUNDEX(?)
          OR SOUNDEX(c.location) = SOUNDEX(?)
        )
        ORDER BY c.concert_date DESC
        LIMIT 20
        ";
                     
        $event_stmt = $pdo->prepare($event_sql);
        
        $event_stmt->execute([
            $wildcard_param,   // artist_name
            $wildcard_param,   // title
            $wildcard_param,   // venue
            $wildcard_param,   // location
            $search_query,     // soundex artist_name
            $search_query,     // soundex title
            $search_query,     // soundex venue
            $search_query      // soundex location
        ]);

        $matched_events = $event_stmt->fetchAll(PDO::FETCH_ASSOC);

        /* --------------------------------------------
           SAVE SEARCH HISTORY
        ---------------------------------------------*/
        try {
            $user_id = $_SESSION['user_id'] ?? null;
            $result_count = count($matched_artists) + count($matched_events);

            $save = $pdo->prepare("
                INSERT INTO user_searches
                (user_id, search, result)
                VALUES (?, ?, ?)
            ");

            $save->execute([
                $user_id,
                $search_query,
                $result_count
            ]);
        } catch (Exception $e) {
            // Never stop the search page if logging fails
        }
        
    } catch (Exception $e) {
        // Query fallback container protection
    }
} else if ($pdo !== null) {
    // Default system presentation state on page initialization
    try {
        $default_stmt = $pdo->prepare("
        SELECT
            c.*,
            a.artist_name,
            a.artist_image
        FROM concerts c
        INNER JOIN artists a
            ON c.artist_id = a.artist_id
        ORDER BY c.concert_date DESC
        LIMIT 20
        ");
        $default_stmt->execute();
        $matched_events = $default_stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {}
}

/* --------------------------------------------
   DYNAMIC POPULAR TERMS / HOTKEYS
---------------------------------------------*/
$top_searches_list = [];

if ($pdo !== null) {
    try {
        // Option A: Pull 10 random artists from your database to keep the dashboard fluid
        $popular_stmt = $pdo->prepare("SELECT artist_name FROM artists ORDER BY RAND() LIMIT 10");
        
        // NOTE: If your artists table has a featured/trending flag, swap the query out for this line:
        // $popular_stmt = $pdo->prepare("SELECT artist_name FROM artists WHERE is_featured = 1 LIMIT 10");
        
        $popular_stmt->execute();
        $top_searches_list = $popular_stmt->fetchAll(PDO::FETCH_COLUMN);
    } catch (Exception $e) {
        // Fallback array if database query breaks to prevent the layout from fracturing
        $top_searches_list = ["BTS", "Taylor Swift", "Coldplay", "Blackpink", "Drake"];
    }
} else {
    $top_searches_list = ["BTS", "Taylor Swift", "Coldplay", "Blackpink", "Drake"];
}
?>
