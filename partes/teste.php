<?php
echo 'comeco<br>';
$dt=date('Ymd');
echo "teste= $dt<br>";

$data = new Datetime();
var_dump($data);
$str = $data->format( 'h:i');
echo "Formatada $str<br>";
