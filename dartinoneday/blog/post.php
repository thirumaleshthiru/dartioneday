<?php
include '../config.php';  

$slug = isset($_GET['slug']) ? $_GET['slug'] : '';

function convertSlugToMetaTitle($slug) {
    return ucwords(str_replace('-', ' ', $slug));
}

$meta_title = convertSlugToMetaTitle($slug);

$stmt = $conn->prepare("SELECT posts.post_id, posts.meta_title, posts.meta_description, posts.content, posts.cover_image, concepts.title AS concept_title, users.username AS author 
    FROM posts 
    JOIN concepts ON posts.concept_id = concepts.concept_id 
    JOIN users ON posts.author_id = users.user_id 
    WHERE posts.meta_title = ?");
$stmt->bind_param("s", $meta_title);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $post = $result->fetch_assoc();
    $post_id = $post['post_id'];  
} else {
    echo "Post not found.";
    exit;
}
$stmt->close();

$stmt = $conn->prepare("SELECT tags.name 
    FROM post_tags 
    JOIN tags ON post_tags.tag_id = tags.tag_id 
    WHERE post_tags.post_id = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$tags_result = $stmt->get_result();
$tags = [];
while ($tag = $tags_result->fetch_assoc()) {
    $tags[] = $tag['name'];
}
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?php echo htmlspecialchars($post['meta_title']); ?></title>
<meta name="description" content="<?php echo htmlspecialchars($post['meta_description']); ?>">
<link rel="canonical" href="http://localhost/dartinoneday/blog/<?php echo $slug; ?>" />
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../style.css">
<style>
main{
    width:80%;
    margin:auto;
    margin-top:100px;
}
pre{
    background-color:gray;
    padding:10px;
    color:white;
}
.ql-syntax{
    color:white;
}
img{
    width:500px;
    height:300px;
    margin-top:10px;
}

@media screen and (max-width:800px){
    main{
        width:95%;
    }
    img{
        width:100%;
        height:200px;
        margin-top:10px;
    }
}
</style>
</head>
<body>
<?php include '../navbar.php' ?>
<main>
<h1 class="title"><?php echo htmlspecialchars($post['concept_title']); ?></h1>

<?php if ($post['cover_image']): ?>
    <img src="data:image/jpeg;base64,<?php echo base64_encode($post['cover_image']); ?>" alt="Cover Image">
<?php else: ?>
    <p>No cover image available.</p>
<?php endif; ?>
<div><?php echo htmlspecialchars_decode($post['content']); ?></div>
<p><strong>Tags:</strong> <?php echo implode(', ', $tags); ?></p>
</main>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
