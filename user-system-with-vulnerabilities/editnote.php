<?php
session_start();
require_once 'db.php';

$user = null;

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    try {
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();
    } catch (PDOException $e) {
    }
}

if (!$user) {
    header("Location: login.php");
    exit;
}

$error = "";
$success = "";
$note = null;

$note_id = $_GET['noteid'] ?? null;

if (!$note_id) {
    die("Note ID parameter is required.");
}

try {
    $stmt = $db->prepare(
        "SELECT * FROM notes WHERE id = ? AND user_id = ?"
    );

    $stmt->execute([$note_id, $user['id']]);

    $note = $stmt->fetch();

    if (!$note) {
        die("Note not found or access denied.");
    }
} catch (PDOException $e) {
    die("Database error.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['delete'])) {

        try {
            $stmt = $db->prepare(
                "DELETE FROM notes WHERE id = ? AND user_id = ?"
            );

            $stmt->execute([$note_id, $user['id']]);

            header("Location: index.php");
            exit;

        } catch (PDOException $e) {
            $error = "Failed to delete note.";
        }

    } else {

        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');

        if (empty($title) || empty($content)) {

            $error = "Please fill in all fields.";

        } else {

            try {

                $stmt = $db->prepare(
                    "UPDATE notes
                     SET title = ?, content = ?
                     WHERE id = ? AND user_id = ?"
                );

                $stmt->execute([
                    $title,
                    $content,
                    $note_id,
                    $user['id']
                ]);

                $note['title'] = $title;
                $note['content'] = $content;

                $success = "Note updated successfully!";

            } catch (PDOException $e) {

                $error = "Failed to update note.";

            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Note - NoteApp Lab</title>
    <link rel="stylesheet" href="css/bulma.min.css">
    <style>
        body {
            background-color: #f7f9fa;
            min-height: 100vh;
        }
    </style>
</head>
<body>

<nav class="navbar is-dark">
    <div class="container">
        <div class="navbar-brand">
            <a class="navbar-item" href="index.php">
                <strong class="has-text-primary is-size-4">
                    📝 NoteApp Lab
                </strong>
            </a>
        </div>

        <div class="navbar-menu">
            <div class="navbar-end">
                <div class="navbar-item">
                    <span class="has-text-light">
                        Logged in as:
                        <strong>
                            <?php echo htmlspecialchars($user['username']); ?>
                        </strong>
                    </span>
                </div>
            </div>
        </div>
    </div>
</nav>

<section class="section">
    <div class="container" style="max-width:600px;">
        <div class="box">

            <h1 class="title is-3 has-text-centered mb-5">
                Edit Note
            </h1>

            <?php if ($error): ?>
                <div class="notification is-danger">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="notification is-success">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <form method="POST">

                <div class="field">
                    <label class="label">Title</label>

                    <div class="control">
                        <input
                            class="input"
                            type="text"
                            name="title"
                            value="<?php echo htmlspecialchars($note['title']); ?>"
                            required>
                    </div>
                </div>

                <div class="field">
                    <label class="label">Content</label>

                    <div class="control">
                        <textarea
                            class="textarea"
                            name="content"
                            rows="6"
                            required><?php echo htmlspecialchars($note['content']); ?></textarea>
                    </div>
                </div>

                <div class="field is-grouped mt-5">

                    <div class="control is-expanded">
                        <button
                            type="submit"
                            class="button is-primary is-fullwidth">
                            Save Changes
                        </button>
                    </div>

                    <div class="control">
                        <button
                            type="submit"
                            name="delete"
                            class="button is-danger"
                            onclick="return confirm('Are you sure you want to delete this note?');">
                            Delete Note
                        </button>
                    </div>

                    <div class="control">
                        <a href="index.php" class="button is-light">
                            Cancel
                        </a>
                    </div>

                </div>

            </form>

        </div>
    </div>
</section>

</body>
</html>

