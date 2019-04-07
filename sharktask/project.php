<?php


$output = shell_exec('py storeFace.py ../TestImages/single.jpg');
// $output = str_replace("'",'"',$output);
// echo $output;
// echo '<br>';
$second = json_decode($output);
// echo var_dump($second);
// var_dump(json_decode($output));

 ?>
