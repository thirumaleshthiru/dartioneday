<?php
include 'config.php';

$sql = "SELECT posts.meta_title, concepts.title AS concept_title FROM posts JOIN concepts ON posts.concept_id = concepts.concept_id";
$result = $conn->query($sql);

function createSlug($string) {
return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string)));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="style.css">
<title>Topics</title>
<style>
main{
width:80%;
margin:auto;
margin-top:100px;
}
.lists {
margin-top:10px;
display:flex;
flex-direction:column;
justify-content:space-around;
align-items:flex-start
}
.lists  .link{
width:90%;
color:#405D72;
font-size:18px;
text-decoration:none;
}
.lists .link-item{
width:80%;
background-color:#EEEEEE;
color:black;
list-style: none;
margin:10px;
padding:10px;
text-align:center;
border-radius:10px;
display:flex;
justify-content:space-between;
transition:all 0.3s;
}
.title{
 margin:10px;
color:#E68369;
}
.link-item:hover{
  text-decoration:none;
  transition:all 0.3s;
  box-shadow: 0px 2px 5px 0px #EE99C2, 0px 1px 1px 0px #EE99C2;
}
 
@media screen and (max-width:800px){
 main{
    width:99%;
  }
.title {
    font-size:21px;
     color:#DCA47C;
}
img{
width:100%;
height:200px;
margin-top:10px;
}
 
.lists li .link{
width:90%;
 color:white;
font-size:18px;
 }
.lists .link-item{
    width:90%;
 }
}

</style>
</head>
<body>
<?php include 'navbar.php' ?>
<main>

<h1 class="title">Dart Programming Tutorial</h1>
 <br>
<div class="lists">
<?php
if ($result && $result->num_rows > 0) {
while($row = $result->fetch_assoc()) {
$meta_title = $row['meta_title'];
$concept_title = $row['concept_title'];
$slug = createSlug($meta_title);
echo "<a href='http://localhost/dartinoneday/blog/$slug' class='link'><div class='link-item'><span>$concept_title</span> <span>&gt;</span></div></a>";
}
} else {
echo "No topics found.";
}

$conn->close();
?>
</div>
</main>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
