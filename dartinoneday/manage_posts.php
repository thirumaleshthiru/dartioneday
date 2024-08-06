<?php
session_start();
if (!isset($_SESSION['user_id'])) {
header("Location: login.php");
exit;
}
include 'config.php';  

 $user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM posts WHERE author_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="style.css">
<title>Manage Posts</title>
<style>
body {
font-family: Arial, sans-serif;
background-color: #EEEDEB;  
color: #2F3645;  
margin: 0;
padding: 0;
}
h2 {
color: #698474;  
margin-top: 20px;
text-align: center;
}
.containerr {
max-width: 90%;
margin: 20px auto;
padding: 0 20px;
display: flex;
flex-wrap: wrap;
justify-content: space-between;
gap:10px;
}
.card {
border: 1px solid #ccc;
border-radius: 5px;
padding: 20px;
margin: 10px;
width: 300px;
display: flex;
flex-direction:column;
gap:10px;
vertical-align: top;
background-color: #FFF; 
box-shadow: 0 0 10px rgba(0,0,0,0.1);  
}
.card img {
max-width: 100%;
height: 200px;
border-radius: 5px;
margin-bottom: 10px;
}
.card .title {
color: #698474;  
}
.card .actions {
margin-top: 10px;
text-align: center;
}
.card .actions a {
text-decoration: none;
color: #939185;  
padding: 5px 10px;
border: 1px solid #939185;
border-radius: 3px;
margin-right: 10px;
transition: all 0.3s ease;
}
.card .actions a:hover {
background-color: #939185;  
color: #EEEDEB;  
}
.no-posts {
text-align: center;
color: #698474;  
}
.back-link {
display: block;
margin-top: 20px;
text-align: center;
color: #698474;  
text-decoration: none;
}
.back-link:hover {
color: #939185;  
}
</style>
</head>
<body>
<?php include 'navbar.php' ?>
<h2>Manage Posts</h2>

<div class="containerr">
<?php if ($result->num_rows > 0) : ?>
<?php while ($row = $result->fetch_assoc()) : ?>
<div class="card">
<?php if ($row['cover_image']) : ?>
<img src="data:image/jpeg;base64,<?php echo base64_encode($row['cover_image']); ?>" alt="Cover Image">
<?php else: ?>
<img src="default_image.jpg" alt="Default Image">
<?php endif; ?>
<div class="title"><?php echo htmlspecialchars($row['meta_title']); ?></div>
<div class="actions">
<a href="update.php?post_id=<?php echo $row['post_id']; ?>">Edit</a>
<a href="delete.php?post_id=<?php echo $row['post_id']; ?>">Delete</a>
</div>
</div>
<?php endwhile; ?>
<?php else : ?>
<p class="no-posts">No posts found.</p>
<?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
