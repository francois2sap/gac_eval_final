<?php
	class ImportController {
		
		function dbConnect()
		{
		    try
		    {
		        $db = new PDO('mysql:host=mysql57;dbname=francois;charset=utf8', 'root', 'secret');
		        return $db;
		    }
		    catch(Exception $e)
		    {
		        die('Erreur : '.$e->getMessage());
		    }
		}


		function getCalls ()
		{
			$db = dbConnect();
			$calls = $db->prepare("SELECT SUM(`temps_appel`) FROM `appels` WHERE date >= '2012-02-15'");
			$calls->execute();
			return $calls;
		}

		function getDatas ()
		{
			$db = $dbConnect();
			$datas = $db->prepare("");
		}
	}
?>