<?php

if (! function_exists('clear_string')) {
    function clear_string($string)
    {
        $string = preg_replace('/[^[:print:]]/', '', $string);
        $badChars = ["&","%","$","#","_","{","}"];
        foreach($badChars as $c){
            $string = str_replace($c, "\\".$c, $string); 
        }

        return $string;
    }
}