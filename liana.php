<?php
include("header.php");
?>
<link rel="apple-touch-icon" href="./woman-touch-icon.png" />
<link rel="apple-touch-startup-image" href="./startup.png" />
<meta name="msapplication-TileImage" content="./woman-touch-icon.png"/>
<title>Home Workout</title>
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


include("config.php");

$user = "liana";
$hiderecentlydone = true;

$exercises = array();
$exercises[] = array('name'=>'squats', 'sets'=>3, 'reps'=>[4, 5, 6, 7, 8]);
$exercises[] = array('name'=>'pullups', 'sets'=>3, 'reps'=>[4, 5, 6, 7, 8]);
$exercises[] = array('name'=>'handstand', 'sets'=>3, 'reps'=>[4, 5, 6, 7, 8]);
$exercises[] = array('name'=>'legraises', 'sets'=>3, 'reps'=>[4, 5, 6, 7, 8]);
$exercises[] = array('name'=>'pushups', 'sets'=>3, 'reps'=>[4, 5, 6, 7, 8]);
$exercises[] = array('name'=>'horizontalpull', 'sets'=>3, 'reps'=>[4, 5, 6, 7, 8]);
$exercises[] = array('name'=>'plank', 'sets'=>1, 'reps'=>[30, 38, 45, 53, 60]);

include("get-exercises.php");

?>
