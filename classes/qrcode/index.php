<?php    
/*
 * PHP QR Code encoder
 *
 * Exemplatory usage
 *
 * PHP QR Code is distributed under LGPL 3
 * Copyright (C) 2010 Dominik Dzienia <deltalab at poczta dot fm>
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 */

  
  require_once __DIR__ . '/../../config.php';
  require_once __DIR__ . '/../../autoload.php';

//use classes\qrcode\qrstr;
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

/*
 * PHP QR Code encoder
 *
 * Root library file, prepares environment and includes dependencies
 *
 * Based on libqrencode C library distributed under LGPL 2.1
 * Copyright (C) 2006, 2007, 2008, 2009 Kentaro Fukuchi <fukuchi@megaui.net>
 *
 * PHP QR Code is distributed under LGPL 3
 * Copyright (C) 2010 Dominik Dzienia <deltalab at poczta dot fm>
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 */
	
	$QR_BASEDIR = dirname(__FILE__).DIRECTORY_SEPARATOR;
	
/*
 * PHP QR Code encoder
 *
 * Config file, feel free to modify
 */
     
    define('QR_CACHEABLE', true);                                                               // use cache - more disk reads but less CPU power, masks and format templates are stored there
    define('QR_CACHE_DIR', dirname(__FILE__).DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR);  // used when QR_CACHEABLE === true
    define('QR_LOG_DIR', dirname(__FILE__).DIRECTORY_SEPARATOR);                                // default error logs dir   
    
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

    define('QR_IMAGE', true);

    define('STRUCTURE_HEADER_BITS',  20);
    define('MAX_STRUCTURED_SYMBOLS', 16);

    define('N1', 3);
    define('N2', 3);
    define('N3', 40);
    define('N4', 10);

    define('QRSPEC_VERSION_MAX', 40);
    define('QRSPEC_WIDTH_MAX',   177);

    define('QRCAP_WIDTH',        0);
    define('QRCAP_WORDS',        1);
    define('QRCAP_REMINDER',     2);
    define('QRCAP_EC',           3);

    define('QR_CACHEABLE', false);       // use cache - more disk reads but less CPU power, masks and format templates are stored there
    define('QR_CACHE_DIR', false);       // used when QR_CACHEABLE === true
    define('QR_LOG_DIR', false);         // default error logs dir   
    
    define('QR_FIND_BEST_MASK', true);                                                          // if true, estimates best mask (spec. default, but extremally slow; set to false to significant performance boost but (propably) worst quality code
    define('QR_FIND_FROM_RANDOM', 2);                                                       // if false, checks all masks available, otherwise value tells count of masks need to be checked, mask id are got randomly
    define('QR_DEFAULT_MASK', 2);                                                               // when QR_FIND_BEST_MASK === false
                                                  
    define('QR_PNG_MAXIMUM_SIZE',  1024);       


    echo "<h1>PHP QR Code</h1><hr/>";
    
    //set it to writable location, a place for temp generated PNG files
    $PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR;
    
    //html PNG location prefix
    $PNG_WEB_DIR = 'temp/';

    //include "qrlib.php";    
    
    //ofcourse we need rights to create temp dir
    if (!file_exists($PNG_TEMP_DIR))
        mkdir($PNG_TEMP_DIR);
    
    
    $filename = $PNG_TEMP_DIR.'test.png';
    
    //processing form input
    //remember to sanitize user input in real-life solution !!!
    $errorCorrectionLevel = 'L';
    if (isset($_REQUEST['level']) && in_array($_REQUEST['level'], array('L','M','Q','H')))
        $errorCorrectionLevel = $_REQUEST['level'];    

    $matrixPointSize = 10;
    if (isset($_REQUEST['size']))
        $matrixPointSize = min(max((int)$_REQUEST['size'], 1), 10);


    if (isset($_REQUEST['data'])) { 
    
        //it's very important!
        if (trim($_REQUEST['data']) == '')
            die('data cannot be empty! <a href="?">back</a>');
            
        // user data
        $filename = $PNG_TEMP_DIR.'test'.md5($_REQUEST['data'].'|'.$errorCorrectionLevel.'|'.$matrixPointSize).'.png';
        //echo "sssss";
        QRcode::png($_REQUEST['data'], $filename, $errorCorrectionLevel, $matrixPointSize, 2);    
        
    } else {    
    //echo "<br>$errorCorrectionLevel<br>";
    //echo "<br>$matrixPointSize<br>";
        //default data
        echo 'You can provide data in GET parameter: <a href="?data=like_that">like that</a><hr/>'; 
      //echo "tttttt";
        QRcode::png('PHP QR Code :)', $filename, $errorCorrectionLevel, $matrixPointSize, 2);    
      //echo "fim";  
    }    
        
    //display generated file
    echo '<img src="'.$PNG_WEB_DIR.basename($filename).'" /><hr/>';  
    
    //config form
    echo '<form action="index.php" method="post">
        Data:&nbsp;<input name="data" value="'.(isset($_REQUEST['data'])?htmlspecialchars($_REQUEST['data']):'PHP QR Code :)').'" />&nbsp;
        ECC:&nbsp;<select name="level">
            <option value="L"'.(($errorCorrectionLevel=='L')?' selected':'').'>L - smallest</option>
            <option value="M"'.(($errorCorrectionLevel=='M')?' selected':'').'>M</option>
            <option value="Q"'.(($errorCorrectionLevel=='Q')?' selected':'').'>Q</option>
            <option value="H"'.(($errorCorrectionLevel=='H')?' selected':'').'>H - best</option>
        </select>&nbsp;
        Size:&nbsp;<select name="size">';
        
    for($i=1;$i<=10;$i++)
        echo '<option value="'.$i.'"'.(($matrixPointSize==$i)?' selected':'').'>'.$i.'</option>';
        
    echo '</select>&nbsp;
        <input type="submit" value="GENERATE"></form><hr/>';
        
    // benchmark
    QRtools::timeBenchmark();    

    