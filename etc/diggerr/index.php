<?
require_once($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");

// The Digger
// This application takes parameters, and finds you the best talent per your request.
$fid = $_GET['id'] ? $_GET['id'] : FID;

$all = new Team;
$all->All($fid);

$team = new Team;
$team->Code($fid);
$team->Information($team->id);
$team->Roster($fid);


foreach ($team->roster_batter_pid as $v) {

	$player = new Player;
	$player->Information($v);

	$results .= ("
		<tr>
			<td>$player->link</td>
		</tr>
	");

}






print <<< CONTENT
<html>

<head>
	<link rel='stylesheet' href='/objects/css/global.php' type='text/css' media='screen' />

	<script language='javascript' src='/objects/third/jquery-1.7.1.min.js' type='text/javascript'></script>
	<script language='javascript' src='/objects/third/jquery-ui-1.8.17.custom.min.js' type='text/javascript'></script>
</head>

<body style='text-align: left; padding: 20px;'>
<div id='content'>
<!--
<div class='cf c2'>
	<div class='b3' style='margin-top: 150px;'>
		<h2>Team Detail</h2>
		<form method='get'>
			<select name='id' onchange='this.form.submit();'>
			$all->list_option_id
			</select>
		</form>

		<table>
		$results
		</table>


	</div>
</div>
-->


	<div class='b3'>
		<form method='post' id='digger'>

			<div class='bar'>
				<input type='radio' name='equation' value='pitcher' checked='checked' class='digger'/> Pure Historicals (WHIP) &nbsp;
				<input type='radio' name='equation' value='hofl' class='digger'/> HoFL 3-year &nbsp;
			</div>

			<h4>Positions</h4>
			<div class='bar'>
				<input type='checkbox' name='position[]' value='SP' checked='checked' class='digger' /> SP &nbsp;
				<input type='checkbox' name='position[]' value='RP' checked='checked' class='digger' /> RP &nbsp;
			</div>

			<h4>Throws</h4>
			<div class='bar'>
				<input type='checkbox' name='bats[]' value='L' checked='checked' class='digger' /> L &nbsp;
				<input type='checkbox' name='bats[]' value='R' checked='checked' class='digger' /> R &nbsp;
			</div>			


			<h4>Miscellaneous</h4>
			<div>
				<input type='checkbox' name='majors' value='1' class='digger' /> Majors &nbsp;
				<input type='checkbox' name='minors' value='1' class='digger' /> Minors &nbsp;
				<input type='checkbox' name='undervalue' value='1' class='digger' /> Undervalue &nbsp;
				<input type='checkbox' name='pps' value='3' class='digger' /> PPS 3 &nbsp;
				<input type='checkbox' name='pps' value='2' class='digger' /> PPS 2 &nbsp;
				<input type='checkbox' name='pps' value='1' class='digger' /> PPS 1 &nbsp;
				<input type='checkbox' name='below' value='2' class='digger' /> Below .500 &nbsp;
			</div>
		
		
		</form>
	</div>

	<div id='loading-results'><div class='core-emphasis'>Loading results please wait. These queries take some time.</div></div>
	
	<div id='results-digger'>
	
	
	</div>



</div>

<script type='text/javascript'>
$(document).ready(function() {

	$(".digger").change(function(e) {
		e.preventDefault();
	
		var data = $("#digger").serialize();

		$.post("/etc/diggerr/data.php", { data: data }, function(data) {
			$("#results-digger").html(data);
		});
	
	});

	$('#loading-results')
		.hide()  // hide it initially
		.ajaxStart(function() {
        $(this).show();
    })
    .ajaxStop(function() {
        $(this).hide();
    });

});
</script>


</body>

</html>

CONTENT;

?>