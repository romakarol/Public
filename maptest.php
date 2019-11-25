<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="refresh" content="20" >
    <style>
       /* Set the size of the div element that contains the map */
      #map {
        height: 400px;
        width: 100%;
       }
    </style>
  </head>
  <body>
    <h3>Last Locations</h3>

    <?php
    //name of DB: muntean Name of collection:TestData
    $user = "muntean";
    require '/var/composer/vendor/autoload.php';
    $manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");
    $collection = new MongoDB\Collection($manager, $user, 'testData');

    $data  = "<table style='border:1px solid red;";
    $data .= "border-collapse:collapse' border='1px'>";
    $data .= "<thead>";
    $data .= "<tr>";
    $data .= "<th>Time</th>";
    $data .= "<th>Longitude</th>";
    $data .= "<th>Latitude</th>";
    $data .= "</tr>";
    $data .= "</thead>";
    $data .= "<tbody>";

  try{
  //get the two latest entries in the database by time
  $options = ['sort' => ['Time' => -1], 'limit' => 2];
  $filter=[];
  $cursor = $collection->find($filter, $options);

  //initialize variables containing the latest entry data
  $largest =  0;
  foreach($cursor as $document){
   if ($largest < $document["Time"]) {
       $largest = $document["Time"];
       $longitude = $document["Longitude"];
       $latitude = $document["Latitude"];
        }

  $data .= "<tr>";
  $data .= "<td>" . $document["Time"] . "</td>";
  $data .= "<td>" . $document["Longitude"]."</td>";
  $data .= "<td>" . $document["Latitude"]."</td>";
  $data .= "</tr>";
  }
  $data .= "</tbody>";
  $data .= "</table>";
  echo $data;
  echo  nl2br ("\n latest entry");
  echo  nl2br ("\n latitude: $latitude");
  echo  nl2br ("\n longitude: $longitude");
  echo  nl2br ("\n timestamp: $largest");
  echo  nl2br ("\n");

  $options = ['sort' => ['Time' => -1], 'limit' => 2, 'skip' => 1];
  $cursor = $collection->find($filter, $options);
  //initialize variables containing the previous entry data
  $largest2 =  0;
  foreach($cursor as $document){
   if ($largest2 < $document["Time"]) {
       $largest2 = $document["Time"];
       $longitude2 = $document["Longitude"];
       $latitude2 = $document["Latitude"];
        }
}
  echo  nl2br ("\n");
  echo  nl2br ("\n previous entry");
  echo  nl2br ("\n latitude: $latitude2");
  echo  nl2br ("\n longitude: $longitude2");
  echo  nl2br ("\n timestamp: $largest2");
  echo  nl2br ("\n");

  $speedlat=($latitude-$latitude2)/($largest-$largest2);
  $speedlong=($longitude-$longitude2)/($largest-$largest2);
  $age=(time()-$largest);

  $elat=$latitude+($age*$speedlat);
  $elong=$longitude+($age*$speedlong);

  echo  nl2br ("\n estimated location based on previous reports");
  echo  nl2br ("\n latitude: $elat");
  echo  nl2br ("\n longitude: $elong");
  echo  nl2br ("\n seconds elapsed since last known: $age");



  }catch(MongoException $mongoException){
    print $mongoException;
    exit;
}
     ?>

    <!--The div element for the map -->
    <div id="map"></div>
    <script>
// Initialize and add the map
function initMap() {
  // The location of the latest point
   var point = {lat: <?php echo $latitude; ?>, lng: <?php echo $longitude; ?>}
  // Estimated new point
   var epoint = {lat: <?php echo $elat; ?>, lng: <?php echo $elong; ?>}
  // The map, centered at point
  var map = new google.maps.Map(
      document.getElementById('map'), {zoom: 12, center: point});
  // The markers, positioned at point and estimated point
  var marker = new google.maps.Marker({position: point, label: {
    text: 'Previous',
    color: "#000000",
    fontSize: "14px",
    fontWeight: "bold"
  }, map: map});
  var marker = new google.maps.Marker({position: epoint, label: {
    text: 'Estimated',
    color: "#000000",
    fontSize: "14px",
    fontWeight: "bold"
  }, map: map});
}
    </script>
    <!--Load the API from the specified URL
    * The async attribute allows the browser to render the page while the API loads
    * The key parameter will contain your own API key (which is not needed for this tutorial)
    * The callback parameter executes the initMap() function
    -->
    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD6CMFIF-m8Z_kNLUGT7HEVBew_wPLno7o&callback=initMap">
    </script>

  </body>
</html>
