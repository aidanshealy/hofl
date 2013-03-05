<?
set_time_limit(600000);
require_once($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");

if ($_REQUEST['ballpark_create']){

	$dataset = array(
								$_REQUEST['id'],
								$_REQUEST['banned'],
								$_REQUEST['year'],
								$_REQUEST['city'],
								$_REQUEST['cover'],
								$_REQUEST['surface'],
								$_REQUEST['foul'],
								$_REQUEST['image'],
								$_REQUEST['fence_left_line'],
								$_REQUEST['fence_left'],
								$_REQUEST['fence_left_gap'],
								$_REQUEST['fence_center'],
								$_REQUEST['fence_right_gap'],
								$_REQUEST['fence_right'],
								$_REQUEST['fence_right_line'],
								$_REQUEST['wall_left_line'],
								$_REQUEST['wall_left'],
								$_REQUEST['wall_left_gap'],
								$_REQUEST['wall_center'],
								$_REQUEST['wall_right_gap'],
								$_REQUEST['wall_right'],
								$_REQUEST['wall_right_line'],
								$_REQUEST['wind_out_lf'],
								$_REQUEST['wind_out_cf'],
								$_REQUEST['wind_out_rf'],
								$_REQUEST['wind_cross_lfrf'],
								$_REQUEST['wind_in_lf'],
								$_REQUEST['wind_in_cf'],
								$_REQUEST['wind_in_rf'],
								$_REQUEST['wind_cross_rflf'],
								$_REQUEST['wind_none'],
								$_REQUEST['factor_lh_singles'],
								$_REQUEST['factor_lh_doubles'],
								$_REQUEST['factor_lh_triples'],
								$_REQUEST['factor_lh_homeruns'],
								$_REQUEST['factor_rh_singles'],
								$_REQUEST['factor_rh_doubles'],
								$_REQUEST['factor_rh_triples'],
								$_REQUEST['factor_rh_homeruns']
							);


	$query = ("INSERT INTO ballparks_breakdown 
							(
							ballpark_id, 
							banned, 
							year, 
							city, 
							cover, 
							surface, 
							foul, 
							image, 
							fence_left_line, 
							fence_left, 
							fence_left_gap, 
							fence_center, 
							fence_right_gap, 
							fence_right, 
							fence_right_line, 
							wall_left_line, 
							wall_left, 
							wall_left_gap, 
							wall_center, 
							wall_right_gap, 
							wall_right, 
							wall_right_line, 
							wind_out_lf, 
							wind_out_cf, 
							wind_out_rf, 
							wind_cross_lfrf, 
							wind_in_lf, 
							wind_in_cf, 
							wind_in_rf, 
							wind_cross_rflf, 
							wind_none, 
							factor_lh_singles, 
							factor_lh_doubles, 
							factor_lh_triples, 
							factor_lh_homeruns, 
							factor_rh_singles, 
							factor_rh_doubles, 
							factor_rh_triples, 
							factor_rh_homeruns
							) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) 
						");
	$result = db::Q()->prepare($query);
	$result->execute($dataset);

}




$query = "SELECT id,name FROM ballparks ORDER BY name";
$result = db::Q()->prepare($query);
$result->bindParam(1, $id);
$result->execute();

while ($x = $result->fetch(PDO::FETCH_OBJ)){
	$ballpark_name .= "<option value='$x->id'>$x->name</option>";
}



print <<< CONTENT

<h2>Ballpark Create</h2>

<form method='post'>

<div>Name</div>
<div><select name='id'><option></option>$ballpark_name</select></div>

<div>Year</div>
<div><input type='text' name='year' /></div>

<div>City</div>
<div><input type='text' name='city' /></div>

<div>Cover</div>
<div><select name='cover'><option></option><option>Dome</option><option>Outdoor</option><option>Both</option></select></div>

<div>Surface</div>
<div><select name='surface'><option></option><option>Grass</option><option>Turf</option></select></div>

<div>Foul</div>
<div><select name='foul'><option></option><option>Large</option><option>Medium</option><option>Small</option></select></div>

<div>Image</div>
<div><input type='text' name='image' /></div>

<div>Fence</div>
<div>
	<input type='text' name='fence_left_line' size='3' />
	<input type='text' name='fence_left' size='3' />
	<input type='text' name='fence_left_gap' size='3' />
	<input type='text' name='fence_center' size='3' />
	<input type='text' name='fence_right_gap' size='3' />
	<input type='text' name='fence_right' size='3' />
	<input type='text' name='fence_right_line' size='3' />
</div>

<div>Wall</div>
<div>
	<input type='text' name='wall_left_line' size='3' />
	<input type='text' name='wall_left' size='3' />
	<input type='text' name='wall_left_gap' size='3' />
	<input type='text' name='wall_center' size='3' />
	<input type='text' name='wall_right_gap' size='3' />
	<input type='text' name='wall_right' size='3' />
	<input type='text' name='wall_right_line' size='3' />
</div>

<div>Wind</div>
<div>
	<input type='text' name='wind_out_lf' size='3' />
	<input type='text' name='wind_out_cf' size='3' />
	<input type='text' name='wind_out_rf' size='3' />
	<input type='text' name='wind_cross_lfrf' size='3' />
	<input type='text' name='wind_in_lf' size='3' />
	<input type='text' name='wind_in_cf' size='3' />
	<input type='text' name='wind_in_rf' size='3' />
	<input type='text' name='wind_cross_rflf' size='3' />
	<input type='text' name='wind_none' size='3' />
</div>

<div>Factors</div>
<div>
	<input type='text' name='factor_lh_singles' size='3' />
	<input type='text' name='factor_lh_doubles' size='3' />
	<input type='text' name='factor_lh_triples' size='3' />
	<input type='text' name='factor_lh_homeruns' size='3' />
	<input type='text' name='factor_rh_singles' size='3' />
	<input type='text' name='factor_rh_doubles' size='3' />
	<input type='text' name='factor_rh_triples' size='3' />
	<input type='text' name='factor_rh_homeruns' size='3' />
</div>


<div><input type='checkbox' name='banned' value='1' /> Banned</div>


<input type='submit' name='ballpark_create' value='Create'/>
</form>

CONTENT;





?>