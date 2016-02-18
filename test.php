<?php
include("header.php");
?>
<link rel="apple-touch-icon" href="./man-touch-icon.png" />
<link rel="apple-touch-startup-image" href="./startup.png" />
<meta name="msapplication-TileImage" content="./man-touch-icon.png"/>
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


include("config.php");

$user = "test";
$hiderecentlydone = false;

$dw = date("N"); # 1=Monday, 7=Sunday
$exerciseroutine = "A";

switch ($dw) {
    case 1: # Monday
        $exerciseroutine = "A";
        break;
    case 2: # Tuesday
        $exerciseroutine = "A";
        break;
    case 3: # Wednesday
        $exerciseroutine = "A";
        break;
    case 4: # Thursday
        $exerciseroutine = "B";
        break;
    case 5: # Friday
        $exerciseroutine = "B";
        break;
    case 6: # Saturday
        $exerciseroutine = "C";
        break;
    case 7: # Sunday
        $exerciseroutine = "C";
        break;
    default:
        $exerciseroutine = "A";
}

$barbell = 45;
$plates = [45, 35, 25, 10, 5, 2.5];

$exercises = array();
$exercises[] = array('name'=>'deadlift', 'sets'=>1, 'reps'=>[4,6], 'routines'=>['A','B', 'C'], 'type' => 'barbell', 'startweight' => 275, 'addweight' => 10);
$exercises[] = array('name'=>'squats', 'sets'=>3, 'reps'=>[4, 5, 6, 7, 8], 'routines'=>['A','B','C']);
$exercises[] = array('name'=>'pullups', 'sets'=>3, 'reps'=>[4, 5, 6, 7, 8], 'routines'=>['A','C']);
$exercises[] = array('name'=>'horizontalpulls', 'sets'=>3, 'reps'=>[4, 5, 6, 7, 8], 'routines'=>['B']);
$exercises[] = array('name'=>'overheadpress', 'sets'=>1, 'reps'=>[4, 6], 'routines'=>['A'], 'type' => 'barbell', 'startweight' => 285, 'addweight' => 10);
$exercises[] = array('name'=>'dips', 'sets'=>3, 'reps'=>[4, 5, 6, 7, 8], 'routines'=>['B']);
$exercises[] = array('name'=>'handstands', 'sets'=>3, 'reps'=>[4, 5, 6, 7, 8], 'routines'=>['C']);
$exercises[] = array('name'=>'legraises', 'sets'=>3, 'reps'=>[4, 5, 6, 7, 8], 'routines'=>['A','B','C']);
$exercises[] = array('name'=>'benchpress', 'sets'=>3, 'reps'=>[4, 6], 'routines'=>['A','B']);
$exercises[] = array('name'=>'pushups', 'sets'=>3, 'reps'=>[4, 5, 6, 7, 8], 'routines'=>['C']);
$exercises[] = array('name'=>'plank', 'sets'=>1, 'reps'=>[30, 38, 45, 53, 60], 'routines'=>['A','B','C']);

include("get-exercises.php");


?>
