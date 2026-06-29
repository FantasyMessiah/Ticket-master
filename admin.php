<?php
// admin.php - Master Administrative Control Center Panel
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config/db.php';

$pdo = null;
try {
    if (class_exists('Database')) {
        $dbInstance = new Database();
        $pdo = $dbInstance->connect(); 
    }
} catch (Exception $e) {
    // Structural database connection exception handling
}

// Status message handlers
$success_msg = "";
$error_msg = "";

// -------------------------------------------------------------------------
// CORE CRUD CONTROLLER PIPELINE (Handles All Form Actions)
// -------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo !== null) {
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    try {
        // --- 1. USER MANAGEMENT CRUD ---
        if ($action === 'save_user') {
            $user_id = !empty($_POST['id']) ? intval($_POST['id']) : null;
            $name = trim($_POST['name']);
            $email = trim($_POST['email']);
            $phone = trim($_POST['phone']);

            if ($user_id) {
                $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?");
                $stmt->execute([$name, $email, $phone, $user_id]);
                $success_msg = "User profile records modified successfully.";
            } else {
                $stmt = $pdo->prepare("INSERT INTO users (name, email, phone) VALUES (?, ?, ?)");
                $stmt->execute([$name, $email, $phone]);
                $success_msg = "New user credential record established cleanly.";
            }
        } elseif ($action === 'delete_user') {
            $user_id = intval($_POST['id']);
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $success_msg = "User removed from system indexing ledger.";
        }

        // --- 2. ARTIST CRUD ---
        elseif ($action === 'save_artist') {
            $artist_id = !empty($_POST['id']) ? intval($_POST['id']) : null;
            $name = trim($_POST['name']);
            $image = trim($_POST['artist_image']); // Expecting file upload string path pointer

            if ($artist_id) {
                $stmt = $pdo->prepare("UPDATE artists SET name = ?, artist_image = ? WHERE id = ?");
                $stmt->execute([$name, $image, $artist_id]);
                $success_msg = "Artist profile configurations synced successfully.";
            } else {
                $stmt = $pdo->prepare("INSERT INTO artists (name, artist_image) VALUES (?, ?)");
                $stmt->execute([$name, $image]);
                $success_msg = "New artist matrix node created.";
            }
        } elseif ($action === 'delete_artist') {
            $artist_id = intval($_POST['id']);
            $stmt = $pdo->prepare("DELETE FROM artists WHERE id = ?");
            $stmt->execute([$artist_id]);
            $success_msg = "Artist record systematically expunged.";
        }

        // --- 3. CONCERT EVENT CRUD ---
        elseif ($action === 'save_event') {
            $event_id = !empty($_POST['id']) ? intval($_POST['id']) : null;
            $artist_id = intval($_POST['artist_id']);
            $title = trim($_POST['title']);
            $venue = trim($_POST['venue']);
            $location = trim($_POST['location']);
            $date_str = trim($_POST['date_string']);
            $time_str = trim($_POST['time_string']);

            if ($event_id) {
                $stmt = $pdo->prepare("UPDATE events SET artist_id = ?, title = ?, venue = ?, location = ?, date_string = ?, time_string = ? WHERE id = ?");
                $stmt->execute([$artist_id, $title, $venue, $location, $date_str, $time_str, $event_id]);
                $success_msg = "Performance platform production criteria rewritten.";
            } else {
                $stmt = $pdo->prepare("INSERT INTO events (artist_id, title, venue, location, date_string, time_string) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$artist_id, $title, $venue, $location, $date_str, $time_str]);
                $success_msg = "Live performance instance registered to timeline maps.";
            }
        } elseif ($action === 'delete_event') {
            $event_id = intval($_POST['id']);
            $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
            $stmt->execute([$event_id]);
            $success_msg = "Target presentation tracking block dropped.";
        }

        // --- 4. DASHBOARD BACKEND SYNC (Admin Tickets & Global Messages) ---
        elseif ($action === 'upload_ticket') {
            $file_url = trim($_POST['file_path']);
            $desc = trim($_POST['description']);
            $stmt = $pdo->prepare("INSERT INTO admin_tickets (file_path, description) VALUES (?, ?)");
            $stmt->execute([$file_url, $desc]);
            $success_msg = "Pass layout manifest uploaded to account pipeline.";
        } elseif ($action === 'delete_ticket') {
            $ticket_id = intval($_POST['id']);
            $stmt = $pdo->prepare("DELETE FROM admin_tickets WHERE id = ?");
            $stmt->execute([$ticket_id]);
            $success_msg = "Ticket file removed.";
        } elseif ($action === 'broadcast_message') {
            $title = trim($_POST['title']);
            $content = trim($_POST['content']);
            $stmt = $pdo->prepare("INSERT INTO admin_messages (title, content, created_at) VALUES (?, ?, NOW())");
            $stmt->execute([$title, $content]);
            $success_msg = "Alert statement broadcast dispatched globally.";
        } elseif ($action === 'delete_message') {
            $msg_id = intval($_POST['id']);
            $stmt = $pdo->prepare("DELETE FROM admin_messages WHERE id = ?");
            $stmt->execute([$msg_id]);
            $success_msg = "Alert notification deleted.";
        }

    } catch (Exception $e) {
        $error_msg = "CRUD Logic Execution Exception Error: " . $e->getMessage();
    }
}

