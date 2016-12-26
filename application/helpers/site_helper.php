<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('pr')) {
   function pr($data){
       echo "<pre>",print_r($data),"</pre>";
   }
}