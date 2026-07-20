<?php 

/**************************************************
 * Image Filter Class
 *
 * @package Image Filter GD
 * @version 2.0
 * @author MT Jordan <mtjo62@gmail.com>
 * @copyright 2026
 * @license MIT
 *************************************************/

class imageFilter { 
  
    /**********************************************
     * Private Class Properties
     *********************************************/
    /**
     * Current filter
     *
     * @var string
     */
    private $image_filter;
    
    /**
     * Array of available filters
     *
     * @var array
     */
    private $image_filter_array = array('brighten',
                                        'brush',
                                        'darken',
                                        'edgedetect',
                                        'emboss',
                                        'flip',
                                        'grayscale',
                                        'larger',
                                        'mirror',
                                        'negative',
                                        'pixelate',
                                        'sephia',
                                        'sharpen',
                                        'sketch',
                                        'smaller',
                                        'smooth');
    
    /**
     * Source image
     *
     * @var mixed
     */
    private $image_src = false;
    
    /**
     * cURL flag
     *
     * @var bool
     */
    private $image_curl = false;
    
    /**
     * Temp destination image
     *
     * @var mixed
     */
    private $image_dest = false;
    
    /**
     * Image information 
     *
     * @var array
     */
    private $image_info;
    
    /**
     * Source image URL
     *
     * @var string
     */
    private $image_url;
    
    /**
     * Transparent image flag
     *
     * @var bool
     */
    private $image_trans = false;
    
    /**
     * cURL data flag
     *
     * @var mixed
     */
    private $curl_data = false;
    
    /**********************************************
     * Public Methods
     *********************************************/
    
    /**
     * Constructor
     *
     * @param string $url
     * @param string $filter
     * @return void
     */
    function __construct($url, $filter) {
        $imageFilter = strtolower($filter);
        $this->image_filter = $imageFilter;
        $this->image_url = $url;
        $this->image_info = $this->getImageInfo();
        $this->image_src = $this->setImageSrc();
        $this->image_trans = $this->getTransparency();
    
        $this->$imageFilter();
    }
    
    /**********************************************
     * Private Methods
     *********************************************/

    /**
     * Process brighten filter
     *
     * @return void
     */   
    private function brighten() {
        imagefilter($this->image_src, IMG_FILTER_BRIGHTNESS, 40);

        $this->returnImage();   
    }

    /**
     * Process brush filter
     *
     * @return void
     */   
    private function brush() {
        $neg_noise = -1;
        $pos_noise = 1;
 
        for ($x = 0; $x < $this->image_info[0]; $x++) {
            for ($y = 0; $y < $this->image_info[1]; $y++) {
                $explode_X = rand($neg_noise, $pos_noise);
                $explode_Y = rand($neg_noise, $pos_noise);

                if ($x + $explode_X >= $this->image_info[0]) {
                    continue;
                }

                if ($x + $explode_X < 0) {
                    continue;
                }

                if ($y + $explode_Y >= $this->image_info[1]) {
                    continue;
                }
                
                if ($y + $explode_Y < 0) {
                    continue;
                }

                imagesetpixel($this->image_src, $x, $y, imagecolorat($this->image_src, $x + $explode_X, $y + $explode_Y));
                imagesetpixel($this->image_src, $x + $explode_X, $y + $explode_Y, imagecolorat($this->image_src, $x, $y));
            }
        }
    
        $this->returnImage(); 
    }
    
    /**
     * Process darken filter
     *
     * @access private
     */   
    private function darken() {
        imagefilter($this->image_src, IMG_FILTER_BRIGHTNESS, -40);

        $this->returnImage(); 
    }

    /**
     * Process edge detect filter
     *
     * @return void
     */   
    private function edgedetect() {
        if ($this->image_info[2] === 1) {
            $this->image_dest = imagecreatetruecolor($this->image_info[0], $this->image_info[1]);
            $rgb = $this->randomRgb($this->image_src);
            imagecolorallocate($this->image_dest, $rgb["r"],$rgb["g"],$rgb["b"]);
            imagecopy($this->image_dest, $this->image_src, 0, 0, 0, 0, $this->image_info[0], $this->image_info[1]);
            imagefilter($this->image_dest, IMG_FILTER_EDGEDETECT);   
            
            if ($this->image_trans) {
                imagecolortransparent($this->image_dest, imagecolorat($this->image_dest, 0, 0));
            }
        } else {
            imagefilter($this->image_src, IMG_FILTER_EDGEDETECT);
        }
     
        $this->returnImage(); 
    }
    
