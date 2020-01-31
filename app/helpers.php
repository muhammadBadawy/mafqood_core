<?php

function extractFace($image_path){

  $script_path = ltrim(Storage::disk('local')->url('py_scripts/storeFace.py'), '/');
  // $image_path  = ltrim(Storage::disk('local')->url('people/single.jpg'), '/');
  $image_path  = ltrim($image_path, '/');

  $cmd = 'py '.$script_path.' '.$image_path;

  $output = shell_exec($cmd);

  $output = str_replace("'",'"',$output);
  $output = strip_tags($output);
  $output = trim($output, ' ');

  $second = json_decode($output);
  // return $second;
  return (array) $second;

}

function compareFaces($markFoundPrint, $markMiassingPrint){

  $script_path = ltrim(Storage::disk('local')->url('py_scripts/compareFaces.py'), '/');
  // $image_path  = ltrim(Storage::disk('local')->url('people/single.jpg'), '/');
  $markFoundPrint  = ltrim($markFoundPrint, '/');
  $markMiassingPrint  = ltrim($markMiassingPrint, '/');

  $cmd = 'py'.' '.$script_path.' '.$markFoundPrint.' '.$markMiassingPrint;

  // return $cmd;
  $output = shell_exec($cmd);
  // return $output;
  $output = str_replace("'",'"',$output);
  $output = strip_tags($output);
  $output = trim($output, ' ');

  $second = json_decode($output);
  // return $second;
  return (array) $second;

}

function randomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
