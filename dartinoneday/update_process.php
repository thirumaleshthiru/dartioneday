<?php
session_start();
require_once 'config.php'; // Include your database connection

// Redirect to login if user is not authenticated
if (!isset($_SESSION['user_id'])) {
header("Location: login.php");
exit;
}

// Initialize variables for error and success messages
$error_msg = $success_msg = '';

// Check if form is submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
// Sanitize and validate input data
$post_id = filter_input(INPUT_POST, 'post_id', FILTER_VALIDATE_INT);
$meta_title = htmlspecialchars($_POST['meta_title']);
$meta_description = htmlspecialchars($_POST['meta_description']);
$content = htmlspecialchars($_POST['content']);
$concept_title = htmlspecialchars($_POST['concept_title']);
$tags = array_map('trim', explode(',', $_POST['tags']));

// Validate post_id
if (!$post_id || $post_id <= 0) {
$error_msg = "Invalid post ID";
} else {
// Check if the concept already exists
$stmt = $conn->prepare("SELECT concept_id FROM concepts WHERE title = ?");
$stmt->bind_param("s", $concept_title);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
// Concept exists, get the existing concept_id
$stmt->bind_result($concept_id);
$stmt->fetch();
} else {
// Concept does not exist, insert a new concept
$stmt = $conn->prepare("INSERT INTO concepts (title) VALUES (?)");
$stmt->bind_param("s", $concept_title);
$stmt->execute();
$concept_id = $stmt->insert_id;
}

$stmt->close();

// Update post in posts table
$sql = "UPDATE posts SET meta_title = ?, meta_description = ?, content = ?, concept_id = ? WHERE post_id = ? AND author_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssiii", $meta_title, $meta_description, $content, $concept_id, $post_id, $_SESSION['user_id']);

if ($stmt->execute()) {
// Delete existing tags for the post
$stmt = $conn->prepare("DELETE FROM post_tags WHERE post_id = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();

// Insert tags into tags table and post_tags table
foreach ($tags as $tag) {
if ($tag) {
// Check if the tag already exists
$stmt = $conn->prepare("SELECT tag_id FROM tags WHERE name = ?");
$stmt->bind_param("s", $tag);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
// Tag exists, get the existing tag_id
$stmt->bind_result($tag_id);
$stmt->fetch();
} else {
// Tag does not exist, insert a new tag
$stmt = $conn->prepare("INSERT INTO tags (name) VALUES (?)");
$stmt->bind_param("s", $tag);
$stmt->execute();
$tag_id = $stmt->insert_id;
}

// Insert into post_tags
$stmt = $conn->prepare("INSERT INTO post_tags (post_id, tag_id) VALUES (?, ?)");
$stmt->bind_param("ii", $post_id, $tag_id);
$stmt->execute();
}
}

$success_msg = "Post updated successfully.";
} else {
$error_msg = "Error updating post: " . $stmt->error;
}

$stmt->close();
}
} else {
// Redirect to manage posts page if accessed directly without POST method
header("Location: manage_posts.php");
exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Update Post</title>
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container">
<h2>Update Post</h2>
<?php if ($error_msg): ?>
<div class="error-message"><?php echo $error_msg; ?></div>
<?php endif; ?>
<?php if ($success_msg): ?>
<div class="success-message"><?php echo $success_msg; ?></div>
<?php endif; ?>
<form method="post" action="update_post.php">
 </form>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
 </body>
</html>