    /**
     * Process emboss filter
     *
     * @return void
     */   
    private function emboss() {
        if ($this->image_info[2] === 1) {
            $this->image_dest = imagecreatetruecolor($this->image_info[0], $this->image_info[1]);
            
            if ($this->image_trans) {
                imagecolortransparent($this->image_dest, imagecolorat($this->image_dest, 0, 0));
            }
            
            imagecopy($this->image_dest, $this->image_src, 0, 0, 0, 0, $this->image_info[0], $this->image_info[1]);
            imagefilter($this->image_dest, IMG_FILTER_GRAYSCALE); 
            imagefilter($this->image_dest, IMG_FILTER_EMBOSS);
        } else {
            imagefilter($this->image_src, IMG_FILTER_GRAYSCALE);    
            imagefilter($this->image_src, IMG_FILTER_EMBOSS);
        } 
               
        $this->returnImage(); 
    }
    
    /**
     * Process flip filter
     *
     * @return void
     */   
    private function flip() {
        $this->image_dest = imagecreatetruecolor($this->image_info[0], $this->image_info[1]);
    
        if ($this->image_trans && $this->image_info[2] === 3) {
            imagealphablending($this->image_dest, false);
            imagesavealpha($this->image_dest, true);
        }
   
        for ($i = 0; $i < $this->image_info[0]; $i++) {
            for ($j = 0; $j < $this->image_info[1]; $j++) {
                imagecopy($this->image_dest, $this->image_src, $i, $this->image_info[1] - $j - 1, $i, $j, 1, 1);
            }
        }

        if ($this->image_trans) {
            imagecolortransparent($this->image_dest, imagecolorallocate($this->image_dest, 0,0,0)); 
        }    
               
        $this->returnImage();       
    }
    
    /**
     * Process grayscale filter
     *
     * @return void
     */   
    private function grayscale() {
        if ($this->image_info[2] === 1) {
            $this->image_dest = imagecreatetruecolor($this->image_info[0], $this->image_info[1]);
            imagecopy($this->image_dest, $this->image_src, 0, 0, 0, 0, $this->image_info[0], $this->image_info[1]);
            imagefilter($this->image_dest, IMG_FILTER_GRAYSCALE);    
        } else {
            imagefilter($this->image_src, IMG_FILTER_GRAYSCALE);
        }
        
        if ($this->image_trans && $this->image_info[2] === 3) {
            imagefill($this->image_src, 0, 0, imagecolorallocatealpha($this->image_src, 147, 147, 147, 127));
        }
        
        if ($this->image_trans && $this->image_info[2] === 1) {
            imagecolortransparent($this->image_dest, imagecolorat($this->image_dest, 0, 0));
        }
        
        $this->returnImage(); 
    }
    
    /**
     * Process larger filter
     *
     * @return void
     */   
    private function larger() {
        $image_width = $this->image_info[0] * 2;
        $image_height = $this->image_info[1] * 2;
        
        if ($this->image_info[2] === 1 && !$this->image_trans) {
            $this->image_dest = imagecreate($image_width, $image_height);
        } elseif ($this->image_info[2] === 1 && $this->image_trans) {
            $this->image_dest = imagecreate($image_width, $image_height);
            $rgb = $this->randomRgb($this->image_src);
            imagecolorallocate($this->image_dest, $rgb["r"],$rgb["g"],$rgb["b"]);
        } else {
            $this->image_dest = imagecreatetruecolor($image_width, $image_height);
            imagealphablending($this->image_dest, false);
            imagesavealpha($this->image_dest, true);
        }

        imagecopyresampled($this->image_dest, $this->image_src, 0, 0, 0, 0, $image_width, $image_height, $this->image_info[0], $this->image_info[1]);
    
        if ($this->image_trans) {    
            imagecolortransparent( $this->image_dest, imagecolorat($this->image_dest, 0, 0));
        }
                
        $this->returnImage(); 
    }

