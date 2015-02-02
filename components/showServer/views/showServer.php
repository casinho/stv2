<h1 class="<?php echo $this->h1class; ?>">SanTiTan][<?php echo Yii::t('utserver','empfohlene_server'); ?></h1>

<div class="kommentar" id="server">
	
	<ul class="bxslider">
<?php 

#GFunctions::pre($xml);
#die();

foreach($xml as $k => $v) {
	if(isset($v->match)) {
		$mode = $v->match->gamemode;
		
		$mapname = (string)$v->match->mapname;
		
		$map = File::model()->findByAttributes(array('name'=>$mapname));
		//GFunctions::pre($map);
		if($map != null) {
			$mappic = $map->bild;
		} else {
			$mappic = '';
		}
?>	
		<li>
			
			<div class="fb ac s12 sliderHeadline">
				<?php 
					echo CHtml::link($v->serverdata->data,Yii::app()->createUrl("utserver/detail",array("id"=>$v->serverdata->serverid,"seo"=>GFunctions::normalisiereString($v->serverdata->data))));
				?>
			</div>
			<div class="ac fb"><?php echo $v->match->mapname; ?></div>
			<div class="ac db sliderImageHolder">
			<a href="<?php echo Yii::app()->createUrl("utserver/detail",array("id"=>$v->serverdata->serverid,"seo"=>GFunctions::normalisiereString($v->serverdata->data)));?>" class="db">	
			<?php 
			$this->widget('ext.SAImageDisplayer', array(
					'image' => !empty($mappic) ? $mappic : 'no_picture.jpg',
					'size' => 'small',
					'group' => 'maps',
					'title' => $v->match->mapname ? $v->match->mapname : null,
					'alt' => $v->match->mapname ? $v->match->mapname : null,
					'class' => 'sliderMapImage',
			));				
			?>			
			</a>
			</div>
			
			<div class="ac"><?php echo Yii::t('utserver','spieler')?>: <?php echo $v->match->numplayers.' / '.$v->match->maxplayers; ?></div>
			<br class="cb" />
			<br class="cb" />
		</li>
<?php } ?>
<?php } ?>
	</ul>
	
</div>

<span class="lasche mt1" style="ma">
	<a href="<?php echo Yii::app()->createUrl('utserver/index'); ?>"><?php echo Yii::t('utserver','uebersicht_aller_server');?></a>
</span>
