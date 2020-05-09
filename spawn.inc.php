<?php
	
if ($_SITE_INCLUDED !== true) exit();
	
//spawn
if (array_key_exists('eid', $_GET) && $site_enabled && !$spawn_timeout && $site_authenticated)
{
	if(is_numeric($_GET['eid']))
	{
		$eid = $_GET['eid'];
		$pre = $mysqli->prepare("SELECT * FROM statuseffects WHERE id=?");
		$pre->bind_param("i", $eid);
		$pre->execute();
		$result = $pre->get_result();
		
		
		if($result->num_rows === 1)
		{
			$row = $result->fetch_assoc();
			
			$_SESSION['sess_timeout'] = time() + $row['price'];
			
			if ($rcon->connect())
			{
				$cmd = explode(PHP_EOL, $row['cmd']);
				$cmd = str_replace("<viewer>", "Ein Zuschauer", $cmd); //$oauth->getUsername()
				$cmd = str_replace("<target>", "@p", $cmd);
				
				foreach($cmd as $c)
				{
					$rcon->sendCommand($c);
				}
				$rcon->disconnect();
			}
			
		}
		
		$pre->close();
		header('location: /?tab=' . $active_tab);
	}	
}

?>