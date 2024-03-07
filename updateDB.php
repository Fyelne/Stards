<?php

// Connect to database phpmyadmin
$servername = "localhost";
$username = "id19625633_stardsuserstats";
$password = "Stards53220!DB";
$dbname = "id19625633_userstats";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$party_uuid = array(
    "OpeningSequence" => "f1a266e6d4f747f28e0caff9acd5500e",
    "Fyelne" => "70d6f283bc9849f89d4d86266fd98f57",
    "ryudipity" => "705da5405af4437682be89fcf1a8eb0c"
);

foreach ($party_uuid as $name => $playerUUID){
    echo "Updating $name's stats...<br>";
    getBedwarsStats($playerUUID);
}


function getBedwarsStats($uuid) {
    $gameUrl = "https://api.hypixel.net/recentGames?key=c6868591-e02a-4ed7-8c78-94cee752b920&uuid=".$uuid;
    $gameJson = file_get_contents($gameUrl);
    $gameData = json_decode($gameJson, true);
    if(isset($gameData['games'])){
        $updated = false;
        for($i = 0; $i < count($gameData["games"]); $i++) {
            //(($gameData['games'][$i]['date'] / 1000)  > time() - 600) && 
            if($gameData['games'][$i]['gameType'] == "BEDWARS") {
                $url = "https://api.hypixel.net/player?key=c6868591-e02a-4ed7-8c78-94cee752b920&uuid=".$uuid;
                $json = file_get_contents($url);
                $data = json_decode($json, true);
                
                updateGameStats($data, $gameData['games'][$i]);
                if($updated == false){
                    updateStats($data);
                    $updated = true;
                }
                
            }
        }
    } else {
        echo "No games found";
    }
}


function updateGameStats($data, $gameData) {
    $uuid = $data['player']['uuid'];
    echo $uuid;
    $bedwars = $data['player']['stats']['Bedwars'];
    $kills = $bedwars['kills_bedwars'];
    $deaths = $bedwars['deaths_bedwars'];
    $finalKills = $bedwars['final_kills_bedwars'];
    $winstreak = $bedwars['winstreak'];
    $bedBroken = $bedwars['beds_broken_bedwars'];
    $bedLost = $bedwars['beds_lost_bedwars'];

    $map = $gameData['map'];
    $mode = $gameData['mode'];
    $date = round($gameData['date'] / 1000);

    if(isset($gameData['ended'])) {
        $ended = round($gameData['ended'] / 1000);
    } else {
        $ended = null;
    }

    global $conn;

    // Get database stats
    $sql = "SELECT * FROM Stats WHERE userUUID = '$uuid'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();

    $diffKills = $kills - $row['totalKills'];
    $diffDeaths = $deaths - $row['totalDeaths'];
    $diffFinalKills = $finalKills - $row['totalFK'];
    $diffBedBroken = $bedBroken - $row['totalBD'];
    $diffBedLost = $bedLost - $row['totalBL'];

    // Update database stats
    $sql = "REPLACE INTO Game (date, gameType, mode, map, ended) VALUES ('$date', 'BEDWARS', '$mode', '$map', '$ended')";
    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    if($ended != null){
        $sql = "INSERT INTO GameStats (playerUUID, gameId, kills, deaths, finalKills, winstreak, bedDestroyed, bedLoss) 
        VALUES ('$uuid', '$date', '$diffKills', '$diffDeaths', '$diffFinalKills', $winstreak, '$diffBedBroken', '$diffBedLost')";
        if ($conn->query($sql) === TRUE) {
            echo "New record created successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

// updateStats($testData);
function updateStats($data){
    $uuid = $data['player']['uuid'];
    $bedwars = $data['player']['stats']['Bedwars'];
    $bedwars_level = $data['player']['achievements']['bedwars_level'];
    $bedwars_final_kills = $bedwars['final_kills_bedwars'];
    $bedwars_final_deaths = $bedwars['final_deaths_bedwars'];
    $bedwars_wins = $bedwars['wins_bedwars'];
    $bedwars_losses = $bedwars['losses_bedwars'];
    $bedwars_kills = $bedwars['kills_bedwars'];
    $bedwars_deaths = $bedwars['deaths_bedwars'];
    $bedwars_beds_broken = $bedwars['beds_broken_bedwars'];
    $bedwars_beds_lost = $bedwars['beds_lost_bedwars'];
    $bedwars_coins = $bedwars['coins'];
    $bedwars_winstreak = $bedwars['winstreak'];
    $bedwars_games_played = $bedwars['games_played_bedwars'];

    global $conn;

    $sql = "REPLACE INTO Stats (coins, currentWinstreak, gamesPlayed, level, totalBD, totalBL, totalDeaths, totalFD, totalFK, totalKills, totalWins, totalLosses, userUUID)
    VALUES ('$bedwars_coins', '$bedwars_winstreak', '$bedwars_games_played', '$bedwars_level', '$bedwars_beds_broken', '$bedwars_beds_lost', '$bedwars_deaths', '$bedwars_final_deaths', '$bedwars_final_kills', '$bedwars_kills', '$bedwars_wins', '$bedwars_losses', '$uuid')";

    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

}
?>