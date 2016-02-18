
<?php
include("plate-calculator.php");
function insertitem($categoryName, $stepNumber, $reps, $completed, $failed)
{
  // Tells PHP to use the global activities, not local one
  global $user, $dbh;

  // 1 is Selected, 2 is Completed, 3 completed and liked, 4 reshuffled, 5 rejected
  // Insert this new activity ID in the activity_log DB
  $insertselection = "
  INSERT INTO `workout`.`tracking` (
    `categoryName`, `stepNumber`, `user`, `reps`, `completed`, `failed`, `date`)
    VALUES (:categoryName, :stepNumber, :user, :reps, :completed, :failed, :now);
  ";

  $stmt = $dbh->prepare($insertselection);
  $today = date("Y-m-d H:i:s");


  if($stmt->execute(array(':categoryName' => $categoryName, ':stepNumber' => $stepNumber, ':user' => $user, ':reps' => $reps, ':completed' => $completed, ':failed' => $failed, ':now' => $today)) === false){
    $msg = 'Error inserting the alias.';
    return $msg;
  }else{
    $msg = "Item updated";
    return $msg;
}
}

?>

<?php
# Check if tracking variable was passed
  if (isset($_POST["tracking"]))
  {
    $trackingitems = $_POST["tracking"];
    #echo "You did pass a value";
    $trackingitem = explode(":", $trackingitems);
    $donecategory = $trackingitem[0];
    $donesteps = $trackingitem[1];
    $donereps = $trackingitem[2];
    $donecompleted = $trackingitem[3];
    $donefailed = $trackingitem[4];
    insertitem($donecategory, $donesteps, $donereps, $donecompleted, $donefailed);
  }
  else
  {
    $trackingitems = null;
  }

?>



<form class="navbar-form navbar-right" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">



<?php





