<?php 
class Map {
  
  var $type;
  var $name;
  var $data;

  private $twig = NULL;
  public $keys = ['yes','no','abstain'];

  function __construct( $url ) {
   $json = json_decode(file_get_contents($url), true);
   $this->type = $json['type'];
   $this->name = get_the_title();
   $this->content = get_the_content();
   $this->data = $json['data'];
   $this->text = $json['text'];
   $this->legend = $json['legend'];
   $this->labels = $json['labels'];
 
  }

  
  function loadTwig()
    { 
    if (!class_exists('Twig_Autoloader')) {
       require_once __DIR__ . '/twig/lib/Twig/Autoloader.php';
        Twig_Autoloader::register();
        $loader = new Twig_Loader_Filesystem( __DIR__ . '/templates/');
        $this->twig = new Twig_Environment($loader, array());
    }
       
    }

   function render($template, $params = array()) {
        if ($this->twig == NULL) {
            $this->loadTwig();
        }
        echo $this->twig->render($template . '.twig', array_merge(['app' => $this], $params));
    }

  function json() {
      $data = $this->data;

      $data[] = [
        'id' => 'all',
        'data' => $this->summary()
      ];
      echo json_encode($data, JSON_HEX_QUOT);
  }
  
  function summary() {
    
    $s = array_map(function($item){
      return $item['data'];
    }, $this->data);
$t = [];
    foreach ($this->keys as $k) {
        $t[$k] = array_sum(array_column($s, $k));

    }

   return $t;
    
  }
  
  
 function patterns() {

    $items = [];

     foreach ($this->data as  $item) {
         $items[$item['id']] = $this->calculate($item);
     }



   $this->render('patterns', [ 
     'unit' => '10',
     'weight' => '10',
     'items' => $items

   ]);
   
 }

 function sum($data) {
         $sum = 0;
         foreach ($this->keys as $k) {
             if (array_key_exists($k, $data)) {
                 $sum = $sum + $data[$k];
             }
         }
        return $sum;
 }





 function calculate($item){

   $data= $item['data'];
   $sum = $this->sum($data);

    $output =[];

    $no_data_really = ( $sum == 0 );


     foreach ($this->keys as $k) {

        if ($data[$k]) {
               $output[] = $k .'-'. round($data[$k]/$this->sum($data)*10);
        }


     }

     if ($no_data_really) {
         $output = ['nodata'];
     }

     if (count($output) == 1) {
         $output = [explode('-', $output[0])[0]];
     }



     return $output;
  }

    function renderSVG(){
   
      $ids = [];



      $this->patterns();

        foreach ($this->data as  $item) {  // pattern-{{ field | join('-') }}
            $ids[$item['id']] = implode('-',$this->calculate($item));
        }

      $this->render('map'.$this->type, ['ids'=> $ids ]);
    
  }
  function renderInfo(){
        $s = $this->summary();
       $summary = $this->text;
    
    foreach ($s as $k=>$v) {
      $summary = str_replace('['.$k.']', $v, $summary);
    }
    

        $this->render('info', [
          
          'labels'=>$this->labels, 
          'title'=> $this->name, 
          'summary' => $summary, 
          'content'=> $this->content]);

  }
    function renderLabels() {

    }
}