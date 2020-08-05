 <?php
 session_start();
 require_once "pdo.php";
 if(!isset($_SESSION['name'])){
	die("ACCESS DENIED");
}
if(!isset($_REQUEST['profile_id'])){
	$_SESSION['error']="Missing profile_id";
	header("Location: index.php");
	return;
}
if(isset($_POST['cancel'])){
	header("Location: index.php");
	return;
}
if(isset($_POST['delete'])&&isset($_REQUEST['profile_id'])){
	$sql="DELETE FROM Profile where profile_id=:pid";
	$stmt=$pdo->prepare($sql);
	$stmt->execute(array(
		':pid'=>$_REQUEST['profile_id']));
	$_SESSION['success']="Profile deleted";
	header("Location: index.php");
	return;
}
$stmt=$pdo->prepare("SELECT first_name, last_name FROM Profile where profile_id=:pid");
$stmt->execute(array(
	':pid'=>$_REQUEST['profile_id']));
$row=$stmt->fetch(PDO::FETCH_ASSOC);
if($row===false){
	$_SESSION['error']="Could not load profile";
	header("Location: index.php");
	return;
}
 ?>
 <!DOCTYPE html>
 <head>
    <title>Aayush Jain</title>
    <?php require_once "head.php"; ?>
</head>
<body>
<div class="container">
<h1>Deleteing Profile</h1>
<?php
echo '<p>First Name: '.$row['first_name'].'</p>';
echo '<p>Last Name: '.$row['last_name'].'</p>';
?>
<form method="POST">

<input type="submit" name="delete" value="Delete"> <input type="submit" name="cancel" value="Cancel">
</form> 
</div>
</body>
 </html>
 