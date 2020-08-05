<?php
session_start();
require_once "pdo.php";
require_once "util.php";
if(!isset($_SESSION['name'])){
	die("ACCESS DENIED");
}
if(isset($_POST['cancel'])){
	header("Location: index.php");
	return;
}
if (!isset($_GET['profile_id'])) {
    $_SESSION['error'] = "Could not load profile";
    header('Location: index.php');
    return;
}
if(isset($_POST['first_name'])&&isset($_POST['last_name'])&&isset($_POST['email'])&&isset($_POST['headline'])&&isset($_POST['summary'])){
	$msg=validateProfile();
	if(is_string($msg)){
		$_SESSION['error']=$msg;
		header("Location: edit.php?profile_id=" . $_REQUEST["profile_id"]);
		return;
	}
	$msg=validatePos();
	if(is_string($msg)){
		$_SESSION['error']=$msg;
		header("Location: edit.php?profile_id=" . $_REQUEST["profile_id"]);
		return;
	}
	$msg=validateEdu();
	if(is_string($msg)){
		$_SESSION['error']=$msg;
		header("Location: edit.php?profile_id=" . $_REQUEST["profile_id"]);
		return;
	}
	$sql="UPDATE Profile SET first_name = :first_name, last_name = :last_name,email=:email,headline=:headline,summary=:summary
            WHERE profile_id = :profile_id";
		$stmt=$pdo->prepare($sql);
		$stmt->execute(array(
			':first_name'=>$_POST['first_name'],
			':last_name'=>$_POST['last_name'],
			':email'=>$_POST['email'],
			':headline'=>$_POST['headline'],
			':summary'=>$_POST['summary'],
			':profile_id'=>$_GET['profile_id']));
			
	$stmt = $pdo->prepare('DELETE FROM Position WHERE profile_id=:pid');
    $stmt->execute(array(':pid' => $_REQUEST['profile_id']));
	insertPos($pdo, $_REQUEST['profile_id']);
	
	$stmt = $pdo->prepare('DELETE FROM Education WHERE profile_id=:pid');
    $stmt->execute(array(':pid' => $_REQUEST['profile_id']));
	insertEdu($pdo, $_REQUEST['profile_id']);
	$_SESSION['success'] = 'Profile updated';
    header('Location: index.php');
    return;
}
$row=loadProfile($pdo, $_REQUEST['profile_id']);
$rowPos=loadPos($pdo, $_REQUEST['profile_id']);
$rowEdu=loadEdu($pdo, $_REQUEST['profile_id']);
?>
<!DOCTYPE html>
<head>
<title>Aayush Jain</title>
<?php require_once "head.php";?>
<body>
<div class="container">
<h1>Editing Profile for UMSI</h1>
<?php flashmsg();?>
<form method="post">
<p>First Name: <input type="text" name="first_name" size="60" value="<?php echo $row['first_name']?>"></p>
<p>Last Name: <input type="text" name="last_name" size="60" value="<?php echo $row['last_name']?>"></p>
<p>Email: <input type="text" name="email" size="30" value="<?php echo $row['email']?>"></p>
<p>Headline:<br/><input type="text" name="headline" size="80" value="<?php echo $row['headline']?>"></p>
<p>Summary:<br/><textarea name="summary" rows="8" cols="80"><?php echo $row['summary']?>"</textarea></p>
<p>
Education: <input type="submit" id="addEdu" value="+">
<div id="edu_fields">
<?php
$rank=1;
foreach($rowEdu as $row){
	echo "<div id='edu".$rank."'>";
	echo "<p>Year: <input type='text' name='edu_year".$rank."' value='".$row['year']."'>";
	echo "<input type='button' value='-' onclick='$(\"#edu".$rank."\").remove();return false;'></p>";
	echo "<p>School: <input type='text' size='80' name='edu_school".$rank."' class='school' value='".$row['name']."'></p>";
	echo "</div>";
	$rank++;
}
?>
</div>
</p>

<p>
Position: <input type="submit" id="addPos" value="+">
<div id="position_fields">
<?php
$edu_rank=1;
foreach($rowPos as $row){
	echo "<div id='position".$edu_rank."'>";
	echo "<p>Year: <input type='text' name='year".$edu_rank."' value='".$row['year']."'>";
	echo "<input type='button' value='-' onclick='$(\"#position".$edu_rank."\").remove();return false;'></p>";
	echo "<p><textarea name='desc".$edu_rank."' rows='8' cols='80'>".$row['description']."</textarea></p>";
	echo "</div>";
	$edu_rank++;
}
?>
</div>
</p>
<p><input type="submit" value="Save">
<input type="submit" name="cancel" value="Cancel"></p>
</form>
<script>
countPos=<?= $rank?>;
countEdu=<?= $edu_rank?>;
$(document).ready(function(){
	console.log("Document ready called");
	$('#addPos').click(function(event){
		event.preventDefault();
		if(countPos>=9){
			alert("Maximum of nine position entries exceeded");
			return;
		}
		countPos++;
		console.log("Adding Position:"+countPos);
		$('#position_fields').append(
		'<div id="position'+countPos+'">\
		<p>Year: <input type="text" name="year'+countPos+'" value="">\
		<input type="button" value="-" onclick="$(\'#position'+countPos+'\').remove();return false;"></p>\
		<textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
		</div>');
	});
	$('#addEdu').click(function(){
		event.preventDefault();
		if(countEdu>=9){
			alert("Maximum of nine educations entries exceeded");
			return;
		}
		countEdu++;
		console.log("Adding Education:"+countEdu);
		$('#edu_fields').append(
		'<div id="edu'+countEdu+'">\
		<p>Year: <input type="text" name="edu_year'+countEdu+'" value="">\
		<input type="button" value="-" onclick="$(\'#edu'+countEdu+'\').remove();return false;"></p>\
		<p>School: <input type="text" name="edu_school'+countEdu+'" class="school" size="80" value="">\
		<p><div>');
		$('.school').autocomplete({
			source: "school.php"
		});
	});
});
</script>
</div>
</body>
</head>
</html>