<?php
@session_start;

use classes\desporto\Desporto;
use classes\authentication\Authentication;


use classes\qrcode\QRtools;
use classes\qrcode\QRspec;
use classes\qrcode\QRimage;
use classes\qrcode\QRinputItem;
use classes\qrcode\QRinput;
use classes\qrcode\QRbitstream;
use classes\qrcode\QRsplit;
use classes\qrcode\QRrsItem;
use classes\qrcode\QRrs;
use classes\qrcode\QRmask;
use classes\qrcode\QRrsblock;
use classes\qrcode\QRrawcode;
use classes\qrcode\QRcode;
use classes\qrcode\FrameFiller;
use classes\qrcode\QRencode;

?>

<!DOCTYPE html>
<html lang="pt">
<head>
  <title>Corta-mato</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
  <div class="container-fluid mt-3">
    <div class="row">
      

<?php
$aut= new Authentication();

//if ($aut->isLoged()){
  if (1==1){
    //está autenticado
    
    $PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR;
    //html PNG location prefix
    $PNG_WEB_DIR = 'temp/';

    //include "qrlib.php";    
 
    //ofcourse we need rights to create temp dir
    if (!file_exists($PNG_TEMP_DIR))
      mkdir($PNG_TEMP_DIR);
    //ver informação sobre o atleta
    $des= new Desporto("inscritos");
    
    //print_r($des->results);
    
    //verifica se o ficheiro do dorsal já existe
    //set it to writable location, a place for temp generated PNG files
    if ($des->results[0]['numElements']>0){

      define('QR_CACHEABLE', true);                                                               // use cache - more disk reads but less CPU power, masks and format templates are stored there
          define('QR_CACHE_DIR', dirname(__FILE__).DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR);  // used when QR_CACHEABLE === true
          //define('QR_LOG_DIR', dirname(__FILE__).DIRECTORY_SEPARATOR) . "/temp/";                                // default error logs dir   
          
          define('QR_LOG_DIR', dirname(__FILE__).DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR);                                // default error logs dir   

          define('QR_FIND_BEST_MASK', true);                                                          // if true, estimates best mask (spec. default, but extremally slow; set to false to significant performance boost but (propably) worst quality code
          define('QR_FIND_FROM_RANDOM', false);                                                       // if false, checks all masks available, otherwise value tells count of masks need to be checked, mask id are got randomly
          define('QR_DEFAULT_MASK', 2);                                                               // when QR_FIND_BEST_MASK === false
                                  
          define('QR_PNG_MAXIMUM_SIZE',  1024);                                                       // maximum allowed png image width (in pixels), tune to make sure GD and PHP can handle such big images
                                    
            // Encoding modes
          
          define('QR_MODE_NUL', -1);
          define('QR_MODE_NUM', 0);
          define('QR_MODE_AN', 1);
          define('QR_MODE_8', 2);
          define('QR_MODE_KANJI', 3);
          define('QR_MODE_STRUCTURE', 4);
          
          // Levels of error correction.
          
          define('QR_ECLEVEL_L', 0);
          define('QR_ECLEVEL_M', 1);
          define('QR_ECLEVEL_Q', 2);
          define('QR_ECLEVEL_H', 3);
          
          // Supported output formats
          
          define('QR_FORMAT_TEXT', 0);
          define('QR_FORMAT_PNG',  1);
              
          define('QRSPEC_VERSION_MAX', 40);
          define('QRSPEC_WIDTH_MAX',   177);
          
          define('QRCAP_WIDTH',        0);
          define('QRCAP_WORDS',        1);
          define('QRCAP_REMINDER',     2);
          define('QRCAP_EC',           3);

      foreach ($des->results as $dorsal){
        $processo=$dorsal['processo'];
        $src="/2do/templates/AdminLTE31/temp/$processo.png";
        $filename = $PNG_TEMP_DIR.$processo.'.png';

        $nomes=explode(" ", $dorsal['nome']);
        if (count($nomes)>2){
          //$nome=$nomes[0] . " " . $nomes[1] . " " . $nomes[count($nomes)-1]; 
          $nome=$nomes[0] . " " . $nomes[count($nomes)-1]; 
        }else{
          $nome=$dorsal['nome'];
        }

        if (!file_exists($filename)) {
          //echo "CRIAR O  ficheiro";       
        
          //echo "tttttt";
          QRcode::png("https://galeria.esmonserrate.org/2do/public/desporto/chegada/add/$processo", $filename, "L", 10, 2); 
          //echo "fim";  

        
          //display generated file
        }

        ?>
        <div class="col-md-2">
        <div class="card text-center" style="width:200px; heigth: 100px;">
          <img class="card-img-top" src="<?=$src?>" alt="Card image" style="width:100%">
          <h1 class="text-center" class="card-text"><?=$processo?></h1>
          <div class="card-body">
            <h5 class="text-center card-title"><?=$nome;?></h5>
            <p class="card-text"><?=$dorsal['Escalao'];?> - <?=$dorsal['turma'];?></p>
          </div>
        </div>
      </div>
      <?php


      }
    }

    
    
    
    
}



?>


</div>
  
</div>

</body>
</html>