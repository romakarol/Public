<html><body>
<?php
   $user = "muntean";
   require '/var/composer/vendor/autoload.php';
   $manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");
   $collection = new MongoDB\Collection($manager, $user, 'testData');
   $filter = [];
   $options = [];
   $query = new MongoDB\Driver\Query($filter, $options);
   $cursor = $manager->executeQuery("$user.testData", $query);
   print("The contents of the collection $user.testData are:");
   print_r($cursor->toArray());
?>