foreach ($exercises as $exerciserow) {

    # Get the exercise that needs to be displayed
    $exercise = $exerciserow['name'];
    $repsarray = $exerciserow['reps'];
    $sets = $exerciserow['sets'];
    $routines = $exerciserow['routines'];

    # Get type of exercise - ie, bodyweight, barbell, dumbbell
    if (isset($exerciserow['type']))
    {
      $exercisetype = $exerciserow['type'];
    }
    else {
      $exercisetype = "bodyweight";
    }

    # Set a bodyweight flag. Any reason?
    $bodyweight = false;
    if ($exercisetype == "bodyweight")
    {
      $bodyweight = true;
    }

    # Get initial weight of the exercise, if barbell or dumbell
    if (isset($exerciserow['startweight']))
    {
      $startweight = $exerciserow['startweight'];
    }
    if (isset($exerciserow['addweight']))
    {
      $addweight = $exerciserow['addweight'];
    }

    # Filter days - A days, B days, C days
    if (!in_array($exerciseroutine, $routines)) {
        #echo "Not showing: " . $exercise;
    }
    else {

    ## Select steps
      # Get Category Info
        $categoryquery = "SELECT categoryID, link, image, video, instructions
            FROM categories
            WHERE categories.name like '%".$exercise."%'";

            $sth = $dbh->prepare($categoryquery);
            $sth->execute();

            $category = $sth->fetch();
            $categorylink = $category['link'];
            $categoryimage = $category['image'];
            $categoryvideo = $category['video'];
            $categoryinstructions = $category['instructions'];

      # Grab tracking steps where status is completed
    	    $trackingquery = "SELECT tracking.categoryName as categoryName, stepNumber, reps, completed, failed, date
          FROM tracking
          WHERE tracking.categoryName like '%".$exercise."%'
          AND tracking.user like '%".$user."%'
            ORDER BY trackingID DESC
            LIMIT 5";

            $sth = $dbh->prepare($trackingquery);
            $sth->execute();

            /* Change tracking results to array */
            $latestresults = $sth->fetchAll();
            if ($latestresults)
            {
              $trackingresults = $latestresults[0]; #What was the last one?
            }
            else {
              $trackingresults = false;
            }




      # Scenario 1 - No results occured. Grab the lowest ranking step of the steps DB, and have the lowest number of reps
      # No action needed?

      if (!$trackingresults)
      {
        $stepNumber = 0;
        if (!$bodyweight)
        {
          $stepNumber = $startweight;
          echo "startweight found";
        }
        $neededreps = $repsarray[0];
      }

      # Something was found
      else {

        $completedreps = $trackingresults['reps'];
        $stepNumber = $trackingresults['stepNumber'];
        $stepstatus = $trackingresults['completed'];
        $key = array_search($completedreps, $repsarray);


        # Scenario Failure - if first or second, repeat the number of completed reps
        if ($trackingresults['failed'])
        {
          $neededreps = $completedreps;

          # 3 failures in a row. In that case, make the stepNumber to the last successful step
          # Needed: failure logic for barbell weight
          if (sizeof($latestresults) > 3)
          {
              if ($trackingresults['failed'] && $latestresults[1]['failed'] && $latestresults[2]['failed'])
              {

                $lastsuccessquery = "SELECT tracking.categoryName as categoryName, stepNumber, reps, completed, failed, date
                FROM tracking
                WHERE tracking.categoryName like '%".$exercise."%'
                AND tracking.user like '%".$user."%'
                AND tracking.stepNumber < ".$stepNumber."
                  ORDER BY trackingID DESC
                  LIMIT 1";

                  $sth = $dbh->prepare($lastsuccessquery);
                  $sth->execute();

                  /* Change tracking results to array */
                  $successfulresults = $sth->fetch();

                  if ($bodyweight)
                  {
                    $stepNumber = $successfulresults['stepNumber'];
                  }
                  else {
                    $stepNumber = $stepNumber-($addweight*2);
                  }
                  $neededreps = $repsarray[0];

              }

          }
        } # If last ONE was a failure


      # Scenario 2 - There is a completed step. Grab same step ID, and increment the reps by one
        elseif ($trackingresults['completed'])
        {

          # Scenario 3 - There is a completed step, but reps are done. Grab next step ID, and go to lowest number of reps
          if ($key == count($repsarray) -1)
          {
            if (!$bodyweight)
            {
              $stepNumber = $stepNumber+$addweight;
            }
            else {
              $stepNumber = $stepNumber+1;
            }

            $neededreps = $repsarray[0];

          }
          # Exercise was completed, and a rep total was found, but not the last one
          elseif (false !== $key)
          {
            $neededreps = $repsarray[$key+1];
          }

        }

    } # If tracking results

      #Hide exercises done in the past hour
      $showexercise = true;
      if ($trackingresults['date'])
      {
        if($hiderecentlydone && time() - strtotime($trackingresults['date']) < 1*60*60) {
           $showexercise = false;
        }
      }


      if ($showexercise)
      {

        $stepsresults['image'] = null;
        $stepsresults['video'] = null;
        $stepsresults['link'] = null;
        $stepsresults['name'] = null;
        $stepsresults['instructions'] = null;

        # Grabbing data for bodyweight exercises
        if ($bodyweight)
        {

        # Finally get the steps needed
        $stepsquery = "SELECT categoryName as categoryName, stepNumber, name, instructions, link, video, image
        FROM steps
        WHERE categoryName like '%".$exercise."%' AND
        stepNumber >= ".$stepNumber."
          ORDER BY stepNumber ASC
          LIMIT 1";

          $sth = $dbh->prepare($stepsquery);
          $sth->execute();

          /* Change tracking results to array */
          $stepsresults = $sth->fetch();
          $stepNumber = $stepsresults['stepNumber']; # Refresh stepNumber to grab what was returned, not what was sent

        } #if bodyweight

        # Weighted exercises - use category instructions
        else
        {
          $loadplates = "";
          if ($exercisetype == "barbell")
          {
            $loadedplates = calculatePlates($stepNumber, $barbell, $plates);
            $loadplates = " - Weight: <strong>Bar + " . implode(" + ", $loadedplates)."</strong>".PHP_EOL;
          }

          $stepsresults['name'] = $stepNumber;
          $stepsresults['instructions'] = "<strong>".$stepNumber." pounds</strong>".$loadplates."<br /><br />".PHP_EOL.$categoryinstructions;
        }

        # Create POST data. Need to change so failed and completed aren't different
        $failedvalue = array($exercise,$stepNumber,$neededreps,0,1);
        $completevalue = array($exercise,$stepNumber,$neededreps,1,0);

        # Construct background image - use step if it's there, else category, else nothing
        if (strlen($stepsresults['image']) > 2 && file_exists("images/" . $stepsresults['image']))
        {
          $backgroundstyle = "background: url('images/" . $stepsresults['image'] . "') center / cover;";
          $hiddenimage = '<img src="images/' . $stepsresults['image'] . '" width= "90%" style="visibility:hidden"/>';
        }
        elseif (strlen($categoryimage) > 2 && file_exists("images/" . $categoryimage))
        {
          $backgroundstyle = "background: url('images/" . $categoryimage . "') center / cover;";
          $hiddenimage = '<img src="images/' . $categoryimage . '" width= "90%" style="visibility:hidden"/>';
        }
        else {
          $backgroundstyle = "height: 100px;";
          $hiddenimage = "";
        }

        # Construct video link - use step if it's there, else category, else nothing
        if (strlen($stepsresults['video']) > 2)
        {
          $videolink = '<br /><br /><a href="'.$stepsresults["video"].'" target="_blank">Video instructions</a><br />';
        }
        elseif (strlen($categoryvideo) > 2)
        {
          $videolink = '<br /><br /><a href="'.$categoryvideo.'" target="_blank">Video instructions</a><br />';
        }
        else {
          $videolink = "";
        }

        # Construct category title - use link if defined
        if (strlen($categorylink) > 2)
        {
          $categorytitle = '<a href="'.$categorylink.'" target="_blank">'.ucwords($exercise).'</a>';
        }
        else {
          $categorytitle = ucwords($exercise);
        }

        # Construct step title - use link if defined
        if (strlen($stepsresults['link']) > 2)
        {
          $stepstitle = '<a href="'.$stepsresults['link'].'" target="_blank">'.ucwords($stepsresults['name']).'</a>';
        }
        else {
          $stepstitle = ucwords($stepsresults['name']);
        }

?>

<div class="demo-card-wide mdl-card mdl-shadow--2dp">
  <div class="mdl-card__title__header">
    <h4 class="mdl-card__title__header-text"><?php echo $categorytitle; ?></h4>
  </div>
  <div class="mdl-card__title" style="<?php echo $backgroundstyle; ?>">
    <h3 class="mdl-card__title-text"><?php echo $stepstitle; ?></h3>
    <?php echo $hiddenimage; ?>

  </div>
  <div class="mdl-card__supporting-text">
    <?php echo $stepsresults['instructions']; ?>
    <?php echo $videolink; ?>
    <br />
          <?php
          # Add in the number of sets

      for ($x = 1; $x <= $sets; $x++) {
        $checkboxid = $exercise . $x . $neededreps;
        ?>

          <label class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" for="<?php echo $checkboxid; ?>">
        <input type="checkbox" id="<?php echo $checkboxid; ?>" class="mdl-checkbox__input">
        <span class="mdl-checkbox__label">Set <?php echo $x; ?>: Reps <?php echo $neededreps; ?></span>
      </label>
      <?php
      }
      ?>
  </div>
  <div class="mdl-card__actions mdl-card--border">
      <button class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent" name="tracking" value="<?php echo implode(":",$failedvalue); ?>">
  Failed
</button>
      <!-- Colored raised button -->
<button class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored" name="tracking" value="<?php echo implode(':',$completevalue); ?>">
  Completed
</button>
  </div>
</div>

<?php



} # If show exercise
      # If latest completed is less than the reps in config, grab the next array imap_deletemailbox

      # If not (including no items at all), grab the next step id
      # Something like - select step id where not in tracking, top 1


    } # If show routine
} # End foreach exercise


?>

</form>

</body>
</html>
