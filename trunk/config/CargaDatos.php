<?php

class CargaDatos
{
    private $fichero;
	public $Datos;
    private $host;
    private $user;
    private $pass;
    private $bbdd;

    function __construct()
    {
        require_once("/parametros.php");
        $this->host = $V_HOST;
        $this->user = $V_USER;
        $this->pass = $V_PASS;
        $this->bbdd = $V_BBDD;
	    $this->fichero = $F_DATOS;
		$this->Datos = array();
    }

    public function cargarDatos()
	{
		return $this->cargarFichero();
	}

    private function cargarFichero(){
	  
	  require("/parametros.php");
	  $lineas = file($this->fichero);

	  $binario = "";
	  
	  $contSevidores = 0;
	  
	  foreach ($lineas as $linea) {

		  $x;
		  if(preg_match("/$S_MAKE"."[a-zA-Z0-9_.]+/", $linea, $x))
		  {
		     $this->Datos[$contSevidores]["make"] = str_replace($S_MAKE, "", $x[0]);
		  }
		  if(preg_match("/$S_RUTA"."[a-zA-Z0-9_.\/]+/", $linea, $x))
		  {
		     $this->Datos[$contSevidores]["ruta"] = str_replace($S_RUTA, "", $x[0]);
		  }
		  if(preg_match("/$S_SERVIDOR"."[a-zA-Z0-9_.]+/", $linea, $x))
		  {
		     $this->Datos[$contSevidores]["servidor"] = str_replace($S_SERVIDOR, "", $x[0]);
		  }
		  if(preg_match("/$S_BULK"."[a-zA-Z0-9_.]+/", $linea, $x))
		  {
		     $this->Datos[$contSevidores]["bulk"] = str_replace($S_BULK, "", $x[0]);
		  }
		  if(preg_match("/^$S_F"."[a-zA-Z0-9_.]+/", $linea, $x))
		  {
		     $this->Datos[$contSevidores]["_f"][] = str_replace($S_F, "", $x[0]);
		  }
		  if(preg_match("/$S_F_GLOBAL"."[a-zA-Z0-9_.]+/", $linea, $x))
		  {
		     $this->Datos[$contSevidores]["_fGlobal"][] = str_replace($S_F_GLOBAL, "", $x[0]);
		  }
		  
		  if(preg_match("/$S_BINARIO"."[a-zA-Z0-9_]+/", $linea, $x))
		  {
		  	 $binario = str_replace($S_BINARIO, "", $x[0]);
		     $this->Datos[$contSevidores]["binarios"]["$binario"] = array();
		  }

		  if(preg_match("/$S_SERVICIO"."[a-zA-Z0-9_]+/", $linea, $x))
		  {
		     $this->Datos[$contSevidores]["binarios"]["$binario"][] = str_replace($S_SERVICIO, "", $x[0]);
		  }
		  
		  if(preg_match("/\Q$FIN\E/", $linea, $x))
		  {
		  	 $this->procesarServidor($this->Datos[$contSevidores]);
		  	 $contSevidores++;
		  }
	  }
    }
	
	private function procesarServidor($datos)
	{
		//require("/parametros.php");
		
		$ID_servidor = $this->InsertarServidor($datos["servidor"], $datos["ruta"], $datos["bulk"], $datos["make"]);
		
		$this->InsertarBinario($datos["binarios"], $ID_servidor);
		
		$this->Insertar_Fs($datos["_f"], $ID_servidor);
		
		$this->Insertar_FGlobs($datos["_fGlobal"], $ID_servidor);

	}
	
	private function Insertar_FGlobs($_fs, $id_servidor)
	{
        if(is_array($_fs) == FALSE || strlen($id_servidor) == 0 ) return FALSE;

        //require("/parametros.php");
		$mySqli = new mysqli($this->host, $this->user, $this->pass, $this->bbdd);

		$mySqli->autocommit(FALSE);

		$sqlD = "DELETE FROM _f_global WHERE ID_SERVIDOR = '$id_servidor'";
		$res = $mySqli->query($sqlD);

		foreach($_fs as $_f)
		{
			$sqlI = "INSERT INTO _f_global (NOMBRE, ID_SERVIDOR) VALUES ('$_f', '$id_servidor')";

			$res = $mySqli->query($sqlI);

	        if($mySqli->affected_rows > 0)
	        {
	        	$mySqli->commit();
				//$id = $mySqli->insert_id;
	        }
		}
		return TRUE;
	}
	
	private function Insertar_Fs($_fs, $id_servidor)
	{
        if(is_array($_fs) == FALSE || strlen($id_servidor) == 0 ) return FALSE;

        //require("/parametros.php");
		$mySqli = new mysqli($this->host, $this->user, $this->pass, $this->bbdd);

		$mySqli->autocommit(FALSE);

		$sqlD = "DELETE FROM _f WHERE ID_SERVIDOR = '$id_servidor'";
		$res = $mySqli->query($sqlD);

		foreach($_fs as $_f)
		{
			$sqlI = "INSERT INTO _f (NOMBRE, ID_SERVIDOR) VALUES ('$_f', '$id_servidor')";

			$res = $mySqli->query($sqlI);

	        if($mySqli->affected_rows > 0)
	        {
	        	$mySqli->commit();
				//$id = $mySqli->insert_id;
	        }
		}
		return TRUE;
	}

