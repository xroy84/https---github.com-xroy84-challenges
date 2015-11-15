<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Form prueba</title>

<?php
$resultado = "";

$conn = new mysqli("localhost", "user", "pass", "dbname", "3306");

$ruta = '/usr/home/5T2rYoL5B4Q3/hosteum.com/web/';
$fichero = $ruta.basename($_FILES['entrada']['name']);

if (move_uploaded_file($_FILES['entrada']['tmp_name'], $fichero)) {
	
	$registros = file($fichero);
	
	$it = 0;

	foreach ($registros as $registro => $valor){

		if($it > 0){
		    
		    $campos = explode("\t",$valor);
		 
		    $title = trim($campos[0]);
		    $description = trim($campos[1]);
		    $price = trim($campos[2]);
		    $init_date = trim($campos[3]);
		    $expiry_date = trim($campos[4]);
		    $m_address = trim($campos[5]);
		    $m_name = trim($campos[6]);

		    $date_added = date("Y-m-d H:i:s");

		    $buscaMerchant = "SELECT * FROM merchants WHERE name='$m_name' LIMIT 1";
		    if ($result = $conn->query($buscaMerchant)) { 
		       $obj = $result->fetch_object();
		       $merchant_id = $obj->id; 
		       if($merchant_id == ""){
		       		$insert_merchant = "INSERT INTO merchants(name,address) VALUES('$m_name','$m_address')";
			    	$conn->query($insert_merchant);
			    	$merchant_id = $conn->insert_id;
		       }
		    } 
		    $result->close(); 
		 
		    $insert_cons = "INSERT INTO items(title,description,price,init_date,expiry_date,date_added,merchant_id) 
		    				VALUES('$title','$description','$price','$init_date','$expiry_date','$date_added','$merchant_id')";
		    $conn->query($insert_cons);


		}

		$it++;
	}

	//Busco los items de cada merchant
	$items_merchant = "SELECT * FROM merchants";
	if ($result = $conn->query($items_merchant)) { 
        while($obj = $result->fetch_object()){ 
        	$merchant_id = $obj->id;
        	$merchant_name = $obj->name;
            $cuenta = "SELECT COUNT(id) AS c FROM items WHERE merchant_id=$merchant_id";
            if ($resulta = $conn->query($cuenta)) { 
	            $hay = $resulta->fetch_object();
	            $cuantos = $hay->c;
	            $resultado .= "Items del merchant $merchant_name : $cuantos <br />";
        	}
        	$resulta->close(); 
        } 
    } 
    $result->close(); 

    //No tengo claro si los items se buscan por fecha de insercion, inicio o fin

} 
?>
</head>

<body>
<form method="post" enctype="multipart/form-data">
	Fichero: <input type="file" name="entrada" />
	<input type="submit" value="subir fichero" />
</form>
<div id="info">
	<?php print $resultado;	?>
</div>
</body>

</html>