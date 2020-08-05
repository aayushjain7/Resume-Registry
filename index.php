<?php
session_start();
require_once "pdo.php";
?>
<!DOCTYPE html>
<head>
<title>Aayush Jain</title>
<?php require_once "head.php";?>
</head>
<body>
<div class="container">
<h1>Aayush Jain's Resume Registry</h1>
<?php
if(isset($_SESSION['success'])){
	echo('<p style="color:green">'.htmlentities($_SESSION['success'])."</p>\n");
	unset($_SESSION['success']);
}
if(isset($_SESSION['error'])){
	echo '<p style="color:red">' . $_SESSION['error'] . "</p>\n";
    unset($_SESSION['error']);
}

if(!isset($_SESSION['name'])&& !isset($_SESSION['user_id'])){
	echo '<p><a href="login.php">Please log in</a></p>';
}
else{
	echo '<p><a href="logout.php">Logout</a></p>';
}
?>
<?php
$stmt=$pdo->query("SELECT profile_id, first_name, last_name, headline from Profile JOIN users ON users.user_id = Profile.user_id");
$rows=$stmt->fetchAll(PDO::FETCH_ASSOC);
if(sizeof($rows)>0){
echo "<table border='1'>";
echo "<thead><tr>";
echo "<th>Name</th>";
echo "<th>Heading</th>";
if(isset($_SESSION['name'])&& isset($_SESSION['user_id'])){
	echo "<th>Action</th>";
}
echo "</tr></head>";
foreach ($rows as $row){
	echo "<tr><td>";
	echo "<a href='view.php?profile_id=".$row['profile_id']."'>".$row['first_name']." ".$row['last_name']."</a>";
	echo "</td><td>";
	echo $row['headline'];
	echo "</td>";
	if(isset($_SESSION['name'])&& isset($_SESSION['user_id'])){
		echo "<td>";
		echo "<a href='edit.php?profile_id=".$row['profile_id']."'>Edit</a> / <a href='delete.php?profile_id=".$row['profile_id']."'>Delete</a>";
	}
}
echo "</table>";
} 
?>
<?php
if(isset($_SESSION['name'])&& isset($_SESSION['user_id'])){
	echo "<p><a href='add.php'>Add New Entry</a></p>";
}
?>
<p><b>Note:</b> Your implementation should retain data across multiple
            logout/login sessions. This sample implementation clears all its
            data periodically - which you should not do in your implementation.</p>
</div>
</body>
</html>