	private function InsertarBinario($binarios, $id_servidor)
	{
		
        if(is_array($binarios) == FALSE || strlen($id_servidor) == 0 ) return FALSE;
        
        //require("/parametros.php");
		$mySqli = new mysqli($this->host, $this->user, $this->pass, $this->bbdd);
		
		$mySqli->autocommit(FALSE);
		
		$sqlD = "DELETE FROM binario WHERE ID_SERVIDOR = '$id_servidor'";
		$res = $mySqli->query($sqlD);
		
        $binarios_nombres = array_keys($binarios);

		foreach($binarios_nombres as $nom_binario)
		{

			$sqlI = "INSERT INTO binario (NOMBRE, ID_SERVIDOR) VALUES ('$nom_binario', '$id_servidor')";

			$res = $mySqli->query($sqlI);
			
	        if($mySqli->affected_rows > 0)
	        {
				$id = $mySqli->insert_id;
				$mySqli->commit();
				$this->InsertarServicios( $binarios["$nom_binario"], $id);
	        }
		}
		return TRUE;
	}
	
	private function InsertarServicios($servicios, $id_binario)
	{
        if(is_array($servicios) == FALSE || strlen($id_binario) == 0 ) return FALSE;
        
        //require("/parametros.php");
		$mySqli = new mysqli($this->host, $this->user, $this->pass, $this->bbdd);
		
		$mySqli->autocommit(FALSE);
		
		$sqlD = "DELETE FROM servicio WHERE ID_BINARIO = '$id_binario'";
		$res = $mySqli->query($sqlD);
		
		foreach($servicios as $servicio)
		{
			$sqlI = "INSERT INTO servicio (NOMBRE, ID_BINARIO) VALUES ('$servicio', '$id_binario')";

			$res = $mySqli->query($sqlI);
			
	        if($mySqli->affected_rows > 0)
	        {
				//$id = $mySqli->insert_id;
				$mySqli->commit();
	        }
		}
		
		return TRUE;
	}
	
	private function InsertarServidor($servidor, $ruta, $bulk, $make)
	{
		if(strlen($servidor) == 0 || strlen($ruta) == 0) return "";

		require("/parametros.php");
		
		$id = "";
		
		$mySqli = new mysqli($this->host, $this->user, $this->pass, $this->bbdd);
		
		$mySqli->autocommit(FALSE);
		
		$this->LimpiarServidor($servidor);
		
		$sqlI = "INSERT INTO servidor (NOMBRE, RUTA, BULK, MAKE) VALUES ('$servidor', '$ruta', '$bulk', '$make')";
		
		$res = $mySqli->query($sqlI);
		
        if($mySqli->affected_rows > 0)
        {
			$id = $mySqli->insert_id;
            $mySqli->commit();
            $mySqli->close();
        }
        else {
           $mySqli->rollback();
           $mySqli->close();
        }

		return "$id";
	}
	
	private function LimpiarServidor($servidor)
	{
		
		if(strlen($servidor) == 0) return "";

		require("/parametros.php");
		
		$mySqli = new mysqli($this->host, $this->user, $this->pass, $this->bbdd);
        
		$sql = "SELECT ID_SERVIDOR FROM servidor WHERE NOMBRE = '$servidor'";
		
		$res = $mySqli->query($sql);
        
        if($mySqli->affected_rows > 0)
        {
            while($row = $res->fetch_assoc())
            {
                $id = $row['ID_SERVIDOR'];
				
		        $mySqli->autocommit(FALSE);
				
				$sqlD = "DELETE FROM s, b, c,  f, fg
				         USING
				             	servidor s 
				             LEFT JOIN binario b    ON s.ID_SERVIDOR = b.ID_SERVIDOR
				             LEFT JOIN servicio c   ON b.ID_BINARIO  = c.ID_BINARIO
				             LEFT JOIN _f f         ON s.ID_SERVIDOR = f.ID_SERVIDOR
				             LEFT JOIN _f_global fg ON s.ID_SERVIDOR = fg.ID_SERVIDOR
				          WHERE s.ID_SERVIDOR = '$id'";
				
				$mySqli->query($sqlD);
				
		        if($mySqli->affected_rows > 0)
		        {
		            $mySqli->commit();
		        }
            }
        }
		$mySqli->close();
	}

    public function depurar()
	{
		return var_dump($this);
	}
}

@session_destroy();
@session_unset();
@session_start();
set_time_limit(0);

$obj = new CargaDatos();

$obj->cargarDatos();

sleep();

echo "<span>".var_dump($obj->Datos). "</span>";
//echo "<span>".$obj->depurar(). "</span>";
//phpinfo();
die;

?>