
<?php
## Takes a target weight, barbell weight, and plates available at gym
## Returns an array of which weights to use to correctly load the bar
function calculatePlates($targetweight, $barbellweight, $plates)
{

$loadedweights = [];
$loadedweight = $barbellweight;

foreach ($plates as $plate)
{
  while ($loadedweight + ($plate*2) <= $targetweight )
  {

    array_push($loadedweights, $plate);
    $loadedweight += $plate*2;
  }

}

return $loadedweights;
}

?>
