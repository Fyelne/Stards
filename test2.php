<?php 
class test {
    public String $uuid;
    //return same object if same uuid
    public function __construct($uuid) {
        $this->uuid = $uuid;
    }

    public function getStats(){
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
    }

    public function updateStats(){
        global $conn;
        $sql = "UPDATE bedwars SET kills = $this->kills, deaths = $this->deaths, final_kills = $this->final_kills, final_deaths = $this->final_deaths, winstreak = $this->winstreak, beds_broken = $this->beds_broken, beds_lost = $this->beds_lost, wins = $this->wins, losses = $this->losses, games_played = $this->games_played, coins = $this->coins WHERE uuid = '$this->uuid'";
        $conn->query($sql);
    }

    

}
?>