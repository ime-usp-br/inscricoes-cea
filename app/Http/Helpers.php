<?php

use \ForceUTF8\Encoding;

if (! function_exists('clear_string')) {
    function clear_string($string)
    {
        $string = Encoding::fixUTF8($string);        
        $string = str_replace("\r\n","\n",$string);
        $string = str_replace("\n\n","\n",$string);
        $string = str_replace("\n","\n\n",$string);

        $badChars = ["&","%","$","#","_","{","}"];
        foreach($badChars as $c){
            $string = str_replace($c, "\\".$c, $string); 
        }

        return $string;
    }
}