<?php


get_header();



if (have_posts()) : the_post();

    include(__DIR__ . '/lib/Map.php');

    $json = json_get_meta( 'json_url' );

   

    function json_valid($json) {
        $file = file_get_contents($json);
        return !is_null(json_decode($file));

    }

    if (json_valid($json)) {
  
      
        $map = new Map($json);
      
     
       
        ?>


   <div id="map" class="row">
    <div id="map-container" class="columns sequential medium-6" data-seq="4">
    <?php $map->renderSVG(); ?>

    </div>
    <?php $map->renderInfo(); ?>
     
 
  </div>
  
 


<script>
    var map_data =  JSON.parse('<?php $map->json(); ?>') ;

</script>


    <?php }


endif;

get_footer();