<?php 

$this->widget('ext.SAImageDisplayer', array(
    'image' => isset($data['bigImage']) && $data['bigImage'] != null ? $data['bigImage']->file_name : 'no_pic_teaser_big.png',
    'size' => 'tiny',
    'group' => 'news',
	'title' => $data['titel'] ? $data['titel'] : null,
	'alt' => $data['titel'] ? $data['titel'] : null,
	'class' => 'fl mr10 mt5'
));
echo CHtml::tag('span',array('style'=>'color:#ccc'),Yii::app()->dateFormatter->formatDateTimeAnzeige($data['datum'],'medium','short',' - ',true));
echo CHtml::tag('br');
//echo CHtml::tag('span',array('class'=>'icon-chevron-right'),'&nbsp;');	
echo CHtml::link($data['titel'],Yii::app()->createUrl('news/detail',array('id'=>$data['id'],'seo'=>GFunctions::normalisiereString($data['titel']))),array('class'=>'fb'));
echo CHtml::tag('br'); 
/*
$kommentare = KommentarZuweisung::holeAnzahlKommentare('news',$data['id']);
$uebersetzung['{Kommentare}'] = Yii::t('news', '0#Kommentare|1#Kommentar|n>=2#Kommentare', array($kommentare));
echo CHtml::tag('span',array('class'=>'s10','style'=>'color:#ccc;'),Yii::t('news','anzahl_kommentare',$uebersetzung));
*/
echo CHtml::tag('br',array('style'=>'clear:both;'));
?>
