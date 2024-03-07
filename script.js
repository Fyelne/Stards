//Hypixel API GET Bedwars stats for a player
var uuid;
var player_search = document.getElementById("player_search");

function getBedwarsStats(player) {
	var url = "https://api.hypixel.net/player?key=c6868591-e02a-4ed7-8c78-94cee752b920&name=" + player;
	var data;
	fetch(url)
	.then(res => {
		if (!res.ok) {
			document.getElementById("player_search_error").innerHTML = "Hypixel API is down";
			throw Error(res.status);
		}
		return res.json();
	})
	.then(response => {
		if(response.success) {
			data = response;
			uuid = data.player.uuid;
			updatePlayerInfo(data.player);
			updatePlayerStats(data.player);
		}
	})
	.catch(error => {
		if(error == "Error: 429") {
			document.getElementById("player_search_error").innerHTML = "Too many requests";
		} else {
			document.getElementById("player_search_error").innerHTML = "Player not found";
		}
		console.log(error);
	});
}

function timestampToDate(timestamp){
	const event = new Date(timestamp);
	const options = { weekday: 'short', year: 'numeric', month: 'long', day: 'numeric' };
	return event.toLocaleDateString(undefined, options);
}

function updatePlayerInfo(player) {
	document.getElementById("player_skin").src = "https://crafatar.com/renders/body/" + uuid + "?overlay";
	var player_name = player.displayname;
	document.getElementById("player_name").innerHTML = player_name.charAt(0).toUpperCase() + player_name.slice(1)
	document.getElementById("player_uuid").innerHTML = "<strong>UUID: </strong>"+ uuid;
	document.getElementById("player_rank").innerHTML = "<strong>Rank: </strong>" + (player.newPackageRank ? player.newPackageRank : "Player");
	document.getElementById("first_login").innerHTML = "<strong>First Join: </strong>" + timestampToDate(player.firstLogin);
	document.getElementById("last_login").innerHTML = "<strong>Last Join: </strong>" + timestampToDate(player.lastLogin);
	// level = 10000 + 2500 * level calculate level
	document.getElementById("player_level").innerHTML = "<strong>Level: </strong>" +  (Math.sqrt((2 * player.networkExp) + 30625) / 50 - 2.5).toFixed(2);
	// document.getElementById("player_friends").innerHTML = "Friends: " + player.socialMedia.links.FRIENDS;
	// document.getElementById("player_guild").innerHTML = "Guild: " + player.socialMedia.links.GUILD;
	var language = player.userLanguage;
	document.getElementById("player_language").innerHTML = "<strong>Language: </strong>" + language.charAt(0) + language.slice(1).toLowerCase();
	// document.getElementById("player_status").innerHTML = "Status: " + (player.socialMedia.links.STATUS ? player.socialMedia.links.STATUS : "Offline");
}


//TODO: Add a function to get the player's guild
//TODO: Add a function to get the player's friends
//TODO: Add a function to get the player's status
//Reference:
//https://api.hypixel.net/recentGames?key=c6868591-e02a-4ed7-8c78-94cee752b920&uuid=f1a266e6d4f747f28e0caff9acd5500e
//https://api.hypixel.net/guild?key=c6868591-e02a-4ed7-8c78-94cee752b920&player=f1a266e6d4f747f28e0caff9acd5500e

function getPlayerStatus() {
	var url = "https://api.hypixel.net/status?key=c6868591-e02a-4ed7-8c78-94cee752b920&uuid=" + uuid;
	var data;
	fetch(url)
	.then(res => {
		if (!res.ok) {
			document.getElementById("player_search_error").innerHTML = "Hypixel API is down";
			if(res.status == 429) {
				document.getElementById("player_search_error").innerHTML = "Too many requests";
			}
			throw Error(res.statusText);
		}
		return res.json();
	})
	.then(response => {
		if(response.success) {
			data = response;
			if(data.session.online) {
				document.getElementById("player_status").innerHTML = "Status: Online";
			} else {
				document.getElementById("player_status").innerHTML = "Status: Offline";
			}
		} else {
			document.getElementById("player_status").innerHTML = "Status: Offline";
		}
	})
	.catch(error => {
		document.getElementById("player_status").innerHTML = "Status: Offline";
		console.log(error);
	});
}

function updatePlayerStats(player_stats) {
	bedwars_stats = player_stats.stats.Bedwars;
	var total = bedwars_stats.coins;
	var games_played = bedwars_stats.games_played_bedwars;
	var kills = bedwars_stats.kills_bedwars;
	var deaths = bedwars_stats.deaths_bedwars;
	var winstreak = bedwars_stats.winstreak;
	var wins = bedwars_stats.wins_bedwars;
	var losses = bedwars_stats.losses_bedwars;
	var win_rate = wins / (wins + losses) * 100;
	var bed_losses = bedwars_stats.beds_lost_bedwars;
	var bed_wins = bedwars_stats.beds_broken_bedwars;
	var kdr = kills / deaths;
	var kill_death_ratio = kdr.toFixed(2);
	var final_kills = bedwars_stats.final_kills_bedwars;
	var final_deaths = bedwars_stats.final_deaths_bedwars;
	var final_kdr = final_kills / final_deaths;
	var final_kill_death_ratio = final_kdr.toFixed(2);
	var bedwars_level = player_stats.achievements.bedwars_level;
	document.getElementById("total").innerHTML = total;
	document.getElementById("games_played").innerHTML = games_played;
	document.getElementById("kills").innerHTML = kills;
	document.getElementById("deaths").innerHTML = deaths;
	document.getElementById("winstreak").innerHTML = winstreak;
	document.getElementById("wins").innerHTML = wins;
	document.getElementById("losses").innerHTML = losses;
	document.getElementById("win_rate").innerHTML = win_rate.toFixed(2) + "%";
	document.getElementById("kill_death_ratio").innerHTML = kill_death_ratio;
	document.getElementById("bed_losses").innerHTML = bed_losses;
	document.getElementById("bed_wins").innerHTML = bed_wins;
	document.getElementById("final_kills").innerHTML = final_kills;
	document.getElementById("final_deaths").innerHTML = final_deaths;
	document.getElementById("final_kill_death_ratio").innerHTML = final_kill_death_ratio;
	document.getElementById("bedwars_level").innerHTML = bedwars_level;
}

function getGameStats() {
	//Ajax phpmyadmin connection
	
}

function saveImage()
{
    var position = JSON.stringify(allMousePos);
    author = prompt("Please type your name");

    $.ajax({
        type: 'post',
        url: 'http://localhost/folder/database.php',
        data: {author: author, position: position},
        success: function( data ) {
        console.log( data );
        }
    });
}

player_search.addEventListener("keypress", function (e) {
	if (e.key === 'Enter') {
		getBedwarsStats(player_search.value);
	}
});


(function($) {
	
	"use strict";
	
	var fullHeight = function() {
		
		$('.js-fullheight').css('height', $(window).height());
		$(window).resize(function(){
			$('.js-fullheight').css('height', $(window).height());
		});
		
	};
	fullHeight();
	
	$('#sidebarCollapse').on('click', function () {
		$('#sidebar').toggleClass('active');
	});
	
})(jQuery);
