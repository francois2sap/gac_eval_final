<?php  

$mysqli = new mysqli("mysql57","root","secret","francois");
if ($mysqli->connect_error){
	die('Erreur de connexion (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}	

if ($mysqli->query("TRUNCATE `appels`") === FALSE){
echo("Truncate NOT OK.\n");
}
$row = 1;

$mapping = [
'sms'=> ['SMS']
];

	if (($handle = fopen('tickets_appels_201202.csv', "r")) !== FALSE){
		
		while (($data = fgetcsv($handle, 500, ";")) !== FALSE) {
			$row++;
			$nbr_account = $mysqli->real_escape_string($data[0]);
			$nbr_bill = $mysqli->real_escape_string($data[1]);
			$nbr_sub = $mysqli->real_escape_string($data[2]);
			$date = date_parse_from_format('d-m-Y', $data[3]);
			
			if (checkdate ($date['month'], $date['day'], $date['year']) == FALSE){
				echo "Date incorrect : '$data[3]', Ligne $row <br>";
			}
			
			$date_mysql = $mysqli->real_escape_string($date['year'].'-'.$date['month'].'-'.$date['day']);
			
			if (strtotime($data[4]) === FALSE){
				$data[4] = NULL;
				echo "Heure incorrect : '$data[4]', ligne $row <br>";
			}
			
			$hour = $mysqli->real_escape_string($data[4]);
			
			if (($data[5] ==  NULL) && (strpos($data[7], "sms"))) {
				$data[5] = 1;
			}

			if (strpos($data[5], ":") == TRUE) {
				$str_time = $data[5];
				sscanf($str_time, "%d:%d:%d", $hours, $minutes, $seconds);
				$data[5] = isset($hours) ? $hours * 3600 + $minutes * 60 + $seconds : $minutes * 60 + $seconds;
			}
			
			if (is_int($data[5]) == FALSE) {
				$data[5] = intval($data[5]);
			}


			$volume = $mysqli->real_escape_string($data[5]);
			
			if ((strpos($data[7], "connexion")) || (strpos($data[7], "3"))){
				$type = $mysqli->real_escape_string("connexion");
			}

			if ((strpos($data[7], "appel")) || (strpos($data[7], "vers"))){
				$type = $mysqli->real_escape_string("appel");
			}

			if (strpos($data[7], "sms")){
				$type = $mysqli->real_escape_string("SMS");
			}

			if ($data[7] == NULL) {
				$type = null;
			}

			$query = "INSERT INTO `appels` (`num_compte`, `num_fac`, `num_abo`, `date`, `heure`, `volume`, `type`) VALUES (
			'$nbr_account', '$nbr_bill', '$nbr_sub', '$date_mysql', '$hour', '$volume', '$type')";
			
			if ($mysqli->query($query) === FALSE){
				echo("$query Insert NOT OK. ligne $row " . $mysqli->error . " " . $mysqli->errno . '<br>');
			}
		}
	}
	$query = "SELECT SUM(`volume`) FROM `appels` WHERE date >= '2012-02-15' AND type = 'appel'";
 	$resultat_query = $mysqli->query($query);	
	if ($resultat_query === FALSE){
		echo("$query Query NOT OK." . $mysqli->error . " " . $mysqli->errno);
	} else {
		$row = $resultat_query->fetch_array(MYSQLI_NUM);
		if ($row === NULL) {
			echo "<br>error";
		} else {
			echo "La durée totale réelle des appels effectués après le 15/02/2012 est de  " .gmdate("H:i:s", (int)$row[0]) . "h soit " . $row[0] . " secondes. <br>";
		}
		$resultat_query->free();
	}
	$query = "SELECT `volume` FROM `appels` WHERE type = 'connexion' AND  `heure` NOT BETWEEN '08:00:00' AND '18:00:00' ORDER BY `volume` DESC LIMIT 10";
	$resultat_query = $mysqli->query($query);	
	if ($resultat_query === FALSE){
		echo("<br>$query Query NOT OK." . $mysqli->error . " " . $mysqli->errno);
	} else {
		echo "<br>TOP 10 temps facture en secondes hors 8:00-18:00 <br>";
		while (($row = $resultat_query->fetch_array(MYSQLI_NUM)) !== NULL) {
			echo $row[0] . '<br>';
		}
		$resultat_query->free();
	}
	
	$query = "SELECT COUNT( * ) FROM `appels` WHERE `type` = 'SMS'";
	$resultat_query = $mysqli->query($query);
	if ($resultat_query === FALSE){
		echo("<br>$query Query NOT OK." . $mysqli->error . " " . $mysqli->errno);
	} else {
		$row = $resultat_query->fetch_array(MYSQLI_NUM);
		echo "<br>Le Total des SMS envoyes est de : $row[0] <br>";
		$resultat_query->free();
	}
	fclose($handle);
	$mysqli->close();


?>
<!-- $sql = "INSERT INTO 'appels'('num_compte','num_fac','num_abo','date','heure','temps_appel','temps_facture','type') VALUES "; -->
