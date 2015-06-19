<?php
/*
* File: SimpleImage.php
* Author: Simon Jarvis
* Copyright: 2006 Simon Jarvis
* Date: 08/11/06
* Link: http://www.white-hat-web-design.co.uk/articles/php-image-resizing.php
* 
* This program is free software; you can redistribute it and/or 
* modify it under the terms of the GNU General Public License 
* as published by the Free Software Foundation; either version 2 
* of the License, or (at your option) any later version.
* 
* This program is distributed in the hope that it will be useful, 
* but WITHOUT ANY WARRANTY; without even the implied warranty of 
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the 
* GNU General Public License for more details: 
* http://www.gnu.org/licenses/gpl.html
*
*/
 
class SimpleImage {
   
   var $image, $stamp;
   var $image_type, $stamp_type;
 
   function load($filename) {
      $image_info = getimagesize($filename);
      $this->image_type = $image_info[2];
      if( $this->image_type == IMAGETYPE_JPEG ) {
         $this->image = imagecreatefromjpeg($filename);
      } elseif( $this->image_type == IMAGETYPE_GIF ) {
         $this->image = imagecreatefromgif($filename);
      } elseif( $this->image_type == IMAGETYPE_PNG ) {
         $this->image = imagecreatefrompng($filename);
      }
   }
   function save($filename, $image_type=IMAGETYPE_JPEG, $compression=75, $permissions=null) {
      if( $image_type == IMAGETYPE_JPEG ) {
         imagejpeg($this->image,$filename,$compression);
      } elseif( $image_type == IMAGETYPE_GIF ) {
         imagegif($this->image,$filename);         
      } elseif( $image_type == IMAGETYPE_PNG ) {
         imagepng($this->image,$filename);
      }   
      if( $permissions != null) {
         chmod($filename,$permissions);
      }
   }
   function output($image_type=IMAGETYPE_JPEG) {
      if( $image_type == IMAGETYPE_JPEG ) {
         imagejpeg($this->image);
      } elseif( $image_type == IMAGETYPE_GIF ) {
         imagegif($this->image);         
      } elseif( $image_type == IMAGETYPE_PNG ) {
         imagepng($this->image);
      }   
   }
   function getWidth() {
      return imagesx($this->image);
   }
   function getHeight() {
      return imagesy($this->image);
   }
   function resizeToHeight($height) {
      $ratio = $height / $this->getHeight();
      $width = $this->getWidth() * $ratio;
      $this->resize($width,$height);
   }
   function resizeToWidth($width) {
      $ratio = $width / $this->getWidth();
      $height = $this->getheight() * $ratio;
      $this->resize($width,$height);
   }
   function scale($scale) {
      $width = $this->getWidth() * $scale/100;
      $height = $this->getheight() * $scale/100; 
      $this->resize($width,$height);
   }
   function resize($width,$height) {
      $new_image = imagecreatetruecolor($width, $height);
      imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
      $this->image = $new_image;   
   } 
   
   //add by me..
   function rotate($degrees=null) {
   
      // Rotate
	  if ( ($degrees != 0) || ($degrees != 360) ) {
        $new_image = imagerotate($this->image, $degrees, 0);  
        $this->image = $new_image; 	 
        return true;		
      }		
	  return false;
   }
   
   function do_action($action=null) {
       //echo '>'.$action;
	   if (!$action) return null;
	   
	   switch ($action) {
	      case 1 : $ret = $this->rotate(90); break;
		  case 2 : $ret = $this->rotate(270); break;
		  case 3 : $ret = $this->rotate(180); break;
		  case 0 :
		  default: //none
	   }
	   
	   return ($ret);
   }
   
   function place_in_frame($xframe=null, $yframe=null) {
      //echo '>'.$xframe.'x'.$yframe;
	  //$xframe = $xframe ? $xframe : imagesy($this->image);
	  //$yframe = $yframe ? $yframe : imagesy($this->image);
	  
	  //$new_image = imagecreatetruecolor($xframe, $yframe);
      // Create some colors
      //$white = imagecolorallocate($new_image, 255, 255, 255);
      //$grey = imagecolorallocate($new_image, 128, 128, 128);
      //$black = imagecolorallocate($new_image, 0, 0, 0);
      //imagefilledrectangle($im, 0, 0, 150, 25, $black);
      //$trans_colour = imagecolorallocatealpha($new_image, 0, 0, 0, 127);
      //imagefill($new_image, 0, 0, $white);	  
	  
	  if (($xframe) && ($yframe)) {
		
		//$this->resize($xframe, $yframe); 
        //return true;			
		$new_image = imagecreatetruecolor($xframe, $yframe);
		$white = imagecolorallocate($new_image, 255, 255, 255);
		imagefill($new_image, 0, 0, $white);	
		
		if ($xframe>=$yframe) 
		  $this->resizeToWidth($xframe);		  
		else 
		  $this->resizeToHeight($yframe);	
		  

		//echo '<br>x:'.$xframe.'-'.$this->getWidth().':'.abs($xframe-$this->getWidth());
		//echo '<br>y:'.$yframe.'-'.$this->getHeight().':'.abs($yframe - $this->getHeight());
		$dx = (imagesx($new_image) - imagesx($this->image))/2;
		$dy = (imagesy($new_image) - imagesy($this->image))/2;
		imagecopy($new_image, $this->image, $dx, $sy, 0, 0, $xframe, $yframe);//$this->getWidth(), $this->getHeight()); 
	  }
	  elseif (($xframe) && (!$yframe)) {
	  
        $new_image = imagecreatetruecolor($xframe, imagesy($this->image));	
		$white = imagecolorallocate($new_image, 255, 255, 255);
		imagefill($new_image, 0, 0, $white);
		
		$this->resizeToWidth($xframe);
		//echo '<br>y:'.$yframe.'-'.$this->getHeight();
		$dx = 0;
		$dy = (imagesy($new_image) - imagesy($this->image))/2;
		imagecopy($new_image, $this->image, $dx, $dy, 0, 0, $this->getWidth(), $this->getHeight());
	  }
	  elseif ((!$xframe) && ($yframe)) {

		$new_image = imagecreatetruecolor(imagesx($this->image), $yframe);
		$white = imagecolorallocate($new_image, 255, 255, 255);
		imagefill($new_image, 0, 0, $white);
		
		$this->resizeToHeight($yframe);
		//echo '<br>x:'.$xframe.'-'.$this->getWidth();
		$dx = (imagesx($new_image) - imagesx($this->image))/2;
		$dy = 0;
		imagecopy($new_image, $this->image, $dx, $dy, 0, 0, $this->getWidth(), $this->getHeight());
	  }
	  else
	    return null;

	  //echo '<br>'.$dx.':'.$dy;	
      $this->image = $new_image; 
      return true;	  
   }
   
