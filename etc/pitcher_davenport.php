<?
set_time_limit(600000);
require_once($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");
include ($_SERVER['DOCUMENT_ROOT'] . "/objects/third/scrape/index.php");
header("Content-Type: text/plain");



header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Content-Type: application/force-download");
header("Content-Type: application/octet-stream");
header("Content-Type: application/download");
header("Content-Disposition: attachment;filename=davenport_pitchers.csv");
header("Content-Transfer-Encoding: binary");





$run = $_REQUEST['run'];
$delete = $_REQUEST['delete'];

$query = ("
	SELECT 
		a.pid,
		a.id,
		b.year
	FROM player a
	LEFT JOIN statistics_vintages b ON b.pid = a.pid
	WHERE a.pid > 10000 AND b.year > 0
	GROUP BY b.pid, b.year
	HAVING SUM(b.pinn) >= 300 AND SUM(b.gs) >= 16
	ORDER BY a.name_last, a.name_first, b.year
");

$result = db::Q()->prepare($query);
$result->execute();

while ($x = $result->fetch(PDO::FETCH_OBJ)){

	$player = new Player;
	$player->Information($x->pid);

	$yquery = ("
		SELECT 
				SUM(pw) AS pw, 
				SUM(pl) AS pl, 
				SUM(ps) AS ps, 
				SUM(pinn) AS pinn, 
				SUM(pouts) AS pouts, 
				SUM(ph) AS ph, 
				SUM(phr) AS phr, 
				SUM(per) AS per, 
				SUM(pbb) AS pbb, 
				SUM(pk) AS pk, 
				SUM(phbp) AS phbp, 
				ROUND((9*SUM(ph))/(SUM(pouts)/3),1) AS ph9,
				ROUND((9*SUM(phr))/(SUM(pouts)/3),1) AS phr9,
				ROUND((9*SUM(pbb))/(SUM(pouts)/3),1) AS pbb9,
				ROUND((9*SUM(pk))/(SUM(pouts)/3),1) AS pk9,
				ROUND(3.1+(sum(phr)*13+(sum(pbb)+sum(phbp))*3-sum(pk)*2)/(sum(pouts)/3),3) AS pfip,
				ROUND(SUM(per)*9/(SUM(pouts)/3),2) AS pera, 
				ROUND((SUM(pbb)+SUM(ph))/(SUM(pouts)/3),3) AS pwhip
			FROM statistics_davenport
			WHERE pid = $player->pid AND year = $x->year
			GROUP BY pid, year
			LIMIT 1
	");
	
	$yresult = db::Q()->prepare($yquery);
	$yresult->execute();
	
	if ($y = $yresult->fetch(PDO::FETCH_OBJ)){

//		print "$player->name_pad $x->year ($y->pfip)\n";	


		$player->averages_pw = round(($y->pw/$y->pinn)*200);
		$player->averages_pl = round(($y->pl/$y->pinn)*200);
		$player->averages_ps = round(($y->ps/$y->pinn)*200);
		$player->averages_per = round(($y->per/$y->pinn)*200);
		$player->averages_pinn = number_format(round(($y->pinn/$y->pinn)*200),1);
		$player->averages_pouts = round(($y->pouts/$y->pinn)*200);
		$player->averages_ph = round(($y->ph/$y->pinn)*200);
		$player->averages_phr = round(($y->phr/$y->pinn)*200);
		$player->averages_pbb = round(($y->pbb/$y->pinn)*200);
		$player->averages_pk = round(($y->pk/$y->pinn)*200);
		$player->averages_phbp = round(($y->phbp/$y->pinn)*200);



		$results[] = array(
			$player->pid,
			$player->name_first,
			$player->name_last,
			$x->year,
			$player->averages_pw,
			$player->averages_pl,
			$player->averages_ps,
			$player->averages_pinn,
			$player->averages_pouts,
			$player->averages_ph,
			$player->averages_phr,
			$player->averages_per,
			$player->averages_pbb,
			$player->averages_pk,
			$player->averages_phbp,
			$y->ph9,
			$y->phr9,
			$y->pbb9,
			$y->pk9,
			$y->pera,
			$y->pwhip,
			$y->pfip
		);

	}	



}

// print_r($results);

outputCSV($results);


function outputCSV($data) {
    $outstream = fopen("php://output", "w");
    function __outputCSV(&$vals, $key, $filehandler) {
        fputcsv($filehandler, $vals); // add parameters if you want
    }
    array_walk($data, "__outputCSV", $outstream);
    fclose($outstream);
}
?>