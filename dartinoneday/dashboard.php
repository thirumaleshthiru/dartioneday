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
<title>Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="style.css">
<style>

.containerr {
max-width:90%;
margin: 20px auto;
padding: 20px;
background-color: #FFF;  
border-radius: 8px;
box-shadow: 0 0 10px rgba(0,0,0,0.1);  
margin-top:100px;
}
h2 {
color: #698474;  
}
p {
color: #698474;  
}
ul {
list-style-type: none;
padding: 0;
}
ul li {
margin-bottom: 10px;
}
ul li a {
text-decoration: none;
color: #DCA47C ;
}
ul li a:hover {
color: #698474; 
}
.logout-link {
display: block;
margin-top: 20px;
text-align: center;
color: #698474; 
}
.logout-link:hover {
color: #DCA47C ;
}
</style>
</head>
<body>
<?php include 'navbar.php' ?>
<div class="containerr">
<h2>Dashboard</h2>
<p>Welcome, <?php echo $_SESSION['username']; ?>!</p>
<ul>
<li><a href="add_post.php">Add New Post</a></li>
<li><a href="manage_posts.php">Manage Posts</a></li>
</ul>
<a class="logout-link" href="logout.php">Logout</a>
</div>
</body>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</html>

<?php
$stmt->close();
$conn->close();
?>
