<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-type" content="text/html; charset=utf-8" />

  <title>AD</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/foundation/6.2.4/foundation.min.css" />
  <link href="lib/assets/css/style.css" rel="stylesheet">
 
</head>

<body>

<div id="container">
  
<?php 
include('lib/Map.php'); 
$map = new Map('data/data.json');




?>


  <div id="map" class="row">
    <div id="map-container" class="columns sequential medium-6" data-seq="4">
    <?php $map->renderSVG(); ?>

    </div>
    <?php $map->renderInfo(); ?>

  </div>
  
  </div>

<script>
    var map_data =  JSON.parse('<?php $map->json(); ?>') ;

</script>

<script src="lib/assets/vendor/jquery.min.js"></script>
<script src="lib/assets/vendor/d3.min.js"></script>
<script src="lib/assets/js/app.min.js"></script>

</body>

</html>
