<?php
session_start();
if (!isset($_SESSION['user_id'])) {
header("Location: login.php");
exit;
}
include 'config.php'; 

 if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['post_id'])) {
 $post_id = filter_var($_GET['post_id'], FILTER_VALIDATE_INT);

if ($post_id === false || $post_id === null) {
 header("Location: manage_posts.php?error=Invalid post ID");
exit;
}

 $sql = "SELECT p.*, c.title AS concept_title, GROUP_CONCAT(t.name) AS tags
FROM posts p
LEFT JOIN concepts c ON p.concept_id = c.concept_id
LEFT JOIN post_tags pt ON p.post_id = pt.post_id
LEFT JOIN tags t ON pt.tag_id = t.tag_id
WHERE p.post_id = ? AND p.author_id = ?
GROUP BY p.post_id";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $post_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

 if ($result->num_rows == 1) {
$post = $result->fetch_assoc();
 
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Post</title>
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="style.css">
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>
main{
margin-top:100px;
}
h2 {
color: #698474; 
}
form {
background-color: #FFF; 
padding: 20px;
border-radius: 8px;
max-width:90%;
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
height: 800px;
margin-top: 10px;
}
</style>
</head>
<body>
<?php include 'navbar.php'; ?>
<main>
<form method="post" action="update_process.php" enctype="multipart/form-data">
<input type="hidden" name="post_id" value="<?php echo $post['post_id']; ?>">
<label for="meta_title">Meta Title:</label>
<input type="text" id="meta_title" name="meta_title" value="<?php echo isset($post['meta_title']) ? htmlspecialchars($post['meta_title']) : ''; ?>" required>
<label for="meta_description">Meta Description:</label>
<textarea id="meta_description" name="meta_description" required><?php echo isset($post['meta_description']) ? htmlspecialchars($post['meta_description']) : ''; ?></textarea>
<label for="content">Content:</label>
<div id="editor"><?php echo isset($post['content']) ? htmlspecialchars_decode($post['content']) : ''; ?></div>
<input type="hidden" name="content" id="content">

<label for="concept_title">Concept Title:</label>
<input type="text" id="concept_title" name="concept_title" value="<?php echo isset($post['concept_title']) ? htmlspecialchars($post['concept_title']) : ''; ?>" required>
<label for="tags">Tags (comma separated):</label>
<input type="text" id="tags" name="tags" value="<?php echo isset($post['tags']) ? htmlspecialchars($post['tags']) : ''; ?>" required><br><br>
<button type="submit">Update Post</button>
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

['link','video', 'code-block', 'clean']  
]
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
</body>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</html>
<?php
} else {
 header("Location: manage_posts.php?error=Post not found or you don't have permission to edit.");
exit;
}
} else {
 header("Location: manage_posts.php");
exit;
}

$stmt->close();
$conn->close();
?>
