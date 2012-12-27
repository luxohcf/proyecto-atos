<?php
require("../config/parametros.php");
$aaData = array();

$query = "SELECT DISTINCT
		      c.NOMBRE AS NOMBRE_CORTO,
			  '' AS NOMBRE_LARGO,
			  b.NOMBRE AS NOMBRE_BINARIO,
			  s.NOMBRE AS NOMBRE_SERVIDOR,
			  s.RUTA   AS RUTA,
			  f.NOMBRE AS NOMBRE_F,
			  fg.NOMBRE AS NOMBRE_FG,
			  s.BULK AS NOMBRE_BULK,
			  '' AS VERSION
		  FROM servicio c 
			 INNER JOIN binario b    ON c.ID_BINARIO  = b.ID_BINARIO
			 INNER JOIN servidor s   ON b.ID_SERVIDOR  = s.ID_SERVIDOR
			 INNER JOIN _f f         ON s.ID_SERVIDOR = f.ID_SERVIDOR
			 INNER JOIN _f_global fg ON s.ID_SERVIDOR = fg.ID_SERVIDOR";

$mySqli = new mysqli($V_HOST, $V_USER, $V_PASS, $V_BBDD);

if($mySqli->connect_errno)
{
    $aErrores["Error conexion MySql"] = $mySqli->connect_error;
}
$res = $mySqli->query($query);

if($mySqli->affected_rows > 0)
{
    while($row = $res->fetch_assoc())
    {
        $aaData[] = array(                  
                $row['NOMBRE_CORTO'],
                $row['NOMBRE_LARGO'],
                $row['NOMBRE_BINARIO'],
                $row['NOMBRE_SERVIDOR'],
                $row['RUTA'],
                $row['VERSION'],
                generarAtajos($row['NOMBRE_BINARIO'],
				              $row['NOMBRE_F'],
							  $row['NOMBRE_FG'],
							  $row['NOMBRE_BULK'])
            );
    }
}

$aa = array(
     'sEcho' => 1,
        "iTotalRecords" => 0,
        "iTotalDisplayRecords" => 0,
        'aaData' => $aaData);

echo json_encode($aa);

function generarAtajos($bin, $_f, $_fg, $bulk)
{
	require("../config/parametros.php");
	
	/* pendiente */
	
	$atj  = "rm -f $R_MW_BIN/$bin\ncp $R_BN_TMP/$bin $R_MW_BIN/$bin\n";
	$atj .= "rm -f $R_MW_BLK/$bulk\ncp $R_RELEASE/$bulk $R_MW_BLK/$bulk\n";
	$atj .= "rm -f $R_MW_FML/$_f\ncp $R_RELEASE/$_f $R_MW_FML/$_f\n";
	$atj .= "$R_COMUNES/$GEN_FML $_fg\n";
	
	return "$atj";
}


?>