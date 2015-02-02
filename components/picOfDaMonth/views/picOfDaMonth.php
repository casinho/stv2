<h1>SanTiTan][<?php echo Yii::t('global','picofdamonth'); ?></h1>
<div class="kommentar ac">
	<a href="<?php echo Yii::app()->createUrl("potm/detail",array("id"=>$potm->id,"seo"=>GFunctions::normalisiereString($potm->name))); ?>">
	<?php
	$this->widget('ext.SAImageDisplayer', array(
	    'image' => $potm->url ? $potm->url : 'no_picture.jpg',
	    'size' => 'medium',
	    'group' => 'potm',
		'title' => $potm->name ? $potm->name : null,
		'alt' => $potm->name ? $potm->name : null,
		'class' => 'mt3 ac'
	));
	?>
	</a>
	<?php 
	echo CHtml::tag('br');
	echo CHtml::tag('br');
	echo CHtml::link($potm->name,Yii::app()->createUrl("potm/detail",array("id"=>$potm->id,"seo"=>GFunctions::normalisiereString($potm->name))),array('class'=>'fb'));
	echo CHtml::tag('br');
	
	$kommentare = KommentarZuweisung::holeAnzahlKommentare('potm',$potm->id);
	$uebersetzung['{Kommentare}'] = Yii::t('news', '0#Kommentare|1#Kommentar|n>=2#Kommentare', array($kommentare));
	echo CHtml::tag('span',array('class'=>'s10','style'=>'color:#ccc;'),Yii::t('news','anzahl_kommentare',$uebersetzung));
	?>	

</div>