   function add_watermark($stampfile, $opacity=null, $position=null, $alpha=null) {
      $opacity = $opacity ? $opacity : 50;
	  //echo '>'.$alpha;
   
      $image_info = getimagesize($stampfile);
      $this->stamp_type = $image_info[2];
      if( $this->stamp_type == IMAGETYPE_JPEG ) {
         $this->stamp = imagecreatefromjpeg($stampfile);
      } elseif( $this->stamp_type == IMAGETYPE_GIF ) {
         $this->stamp = imagecreatefromgif($stampfile);
      } elseif( $this->stamp_type == IMAGETYPE_PNG ) {
         $this->stamp = imagecreatefrompng($stampfile);
      }

      // Set the margins for the stamp and get the height/width of the stamp image
      $marge_right = 10;
      $marge_bottom = 10;
	  /*$tsx = imagesx($this->image) - imagesx($this->stamp) - $marge_right;
	  $tsy = imagesy($this->image) - imagesy($this->stamp) - $marge_bottom;*/
	  switch ($position) {
	  
	    case 1 : //up left
		         $tsx = $this->calc_pos_x('LEFT', $marge_right);
               	 $tsy = $this->calc_pos_y('TOP', $marge_bottom); 
		         break;
	    case 2 : //up right
		         $tsx = $this->calc_pos_x('RIGHT', $marge_right);
               	 $tsy = $this->calc_pos_y('TOP', $marge_bottom); 
		         break;
	    case 3 : //down left
		         $tsx = $this->calc_pos_x('LEFT', $marge_right);
               	 $tsy = $this->calc_pos_y('BOTTOM', $marge_bottom); 
		         break;
	    case 4 : //down right
		         $tsx = $this->calc_pos_x('RIGHT', $marge_right);
               	 $tsy = $this->calc_pos_y('BOTTOM', $marge_bottom); 
		         break;
	    case 5 : //center
		         $tsx = $this->calc_pos_x('CENTER', $marge_right);
               	 $tsy = $this->calc_pos_y('MIDDLE', $marge_bottom); 
		         break;
        default: //down right 
                 $tsx = $this->calc_pos_x('RIGHT', $marge_right);
               	 $tsy = $this->calc_pos_y('BOTTOM', $marge_bottom); 			
	  }

	  if ($alpha) //use alpha components
	    imagecopy($this->image, $this->stamp, $tsx, $tsy, 0, 0, imagesx($this->stamp), imagesy($this->stamp));	  
      else	//use opacity	
      // Merge the stamp onto our photo with an opacity (transparency) of 50%
        imagecopymerge($this->image, $this->stamp, $tsx, $tsy, 0, 0, imagesx($this->stamp), imagesy($this->stamp), $opacity);	 
	  	 
   }

   //add by me..copy from watermark lib dpc
   
		function calc_pos_x($position_x='LEFT', $margin_x=0)
		{
			switch($position_x)
			{
				case 'LEFT':
					$x = $margin_x;
					break;
				case 'CENTER':
					$x = imagesx($this->image) / 2 - imagesx($this->stamp) / 2;
					break;
				case 'RIGHT':
					$x = imagesx($this->image) - imagesx($this->stamp) - $margin_x;
					break;
				default:
					$x = 0;
			
			} 
			return $x;
		
		}
		
		function calc_pos_y($position_y='TOP', $margin_y=0)
		{
			switch($position_y)
			{
				case 'TOP':
					$y = $margin_y;
					break;
				case 'MIDDLE':
					$y = imagesy($this->image) / 2 - imagesy($this->stamp) / 2;
					break;
				case 'BOTTOM':
					$y = imagesy($this->image) - imagesy($this->stamp) - $margin_y;
					break;
				default:
					$y = 0;
			
			}
			return $y;
		
		}
		
		//added by me...
		function set_jpg_quality($filesize) {
		
		   if ($filesize>2000000)//2mb 
		      $qlty = 20;
           elseif ($filesize>1000000)//1mb 			  
		      $qlty = 40;
           elseif ($filesize>500000)//0,5mb 	
		      $qlty = 60;		  
		   else 
		      $qlty = 80;
		
		   $this->quality = $qlty;
		   
		   return ($qlty);
		}   
}
?>