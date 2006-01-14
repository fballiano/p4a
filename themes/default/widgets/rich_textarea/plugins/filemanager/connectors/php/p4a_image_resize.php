<?php

if (function_exists("mime_content_type") and strpos(mime_content_type($sFilePath), "image") !== FALSE ) { //The file is an image?
    if (function_exists("gd_info")) { //libGD is installed ?
    
        if (array_key_exists("EnableImageResize",$Config) 
            and $Config['EnableImageResize'] == true) { //Image Resize is enabled?
            
            $createfunction = "";
            if (strpos(mime_content_type($sFilePath), "png")) {
                $createfunction = "imagecreatefrompng";
                $savefunction = "imagepng";
            } elseif (strpos(mime_content_type($sFilePath), "jpeg")) {
                $createfunction = "imagecreatefromjpeg";
                $savefunction = "imagejpeg";
            }
            
            list($width, $height) = getimagesize($sFilePath);
            
            foreach ($Config['ImageSize'] as $size=>$value) {
                list($new_width,$new_height) = explode("x",$value);
                if (($width > $new_width) or($height > $new_height)) {
                    if (($width/$height)>($new_width/$new_height)) {
                        $new_height = $new_width / ($width /$height);
                    } else {
                        $new_width = $new_height * ($width /$height);
                    }                
                } else {
                    $new_width = $width;
                    $new_height = $height;
                }
                
                if ($createfunction) {
                    $im_new = @imagecreatetruecolor($new_width, $new_height);
                    $im = $createfunction($sFilePath);
                    imagecopyresampled($im_new, $im, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                    
                    if ($size == "__original__") {
                    	$sNewFilePath = $sFilePath;
                    } else {
                    	$sNewFileName = RemoveExtension( $sFileName) . "_{$size}.{$sExtension}";
                    	$sNewFilePath = $sServerDir . $sNewFileName;
                    }
                    $savefunction($im_new, $sNewFilePath);
                    
                    if (is_file($sNewFilePath)) {
                        $oldumask = umask(0);
                        chmod($sNewFilePath, 0777);
                        umask($oldumask);
                    }
                }                
            } 
        }
    }
}

?>