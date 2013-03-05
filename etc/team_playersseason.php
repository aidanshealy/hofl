<?
set_time_limit(600000);
require_once($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");
header("Content-Type: text/plain");

$query = ("
	SELECT 
		id,
		pid,
		vintage,
		franchise_id,
		position,
		leaders,
		SUM(g) AS g, 
		SUM(gs) AS gs, 

		SUM(bpa) AS bpa, 
		SUM(bab) AS bab, 
		SUM(bh) AS bh, 
		SUM(bsi) AS bsi, 
		SUM(bdb) AS bdb, 
		SUM(btr) AS btr, 
		SUM(bhr) AS bhr, 
		SUM(br) AS br, 
		SUM(brbi) AS brbi, 
		SUM(bbb) AS bbb, 
		SUM(bk) AS bk, 
		SUM(bsb) AS bsb, 
		SUM(bbb) AS bbb, 
		SUM(btb) AS btb, 
		SUM(bcs) AS bcs, 
		SUM(bsh) AS bsh, 
		SUM(bsf) AS bsf, 
		SUM(biw) AS biw, 
		SUM(bhbp) AS bhbp, 
		SUM(bgdp) AS bgdp, 
		SUM(bpa) AS bpa, 
		SUM(bgw) AS bgw, 
		SUM(bci) AS bci, 
		MAX(bhsc) AS bhsc, 
		MAX(bhsl) AS bhsl, 

		SUM(blpa) AS blpa, 
		SUM(blab) AS blab, 
		SUM(blh) AS blh, 
		SUM(blsi) AS blsi, 
		SUM(bldb) AS bldb, 
		SUM(bltr) AS bltr, 
		SUM(blhr) AS blhr, 
		SUM(blrbi) AS blrbi, 
		SUM(blbb) AS blbb, 
		SUM(blk) AS blk, 
		SUM(brpa) AS brpa, 
		SUM(brab) AS brab, 
		SUM(brh) AS brh, 
		SUM(brsi) AS brsi, 
		SUM(brdb) AS brdb, 
		SUM(brtr) AS brtr, 
		SUM(brhr) AS brhr, 
		SUM(brrbi) AS brrbi, 
		SUM(brbb) AS brbb, 
		SUM(brk) AS brk, 
		
		ROUND(SUM(bh)/SUM(bab),3) AS bavg, 
		ROUND((SUM(bH)+SUM(bBB)+SUM(bHBP))/(SUM(bAB)+SUM(bBB)+SUM(bHBP)+SUM(bSF)),3) AS bobp,
		ROUND(((SUM(bH)-(SUM(bDB)+SUM(bTR)+SUM(bHR)))+(SUM(bDB)*2)+(SUM(bTR)*3)+(SUM(bHR)*4))/SUM(bAB),3) AS bslg,
		ROUND((SUM(bH)+SUM(bBB)+SUM(bHBP))/(SUM(bAB)+SUM(bBB)+SUM(bHBP)+SUM(bSF))+((SUM(bH)-(SUM(bDB)+SUM(bTR)+SUM(bHR)))+(SUM(bDB)*2)+(SUM(bTR)*3)+(SUM(bHR)*4))/SUM(bAB),3) AS bops,
		ROUND(((((((SUM(bab)+SUM(bbb)+SUM(bhbp))*2.4)+(SUM(bh)+SUM(bbb)-SUM(bcs)+SUM(bhbp)))*(((SUM(bab)+SUM(bbb)+SUM(bhbp))*3)+(((SUM(bh)-SUM(bdb)-SUM(btr)-SUM(bhr))+(SUM(bdb)*2)+(SUM(btr)*3)+(SUM(bhr)*4))+(0.24*(SUM(bbb)+SUM(bhbp)))+(0.62*SUM(bsb))-(0.03*SUM(bk)))))/((SUM(bab)+SUM(bbb)+SUM(bhbp))*9))-((SUM(bab)+SUM(bbb)+SUM(bhbp))*0.9)),1) AS brc,
		ROUND((((((((SUM(bab)+SUM(bbb)+SUM(bhbp))*2.4)+(SUM(bh)+SUM(bbb)-SUM(bcs)+SUM(bhbp)))*(((SUM(bab)+SUM(bbb)+SUM(bhbp))*3)+(((SUM(bh)-SUM(bdb)-SUM(btr)-SUM(bhr))+(SUM(bdb)*2)+(SUM(btr)*3)+(SUM(bhr)*4))+(0.24*(SUM(bbb)+SUM(bhbp)))+(0.62*SUM(bsb))-(0.03*SUM(bk)))))/((SUM(bab)+SUM(bbb)+SUM(bhbp))*9))-((SUM(bab)+SUM(bbb)+SUM(bhbp))*0.9))/(SUM(bab)-SUM(bh)+SUM(bsh)+SUM(bsh)+SUM(bsf)+SUM(bcs)+SUM(bgdp))*27),1)    AS brc27,
		ROUND(((SUM(bH)-(SUM(bDB)+SUM(bTR)+SUM(bHR)))+(SUM(bDB)*2)+(SUM(bTR)*3)+(SUM(bHR)*4))/SUM(bAB),3) - ROUND(SUM(bh)/SUM(bab),3) AS biso,
		ROUND(((SUM(btb)+SUM(bhbp)+SUM(bbb)+SUM(bsb))-SUM(bcs))/((SUM(bab)-SUM(bh))+SUM(bcs)+SUM(bgdp)),3) AS btavg,
		ROUND((SUM(btb)-SUM(bh)+SUM(bbb)+SUM(bsb)-SUM(bcs))/SUM(bab),3) AS bsec,
		ROUND((SUM(bH)-SUM(bHR))/(SUM(bAB)-SUM(bK)-SUM(bHR)+SUM(bSF)),3) AS bbabip, 
		ROUND(SUM(bab)/SUM(bdb),1) AS babdb,
		ROUND(SUM(bab)/SUM(btr),1) AS babtr,
		ROUND(SUM(bab)/SUM(bhr),1) AS babhr,
		ROUND(SUM(bab)/SUM(bbb),1) AS babbb,
		ROUND(SUM(bab)/SUM(bk),1) AS babk,

		SUM(pw) AS pw, 
		SUM(pl) AS pl, 
		SUM(ps) AS ps, 
		SUM(pcg) AS pcg, 
		SUM(psho) AS psho, 
		SUM(pinn) AS pinn, 
		SUM(ph) AS ph, 
		SUM(phr) AS phr, 
		SUM(pr) AS pr, 
		SUM(per) AS per, 
		SUM(pbb) AS pbb, 
		SUM(pk) AS pk, 
		SUM(pqs) AS pqs, 
		SUM(pgf) AS pgf, 
		SUM(phld) AS phld, 
		SUM(pbs) AS pbs, 
		SUM(pir) AS pir, 
		SUM(pirs) AS pirs, 
		SUM(phbp) AS phbp, 
		SUM(piw) AS piw, 
		SUM(pgdp) AS pgdp, 
		SUM(pbk) AS pbk, 
		SUM(pwp) AS pwp, 
		SUM(ppch) AS ppch, 
		SUM(pbip) AS pbip, 
		SUM(psb) AS psb, 
		SUM(pcs) AS pcs, 
		SUM(ppk) AS ppk, 
		SUM(psi) AS psi, 
		SUM(pdb) AS pdb, 
		SUM(ptr) AS ptr, 
		SUM(psh) AS psh, 
		SUM(psf) AS psf, 
		ROUND((9*SUM(ph))/(SUM(pinn)/3),1) AS ph9,
		ROUND((9*SUM(phr))/(SUM(pinn)/3),1) AS phr9,
		ROUND((9*SUM(pbb))/(SUM(pinn)/3),1) AS pbb9,
		ROUND((9*SUM(pk))/(SUM(pinn)/3),1) AS pk9,
		ROUND(SUM(pirs)/SUM(pir),3)*100 AS pirsp, 
		ROUND(SUM(ph)/SUM(pab),3) AS poavg, 
		ROUND(SUM(per)*9/(SUM(pinn)/3),2) AS pera, 
		ROUND((SUM(pbb)+SUM(ph))/(SUM(pinn)/3),3) AS pwhip, 
		ROUND((SUM(pH)-SUM(pHR))/(SUM(pAB)-SUM(pK)-SUM(pHR)+SUM(pSF)),3) AS pbabip, 
		ROUND((SUM(pH)+SUM(pBB)+SUM(pHBP))/(SUM(pAB)+SUM(pBB)+SUM(pHBP)+SUM(pSF))+((SUM(pH)-(SUM(pDB)+SUM(pTR)+SUM(pHR)))+(SUM(pDB)*2)+(SUM(pTR)*3)+(SUM(pHR)*4))/SUM(pAB),3) AS poops 

	FROM statistics 
	WHERE organization = 1 AND season = 18 AND type = 2
	GROUP BY franchise_id, pid, year 
	ORDER BY franchise_id, g DESC
");
$result = db::Q()->prepare($query);
$result->bindParam(1, $pid);
$result->bindParam(2, $season);
$result->bindParam(3, $org);
$result->bindParam(4, $type);
$result->execute();

while ($x = $result->fetch(PDO::FETCH_OBJ)){


	if ($x->franchise_id != $team->franchise_id){ print "$count\n----\n\n"; unset($count); }
	
	$count++;
	
	$team = new Team;
	$team->Code($x->franchise_id, 1, 18);
	$team->Information($team->id);
	
	$player = new Player;
	$player->Information($x->pid);
	
	print "$count. $team->city - $player->name_pad $x->g\n";




}






?>