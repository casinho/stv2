<h1 class="<?php echo $this->h1class; ?>">SanTiTan][<?php echo Yii::t('global','latest_wars'); ?></h1>

<div class="kommentar" id="wars">
<?php 
if($this->type != 'all' && $this->squad != false) {

	$t['{squad}']			= $this->squad->squad_name;
	$t['{spiele}'] 			= Yii::t('member','0#Spiele|1#Spiel|n>=2#Spiele',array($this->squad->getEinsaetze($this->squad->squad_id)));
	$t['{siege}']			= Yii::t('member','0#Siege|1#Sieg|n>=2#Siege',array($this->squad->getClanwarStatistik($this->squad->squad_id,3)));
	$t['{unentschieden}']	= Yii::t('member','0#Unentschieden|1#Unentschieden|n>=2#Unentschieden',array($this->squad->getClanwarStatistik($this->squad->squad_id,1)));
	$t['{niederlage}']		= Yii::t('member','0#Niederlage|1#Niederlage|n>=2#Niederlagen',array($this->squad->getClanwarStatistik($this->squad->squad_id,2)));
	
	echo Yii::t('member','squad_match_statistik_info',$t);
	echo CHtml::tag('br');
	echo CHtml::tag('br');

?>
<?php 
}
?>	
	<ul class="bxslider">
<?php 
foreach($cw as $k => $v) {
	$titel = Yii::t('clanwars','competition_vs_clan',array('{Clan}'=> $v['tag'], '{liga}'=>$v['liga_tag']));
	$linktitel = Yii::t('clanwars','match_vs_clan',array('{squad}' => $v['squad_tag'],'{Clan}'=> $v['clan'], '{liga}'=>$v['liga_tag']));
?>	
		<li>
			<div class="fb ac mb10 s14"><?php echo CHtml::link($titel,Yii::app()->createUrl('clanwars/detail',array('id'=>$v['id'],'seo'=>GFunctions::normalisiereString($linktitel)))); ?></div>
			<table class="fl ml10 mt10">
				<tr>
					<td class="ar pr10"><?php echo Yii::t('clanwars','datum')?>:</td>
					<td style="color:#ccc"><?php echo Yii::app()->dateFormatter->formatDateTime($v['datum'],'medium',false); ?></td>
				</tr>
				<tr>
					<td class="ar pr10"><?php echo Yii::t('clanwars','squad')?>:</td>
					<td style="color:#ccc"><?php echo $v['squad_tag']; ?></td>
				</tr>
				<tr>
					<td class="ar pr10"><?php echo Yii::t('clanwars','gegner')?>:</td>
					<td style="color:#ccc">
					<?php echo CHtml::link($v["clan"],Yii::app()->createUrl("clans/detail",array("id"=>$v["clan_id"],"seo"=>GFunctions::normalisiereString($v["clan"])))) ?>
					</td>
				</tr>
			</table>
			<div class="fr ar mr10 mt10">
<?php 		
echo Clanwars::getWertung($v['wertung'],'padding:8px 5px 5px 5px;margin:5px 0 0  0;',CHtml::tag('b',array('style'=>'font-size:23px;'),Clanwars::getScore($v)));
echo CHtml::tag('br');
$kommentare = KommentarZuweisung::holeAnzahlKommentare('clanwars',$v['id']);
$uebersetzung['{Kommentare}'] = Yii::t('news', '0#Kommentare|1#Kommentar|n>=2#Kommentare', array($kommentare));
echo CHtml::tag('span',array('class'=>'s10','style'=>'color:#ccc;'),Yii::t('news','anzahl_kommentare',$uebersetzung));
?>	
			</div>		
			<br class="cb" />
		</li>

<?php } ?>
	</ul>
</div>
<?php 
if($this->type == 'all') {
?>
<span class="lasche mt1" style="ma">
	<a href="<?php echo Yii::app()->createUrl('clanwars/index'); ?>"><?php echo Yii::t('global','uebersicht_aller_clanwars');?></a>
</span>
<?php 
} else {
?>
<span class="lasche mt1">
	<a href="<?php echo Yii::app()->createUrl('clanwars/squad',array('id'=>$this->type,'seo'=>GFunctions::normalisiereString($this->squad->squad_name))); ?>"><?php echo Yii::t('global','uebersicht_aller_squadwars');?></a>
</span>
<?php 
}
?>