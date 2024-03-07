<?php

class PlayerStats {
    public $uuid = "";
    public $playerName = "";
    public $rank = "";
    public $firstLogin = "";
    public $lastLogin = "";
    public $friends = [];
    public $guild = "";
    public $kills = 0;
    public $deaths = 0;
    public $final_kills = 0;
    public $final_deaths = 0;
    public $winstreak = 0;
    public $beds_broken = 0;
    public $beds_lost = 0;
    public $wins = 0;
    public $losses = 0;
    public $games_played = 0;
    public $coins = 0;
    public $bedwars_level = 0;
    public $language = "";

    function __construct($uuid) {
        $this->uuid = $uuid;
        $this->getStats();
    }

    function getStats(){
        $url = "https://api.hypixel.net/player?key=c6868591-e02a-4ed7-8c78-94cee752b920&uuid=".$this->uuid;
        $json = file_get_contents($url);
        $data = json_decode($json, true);
        $bedwars = $data['player']['stats']['Bedwars'];
        $this->kills = $bedwars['kills_bedwars'];
        $this->deaths = $bedwars['deaths_bedwars'];
        $this->final_kills = $bedwars['final_kills_bedwars'];
        $this->final_deaths = $bedwars['final_deaths_bedwars'];
        $this->winstreak = $bedwars['winstreak'];
        $this->beds_broken = $bedwars['beds_broken_bedwars'];
        $this->beds_lost = $bedwars['beds_lost_bedwars'];
        $this->wins = $bedwars['wins_bedwars'];
        $this->losses = $bedwars['losses_bedwars'];
        $this->games_played = $bedwars['games_played_bedwars'];
        $this->coins = $bedwars['coins'];
        $this->bedwars_level = $bedwars['level_bedwars'];
        $this->playerName = $data['player']['displayname'];
        $this->rank = $data['player']['rank'];
        $this->firstLogin = $data['player']['firstLogin'];
        $this->lastLogin = $data['player']['lastLogin'];
        $this->friends = $data['player']['socialMedia']['links'];
        $this->guild = $data['player']['guild'];
        $this->language = $data['player']['userLanguage'];
    }

    function updateStats(){
        global $conn;
        $sql = "UPDATE bedwars SET kills = $this->kills, deaths = $this->deaths, final_kills = $this->final_kills, final_deaths = $this->final_deaths, winstreak = $this->winstreak, beds_broken = $this->beds_broken, beds_lost = $this->beds_lost, wins = $this->wins, losses = $this->losses, games_played = $this->games_played, coins = $this->coins WHERE uuid = '$this->uuid'";
        $conn->query($sql);
    }

    public function getKDR(){
        return $this->kills / $this->deaths;
    }

    public function getFKDR(){
        return $this->final_kills / $this->final_deaths;
    }

    public function getWLR(){
        return $this->wins / $this->losses;
    }

    public function getBBLR(){
        return $this->beds_broken / $this->beds_lost;
    }

    public function getLanguage(){
        return ucfirst(strtolower(($this->language)));
    }

    

}
?>