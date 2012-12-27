<?php
error_reporting(E_ALL | E_STRICT);
require("parametros.php");

@session_start();
echo "<hr><h3>\$_SESSION</h3><hr>";
var_dump($_SESSION);
echo "<hr><h3>\$_POST</h3><hr>";
//var_dump($_POST);
echo "<hr><h3>\$_SERVER</h3><hr>";
//var_dump($_SERVER);
echo "<hr>";
//var_dump($sXmlConfig);
//var_dump($xml);
//var_dump($url);
//var_dump($V_HOST);
//var_dump($V_USER);
//var_dump($V_PASS);
//var_dump($V_BBDD);


?>