<?php
$a=urlencode($_POST['a']);
$a=strtr($a,array('"'=>"","'"=>"","INSERT"=>"","SELECT"=>"","DELETE"=>"","UPDATE"=>"","^"=>"","`"=>"",
    "javascript:"=>"","function"=>"","."=>"","'"=>"",","=>""," "=>"+",":"=>"",";"=>"","/"=>"","http://"=>"","?"=>"","="=>""));
header("Location: ".$a."");
?>