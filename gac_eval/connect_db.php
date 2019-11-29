<?php

try {
	$db = new PDO('mysql:host=mysql57;dbname=francois', 'root', 'secret');
}
catch(Exception $e) {
    die('Erreur : '.$e->getMessage());
}

if ($db->query("TRUNCATE `appels`") === FALSE){
echo("Truncate NOT OK.\n");
}
$row = 1;

$mapping = [
'sms'=> ['SMS']
];

	if (($handle = fopen('tickets_appels_201202.csv', "r")) !== FALSE){
		
		while (($data = fgetcsv($handle, 500, ";")) !== FALSE) {
			$row++;
			$nbr_account = $data[0];
			$nbr_bill = $data[1];
			$nbr_sub = $data[2];
			$date = date_parse_from_format('d-m-Y', $data[3]);
			if (checkdate ($date['month'], $date['day'], $date['year']) == FALSE){
				echo "Date incorrect : '$data[3]', Ligne $row <br>";
			}
			
			$date_mysql = $date['year'].'-'.$date['month'].'-'.$date['day'];
			
			if (strtotime($data[4]) === FALSE){
				$data[4] = NULL;
				echo "Heure incorrect : '$data[4]', ligne $row <br>";
			}
			
			$hour = $data[4];
			
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

			$volume = $data[5];
			
			if ((strpos($data[7], "connexion")) || (strpos($data[7], "3"))){
				$type = "connexion";
			}

			if ((strpos($data[7], "appel")) || (strpos($data[7], "vers"))){
				$type = "appel";
			}

			if (strpos($data[7], "sms")){
				$type = "SMS";
			}

			if ($data[7] == NULL) {
				$type = null;
			}		
			$query = $db->prepare("INSERT INTO `appels` (`num_compte`, `num_fac`, `num_abo`, `date`, `heure`, `volume`, `type`) VALUES ('$nbr_account', '$nbr_bill', '$nbr_sub', '$date_mysql', '$hour', '$volume', '$type')");
			$query->execute();
		}
	$sql = "SELECT SUM(`volume`) FROM `appels` WHERE date >= '2012-02-15' AND type = 'appel'";
	$request = $db->query($sql);
	echo $request;
	}
	fclose($handle);
?>
