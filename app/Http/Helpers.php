<?php

use \ForceUTF8\Encoding;

if (! function_exists('clear_string')) {
    function clear_string($string)
    {
        $string = Encoding::fixUTF8($string);
        $badChars = ["&","%","$","#","_","{","}"];
        foreach($badChars as $c){
            $string = str_replace($c, "\\".$c, $string); 
        }

        return $string;
    }
}