// -------------------------------------------------------------------------
// DATABASE LEDGER RETRIEVAL MATRIX DATA FETCHES
// -------------------------------------------------------------------------
$users = []; $artists = []; $events = []; $tickets = []; $messages = [];

if ($pdo !== null) {
    try {
        $users = $pdo->query("SELECT * FROM users ORDER BY id DESC")->fetchAll();
        $artists = $pdo->query("SELECT * FROM artists ORDER BY id DESC")->fetchAll();
        $events = $pdo->query("SELECT e.*, a.name AS artist_name FROM events e JOIN artists a ON e.artist_id = a.id ORDER BY e.id DESC")->fetchAll();
        $tickets = $pdo->query("SELECT * FROM admin_tickets ORDER BY id DESC")->fetchAll();
        $messages = $pdo->query("SELECT * FROM admin_messages ORDER BY id DESC")->fetchAll();
    } catch (Exception $e) {
        // Table layout schema safety catch configurations
    }
}

// Tab Selector parameter initialization logic mapping
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'events';
?>
<!DOCTYPE html>
<html lang="en">
<?php include "inc/head.php"; ?>
<?php include "inc/header.php"; ?>

<body class="bg-gray-50 text-gray-900 font-sans antialiased">
    <div class="min-h-screen flex flex-col justify-between">
        
        <div class="bg-slate-900 text-white py-8 px-4 md:px-8 border-b border-slate-800">
            <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <span class="text-xs font-black uppercase tracking-widest text-amber-400 bg-amber-950/60 px-3 py-1 rounded-full border border-amber-900">System Core Privileged Node</span>
                    <h1 class="text-2xl md:text-4xl font-black tracking-tight mt-2">Platform Administrative Engine</h1>
                </div>
                <div class="flex gap-2">
                    <a href="dashboard.php" target="_blank" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 border border-slate-700">
                        <i class="fas fa-desktop"></i> View Dashboard Perspective
                    </a>
                </div>
            </div>
        </div>

        <main class="max-w-7xl mx-auto w-full px-4 md:px-8 py-8 flex-1">
            
            <?php if (!empty($success_msg)): ?>
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 font-bold text-xs p-4 rounded-xl mb-6 shadow-sm flex items-center gap-2">
                    <i class="fas fa-check-circle text-emerald-600 text-base"></i> <?php echo $success_msg; ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($error_msg)): ?>
                <div class="bg-rose-50 border border-rose-200 text-rose-800 font-bold text-xs p-4 rounded-xl mb-6 shadow-sm flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle text-rose-600 text-base"></i> <?php echo $error_msg; ?>
                </div>
            <?php endif; ?>

            <div class="flex flex-wrap items-center border-b border-gray-200 gap-1 mb-8 overflow-x-auto whitespace-nowrap">
                <a href="admin.php?tab=events" class="px-5 py-3 text-xs font-black uppercase tracking-wider border-b-2 transition-all <?php echo $active_tab === 'events' ? 'border-[#024DDF] text-[#024DDF]' : 'border-transparent text-gray-400 hover:text-gray-700'; ?>">
                    <i class="fas fa-calendar-alt mr-1"></i> Performance Presentations (<?php echo count($events); ?>)
                </a>
                <a href="admin.php?tab=artists" class="px-5 py-3 text-xs font-black uppercase tracking-wider border-b-2 transition-all <?php echo $active_tab === 'artists' ? 'border-[#024DDF] text-[#024DDF]' : 'border-transparent text-gray-400 hover:text-gray-700'; ?>">
                    <i class="fas fa-guitar mr-1"></i> Artist Roster Index (<?php echo count($artists); ?>)
                </a>
                <a href="admin.php?tab=users" class="px-5 py-3 text-xs font-black uppercase tracking-wider border-b-2 transition-all <?php echo $active_tab === 'users' ? 'border-[#024DDF] text-[#024DDF]' : 'border-transparent text-gray-400 hover:text-gray-700'; ?>">
                    <i class="fas fa-users mr-1"></i> Platform User Identities (<?php echo count($users); ?>)
                </a>
                <a href="admin.php?tab=dashboard_sync" class="px-5 py-3 text-xs font-black uppercase tracking-wider border-b-2 transition-all <?php echo $active_tab === 'dashboard_sync' ? 'border-[#024DDF] text-[#024DDF]' : 'border-transparent text-gray-400 hover:text-gray-700'; ?>">
                    <i class="fas fa-sliders-h mr-1"></i> User Dashboard Allocation Links
                </a>
            </div>

            <?php if ($active_tab === 'events'): ?>
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                    <form action="admin.php?tab=events" method="POST" class="lg:col-span-4 bg-white border border-gray-200 rounded-2xl p-6 shadow-sm space-y-4">
                        <h3 class="text-xs font-black uppercase tracking-widest text-gray-400 border-b border-gray-100 pb-2 mb-2">Build / Adjust Event Target</h3>
                        <input type="hidden" name="action" value="save_event">
                        <input type="hidden" name="id" id="event_edit_id" value="">
                        
                        <div>
                            <label class="block text-[10px] font-black uppercase text-gray-400 tracking-wide mb-1">Select Artist Entity mapping</label>
                            <select name="artist_id" id="event_edit_artist" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs font-bold outline-none" required>
                                <?php foreach ($artists as $art): ?>
                                    <option value="<?php echo $art['id']; ?>"><?php echo htmlspecialchars($art['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase text-gray-400 tracking-wide mb-1">Production Performance Title</label>
                            <input type="text" name="title" id="event_edit_title" placeholder="e.g. World Tour Live Finale" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs font-bold outline-none shadow-inner" required>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase text-gray-400 tracking-wide mb-1">Venue Infrastructure Designation</label>
                            <input type="text" name="venue" id="event_edit_venue" placeholder="e.g. Madison Square Garden" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs font-bold outline-none shadow-inner" required>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase text-gray-400 tracking-wide mb-1">Geographic Location Label</label>
                            <input type="text" name="location" id="event_edit_location" placeholder="e.g. New York, NY" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs font-bold outline-none shadow-inner" required>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[10px] font-black uppercase text-gray-400 tracking-wide mb-1">Date String Block</label>
                                <input type="text" name="date_string" id="event_edit_date" placeholder="e.g. AUG 18, 2026" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs font-bold outline-none" required>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black uppercase text-gray-400 tracking-wide mb-1">Time Designation</label>
                                <input type="text" name="time_string" id="event_edit_time" placeholder="e.g. 7:30 PM" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs font-bold outline-none" required>
                            </div>
                        </div>
                        <button type="submit" class="w-full bg-[#024DDF] hover:bg-blue-800 text-white font-black text-xs uppercase tracking-widest py-3 rounded-xl transition-all shadow-sm">
                            Commit Event Data Structure
                        </button>
                    </form>

                    <div class="lg:col-span-8 bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs font-medium text-gray-600">
                                <thead class="bg-gray-50 text-gray-400 uppercase tracking-wider text-[10px] font-black border-b border-gray-200">
                                    <tr>
                                        <th class="p-3">Event Node / Artist Mapping</th>
                                        <th class="p-3">Arena Venue Location</th>
                                        <th class="p-3">Timeline Schedule</th>
                                        <th class="p-3 text-center">Data Interventions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <?php foreach ($events as $ev): ?>
                                        <tr class="hover:bg-gray-50/60 transition-colors">
                                            <td class="p-3">
                                                <span class="text-xs font-black text-gray-900 block"><?php echo htmlspecialchars($ev['title']); ?></span>
                                                <span class="text-[10px] text-[#024DDF] font-bold uppercase tracking-wide">Artist: <?php echo htmlspecialchars($ev['artist_name']); ?></span>
                                            </td>
                                            <td class="p-3">
                                                <span class="text-gray-800 font-bold block"><?php echo htmlspecialchars($ev['venue']); ?></span>
                                                <span class="text-[10px] text-gray-400 block font-medium"><?php echo htmlspecialchars($ev['location']); ?></span>
                                            </td>
                                            <td class="p-3 font-mono text-gray-500 text-[11px]">
                                                <span class="block font-bold text-gray-700"><?php echo htmlspecialchars($ev['date_string']); ?></span>
                                                <span><?php echo htmlspecialchars($ev['time_string']); ?></span>
                                            </td>
                                            <td class="p-3 text-center">
                                                <div class="inline-flex gap-1">
                                                    <button onclick="populateEventEdit(<?php echo htmlspecialchars(json_encode($ev)); ?>)" class="px-2.5 py-1 bg-gray-100 hover:bg-blue-100 text-gray-700 hover:text-[#024DDF] rounded font-bold transition-all text-[11px]">Edit</button>
                                                    <form action="admin.php?tab=events" method="POST" onsubmit="return confirm('Expunge this event track instance permanently?');">
                                                        <input type="hidden" name="action" value="delete_event">
                                                        <input type="hidden" name="id" value="<?php echo $ev['id']; ?>">
                                                        <button type="submit" class="px-2.5 py-1 bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white rounded font-bold transition-all text-[11px]">Drop</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($active_tab === 'artists'): ?>
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                    <form action="admin.php?tab=artists" method="POST" class="lg:col-span-4 bg-white border border-gray-200 rounded-2xl p-6 shadow-sm space-y-4">
                        <h3 class="text-xs font-black uppercase tracking-widest text-gray-400 border-b border-gray-100 pb-2 mb-2">Register Artist Profile</h3>
                        <input type="hidden" name="action" value="save_artist">
                        <input type="hidden" name="id" id="artist_edit_id" value="">
                        
                        <div>
                            <label class="block text-[10px] font-black uppercase text-gray-400 tracking-wide mb-1">Artist Performance Identity String</label>
                            <input type="text" name="name" id="artist_edit_name" placeholder="e.g. Coldplay" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs font-bold outline-none" required>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase text-gray-400 tracking-wide mb-1">Profile Photo Upload Resource Path</label>
                            <input type="text" name="artist_image" id="artist_edit_image" placeholder="e.g. artist_profile_pic.jpg" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs font-mono outline-none">
                        </div>
                        <button type="submit" class="w-full bg-[#024DDF] hover:bg-blue-800 text-white font-black text-xs uppercase tracking-widest py-3 rounded-xl transition-all">
                            Save Artist Profile Node
                        </button>
                    </form>

                    <div class="lg:col-span-8 bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                        <table class="w-full text-left text-xs font-medium text-gray-600">
                            <thead class="bg-gray-50 text-gray-400 uppercase tracking-wider text-[10px] font-black border-b border-gray-200">
                                <tr>
                                    <th class="p-3">Database Index Key ID</th>
                                    <th class="p-3">Artist Entity Identity</th>
                                    <th class="p-3">Image Vector Reference Point</th>
                                    <th class="p-3 text-center">Interventions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php foreach ($artists as $art): ?>
                                    <tr class="hover:bg-gray-50/60 transition-colors">
                                        <td class="p-3 font-mono font-bold text-gray-400">#<?php echo $art['id']; ?></td>
                                        <td class="p-3 font-black text-gray-900 text-sm"><?php echo htmlspecialchars($art['name']); ?></td>
                                        <td class="p-3 font-mono text-[11px] text-gray-500 truncate max-w-[200px]"><?php echo htmlspecialchars($art['artist_image'] ?? 'NULL'); ?></td>
                                        <td class="p-3 text-center">
                                            <div class="inline-flex gap-1">
                                                <button onclick="populateArtistEdit(<?php echo htmlspecialchars(json_encode($art)); ?>)" class="px-2.5 py-1 bg-gray-100 hover:bg-blue-100 text-gray-700 hover:text-[#024DDF] rounded font-bold transition-all text-[11px]">Edit</button>
                                                <form action="admin.php?tab=artists" method="POST" onsubmit="return confirm('Purge this artist and all cascade associated presentation dependencies?');">
                                                    <input type="hidden" name="action" value="delete_artist">
                                                    <input type="hidden" name="id" value="<?php echo $art['id']; ?>">
                                                    <button type="submit" class="px-2.5 py-1 bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white rounded font-bold transition-all text-[11px]">Drop</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($active_tab === 'users'): ?>
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                    <form action="admin.php?tab=users" method="POST" class="lg:col-span-4 bg-white border border-gray-200 rounded-2xl p-6 shadow-sm space-y-4">
                        <h3 class="text-xs font-black uppercase tracking-widest text-gray-400 border-b border-gray-100 pb-2 mb-2">Configure User Account Vector</h3>
                        <input type="hidden" name="action" value="save_user">
                        <input type="hidden" name="id" id="user_edit_id" value="">
                        
                        <div>
                            <label class="block text-[10px] font-black uppercase text-gray-400 tracking-wide mb-1">User Full Name Identity</label>
                            <input type="text" name="name" id="user_edit_name" placeholder="Jane Doe" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs font-bold outline-none" required>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase text-gray-400 tracking-wide mb-1">Email Electronic Endpoint Address</label>
                            <input type="email" name="email" id="user_edit_email" placeholder="janedoe@mail.com" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs font-bold outline-none" required>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase text-gray-400 tracking-wide mb-1">Mobile Communications Link Channel</label>
                            <input type="text" name="phone" id="user_edit_phone" placeholder="+1 (555) 000-0000" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs font-bold outline-none">
                        </div>
                        <button type="submit" class="w-full bg-[#024DDF] hover:bg-blue-800 text-white font-black text-xs uppercase tracking-widest py-3 rounded-xl transition-all">
                            Synchronize User Account Profile
                        </button>
                    </form>

                    <div class="lg:col-span-8 bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                        <table class="w-full text-left text-xs font-medium text-gray-600">
                            <thead class="bg-gray-50 text-gray-400 uppercase tracking-wider text-[10px] font-black border-b border-gray-200">
                                <tr>
                                    <th class="p-3">User Profile Context Parameters</th>
                                    <th class="p-3">Mobile Link Address</th>
                                    <th class="p-3 text-center">Interventions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php foreach ($users as $usr): ?>
                                    <tr class="hover:bg-gray-50/60 transition-colors">
                                        <td class="p-3">
                                            <span class="text-xs font-black text-gray-900 block"><?php echo htmlspecialchars($usr['name']); ?></span>
                                            <span class="text-[11px] text-gray-400 font-mono"><?php echo htmlspecialchars($usr['email']); ?></span>
                                        </td>
                                        <td class="p-3 font-bold text-gray-700 text-xs"><?php echo htmlspecialchars($usr['phone'] ?? 'Unlisted Parameter'); ?></td>
                                        <td class="p-3 text-center">
                                            <div class="inline-flex gap-1">
                                                <button onclick="populateUserEdit(<?php echo htmlspecialchars(json_encode($usr)); ?>)" class="px-2.5 py-1 bg-gray-100 hover:bg-blue-100 text-gray-700 hover:text-[#024DDF] rounded font-bold transition-all text-[11px]">Edit</button>
                                                <form action="admin.php?tab=users" method="POST" onsubmit="return confirm('Wipe user allocation credentials safely from table array blocks?');">
                                                    <input type="hidden" name="action" value="delete_user">
                                                    <input type="hidden" name="id" value="<?php echo $usr['id']; ?>">
                                                    <button type="submit" class="px-2.5 py-1 bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white rounded font-bold transition-all text-[11px]">Drop</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($active_tab === 'dashboard_sync'): ?>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
                    
                    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm space-y-6">
                        <div>
                            <h3 class="text-sm font-black uppercase text-gray-900 tracking-tight flex items-center gap-1.5 border-b border-gray-100 pb-2">
                                <i class="fas fa-ticket-alt text-[#024DDF]"></i> Push Downloadable Ticket Pass Vector File
                            </h3>
                            <form action="admin.php?tab=dashboard_sync" method="POST" class="mt-4 space-y-3">
                                <input type="hidden" name="action" value="upload_ticket">
                                <div>
                                    <label class="block text-[10px] font-black uppercase text-gray-400 tracking-wide mb-1">Media Pass File URL / Absolute System Path</label>
                                    <input type="text" name="file_path" placeholder="https://images.unsplash.com/... or local path" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs font-mono outline-none" required>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black uppercase text-gray-400 tracking-wide mb-1">Administrative Explanatory Description Context</label>
                                    <textarea name="description" placeholder="VIP Circle Access Entry confirmation file definitions..." rows="3" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs font-bold outline-none" required></textarea>
                                </div>
                                <button type="submit" class="w-full bg-slate-900 hover:bg-black text-white font-black text-xs uppercase tracking-widest py-2.5 rounded-xl transition-all">
                                    Publish Active Downloadable Pass
                                </button>
                            </form>
                        </div>

                        <div class="space-y-2 max-h-[300px] overflow-y-auto border border-gray-100 rounded-xl p-2 bg-gray-50/50">
                            <?php foreach ($tickets as $tk): ?>
                                <div class="bg-white border border-gray-200 p-3 rounded-xl flex items-center justify-between gap-4 shadow-sm">
                                    <div class="min-w-0">
                                        <p class="text-xs font-black text-gray-900 truncate"><?php echo htmlspecialchars($tk['description']); ?></p>
                                        <span class="text-[10px] text-gray-400 font-mono block truncate mt-0.5"><?php echo htmlspecialchars($tk['file_path']); ?></span>
                                    </div>
                                    <form action="admin.php?tab=dashboard_sync" method="POST">
                                        <input type="hidden" name="action" value="delete_ticket">
                                        <input type="hidden" name="id" value="<?php echo $tk['id']; ?>">
                                        <button type="submit" class="p-1.5 bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white rounded-md transition-colors"><i class="fas fa-trash-alt text-xs"></i></button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm space-y-6">
                        <div>
                            <h3 class="text-sm font-black uppercase text-gray-900 tracking-tight flex items-center gap-1.5 border-b border-gray-100 pb-2">
                                <i class="fas fa-bell text-amber-500"></i> Broadcast Global Operations Alert Message
                            </h3>
                            <form action="admin.php?tab=dashboard_sync" method="POST" class="mt-4 space-y-3">
                                <input type="hidden" name="action" value="broadcast_message">
                                <div>
                                    <label class="block text-[10px] font-black uppercase text-gray-400 tracking-wide mb-1">Broadcast Alert Subject Title Header</label>
                                    <input type="text" name="title" placeholder="Venue Gateway Access Protocol Advisory" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs font-bold outline-none" required>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black uppercase text-gray-400 tracking-wide mb-1">Operational Instructions Message Body Content</label>
                                    <textarea name="content" placeholder="Please arrive 2 hours prior for mandatory security checks..." rows="3" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs font-medium outline-none" required></textarea>
                                </div>
                                <button type="submit" class="w-full bg-[#024DDF] hover:bg-blue-800 text-white font-black text-xs uppercase tracking-widest py-2.5 rounded-xl transition-all">
                                    Broadcast Alert Stream
                                </button>
                            </form>
                        </div>

                        <div class="space-y-2 max-h-[300px] overflow-y-auto border border-gray-100 rounded-xl p-2 bg-gray-50/50">
                            <?php foreach ($messages as $ms): ?>
                                <div class="bg-white border border-gray-200 p-3 rounded-xl flex items-center justify-between gap-4 shadow-sm">
                                    <div class="min-w-0">
                                        <h5 class="text-xs font-black text-gray-900 truncate"><?php echo htmlspecialchars($ms['title']); ?></h5>
                                        <p class="text-[11px] text-gray-500 truncate mt-0.5"><?php echo htmlspecialchars($ms['content']); ?></p>
                                    </div>
                                    <form action="admin.php?tab=dashboard_sync" method="POST">
                                        <input type="hidden" name="action" value="delete_message">
                                        <input type="hidden" name="id" value="<?php echo $ms['id']; ?>">
                                        <button type="submit" class="p-1.5 bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white rounded-md transition-colors"><i class="fas fa-trash-alt text-xs"></i></button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                </div>
            <?php endif; ?>

        </main>

        <?php include "inc/footer.php"; ?>
    </div>

    <script>
        function populateEventEdit(data) {
            document.getElementById('event_edit_id').value = data.id;
            document.getElementById('event_edit_artist').value = data.artist_id;
            document.getElementById('event_edit_title').value = data.title;
            document.getElementById('event_edit_venue').value = data.venue;
            document.getElementById('event_edit_location').value = data.location;
            document.getElementById('event_edit_date').value = data.date_string;
            document.getElementById('event_edit_time').value = data.time_string;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function populateArtistEdit(data) {
            document.getElementById('artist_edit_id').value = data.id;
            document.getElementById('artist_edit_name').value = data.name;
            document.getElementById('artist_edit_image').value = data.artist_image || '';
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function populateUserEdit(data) {
            document.getElementById('user_edit_id').value = data.id;
            document.getElementById('user_edit_name').value = data.name;
            document.getElementById('user_edit_email').value = data.email;
            document.getElementById('user_edit_phone').value = data.phone || '';
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    </script>
</body>
</html>
