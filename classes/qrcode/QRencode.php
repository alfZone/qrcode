<?php

namespace classes\qrcode;

/*
 * PHP QR Code encoder
 *
 * Main encoder classes.
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
 
    

    //##########################################################################    
    
    class QRencode {
    
        public $casesensitive = true;
        public $eightbit = false;
        
        public $version = 0;
        public $size = 3;
        public $margin = 4;
        
        public $structured = 0; // not supported yet
        
        public $level = QR_ECLEVEL_L;
        public $hint = QR_MODE_8;
        
        //----------------------------------------------------------------------
        public static function factory($level = QR_ECLEVEL_L, $size = 3, $margin = 4){
            $enc = new QRencode();
            $enc->size = $size;
            $enc->margin = $margin;
            
            switch ($level.'') {
                case '0':
                case '1':
                case '2':
                case '3':
                        $enc->level = $level;
                    break;
                case 'l':
                case 'L':
                        $enc->level = QR_ECLEVEL_L;
                    break;
                case 'm':
                case 'M':
                        $enc->level = QR_ECLEVEL_M;
                    break;
                case 'q':
                case 'Q':
                        $enc->level = QR_ECLEVEL_Q;
                    break;
                case 'h':
                case 'H':
                        $enc->level = QR_ECLEVEL_H;
                    break;
            }
            
            return $enc;
        }
        
        //----------------------------------------------------------------------
        public function encodeRAW($intext, $outfile = false) 
        {
            $code = new QRcode();

            if($this->eightbit) {
                $code->encodeString8bit($intext, $this->version, $this->level);
            } else {
                $code->encodeString($intext, $this->version, $this->level, $this->hint, $this->casesensitive);
            }
            
            return $code->data;
        }

        //----------------------------------------------------------------------
        public function encode($intext, $outfile = false){
            //echo "QRencode::encode";
            $code = new QRcode();
            //echo "QRencode::encode -  depois de QRcode";
            if($this->eightbit) {
              //echo "QRencode::encode -  antes de encodeString8bit ";
                $code->encodeString8bit($intext, $this->version, $this->level);
            } else {
              //echo "QRencode::encode -  antes de encodeString ";
                $code->encodeString($intext, $this->version, $this->level, $this->hint, $this->casesensitive);
            }
            //echo "QRencode::encode -  antes de QRtools";
            QRtools::markTime('after_encode');
            //echo "QRencode::encode -  depois de QRtools";
            if ($outfile!== false) {
                file_put_contents($outfile, join("\n", QRtools::binarize($code->data)));
            } else {
                return QRtools::binarize($code->data);
            }
        }
        
        //----------------------------------------------------------------------
        public function encodePNG($intext, $outfile = false,$saveandprint=false){
            try {
                //echo "QRencode::encodePNG";
                ob_start();
                $tab = $this->encode($intext);
                //echo "QRencode::encodePNG depois de encode";
                $err = ob_get_contents();
                ob_end_clean();
                
                if ($err != '')
                    QRtools::log($outfile, $err);
                
                $maxSize = (int)(QR_PNG_MAXIMUM_SIZE / (count($tab)+2*$this->margin));
                //echo "QRencode::encodePNG antes de qrimage";
                QRimage::png($tab, $outfile, min(max(1, $this->size), $maxSize), $this->margin,$saveandprint);
            
            } catch (Exception $e) {
            
                QRtools::log($outfile, $e->getMessage());
            
            }
        }
    }