    /**
     * Process mirror filter
     *
     * @return void
     */   
    private function mirror() {
        $this->image_dest = imagecreatetruecolor($this->image_info[0], $this->image_info[1] + ($this->image_info[1] / 2));
        
        imagealphablending($this->image_dest, false);
        imagesavealpha($this->image_dest, true);
        imagecopyresampled($this->image_dest, $this->image_src, 0, 0, 0, 0, $this->image_info[0], $this->image_info[1], $this->image_info[0], $this->image_info[1]);
    
        for ($i = 1; $i <= $this->image_info[1] / 2; $i++) {
            for ($j = 0; $j < $this->image_info[0]; $j++) {
                $rgb = imagecolorat($this->image_src, $j, $this->image_info[1] - $i);
                $alpha = ($rgb & 0x7F000000) >> 24;
                $alpha =  max($alpha, 47 + ($i * (80 / ($this->image_info[1] / 2))));
                $rgb = imagecolorsforindex($this->image_src, $rgb);
                 
                //Check for transparent pixel
                if ($rgb['alpha'] == 127) {
                    $rgb = imagecolorallocatealpha($this->image_dest, $rgb['red'], $rgb['green'], $rgb['blue'], 127);
                    imagesetpixel($this->image_dest, $j, $this->image_info[1] + $i - 1, $rgb);
                } else {
                    $rgb = imagecolorallocatealpha($this->image_dest, $rgb['red'], $rgb['green'], $rgb['blue'], $alpha);
                    imagesetpixel($this->image_dest, $j, $this->image_info[1] + $i - 1, $rgb);
                }
            }
        }
  
        $this->returnImage(); 
    }
    
    /**
     * Process negative filter
     *
     * @return void
     */   
    private function negative() {
         if ($this->image_info[2] === 1) {
            $this->image_dest = imagecreatetruecolor($this->image_info[0], $this->image_info[1]);
            imagecopy($this->image_dest, $this->image_src, 0, 0, 0, 0, $this->image_info[0], $this->image_info[1]);
            
            if ($this->image_trans) {    
              imagecolortransparent($this->image_dest, imagecolorat($this->image_dest, 0, 0));
            }
           
            imagefilter($this->image_dest, IMG_FILTER_NEGATE);    
        } else {
            imagefilter($this->image_src, IMG_FILTER_NEGATE);
        }
        
        $this->returnImage(); 
    }
    
    /**
     * Process pixelate filter
     *
     * @return void
     */   
    private function pixelate() {
        if ($this->image_info[2] === 1) {
            $this->image_dest = imagecreate($this->image_info[0], $this->image_info[1]);
            $rgb = $this->randomRgb($this->image_src);
            imagecolorallocate($this->image_dest, $rgb["r"],$rgb["g"],$rgb["b"]);
            imagecopy($this->image_dest, $this->image_src, 0, 0, 0, 0, $this->image_info[0], $this->image_info[1]);
            imagefilter($this->image_dest, IMG_FILTER_PIXELATE, 3, 2);
            
            if ($this->image_trans) {    
                imagecolortransparent($this->image_dest, imagecolorat($this->image_dest, 0, 0));
            }
            
        } else {
            imagefilter($this->image_src, IMG_FILTER_PIXELATE, 3, 2);
        }
                    
        $this->returnImage(); 
    }
    
    /**
     * Process sephia filter
     *
     * @return void
     */       
    private function sephia() {
        $this->image_dest = imagecreatetruecolor($this->image_info[0], $this->image_info[1]);
        $temp_image = imagecreatetruecolor($this->image_info[0], $this->image_info[1]);
        imagefill($this->image_dest, 0, 0, imagecolorallocate($this->image_dest, 234, 224, 213));
        imagecopy($this->image_dest, $this->image_src, 0, 0, 0, 0, $this->image_info[0], $this->image_info[1]);
        imagefilter($this->image_dest, IMG_FILTER_GRAYSCALE);
        imagefill($temp_image, 0, 0, imagecolorallocate($temp_image, 112, 66, 20));
        imagecopymerge($this->image_dest, $temp_image, 0, 0, 0, 0, $this->image_info[0], $this->image_info[1], 30);
        
        $this->returnImage(); 
    }
    
