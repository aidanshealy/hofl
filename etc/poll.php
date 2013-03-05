<?
set_time_limit(600000);
require_once($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");

print <<< CONTENT
<html>

<head>
	<link rel='stylesheet' href='/objects/css/global.php' type='text/css' media='screen' />

	<script language='javascript' src='/objects/third/jquery-1.7.1.min.js' type='text/javascript'></script>
	<script language='javascript' src='/objects/third/jquery-ui-1.8.17.custom.min.js' type='text/javascript'></script>
</head>

<body style='text-align: left; padding: 20px;'>
<div id='content'>

CONTENT;




// Pull Back All Polls
$query = ("
	SELECT 
		id
	FROM core
	WHERE type = 2
	ORDER BY date_create DESC
");
$result = db::Q()->query($query);

while ($x = $result->fetch(PDO::FETCH_OBJ)){

	$core = new Core;
	$core->Information($x->id);

	$date = convert_timestamp($core->date_create,6);

	// Okay, now that we have all of the polls. Let's give the ability to close polls.
	if ($core->open == 2){
		$core->link_openclose = "<a href='?action=poll_modify&id=$core->id_encode&open=1'>Open Poll</a>";
	}
	else if ($core->open == 1){
		$core->link_openclose = "<a href='?action=poll_modify&id=$core->id_encode&open=2'>Close Poll</a>";
		$core->link_showowners = "<a href='?action=poll_modify&id=$core->id_encode&open=0'>Hide Owners</a>";
	}
	else if ($core->open == 0){
		$core->link_openclose = "<a href='?action=poll_modify&id=$core->id_encode&open=2'>Close Poll</a>";
		$core->link_showowners = "<a href='?action=poll_modify&id=$core->id_encode&open=1'>Show Owners</a>";
	}



	// Now list all of the options and the answers
	$yquery = "SELECT owner_id FROM owner WHERE franchise_id BETWEEN 1 AND 48 ORDER BY name_first, name_last";
	$yresult = db::Q()->prepare($yquery);
	$yresult->execute();

  while ($y = $yresult->fetch(PDO::FETCH_OBJ)){
	
		$zquery = "SELECT user_id, slot FROM core WHERE connect='$core->id' AND type='57' AND user_id='$y->owner_id' ORDER BY date_update DESC LIMIT 1";
		$zresult = db::Q()->prepare($zquery);
		$zresult->execute();

    if ($z = $zresult->fetch(PDO::FETCH_OBJ)){
			$total->votes++;

			$owner = new Owner; $owner->Information($z->user_id); 
			$total->owners_voting .= "<nobr>" . $owner->name_short . "</nobr> ";
		
			if ($z->slot == 1){ 
				$total->o1++; 
				$owner = new Owner; $owner->Information($z->user_id); $total->o1_names .= "<nobr>" . $owner->name_short . "</nobr> ";
			}
			else if ($z->slot == 2){ 
				$total->o2++; 
				$owner = new Owner; $owner->Information($z->user_id); $total->o2_names .= "<nobr>" . $owner->name_short . "</nobr> ";
			}
			else if ($z->slot == 3){ 
				$total->o3++; 
				$owner = new Owner; $owner->Information($z->user_id); $total->o3_names .= "<nobr>" . $owner->name_short . "</nobr> ";
			}
			else if ($z->slot == 4){ 
				$total->o4++; 
				$owner = new Owner; $owner->Information($z->user_id); $total->o4_names .= "<nobr>" . $owner->name_short . "</nobr> ";
			}
			else if ($z->slot == 5){ 
				$total->o5++; 
				$owner = new Owner; $owner->Information($z->user_id); $total->o5_names .= "<nobr>" . $owner->name_short . "</nobr> ";
			}
			else if ($z->slot == 6){ 
				$total->o6++; 
				$owner = new Owner; $owner->Information($z->user_id); $total->o6_names .= "<nobr>" . $owner->name_short . "</nobr> ";
			}
			else if ($z->slot == 7){ 
				$total->o7++; 
				$owner = new Owner; $owner->Information($z->user_id); $total->o7_names .= "<nobr>" . $owner->name_short . "</nobr> ";
			}
			else if ($z->slot == 8){ 
				$total->o8++; 
				$owner = new Owner; $owner->Information($z->user_id); $total->o8_names .= "<nobr>" . $owner->name_short . "</nobr> ";
			}
		
			// This will show owners initials and team abbreviation
		
		}
		else {
			$owner = new Owner; $owner->Information($y->owner_id); 
			$total->owners_notvoting .= "<nobr>" . $owner->name_short . "</nobr> ";
			$notify->owners .= "$owner->id|";
		}
		
		
	}

	$option1 = $core->option1;
	$option2 = $core->option2;
	$option3 = $core->option3;
	$option4 = $core->option4;
	$option5 = $core->option5;
	$option6 = $core->option6;
	$option7 = $core->option7;
	$option8 = $core->option8;

//	$display_owners = ($total->votes >= 10 ? $total->owners_voting : "10 Owners must vote of a poll for the owners list to show.");
	$display_nonowners = ($total->votes >= 10 ? $total->owners_notvoting : "All Owners have voted on this poll.");

	$total->o1 = ($total->o1 ? $total->o1 : 0);
	$total->o2 = ($total->o2 ? $total->o2 : 0);
	$total->o3 = ($total->o3 ? $total->o3 : 0);
	$total->o4 = ($total->o4 ? $total->o4 : 0);
	$total->o5 = ($total->o5 ? $total->o5 : 0);
	$total->o6 = ($total->o6 ? $total->o6 : 0);
	$total->o7 = ($total->o7 ? $total->o7 : 0);
	$total->o8 = ($total->o8 ? $total->o8 : 0);

	$total->o1_pct = ($total->o1 ? round($total->o1/$total->votes,2)*100 . "%" : NULL);
	$total->o2_pct = ($total->o2 ? round($total->o2/$total->votes,2)*100 . "%" : NULL);
	$total->o3_pct = ($total->o3 ? round($total->o3/$total->votes,2)*100 . "%" : NULL);
	$total->o4_pct = ($total->o4 ? round($total->o4/$total->votes,2)*100 . "%" : NULL);
	$total->o5_pct = ($total->o5 ? round($total->o5/$total->votes,2)*100 . "%" : NULL);
	$total->o6_pct = ($total->o6 ? round($total->o6/$total->votes,2)*100 . "%" : NULL);
	$total->o7_pct = ($total->o7 ? round($total->o7/$total->votes,2)*100 . "%" : NULL);
	$total->o8_pct = ($total->o8 ? round($total->o8/$total->votes,2)*100 . "%" : NULL);

	$total->o1_meter = "<div class='vote-back'><img src='/objects/images/vote-meter.png' border='0' style='display: block; height: 14px; width:" . $total->o1_pct ."'></div>" ;
	$total->o2_meter = "<div class='vote-back'><img src='/objects/images/vote-meter.png' border='0' style='display: block; height: 14px; width:" . $total->o2_pct ."'></div>" ;
	$total->o3_meter = "<div class='vote-back'><img src='/objects/images/vote-meter.png' border='0' style='display: block; height: 14px; width:" . $total->o3_pct ."'></div>" ;
	$total->o4_meter = "<div class='vote-back'><img src='/objects/images/vote-meter.png' border='0' style='display: block; height: 14px; width:" . $total->o4_pct ."'></div>" ;
	$total->o5_meter = "<div class='vote-back'><img src='/objects/images/vote-meter.png' border='0' style='display: block; height: 14px; width:" . $total->o5_pct ."'></div>" ;
	$total->o6_meter = "<div class='vote-back'><img src='/objects/images/vote-meter.png' border='0' style='display: block; height: 14px; width:" . $total->o6_pct ."'></div>" ;
	$total->o7_meter = "<div class='vote-back'><img src='/objects/images/vote-meter.png' border='0' style='display: block; height: 14px; width:" . $total->o7_pct ."'></div>" ;
	$total->o8_meter = "<div class='vote-back'><img src='/objects/images/vote-meter.png' border='0' style='display: block; height: 14px; width:" . $total->o8_pct ."'></div>" ;

	$display->option1 = ($option1 ? "<tr class='a'><td><strong>$core->option1</strong></td><td>$total->o1_meter<div style='font-size: 11px;' class='dim'>$total->o1_names</div></td><td width='10%' class='r'>$total->o1</t><td class='r'>$total->o1_pct</td></tr>" : NULL);
	$display->option2 = ($option2 ? "<tr class='b'><td><strong>$core->option2</strong></td><td>$total->o2_meter<div style='font-size: 11px;' class='dim'>$total->o2_names</div></td><td width='10%' class='r'>$total->o2</t><td class='r'>$total->o2_pct</td></tr>" : NULL);
	$display->option3 = ($option3 ? "<tr class='a'><td><strong>$core->option3</strong></td><td>$total->o3_meter<div style='font-size: 11px;' class='dim'>$total->o3_names</div></td><td width='10%' class='r'>$total->o3</t><td class='r'>$total->o3_pct</td></tr>" : NULL);
	$display->option4 = ($option4 ? "<tr class='b'><td><strong>$core->option4</strong></td><td>$total->o4_meter<div style='font-size: 11px;' class='dim'>$total->o4_names</div></td><td width='10%' class='r'>$total->o4</td><td class='r'>$total->o4_pct</td></tr>" : NULL);
	$display->option5 = ($option5 ? "<tr class='a'><td><strong>$core->option5</strong></td><td>$total->o5_meter<div style='font-size: 11px;' class='dim'>$total->o5_names</div></td><td width='10%' class='r'>$total->o5</td><td class='r'>$total->o5_pct</td></tr>" : NULL);
	$display->option6 = ($option6 ? "<tr class='b'><td><strong>$core->option6</strong></td><td>$total->o6_meter<div style='font-size: 11px;' class='dim'>$total->o6_names</div></td><td width='10%' class='r'>$total->o6</td><td class='r'>$total->o6_pct</td></tr>" : NULL);
	$display->option7 = ($option7 ? "<tr class='a'><td><strong>$core->option7</strong></td><td>$total->o7_meter<div style='font-size: 11px;' class='dim'>$total->o7_names</div></td><td width='10%' class='r'>$total->o7</td><td class='r'>$total->o7_pct</td></tr>" : NULL);
	$display->option8 = ($option8 ? "<tr class='b'><td><strong>$core->option8</strong></td><td>$total->o8_meter<div style='font-size: 11px;' class='dim'>$total->o8_names</div></td><td width='10%' class='r'>$total->o8</td><td class='r'>$total->o8_pct</td></tr>" : NULL);





	$option1 = $core->option1 ? "<tr valign='top'><td><strong>$core->option1</strong></td><td>$option1_results</td></tr>" : NULL;

	$link->notify = $notify->owners ? "<a href='?action=poll_notify&id=$core->id_encode&owners=$notify->owners'>Notify Owners</a>" : NULL;


	print ("
		
		<div class='b4' style='padding-bottom: 20px;'>
			<div style='font-size: 22px;'>$core->link</div>
			<div>$date</div>
			<div class='block'>
				$core->body
			</div>
			<div class='bar'>$core->link_openclose $core->link_showowners</div>
			<div style='padding-left: 20px;'>
				<table>
				$display->option1
				$display->option2
				$display->option3
				$display->option4
				$display->option5
				$display->option6
				$display->option7
				$display->option8
				<tr>
					<td width='40%'></td>
					<td width='30%'></td>
					<td width='15%'></td>
					<td width='15%'></td>
				</tr>
				</table>
				<div>
					<em>Non-votes</em> <span style='color: #666'>$total->owners_notvoting</span> $link->notify
				</div>
			</div>
		</div>
	");

	unset($notify,$display,$total);

}


?>