<?
set_time_limit(600000);
require_once($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");
header("Content-Type: text/plain");


print "- Batters\n";

$query = ("
	SELECT 
		pid,
		franchise_id,
		organization,
		type,
		SUM(g) as g, 
		SUM(gs) as gs, 
		SUM(ab) as ab, 
		SUM(pa) as pa, 
		SUM(h) as h, 
		SUM(h)-SUM(db)-SUM(tr)-SUM(hr) as si, 
		SUM(db) as db, 
		SUM(tr) as tr, 
		SUM(hr) as hr, 
		SUM(tb) as tb, 
		SUM(r) as r, 
		SUM(rbi) as rbi, 
		SUM(bb) as bb, 
		SUM(k) as k, 
		SUM(sb) as sb, 
		SUM(bb) as bb, 
		SUM(cs) as cs, 
		SUM(sh) as sh, 
		SUM(sf) as sf, 
		SUM(iw) as iw, 
		SUM(hbp) as hbp, 
		SUM(gdp) as gdp, 
		position,
		ROUND(SUM(h)/SUM(ab),3) AS avg, 
		ROUND((SUM(H)+SUM(BB)+SUM(HBP))/(SUM(AB)+SUM(BB)+SUM(HBP)+SUM(SF)),3) AS obp,
		ROUND(((SUM(H)-(SUM(DB)+SUM(TR)+SUM(HR)))+(SUM(DB)*2)+(SUM(TR)*3)+(SUM(HR)*4))/SUM(AB),3) AS slg,
		ROUND((SUM(H)+SUM(BB)+SUM(HBP))/(SUM(AB)+SUM(BB)+SUM(HBP)+SUM(SF))+((SUM(H)-(SUM(DB)+SUM(TR)+SUM(HR)))+(SUM(DB)*2)+(SUM(TR)*3)+(SUM(HR)*4))/SUM(AB),3) AS ops,
		ROUND(((((((SUM(ab)+SUM(bb)+SUM(hbp))*2.4)+(SUM(h)+SUM(bb)-SUM(cs)+SUM(hbp)))*(((SUM(ab)+SUM(bb)+SUM(hbp))*3)+(((SUM(h)-SUM(db)-SUM(tr)-SUM(hr))+(SUM(db)*2)+(SUM(tr)*3)+(SUM(hr)*4))+(0.24*(SUM(bb)+SUM(hbp)))+(0.62*SUM(sb))-(0.03*SUM(k)))))/((SUM(ab)+SUM(bb)+SUM(hbp))*9))-((SUM(ab)+SUM(bb)+SUM(hbp))*0.9)),1) AS rc,
		ROUND((((((((SUM(ab)+SUM(bb)+SUM(hbp))*2.4)+(SUM(h)+SUM(bb)-SUM(cs)+SUM(hbp)))*(((SUM(ab)+SUM(bb)+SUM(hbp))*3)+(((SUM(h)-SUM(db)-SUM(tr)-SUM(hr))+(SUM(db)*2)+(SUM(tr)*3)+(SUM(hr)*4))+(0.24*(SUM(bb)+SUM(hbp)))+(0.62*SUM(sb))-(0.03*SUM(k)))))/((SUM(ab)+SUM(bb)+SUM(hbp))*9))-((SUM(ab)+SUM(bb)+SUM(hbp))*0.9))/(SUM(ab)-SUM(h)+SUM(sh)+SUM(sh)+SUM(sf)+SUM(cs)+SUM(gdp))*27),1)    AS rc27,
		ROUND(((SUM(H)-(SUM(DB)+SUM(TR)+SUM(HR)))+(SUM(DB)*2)+(SUM(TR)*3)+(SUM(HR)*4))/SUM(AB),3) - ROUND(SUM(h)/SUM(ab),3) AS iso,
		ROUND(SUM(ab)/SUM(db),1) AS abdb,
		ROUND(SUM(ab)/SUM(tr),1) AS abtr,
		ROUND(SUM(ab)/SUM(hr),1) AS abhr,
		ROUND(SUM(ab)/SUM(bb),1) AS abbb,
		ROUND(SUM(ab)/SUM(k),1) AS abk
	FROM statistics_batting 
	WHERE year = 2002 AND organization = 1 AND type = 2
	GROUP BY pid, franchise_id 
	ORDER BY organization
");

$result = db::Q()->prepare($query);
$result->execute();

while ($x = $result->fetch(PDO::FETCH_OBJ)){

	$player = new Player;
	$player->Information($x->pid);
	$player->Statistics($x->organization,$x->season,$x->type,$x->franchise_id);
	$player->Vintage($x->pid,$x->season);

	print "$player->name $x->year $x->organization $x->split ($x->hr)\n";

	$dataset = array($x->pid,$x->franchise_id,8,2002,0,$player->vintage,$x->organization,$x->type,$x->position,0,0,$x->g,$x->gs,$x->ab,$x->h,$x->si,$x->db,$x->tr,$x->hr,$x->tb,$x->r,$x->rbi,$x->iw,$x->hbp,$x->bb,$x->k,$x->sb,$x->cs,$x->sh,$x->sf,$x->gdp,$x->bip);

	$yinsert = "INSERT INTO statistics (pid,franchise_id,season,year,split,vintage,organization,type,position,league,current,g,gs,bab,bh,bsi,bdb,btr,bhr,btb,br,brbi,biw,bhbp,bbb,bk,bsb,bcs,bsh,bsf,bgdp,bbip) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
	$yresult = db::Q()->prepare($yinsert);
	$yresult->execute($dataset);	



}


print "\n - Pitchers\n";


$query = ("
	SELECT 
		pid,
		franchise_id,
		organization,
		type,
		franchise_id,
		season,
		year,
		SUM(g) as g, 
		SUM(w) as w, 
		SUM(l) as l, 
		SUM(s) as s, 
		SUM(cg) as cg, 
		SUM(gs) as gs, 
		SUM(sho) as sho, 
		SUM(inn) as inn, 
		SUM(h) as h, 
		SUM(hr) as hr, 
		SUM(r) as r, 
		SUM(er) as er, 
		SUM(bb) as bb, 
		SUM(k) as k, 
		SUM(qs) as qs, 
		SUM(gf) as gf, 
		SUM(hld) as hld, 
		SUM(bs) as bs, 
		SUM(ir) as ir, 
		SUM(irs) as irs, 
		SUM(hbp) as hbp, 
		SUM(iw) as iw, 
		SUM(gdp) as gdp, 
		SUM(bk) as bk, 
		SUM(wp) as wp, 
		SUM(pch) as pch, 
		SUM(bip) as bip, 
		SUM(sb) as sb, 
		SUM(cs) as cs, 
		SUM(pk) as pk, 
		SUM(si) as si, 
		SUM(db) as db, 
		SUM(tr) as tr, 
		SUM(sh) as sh, 
		SUM(sf) as sf, 
		position,
		ROUND(SUM(h)/SUM(ab),3) AS avg, 
		ROUND(SUM(ER)*9/(SUM(INN)/3),2) AS era, 
		ROUND((SUM(BB)+SUM(H))/(SUM(INN)/3),3) as whip, 
		ROUND((SUM(H)-SUM(HR))/(SUM(AB)-SUM(K)-SUM(HR)+SUM(SF)),3) as babip, 
		ROUND((SUM(H)+SUM(BB)+SUM(HBP))/(SUM(AB)+SUM(BB)+SUM(HBP)+SUM(SF))+((SUM(H)-(SUM(DB)+SUM(TR)+SUM(HR)))+(SUM(DB)*2)+(SUM(TR)*3)+(SUM(HR)*4))/SUM(AB),3) AS ops 
	FROM statistics_pitching 
	WHERE year = 2002 AND organization = 1 AND type = 2
	GROUP BY pid, franchise_id 
	ORDER BY organization

");

$result = db::Q()->prepare($query);
$result->execute();

while ($x = $result->fetch(PDO::FETCH_OBJ)){

	$player = new Player;
	$player->Information($x->pid);
	$player->Statistics($x->organization,$x->season,$x->type,$x->franchise_id);
	$player->Vintage($x->pid,$x->season);

	print "$player->name $x->year $x->organization $x->split ($x->db)\n";

	if ($player->season_sid){
		// Update

		$dataset = array($x->g,$x->gs,$x->gf,$x->cg,$x->w,$x->l,$x->hld,$x->s,$x->bs,$x->sho,$x->inn,$x->r,$x->er,$x->iw,$x->hbp,$x->bb,$x->k,$x->bk,$x->wp,$x->gdp,$x->pch,$x->qs,$x->rs,$x->bf,$x->ab,$x->h,$x->si,$x->db,$x->tr,$x->hr,$x->tb,$x->sh,$x->sf,$x->bip,$x->ir,$x->irs,$x->sb,$x->cs,$x->pk);

		$yinsert = "UPDATE statistics SET g=?,gs=?,pgf=?,pcg=?,pw=?,pl=?,phld=?,ps=?,pbs=?,psho=?,pinn=?,pr=?,per=?,piw=?,phbp=?,pbb=?,pk=?,pbk=?,pwp=?,pgdp=?,ppch=?,pqs=?,prs=?,pbf=?,pab=?,ph=?,psi=?,pdb=?,ptr=?,phr=?,ptb=?,psh=?,psf=?,pbip=?,pir=?,pirs=?,psb=?,pcs=?,ppk=? WHERE id = $player->season_sid LIMIT 1";
		$yresult = db::Q()->prepare($yinsert);
		$yresult->execute($dataset);

		$player->type = "(Update)";
	
	}
	else {
		// Insert

		$dataset = array($x->pid,$x->franchise_id,$x->season,$x->year,0,$x->vintage,$x->organization,$x->type,$x->position,0,0,$x->g,$x->gs,$x->gf,$x->cg,$x->w,$x->l,$x->hld,$x->s,$x->bs,$x->sho,$x->inn,$x->r,$x->er,$x->iw,$x->hbp,$x->bb,$x->k,$x->bk,$x->wp,$x->gdp,$x->pch,$x->qs,$x->rs,$x->bf,$x->ab,$x->h,$x->si,$x->db,$x->tr,$x->hr,$x->tb,$x->sh,$x->sf,$x->bip,$x->ir,$x->irs,$x->sb,$x->cs,$x->pk);
	
		$yinsert = "INSERT INTO statistics (pid,franchise_id,season,year,split,vintage,organization,type,position,league,current,g,gs,pgf,pcg,pw,pl,phld,ps,pbs,psho,pinn,pr,per,piw,phbp,pbb,pk,pbk,pwp,pgdp,ppch,pqs,prs,pbf,pab,ph,psi,pdb,ptr,phr,ptb,psh,psf,pbip,pir,pirs,psb,pcs,ppk) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
		$yresult = db::Q()->prepare($yinsert);
		$yresult->execute($dataset);

		$player->type = "(Insert)";

	}


}





print "- Batters\n";

$query = ("
	SELECT 
		pid,
		franchise_id,
		organization,
		type,
		SUM(g) as g, 
		SUM(gs) as gs, 
		SUM(ab) as ab, 
		SUM(pa) as pa, 
		SUM(h) as h, 
		SUM(h)-SUM(db)-SUM(tr)-SUM(hr) as si, 
		SUM(db) as db, 
		SUM(tr) as tr, 
		SUM(hr) as hr, 
		SUM(tb) as tb, 
		SUM(r) as r, 
		SUM(rbi) as rbi, 
		SUM(bb) as bb, 
		SUM(k) as k, 
		SUM(sb) as sb, 
		SUM(bb) as bb, 
		SUM(cs) as cs, 
		SUM(sh) as sh, 
		SUM(sf) as sf, 
		SUM(iw) as iw, 
		SUM(hbp) as hbp, 
		SUM(gdp) as gdp, 
		position,
		ROUND(SUM(h)/SUM(ab),3) AS avg, 
		ROUND((SUM(H)+SUM(BB)+SUM(HBP))/(SUM(AB)+SUM(BB)+SUM(HBP)+SUM(SF)),3) AS obp,
		ROUND(((SUM(H)-(SUM(DB)+SUM(TR)+SUM(HR)))+(SUM(DB)*2)+(SUM(TR)*3)+(SUM(HR)*4))/SUM(AB),3) AS slg,
		ROUND((SUM(H)+SUM(BB)+SUM(HBP))/(SUM(AB)+SUM(BB)+SUM(HBP)+SUM(SF))+((SUM(H)-(SUM(DB)+SUM(TR)+SUM(HR)))+(SUM(DB)*2)+(SUM(TR)*3)+(SUM(HR)*4))/SUM(AB),3) AS ops,
		ROUND(((((((SUM(ab)+SUM(bb)+SUM(hbp))*2.4)+(SUM(h)+SUM(bb)-SUM(cs)+SUM(hbp)))*(((SUM(ab)+SUM(bb)+SUM(hbp))*3)+(((SUM(h)-SUM(db)-SUM(tr)-SUM(hr))+(SUM(db)*2)+(SUM(tr)*3)+(SUM(hr)*4))+(0.24*(SUM(bb)+SUM(hbp)))+(0.62*SUM(sb))-(0.03*SUM(k)))))/((SUM(ab)+SUM(bb)+SUM(hbp))*9))-((SUM(ab)+SUM(bb)+SUM(hbp))*0.9)),1) AS rc,
		ROUND((((((((SUM(ab)+SUM(bb)+SUM(hbp))*2.4)+(SUM(h)+SUM(bb)-SUM(cs)+SUM(hbp)))*(((SUM(ab)+SUM(bb)+SUM(hbp))*3)+(((SUM(h)-SUM(db)-SUM(tr)-SUM(hr))+(SUM(db)*2)+(SUM(tr)*3)+(SUM(hr)*4))+(0.24*(SUM(bb)+SUM(hbp)))+(0.62*SUM(sb))-(0.03*SUM(k)))))/((SUM(ab)+SUM(bb)+SUM(hbp))*9))-((SUM(ab)+SUM(bb)+SUM(hbp))*0.9))/(SUM(ab)-SUM(h)+SUM(sh)+SUM(sh)+SUM(sf)+SUM(cs)+SUM(gdp))*27),1)    AS rc27,
		ROUND(((SUM(H)-(SUM(DB)+SUM(TR)+SUM(HR)))+(SUM(DB)*2)+(SUM(TR)*3)+(SUM(HR)*4))/SUM(AB),3) - ROUND(SUM(h)/SUM(ab),3) AS iso,
		ROUND(SUM(ab)/SUM(db),1) AS abdb,
		ROUND(SUM(ab)/SUM(tr),1) AS abtr,
		ROUND(SUM(ab)/SUM(hr),1) AS abhr,
		ROUND(SUM(ab)/SUM(bb),1) AS abbb,
		ROUND(SUM(ab)/SUM(k),1) AS abk
	FROM statistics_batting 
	WHERE year = 2002 AND organization = 2 AND type = 2
	GROUP BY pid, franchise_id 
	ORDER BY organization
");

$result = db::Q()->prepare($query);
$result->execute();

while ($x = $result->fetch(PDO::FETCH_OBJ)){

	$player = new Player;
	$player->Information($x->pid);
	$player->Statistics($x->organization,$x->season,$x->type,$x->franchise_id);
	$player->Vintage($x->pid,$x->season);

	print "$player->name $x->year $x->organization $x->split ($x->hr)\n";

	$dataset = array($x->pid,$x->franchise_id,8,2002,0,$player->vintage,$x->organization,$x->type,$x->position,0,0,$x->g,$x->gs,$x->ab,$x->h,$x->si,$x->db,$x->tr,$x->hr,$x->tb,$x->r,$x->rbi,$x->iw,$x->hbp,$x->bb,$x->k,$x->sb,$x->cs,$x->sh,$x->sf,$x->gdp,$x->bip);

	$yinsert = "INSERT INTO statistics (pid,franchise_id,season,year,split,vintage,organization,type,position,league,current,g,gs,bab,bh,bsi,bdb,btr,bhr,btb,br,brbi,biw,bhbp,bbb,bk,bsb,bcs,bsh,bsf,bgdp,bbip) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
	$yresult = db::Q()->prepare($yinsert);
	$yresult->execute($dataset);	



}


print "\n - Pitchers\n";


$query = ("
	SELECT 
		pid,
		franchise_id,
		organization,
		type,
		franchise_id,
		season,
		year,
		SUM(g) as g, 
		SUM(w) as w, 
		SUM(l) as l, 
		SUM(s) as s, 
		SUM(cg) as cg, 
		SUM(gs) as gs, 
		SUM(sho) as sho, 
		SUM(inn) as inn, 
		SUM(h) as h, 
		SUM(hr) as hr, 
		SUM(r) as r, 
		SUM(er) as er, 
		SUM(bb) as bb, 
		SUM(k) as k, 
		SUM(qs) as qs, 
		SUM(gf) as gf, 
		SUM(hld) as hld, 
		SUM(bs) as bs, 
		SUM(ir) as ir, 
		SUM(irs) as irs, 
		SUM(hbp) as hbp, 
		SUM(iw) as iw, 
		SUM(gdp) as gdp, 
		SUM(bk) as bk, 
		SUM(wp) as wp, 
		SUM(pch) as pch, 
		SUM(bip) as bip, 
		SUM(sb) as sb, 
		SUM(cs) as cs, 
		SUM(pk) as pk, 
		SUM(si) as si, 
		SUM(db) as db, 
		SUM(tr) as tr, 
		SUM(sh) as sh, 
		SUM(sf) as sf, 
		position,
		ROUND(SUM(h)/SUM(ab),3) AS avg, 
		ROUND(SUM(ER)*9/(SUM(INN)/3),2) AS era, 
		ROUND((SUM(BB)+SUM(H))/(SUM(INN)/3),3) as whip, 
		ROUND((SUM(H)-SUM(HR))/(SUM(AB)-SUM(K)-SUM(HR)+SUM(SF)),3) as babip, 
		ROUND((SUM(H)+SUM(BB)+SUM(HBP))/(SUM(AB)+SUM(BB)+SUM(HBP)+SUM(SF))+((SUM(H)-(SUM(DB)+SUM(TR)+SUM(HR)))+(SUM(DB)*2)+(SUM(TR)*3)+(SUM(HR)*4))/SUM(AB),3) AS ops 
	FROM statistics_pitching 
	WHERE year = 2002 AND organization = 2 AND type = 2
	GROUP BY pid, franchise_id 
	ORDER BY organization

");

$result = db::Q()->prepare($query);
$result->execute();

while ($x = $result->fetch(PDO::FETCH_OBJ)){

	$player = new Player;
	$player->Information($x->pid);
	$player->Statistics($x->organization,$x->season,$x->type,$x->franchise_id);
	$player->Vintage($x->pid,$x->season);

	print "$player->name $x->year $x->organization $x->split ($x->db)\n";

	if ($player->season_sid){
		// Update

		$dataset = array($x->g,$x->gs,$x->gf,$x->cg,$x->w,$x->l,$x->hld,$x->s,$x->bs,$x->sho,$x->inn,$x->r,$x->er,$x->iw,$x->hbp,$x->bb,$x->k,$x->bk,$x->wp,$x->gdp,$x->pch,$x->qs,$x->rs,$x->bf,$x->ab,$x->h,$x->si,$x->db,$x->tr,$x->hr,$x->tb,$x->sh,$x->sf,$x->bip,$x->ir,$x->irs,$x->sb,$x->cs,$x->pk);

		$yinsert = "UPDATE statistics SET g=?,gs=?,pgf=?,pcg=?,pw=?,pl=?,phld=?,ps=?,pbs=?,psho=?,pinn=?,pr=?,per=?,piw=?,phbp=?,pbb=?,pk=?,pbk=?,pwp=?,pgdp=?,ppch=?,pqs=?,prs=?,pbf=?,pab=?,ph=?,psi=?,pdb=?,ptr=?,phr=?,ptb=?,psh=?,psf=?,pbip=?,pir=?,pirs=?,psb=?,pcs=?,ppk=? WHERE id = $player->season_sid LIMIT 1";
		$yresult = db::Q()->prepare($yinsert);
		$yresult->execute($dataset);

		$player->type = "(Update)";
	
	}
	else {
		// Insert

		$dataset = array($x->pid,$x->franchise_id,$x->season,$x->year,0,$x->vintage,$x->organization,$x->type,$x->position,0,0,$x->g,$x->gs,$x->gf,$x->cg,$x->w,$x->l,$x->hld,$x->s,$x->bs,$x->sho,$x->inn,$x->r,$x->er,$x->iw,$x->hbp,$x->bb,$x->k,$x->bk,$x->wp,$x->gdp,$x->pch,$x->qs,$x->rs,$x->bf,$x->ab,$x->h,$x->si,$x->db,$x->tr,$x->hr,$x->tb,$x->sh,$x->sf,$x->bip,$x->ir,$x->irs,$x->sb,$x->cs,$x->pk);
	
		$yinsert = "INSERT INTO statistics (pid,franchise_id,season,year,split,vintage,organization,type,position,league,current,g,gs,pgf,pcg,pw,pl,phld,ps,pbs,psho,pinn,pr,per,piw,phbp,pbb,pk,pbk,pwp,pgdp,ppch,pqs,prs,pbf,pab,ph,psi,pdb,ptr,phr,ptb,psh,psf,pbip,pir,pirs,psb,pcs,ppk) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
		$yresult = db::Q()->prepare($yinsert);
		$yresult->execute($dataset);

		$player->type = "(Insert)";

	}


}


?>