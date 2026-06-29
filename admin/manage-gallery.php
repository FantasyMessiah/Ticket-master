<?php
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/inc/header.php';

/* --------------------------------------------------
   GET ARTIST ID
-------------------------------------------------- */
$artist_id = (int)($_GET['artist_id'] ?? 0);

if ($artist_id <= 0) {
    $_SESSION['error'] = "Invalid artist ID.";
    header("Location: manage-artists.php");
    exit;
}

/* --------------------------------------------------
   FETCH ARTIST
-------------------------------------------------- */
try {
    $stmt = $pdo->prepare("SELECT * FROM artists WHERE artist_id = ?");
    $stmt->execute([$artist_id]);
    $artist = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$artist) {
        $_SESSION['error'] = "Artist not found.";
        header("Location: manage-artists.php");
        exit;
    }
} catch (PDOException $e) {
    $_SESSION['error'] = $e->getMessage();
    header("Location: manage-artists.php");
    exit;
}

/* --------------------------------------------------
   FETCH GALLERY
-------------------------------------------------- */
try {
    $stmt = $pdo->prepare("SELECT * FROM gallery WHERE artist_id = ? ORDER BY gallery_id DESC");
    $stmt->execute([$artist_id]);
    $gallery = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $gallery = [];
}

/* --------------------------------------------------
   HANDLE UPLOAD / DELETE
-------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {

        $action = $_POST['action'] ?? '';

        /* ---------------- UPLOAD ---------------- */
        if ($action === 'upload') {

            if (empty($_FILES['images']['name'][0])) {
                throw new Exception("Please select at least one image.");
            }

            $uploadDir = __DIR__ . "/../uploads/gallery/";

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            foreach ($_FILES['images']['tmp_name'] as $key => $tmpName) {

                $fileName = time() . '_' . basename($_FILES['images']['name'][$key]);
                $target = $uploadDir . $fileName;

                move_uploaded_file($tmpName, $target);

                $stmt = $pdo->prepare("
                    INSERT INTO gallery (artist_id, image)
                    VALUES (?, ?)
                ");

                $stmt->execute([$artist_id, $fileName]);
            }

            $_SESSION['success'] = "Images uploaded successfully.";
            header("Location: manage-gallery.php?artist_id=" . $artist_id);
            exit;
        }

        /* ---------------- DELETE ---------------- */
        if ($action === 'delete') {

            $id = (int)($_POST['id'] ?? 0);

            if ($id <= 0) {
                throw new Exception("Invalid image ID.");
            }

            $stmt = $pdo->prepare("DELETE FROM gallery WHERE gallery_id = ?");
            $stmt->execute([$id]);

            $_SESSION['success'] = "Image deleted.";
            header("Location: manage-gallery.php?artist_id=" . $artist_id);
            exit;
        }

    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        header("Location: manage-gallery.php?artist_id=" . $artist_id);
        exit;
    }
}
?>

<main style="max-width:1000px;margin:2rem auto;">

<h1 style="text-align:center;margin-bottom:1rem;">Gallery</h1>

<!-- ARTIST HEADER -->
<div style="display:flex;align-items:center;gap:15px;background:var(--card);padding:15px;border-radius:10px;border:1px solid var(--border);margin-bottom:2rem;">

    <img src="../uploads/artists/<?= htmlspecialchars($artist['artist_image']) ?>"
         style="width:60px;height:60px;object-fit:cover;border-radius:8px;">

    <div>
        <h3 style="margin:0;"><?= htmlspecialchars($artist['artist_name']) ?></h3>
        <small style="color:#888;">Manage Gallery</small>
    </div>

</div>

<!-- UPLOAD FORM -->
<div style="background:#111827;padding:20px;border-radius:10px;margin-bottom:2rem;">

<form method="POST" enctype="multipart/form-data">

    <input type="hidden" name="action" value="upload">

    <label style="display:block;margin-bottom:10px;">Upload Images</label>

    <input type="file"
           name="images[]"
           multiple
           style="margin-bottom:10px;width:100%;">

    <button class="btn" style="width:100%;">Upload</button>

</form>

</div>

<!-- GALLERY GRID -->
<?php if (empty($gallery)): ?>
<p style="text-align:center;color:#888;">No images found.</p>
<?php endif; ?>

<div style="
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(180px,1fr));
    gap:15px;
">

<?php foreach ($gallery as $img): ?>

<div style="background:#111827;border:1px solid var(--border);border-radius:10px;overflow:hidden;">

    <img src="../uploads/gallery/<?= htmlspecialchars($img['image']) ?>"
         style="width:100%;height:160px;object-fit:cover;">

    <form method="POST" onsubmit="return confirm('Delete this image?');" style="padding:10px;">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" value="<?= $img['gallery_id'] ?>">

        <button class="btn red" style="width:100%;">Delete</button>
    </form>

</div>

<?php endforeach; ?>

</div>

</main>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
