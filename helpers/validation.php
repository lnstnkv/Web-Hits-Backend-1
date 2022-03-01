<?php

    function validateStringNotLess( $str= '', $lenght = 8){

        if(strlen($str) >= $lenght){
            return true;
        }
        else{
            return false;
        }
    }