    /**
     * Process sharpen filter
     *
     * @return void
     */   
    private function sharpen() {
        $sharpen = array(array( -1.2, -1, -1.2), 
                   array(-1, 20, -1), 
                   array(-1.2, -1, -1.2)); 

        $divisor = array_sum(array_map("array_sum", $sharpen));            
            
        if ($this->image_trans) {    
            $this->image_dest = imagecreatetruecolor($this->image_info[0], $this->image_info[1]);
            imagealphablending($this->image_dest, false);
            imagesavealpha($this->image_dest, true);
            imagecopyresampled($this->image_dest, $this->image_src, 0, 0, 0, 0, $this->image_info[0], $this->image_info[1], $this->image_info[0], $this->image_info[1]);
            imagecolortransparent($this->image_dest, imagecolorat($this->image_dest, 0, 0));
            imageconvolution($this->image_dest, $sharpen, $divisor, 0);  
            imagecolortransparent($this->image_dest, imagecolorat($this->image_dest, 0, 0));
        } else {
            imageconvolution($this->image_src, $sharpen, $divisor, 0); 
        }
    
        $this->returnImage(); 
    }
    
    /**
     * Process sketch filter
     *
     * @return void
     */   
    private function sketch() {
        if ($this->image_trans) {    
            $this->image_dest = imagecreatetruecolor($this->image_info[0], $this->image_info[1]);
            imagealphablending($this->image_dest, false);
            imagesavealpha($this->image_dest, true);
            imagecopyresampled($this->image_dest, $this->image_src, 0, 0, 0, 0, $this->image_info[0], $this->image_info[1], $this->image_info[0], $this->image_info[1]);
            imagecolortransparent($this->image_dest, imagecolorat($this->image_dest, 0, 0));
            imagefilter($this->image_dest, IMG_FILTER_MEAN_REMOVAL);
            imagecolortransparent($this->image_dest, imagecolorat($this->image_dest, 0, 0));
        } else {
            imagefilter($this->image_src, IMG_FILTER_MEAN_REMOVAL);
        }
    
        $this->returnImage(); 
    }
    
    /**
     * Process smaller filter
     *
     * @return void
     */   
    private function smaller() {
        $image_width = $this->image_info[0] / 2;
        $image_height = $this->image_info[1] / 2;
        
        if ($this->image_info[2] === 1 && $this->image_trans) {
            $this->image_dest = imagecreate($image_width, $image_height);
            $rgb = $this->randomRgb($this->image_src);
            imagecolorallocate($this->image_dest, $rgb["r"],$rgb["g"],$rgb["b"]);
        } elseif ($this->image_info[2] == 2) {
            $this->image_dest = imagecreatetruecolor($image_width, $image_height);
        } else {
            $this->image_dest = imagecreatetruecolor($image_width, $image_height);
            imagealphablending($this->image_dest, false);
            imagesavealpha($this->image_dest, true);
        }
        
        imagecopyresampled($this->image_dest, $this->image_src, 0, 0, 0, 0, $image_width, $image_height, $this->image_info[0], $this->image_info[1]);
    
        if ($this->image_trans) {    
            imagecolortransparent($this->image_dest, imagecolorat($this->image_dest, 0, 0));
        }
                
        $this->returnImage(); 
    }
    
    /**
     * Process smooth filter
     *
     * @return void
     */   
    private function smooth() {
        if ($this->image_trans) {    
            $this->image_dest = imagecreatetruecolor($this->image_info[0], $this->image_info[1]);
            imagealphablending($this->image_dest, false);
            imagesavealpha($this->image_dest, true);
            imagecopyresampled($this->image_dest, $this->image_src, 0, 0, 0, 0, $this->image_info[0], $this->image_info[1], $this->image_info[0], $this->image_info[1]);
            imagecolortransparent($this->image_dest, imagecolorat($this->image_dest, 0, 0));
            imagefilter($this->image_dest, IMG_FILTER_GAUSSIAN_BLUR);
        } else {
            imagefilter($this->image_src, IMG_FILTER_GAUSSIAN_BLUR);
        }
    
        $this->returnImage(); 
    }
    
