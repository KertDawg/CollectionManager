<?php

namespace utils;


class Image
{
    public static function ResizeImageForDatabase($ImageString) 
    {
        list($ImageType, $ImageSet) = explode(';', $ImageString);
        list(, $TextData) = explode(',', $ImageSet);
        $ImageBytes = base64_decode($TextData);        
        $SourceImage = imagecreatefromstring($ImageBytes);

        $SourceX = imageSX($SourceImage);
        $SourceY = imageSY($SourceImage);
        $MaxX = 1200;
        $MaxY = 1200;
    
        if($SourceX > $SourceY) 
        {
            $DestX = $MaxX;
            $DestY = $SourceY*($MaxY / $SourceX);
        }
    
        if($SourceX < $SourceY) 
        {
            $DestX = $SourceX*($MaxX / $SourceY);
            $DestY = $MaxY;
        }
    
        if($SourceX == $SourceY) 
        {
            $DestX = $MaxX;
            $DestY = $MaxY;
        }
    
        $DestImage = ImageCreateTrueColor($DestX, $DestY);
        imagecopyresampled($DestImage, $SourceImage, 0, 0, 0, 0, $DestX, $DestY, $SourceX, $SourceY); 
    
        //  Capture the output of imagejpeg()
        ob_start();
        imagejpeg($DestImage, null, 80);
        $DestBytes = ob_get_contents();
        ob_end_clean();
    
        imagedestroy($DestImage); 
        imagedestroy($SourceImage);

        $DataURL = $dataUri = "data:image/jpg;base64," . base64_encode($DestBytes);
        return $DataURL;
    }
}
