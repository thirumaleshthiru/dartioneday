<?php
session_start();
if (!isset($_SESSION['user_id'])) {
header("Location: login.php");
exit;
}
include 'config.php';  

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['post_id'])) {
$post_id = filter_var($_GET['post_id'], FILTER_VALIDATE_INT);

$sql = "DELETE FROM posts WHERE post_id = ? AND author_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $post_id, $_SESSION['user_id']);
$stmt->execute();

if ($stmt->affected_rows > 0) {
header("Location: manage_posts.php?message=Post deleted successfully.");
exit;
} else {
header("Location: manage_posts.php?error=Unable to delete post.");
exit;
}
} else {
header("Location: manage_posts.php");
exit;
}

$stmt->close();
$conn->close();
?>
