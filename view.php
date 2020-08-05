<?php
session_start();
require_once "pdo.php";
require_once "util.php";
if (!isset($_GET['profile_id'])) {
    $_SESSION['error'] = "Could not load profile";
    header('Location: index.php');
    return;
}

$profile_id=$_GET['profile_id'];
$row=loadProfile($pdo, $profile_id);
$rowPos=loadPos($pdo, $profile_id);
$rowEdu=loadEdu($pdo, $profile_id);

?>
<!DOCTYPE html>
<html>
<head>
    <?php require_once "head.php"; ?>
    <title>Aayush Jain</title>
</head>
<body>
<div class="container">
    <h1>Profile information</h1>
    <p>First Name: <?php echo($row['first_name']); ?></p>
    <p>Last Name: <?php echo($row['last_name']); ?></p>
    <p>Email: <?php echo($row['email']); ?></p>
    <p>Headline:<br/> <?php echo($row['headline']); ?></p>
    <p>Summary: <br/><?php echo($row['summary']); ?></p>
	<p>Education: <br/><ul>
        <?php
        foreach ($rowEdu as $row) {
            echo('<li>'.$row['year'].' : '.$row['name'].'</li>');
        } ?>
        </ul></p>
    <p>Position: <br/><ul>
        <?php
        foreach ($rowPos as $row) {
            echo('<li>'.$row['year'].' : '.$row['description'].'</li>');
        } ?>
        </ul></p>
    <a href="index.php">Done</a>
</div>
</body>
</html>