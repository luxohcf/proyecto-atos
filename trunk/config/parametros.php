<?php
require_once("comunes.php");
/* Archivo de configuracion */
//$url = $_SERVER["REQUEST_SCHEME"]."://".$_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"];
$url = "http://".$_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"];
$sXmlConfig = "$url/ProyectoAtos/config/Config.xml";;
$xml = simplexml_load_file($sXmlConfig);

/* Variables de base de datos */
$V_HOST = $xml->Host;
$V_USER = $xml->User;
$V_PASS = $xml->Password;
$V_BBDD = $xml->BBDD;
$F_DATOS = $xml->datos;

/* Variable de separacion del fichero de datos */
$INI = $xml->sep_ini;
$FIN = $xml->sep_fin;

$S_SERVICIO = $xml->sep_ser;
$S_SERVIDOR = $xml->sep_srv;
$S_F_GLOBAL = $xml->sep_fbl;
$S_BINARIO = $xml->sep_bin;
$S_BULK = $xml->sep_blk;
$S_RUTA = $xml->sep_rut;
$S_MAKE = $xml->sep_mak;
$S_F = $xml->sep_f;

/* Variables para mostrar trazas */
$depurar = FALSE; 
if($xml->depurarSQL == "1")
{
    $depurar = TRUE;
}
$depurarMax = FALSE; 
if($xml->depurarDUMP == "1")
{
    $depurarMax = TRUE;
    error_reporting(E_ALL | E_STRICT);
}

?>