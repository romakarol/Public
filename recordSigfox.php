<html>
  <head>
   <title>Unpack</title>
  </head>
  <body>
<?php
    $user = "muntean";
    require '/var/composer/vendor/autoload.php';
    $manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");
    echo "Connection to database successfully. ";
    $collection = new MongoDB\Collection($manager, $user, 'testData');
    echo "Connection to collection successful. ";

    //get data from the address bar/sigfox message
    //http://sbsrv1.cs.nuim.ie/fyp/muntean/list.php
    $_data = $_GET["data"];
    $_time = $_GET["time"];
    $coord = unpack('flat/flon', pack('H*', $_data));
    $lt = $coord['lat'];
    $lg = $coord['lon'];
    echo $_time;

    $document = array(
      'Latitude' => $lt,
      'Longitude' => $lg,
      'Time' => $_time
    );

    try {
      $collection->insertOne($document);
   } catch (\Exception $e) {
      print("Insert failed.");
      print_r($document);
      print_r($e);
      exit();
   }
    //$myfile = file_put_contents('logs.txt', $json_str.PHP_EOL , FILE_APPEND | LOCK_EX);
  //  try {
    //$manager->$collection->insert($document);
    //  or  $manager->insert($document);
  //  } catch (\Exception $e) {
    ///   print("Insert failed.");
    //   print_r($e);
    //   exit();
  //  }

    $filter = [];
    $options = [];
    $query = new MongoDB\Driver\Query($filter, $options);
    $cursor = $manager->executeQuery("$user.testData", $query);
    print("The contents of the collection $user.testData are:");
    print_r($cursor->toArray());
?>
  </body>
</html>
