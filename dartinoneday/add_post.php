<?php
session_start();
require_once 'config.php';  

 if (!isset($_SESSION['user_id'])) {
header("Location: login.php");
exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
$meta_title = $_POST['meta_title'];
$meta_description = $_POST['meta_description'];
$content = $_POST['content'];
$author_id = $_SESSION['user_id'];
$concept_title = $_POST['concept_title'];
$tags = array_map('trim', explode(',', $_POST['tags']));

 $cover_image = null;
if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] == 0) {
$cover_image_tmp = $_FILES['cover_image']['tmp_name'];
$cover_image = file_get_contents($cover_image_tmp);
}

 $stmt = $conn->prepare("SELECT concept_id FROM concepts WHERE title = ?");
$stmt->bind_param("s", $concept_title);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
 $stmt->bind_result($concept_id);
$stmt->fetch();
} else {
 $stmt = $conn->prepare("INSERT INTO concepts (title) VALUES (?)");
$stmt->bind_param("s", $concept_title);
$stmt->execute();
$concept_id = $stmt->insert_id;
}

$stmt->close();

$stmt = $conn->prepare("INSERT INTO posts (meta_title, meta_description, content, cover_image, author_id, concept_id) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssii", $meta_title, $meta_description, $content, $cover_image, $author_id, $concept_id);

if ($stmt->execute()) {
$post_id = $stmt->insert_id;

 foreach ($tags as $tag) {
if ($tag) {
 $stmt = $conn->prepare("SELECT tag_id FROM tags WHERE name = ?");
$stmt->bind_param("s", $tag);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
 $stmt->bind_result($tag_id);
$stmt->fetch();
} else {
 $stmt = $conn->prepare("INSERT INTO tags (name) VALUES (?)");
$stmt->bind_param("s", $tag);
$stmt->execute();
$tag_id = $stmt->insert_id;
}

 
$stmt = $conn->prepare("INSERT INTO post_tags (post_id, tag_id) VALUES (?, ?)");
$stmt->bind_param("ii", $post_id, $tag_id);
$stmt->execute();
}
}

$_SESSION['message'] = "Post added successfully.";
} else {
$_SESSION['message'] = "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();

header("Location: add_post.php");
exit;
}

$message = "";
if (isset($_SESSION['message'])) {
$message = $_SESSION['message'];
unset($_SESSION['message']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Post</title>
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<link rel="stylesheet" href="style.css">
<style>
h2 {
color: #698474;
}
form {
background-color: #FFF;
padding: 20px;
border-radius: 8px;
max-width: 90%;
margin: auto;
box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}
label {
display: block;
margin-top: 10px;
color: #2F3645;
}
input[type="text"], input[type="file"], textarea {
width: 100%;
padding: 10px;
margin-top: 5px;
border: 1px solid #939185;
border-radius: 4px;
}
button {
background-color: #698474;
color: #FFF;
padding: 10px 20px;
border: none;
border-radius: 4px;
cursor: pointer;
margin-top: 20px;
}
button:hover {
background-color: #2F3645;
}
#editor {
background-color: #FFF;
border: 1px solid #939185;
border-radius: 4px;
height: 500px;
margin-top: 10px;
}
main {
margin-top: 100px;
}
.message {
margin: 20px auto;
padding: 10px;
border-radius: 4px;
max-width: 90%;
text-align: center;
}
.message.success {
background-color: #DFF2BF;
color: #4F8A10;
}
.message.error {
background-color: #FFBABA;
color: #D8000C;
}
</style>
</head>
<body>

<?php include 'navbar.php' ?>
<main>
<?php if ($message): ?>
<div class="message <?php echo strpos($message, 'Error') === false ? 'success' : 'error'; ?>">
<?php echo $message; ?>
</div>
<?php endif; ?>
<form method="post" action="add_post.php" enctype="multipart/form-data">
<label for="meta_title">Meta Title:</label>
<input type="text" id="meta_title" name="meta_title" required>
<label for="meta_description">Meta Description:</label>
<textarea id="meta_description" name="meta_description" required></textarea>
<label for="content">Content:</label>
<div id="editor"></div>
<input type="hidden" name="content" id="content">
<label for="cover_image">Cover Image:</label>
<input type="file" id="cover_image" name="cover_image">
<label for="concept_title">Concept Title:</label>
<input type="text" id="concept_title" name="concept_title" required>
<label for="tags">Tags (comma separated):</label>
<input type="text" id="tags" name="tags" required><br><br>
<button type="submit">Add Post</button>
</form>
</main>
 <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
var toolbarOptions = [
['bold', 'italic', 'underline', 'strike'],         
['blockquote', 'code-block'],
[{ 'header': 1 }, { 'header': 2 }],               
[{ 'list': 'ordered'}, { 'list': 'bullet' }],
[{ 'script': 'sub'}, { 'script': 'super' }],       
[{ 'indent': '-1'}, { 'indent': '+1' }],         
[{ 'direction': 'rtl' }],                          
[{ 'size': ['small', false, 'large', 'huge'] }],  
[{ 'header': [1, 2, 3, 4, 5, 6, false] }],
[{ 'color': [] }, { 'background': [] }],          
[{ 'font': [] }],
[{ 'align': [] }],
['link', 'video', 'code-block', 'clean'] 
];

var quill = new Quill('#editor', {
theme: 'snow',
modules: {
toolbar: toolbarOptions
}
});

document.querySelector('form').onsubmit = function() {
document.querySelector('input[name=content]').value = quill.root.innerHTML;
};
</script>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
