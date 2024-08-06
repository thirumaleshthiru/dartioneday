 

<?php

if (session_status() === PHP_SESSION_NONE) {
session_start();
}
?>

<nav class="navbar navbar-expand-lg navbar-dark">
<a class="navbar-brand" href="/dartinoneday/index.php">Dart In One Day</a>
<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
<span class="navbar-toggler-icon"></span>
</button>
<div class="collapse navbar-collapse justify-content-around" id="navbarNav">
<ul class="navbar-nav">
<li class="nav-item">
<a class="nav-link" href="/dartinoneday/index.php">Home</a>
</li>
<li class="nav-item">
<a class="nav-link" href="/dartinoneday/dart-topics.php">Dart Topics</a>
</li>
<?php if (isset($_SESSION['user_id'])): ?>
<li class="nav-item">
<a class="nav-link" href="/dartinoneday/dashboard.php">Dashboard</a>
</li>
<li class="nav-item">
<a class="nav-link" href="/dartinoneday/logout.php">Logout</a>
</li>
<?php else: ?>

<?php endif; ?>
</ul>
</div>
</nav>

 