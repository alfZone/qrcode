<?php 

/**
 * The idea for this exemple is to create a simple qrcode. 
 * @author António Lira Fernandes
 * @version 1.0
 * @updated 18-06-2020 21:50:00
 */

    
    $chave=$_REQUEST['chave'];
    
    //set it to writable location, a place for temp generated PNG files
    $PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR;
    
    //html PNG location prefix
    $PNG_WEB_DIR = 'temp/';

    include "qrlib.php";    
    
    //ofcourse we need rights to create temp dir
    if (!file_exists($PNG_TEMP_DIR))
        mkdir($PNG_TEMP_DIR);
    
    
    $filename = $PNG_TEMP_DIR.'cantino.png';
    
    //processing form input
    //remember to sanitize user input in real-life solution !!!
    $errorCorrectionLevel = 'H';  

    $matrixPointSize = 4;
    //$chave="o quintino é lindo";
    if ($chave<>"") { 
           
            
        // user data
        $filename = $PNG_TEMP_DIR.'test'.md5($chave.'|'.$errorCorrectionLevel.'|'.$matrixPointSize).'.png';
        QRcode::png($chave, $filename, $errorCorrectionLevel, $matrixPointSize, 2);    
        
    } else {    
    
        //default data
        echo 'Não tem chave de acesso';               
        
    }    
        
    //display generated file
    echo '<img src="'.$PNG_WEB_DIR.basename($filename).'" />';  
    
    ?>