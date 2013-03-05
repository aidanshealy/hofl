<?
set_time_limit(600000);
require_once($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");

if ($_REQUEST['award_entry']){


	$do = new Core;
	$do->type = 26;
	$do->user_id = $o->id;
	$do->connect_player = $_REQUEST['pid'];
	$do->title = "Seasonal Awards";
	$do->body = scrub_text($_REQUEST['body']);
	$do->misc = $_REQUEST['type'];
	$do->comments = 1;
	$do->Create();
	



}



$org = new Organization;
$org->Information(1);

// Get All of the Awards to build navigation
$query = "SELECT award_id, name, short, league FROM awards_type WHERE seasonal = 1 ORDER BY sort";
$result = db::Q()->prepare($query);
$result->execute();

while ($x = $result->fetch(PDO::FETCH_OBJ)){

	if ($x->league == 1){
		$league = $org->league_one;
	}
	else if ($x->league == 2){
		$league = $org->league_two;
	}
	else {
		unset($league);
	}
	$option .= $_REQUEST['type'] == $x->award_id ? "<option value='$x->award_id' selected='selected'>$league $x->name</option>" : "<option value='$x->award_id'>$league $x->name</option>";


}


print <<< CONTENT

<form method='post'>

Type<br>
<select name='type'>
$option
</select>
<br>

PID<br>
<input type='text' name='pid' maxlength='5'/><br>

BODY<br>
<textarea name='body'></textarea><br>

<input type='submit' name='award_entry'>


</form>



CONTENT;



?>