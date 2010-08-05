<?php
/* Copyright (C) 2009 Nicolas Chourrout <nchourrout at gmail dot com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 * or see http://www.gnu.org/
 *
 */

class MirrorEffect{
		//Settings
		private $output_directory = "output_images/";
		private $input_directory = "input_images/";
		private $gradient_height;
		private $transparency;
		
		//Attributes
		private $img;
		private $imgWidth;
		private $imgHeight;
		private $imgName;
		private $ext;

		//Constructor
		function __construct($imgName='demo.png',$gradient_h=100,$trans=0){
			$this->imgName = $imgName;
			$this->gradient_height = $gradient_h;
			$this->transparency = $trans;
			$this->load();
		}
		
		//Public Methods
		
		public function reflect(){	
			$transparency_step = (100-$this->transparency)/($this->gradient_height);
			$background = imagecreatetruecolor($this->imgWidth, $this->gradient_height + $this->imgHeight);
			$gdGradientColor=ImageColorAllocate($background,255,255,255);  
			$newImage = imagecreatetruecolor($this->imgWidth, $this->imgHeight);
			for ($x = 0; $x < $this->imgWidth; $x++) 
				for ($y = 0; $y < $this->imgHeight; $y++)
					imagecopy($newImage, $this->img, $x, $this->imgHeight - $y - 1, $x, $y, 1, 1);
			
			imagecopymerge ($background, $newImage, 0,  $this->imgHeight, 0, 0, $this->imgWidth, $this->imgHeight, 100);  
			imagecopymerge ($background,$this->img, 0, 0, 0, 0, $this->imgWidth, $this->imgHeight, 100);
			$gradient_line = imagecreatetruecolor($this->imgWidth, 1); //voir si on garde ça et la suite
			// Next we draw a GD line into our gradient_line
			imageline ($gradient_line, 0, 0, $this->imgWidth, 0, $gdGradientColor);

			for ($i=$this->imgHeight;$i<$this->gradient_height+$this->imgHeight;$i++){
				imagecopymerge ($background, $gradient_line, 0,$i, 0, 0, $this->imgWidth, 1, $this->transparency);
				if ($this->transparency != 100) 
					$this->transparency +=$transparency_step; 
			}  
			$this->img = $background;
			
		}

		public function display($outputName=null){
			if($outputName!=null)
				$outputName = $this->output_directory.$outputName;
				
			switch($this->ext){			
				case 'png':
					$this->displayPNG($outputName);
					break;
				case 'gif':
					$this->displayGIF($outputName);
					break;
				case 'jpeg':
				case 'jpg' : 
					$this->displayJPEG($outputName);
					break;
			}			
		}
		
		public function displayJPEG($outputName=null){
			if($outputName==null){
				Header ( 'Content-type:image/jpeg' );
				imagejpeg($this->img);
			}else
				imagejpeg($this->img,$outputName);
		}
		
		public function displayPNG($outputName=null){
			if($outputName==null){
				Header ( 'Content-type:image/png' );
				imagepng($this->img);	
			}else
				imagepng($this->img,$outputName);	
		}
		
		public function displayGIF($outputName=null){
			if($outputName==null){
				Header ( 'Content-type:image/gif' );
				imagegif($this->img);
			}else
				imagegif($img,$outputName);
		}
		
		public function save($outputName=null){
			if($outputName==null)
				$outputName = $this->imgName;
			$this->setExt($outputName);
			$this->display($outputName);
		}
		
		public function setInputDirectory($dir){
			$this->input_directory = $dir;
		}
		
		public function setOutputDirectory($dir){
			$this->output_directory = $dir;
		}
		
		
		//Private Methods
		
		private function setExt($imgName){
			$this->ext = strtolower(substr(strrchr($imgName,'.'),1));
		}
		
		private function load(){ //placer le code dans __construct ou passer la méthode en privée
			$imgSize = getimagesize($this->input_directory.$this->imgName);  
			$this->imgWidth = $imgSize[0];
			$this->imgHeight = $imgSize[1]; 
			if($this->gradient_height>$this->imgHeight)
				$this->gradient_height = $this->imgHeight;
			$this->setExt($this->imgName);
			$path = $this->input_directory.$this->imgName;
			switch($this->ext){			
				case 'png':
					$this->img = imagecreatefrompng($path);
					break;
				case 'gif':
					$this->img = imagecreatefrompng($path);
					break;
				case 'jpeg':
				case 'jpg' : 
					$this->img = imagecreatefromjpeg($path);
					break;
				default : 
					die("Incorrect image file extension");
			}
		}
}



?>