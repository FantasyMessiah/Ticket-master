<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/inc/header.php';

$message = '';
$error   = '';

/* --------------------------------------------------
   FETCH ALL ARTISTS
-------------------------------------------------- */
try {
    $stmt = $pdo->query("SELECT * FROM artists ORDER BY artist_id DESC");
    $artists = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
    $artists = [];
}

/* --------------------------------------------------
   HANDLE ACTIONS
-------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {

    $action = $_POST['action'] ?? '';

    try {

        /* ---------------- ADD ARTIST ---------------- */
        if ($action === 'add') {

            $artist_name = trim($_POST['artist_name'] ?? '');
            $genre       = trim($_POST['genre'] ?? '');
            $rating      = trim($_POST['rating'] ?? '');
            $about       = trim($_POST['about'] ?? '');

            if ($artist_name === '') {
                throw new Exception("Artist name is required.");
            }

            /* IMAGE UPLOAD */
            $imageName = '';
            if (!empty($_FILES['artist_image']['name'])) {

                $uploadDir = __DIR__ . "/../uploads/artists/";
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $imageName = time() . '_' . basename($_FILES['artist_image']['name']);
                $target = $uploadDir . $imageName;

                move_uploaded_file($_FILES['artist_image']['tmp_name'], $target);
            }

            $stmt = $pdo->prepare("
                INSERT INTO artists (artist_name, artist_image, genre, rating, about)
                VALUES (?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $artist_name,
                $imageName,
                $genre,
                $rating,
                $about
            ]);

            $message = "Artist added successfully.";
        }

        /* ---------------- DELETE ARTIST ---------------- */
        if ($action === 'delete') {

            $id = (int)($_POST['id'] ?? 0);

            if ($id <= 0) {
                throw new Exception("Invalid artist ID.");
            }

            $stmt = $pdo->prepare("DELETE FROM artists WHERE artist_id = ?");
            $stmt->execute([$id]);

            $message = "Artist deleted successfully.";
        }

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<main>

<h1 style="text-align:center; margin:2rem 0;">Manage Artists</h1>

<?php if ($message): ?>
<div style="background:#238636;color:#fff;padding:1rem;border-radius:8px;text-align:center;max-width:900px;margin:1rem auto;">
    <?= htmlspecialchars($message) ?>
</div>
<?php endif; ?>

<?php if ($error): ?>
<div style="background:#f85149;color:#fff;padding:1rem;border-radius:8px;text-align:center;max-width:900px;margin:1rem auto;">
    <?= htmlspecialchars($error) ?>
</div>
<?php endif; ?>

<!-- ADD BUTTON -->
<div style="text-align:center;margin-bottom:2rem;">
    <button onclick="openModal()" class="btn">
        + Add Artist
    </button>
</div>

<!-- TABLE -->
<div style="max-width:1100px;margin:0 auto;">
<table style="width:100%;border-collapse:collapse;background:var(--card);border:1px solid var(--border);">

<thead>
<tr style="text-align:left;">
    <th>Image</th>
    <th>Name</th>
    <th>Genre</th>
    <th>Rating</th>
    <th>About</th>
    <th>Actions</th>
</tr>
</thead>

<tbody>

<?php foreach ($artists as $artist): ?>
<tr style="border-top:1px solid var(--border);">

    <td>
        <?php if (!empty($artist['artist_image'])): ?>
            <img src="../uploads/artists/<?= htmlspecialchars($artist['artist_image']) ?>"
                 style="width:60px;height:60px;object-fit:cover;border-radius:6px;">
        <?php else: ?>
            N/A
        <?php endif; ?>
    </td>

    <td><?= htmlspecialchars($artist['artist_name']) ?></td>
    <td><?= htmlspecialchars($artist['genre']) ?></td>
    <td><?= htmlspecialchars($artist['rating']) ?></td>
    <td><?= nl2br(htmlspecialchars(substr($artist['about'], 0, 80))) ?>...</td>

    <td style="display:flex;gap:10px;">

        <a href="edit-artist.php?id=<?= $artist['artist_id'] ?>" class="btn green">Edit</a>

        <form method="POST" onsubmit="return confirm('Delete this artist?');">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= $artist['artist_id'] ?>">
            <button class="btn red">Delete</button>
        </form>

    </td>

</tr>
<?php endforeach; ?>

</tbody>
</table>
</div>

<!-- MODAL -->
<div id="artistModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.7);">

<div style="background:#0d1117;max-width:500px;margin:5% auto;padding:2rem;border-radius:10px;">

<h2>Add Artist</h2>

<form method="POST" enctype="multipart/form-data">

<input type="hidden" name="action" value="add">

<label>Name</label>
<input type="text" name="artist_name" required style="width:100%;padding:.7rem;margin-bottom:1rem;">

<label>Genre</label>
<input type="text" name="genre" style="width:100%;padding:.7rem;margin-bottom:1rem;">

<label>Rating</label>
<input type="number" step="0.1" name="rating" style="width:100%;padding:.7rem;margin-bottom:1rem;">

<label>About</label>
<textarea name="about" style="width:100%;padding:.7rem;margin-bottom:1rem;"></textarea>

<label>Image</label>
<input type="file" name="artist_image" style="margin-bottom:1rem;">

<button type="submit" class="btn" style="width:100%;">Save</button>

</form>

<br>
<button onclick="closeModal()" class="btn red" style="width:100%;">Close</button>

</div>
</div>

<script>
function openModal(){
    document.getElementById('artistModal').style.display='block';
}
function closeModal(){
    document.getElementById('artistModal').style.display='none';
}
</script>

</main>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
