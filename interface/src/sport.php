<?php

$parts = parse_url($_SERVER['REQUEST_URI']);
parse_str($parts['query'], $query);
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'http://localhost/interface/sports/'.$query['id']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

$response = curl_exec($ch);
$sport = json_decode($response, true);
?>
<h1>Name: <?php echo $sport['name'];?></h1>
<h2>Teamsport: <?php echo $sport['teamsport'];?></h2>
<h2>Best Player: <?php echo $sport['best_player'];?></h2>
<h2>eSport: <?php echo $sport['esport'];?></h2>

<a href="http://192.168.33.10/slimClient">Back To List</a>
