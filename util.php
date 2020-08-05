<?php

require_once "pdo.php";
function flashmsg(){
	if(isset($_SESSION['error'])){
	echo '<p style="color:red;">' . $_SESSION['error'] . "</p>\n";
    unset($_SESSION['error']);
}
	if(isset($_SESSION['success'])){
	echo('<p style="color:green">'.htmlentities($_SESSION['success'])."</p>\n");
	unset($_SESSION['success']);
}
}

function validateProfile(){
	if(strlen($_POST['first_name'])<1||strlen($_POST['last_name'])<1||strlen($_POST['email'])<1||strlen($_POST['headline'])<1||strlen($_POST['summary'])<1){
		return "All fields are required";
	}
	if(strpos($_POST['email'],'@')==false){
		return "Email address must contain @";
	}
}
function validatePos(){
	for($i=1;$i<=9;$i++){
		if(!isset($_POST['year'.$i])) continue;
		if(!isset($_POST['desc'.$i])) continue;
		$year=$_POST['year'.$i];
		$desc=$_POST['desc'.$i];
		if(strlen($year)==0||strlen($desc)==0){
			return "All fields are required";
		}
		if(!is_numeric($year)){
			return "Position year must be numeric";
		}
	}
	return true;
}
function validateEdu(){
	for($i=1;$i<=9;$i++){
		if(!isset($_POST['edu_year'.$i])) continue;
		if(!isset($_POST['edu_school'.$i])) continue;
		$edu_year=$_POST['edu_year'.$i];
		$edu_school=$_POST['edu_school'.$i];
		if(strlen($edu_year)==0||strlen($edu_school)==0){
			return "All fields are required";
		}
		if(!is_numeric($edu_year)){
			return "Education year must be numeric";
		}
	}
	return true;
}

function insertPos($pdo, $profile_id){
	$rank=1;
	for($i=1;$i<=9;$i++){
		if ( ! isset($_POST['year'.$i]) ) continue;
		if ( ! isset($_POST['desc'.$i]) ) continue;
		$year = $_POST['year'.$i];
        $desc = $_POST['desc'.$i];
		$stmt=$pdo->prepare("INSERT INTO Position (profile_id, rank, year, description) VALUES (:pid, :rank, :year, :desc)");
		$stmt->execute(array(
			':pid'=>$profile_id,
			':rank'=>$rank,
			':year'=>$year,
			':desc'=>$desc));
		$rank++;
	}
}
function insertEdu($pdo, $profile_id){
	$rank=1;
	for($i=1;$i<=9;$i++){
		if(!isset($_POST['edu_year'.$i])) continue;
		if(!isset($_POST['edu_school'.$i])) continue;
		$edu_year=$_POST['edu_year'.$i];
		$edu_school=$_POST['edu_school'.$i];
		$stmt=$pdo->prepare("SELECT * FROM Institution WHERE name=:nm");
		$stmt->execute(array(
			':nm'=>$edu_school));
		$row=$stmt->fetch(PDO::FETCH_ASSOC);
		if($row!==false){
			$institution_id=$row['institution_id'];
		}
		else{
			$stmt=$pdo->prepare("INSERT INTO Institution (name) VALUE (:name)");
			$stmt->execute(array(
				':name'=>$edu_school));
			$institution_id=$pdo->lastInsertId();
		}
	$stmt=$pdo->prepare("INSERT INTO Education (profile_id, institution_id, year, rank) VALUES (:pid, :iid, :year,:rank)");
	$stmt->execute(array(
		':pid'=>$profile_id,
		':iid'=>$institution_id,
		':year'=>$edu_year,
		':rank'=>$rank));
		$rank++;
}
}

function loadProfile($pdo, $profile_id){
	$sql="SELECT * FROM Profile where profile_id=:pid";
	$stmt=$pdo->prepare($sql);
	$stmt->execute(array(
		':pid'=>$profile_id));
	$row=$stmt->fetch(PDO::FETCH_ASSOC);
	return $row;
}
function loadPos($pdo, $profile_id)
{
    $stmt = $pdo->prepare('SELECT * FROM Position where profile_id = :prof ORDER BY rank');
    $stmt->execute(array(":prof" => $profile_id));
    $rowPos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $rowPos;
}

function loadEdu($pdo, $profile_id)
{
    $stmt = $pdo->prepare('SELECT year,name FROM Education join Institution on Education.institution_id = Institution.institution_id
 where profile_id = :prof ORDER BY rank');
    $stmt->execute(array(":prof" => $profile_id));
    $rowEdu = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $rowEdu;
}
?>