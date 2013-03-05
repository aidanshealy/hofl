<?
require_once($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");


print <<< CONTENT
<h1>Hall of Fame League API Documentation</h1>
<p>
I have decided to build a pretty basic API to allow you all to get to some of the data in regards to players, teams and league based information. I will continue to build this over time.
</p>

<h3>Players</h3>
<ul>
	<li><a href='api.php?load=player'>Basic Player Information</a>
	<li><a href='api.php?load=player_data&section=statistics'>All Player Statistics (HoFL)</a>
<!--	<li><a href='api.php?load=player_data&section=statistics_pitching'>All Pitching Data (HoFL)</a>-->
	<li><a href='api.php?load=player_data&section=statistics_fielding'>All Fielding Data (HoFL)</a>
<!--
	<li><a href='api.php?load=player_data&section=mlb_batting'>All Batting Data (MLB)</a>
	<li><a href='api.php?load=player_data&section=mlb_pitching'>All Pitching Data (MLB)</a>
	<li><a href='api.php?load=player_data&section=player_ratings_batters_league'>All Batter Ratings (HoFL)</a>
	<li><a href='api.php?load=player_data&section=player_ratings_pitchers_league'>All Pitcher Ratings (HoFL)</a>
	<li><a href='api.php?load=player_data&section=player_ratings_batters_dmb'>All Batter Ratings (DMB)</a>
	<li><a href='api.php?load=player_data&section=player_ratings_pitchers_dmb'>All Pitcher Ratings (DMB)</a>
-->
</ul>


CONTENT;






?>