    /**
     * Process image URL and create image information
     *
     * @return mixed
     */   
    private function getImageInfo() {
        $valid_ext = array("image/gif","image/jpeg","image/png");
        $image_info = @getimagesize($this->image_url);

        if ($image_info !== false && in_array($image_info["mime"], $valid_ext)) {
            return $image_info;
        } elseif (function_exists("curl_version")) {
            $this->image_curl = true;
            $this->curl_data = $this->getImageString();

            if ($this->curl_data !== false) {
                $image_info = getimagesizefromstring($this->curl_data);
    
                if ($image_info !== false) {
                   return $image_info;
                } 
            }
        } else {
            return false;
        }
    }
    
    
    /**
     * Process image URL and return image string if using cURL
     *
     * @param bool $range
     * @return mixed
     */   
    private function getImageString() {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); 
        curl_setopt($curl, CURLOPT_URL, $this->image_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        
        return curl_exec($curl);
    }
    
    /**
     * Test GIF or PNG image for transparency
     *
     * @return bool
     */       
    private function getTransparency() {
        if ($this->image_info[2] == 1) {
            $image = $this->gifImage();
            $index = imagecolortransparent($image);
            
            if ($index != -1) {
                return true;
            }
        } elseif ($this->image_info[2] == 3) {
            $image = $this->pngImage();
            imagesavealpha($image, true);
            
            for ($i = 0; $i < $this->image_info[0]; $i++) {
                for($j = 0; $j < $this->image_info[1]; $j++) {
                    $rgb = imagecolorat($image, $i, $j);
                    $alpha = imagecolorsforindex($image, $rgb);
            
                    if ($alpha['alpha'] == 127) {
                        return true;
                    }
                }
            }
        } else {
            return false;
        }
    }
   
    /**
     * Return GIF image resource
     *
     * @return mixed
     */   
    private function gifImage() {
        if ($this->image_curl) {
            return imagecreatefromstring($this->curl_data);
        } else {
            return imagecreatefromgif($this->image_url);
        }
    }
    
    /**
     * Return JPEG image resource
     *
     * @return mixed
     */   
    private function jpegImage() {
        if ($this->image_curl) {
            return imagecreatefromstring($this->curl_data);
        } else {
            return imagecreatefromjpeg($this->image_url);
        }
    }
    
    /**
     * Return PNG image resource
     *
     * @return mixed
     */   
    private function pngImage() {
        if ($this->image_curl) {
            return imagecreatefromstring($this->curl_data);
        } else {
            return imagecreatefrompng($this->image_url);
        }
    }
    
    /**
     * Determine RGB value not in current color palette
     *
     * @param mixed $image_src
     * @return array
     */   
    private function randomRgb($image_src) {
        $total = (imagecolorstotal($image_src) <= 0) ? 256 : imagecolorstotal($image_src);
        $red   = (rand() % 255);
        $green = (rand() % 255);
        $blue  = (rand() % 255);

        for ($i = 1; $i <= $total; $i++) {
            if (imagecolorexact($image_src, $red, $green, $blue) === -1) {
                return array("r" => $red,
                             "g" => $green,
                             "b" => $blue);
            }
        }
    }
    
    /**
     * Return filtered/error image
     *
     * @return void
     */   
    private function returnImage() {
        header("Content-type: image/png");
        
        if ($this->image_dest) {
            imagepng($this->image_dest);
        } else {
            imagepng($this->image_src);
        }
    }
    
    /**
     * Process image URL and create image resource
     *
     * @return mixed
     */   
    private function setImageSrc() {
        if ($this->image_info[2] === 1) {
            $image = $this->gifImage();
            $image_dest = imagecreate($this->image_info[0], $this->image_info[1]);
            imagecolortransparent($image_dest, imagecolorallocate($image_dest, 0, 0, 0)); 
            imagecopy($image_dest, $image, 0, 0, 0, 0, $this->image_info[0], $this->image_info[1]);
            
            return $image_dest;
        } elseif ($this->image_info[2] === 2) {
            return $this->jpegImage();
        } elseif ($this->image_info[2] === 3) {
            $image = $this->pngImage();
            imagesavealpha($image, true);
            
            return $image;
        } else {
            return false;
        }
    }
}

$image = new imageFilter($_GET['filename_gd'], $_GET['filter_gd']);  

/* EOF imageFilter.php */
/* Location: ./imageFilter.php */
