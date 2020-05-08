<?php
session_start();

require_once "config.inc.php";
require_once "function.inc.php";
require_once "rcon.php";
use Thedudeguy\Rcon;


//session timeout
if (!array_key_exists('sess_timeout', $_SESSION))
{
	$_SESSION['sess_timeout'] = 0;
}

$spawn_timeout = false;
$spawn_timeout_time = 0;
if ($_SESSION['sess_timeout'] > time())
{
	$spawn_timeout = true;
	$spawn_timeout_time = $_SESSION['sess_timeout'] - time();
}

//tab control
$active_tab = 0;
if (array_key_exists('tab', $_GET))
{
	if(is_numeric($_GET['tab']))
	{
		$active_tab = $_GET['tab'];
		
		if ($active_tab != 0 && $active_tab != 1 && $active_tab != 2)
		{
			$active_tab = 0;
		}
	}
}

//spawn
if (array_key_exists('eid', $_GET) && $site_enabled && !$spawn_timeout)
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
			$rcon = new Rcon($mc_host, $mc_port, $mc_password, $mc_timeout);
			
			if ($rcon->connect())
			{
				$cmd = explode(PHP_EOL, $row['cmd']);
				$cmd = str_replace("<viewer>", "Ein Zuschauer", $cmd);
				$cmd = str_replace("<target>", "@p", $cmd);
				
				foreach($cmd as $c)
				{
					$rcon->sendCommand($c);
				}
			}
			
		}
		
		$pre->close();
		header('location: /?tab=' . $active_tab);
	}	
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Ruiniere das Spiel</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
<style>
body {
	background-color: #343a40;
	color: white;
	margin-top: 15px;
}
h2 {
	margin-bottom: 15px;
}
</style>
<script>
window.setInterval(function(){
$('.js-countdown').each(function () {
	var time = $(this).html();
	time--;
	if (time < 0)
	{
		time = 0;
		clearInterval();
		$('.js-spawnbutton').each(function () {
			$(this).html("Spawn");
			$(this).removeClass("disabled");
		});
	}
	
	$(this).html(time);
});
}, 1000);
</script>
</head>
<body>

<?php if ($site_enabled) {
	  
	include "table.inc.php";
	
} 
else 
{
	include "inactive.inc.php";
} 

?>
<!-- Scripts am Ende //-->
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</body>
</html>