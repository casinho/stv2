<?php 

?>

<div class="kommentar mt1 kommentar-liste">
<h2 class="s11 mb10">
<?php echo ($data['poster_id'] > 0) ? CHtml::link($data['name'],Yii::app()->createUrl("member/detail",array("id"=>$data['poster_id'],"seo"=>GFunctions::normalisiereString($data['name'])))) : $data['name'];?>
<?php 

if( (Yii::app()->user->checkAccess('KommentarAdmin') === true) || (Yii::app()->user->checkAccess('Eigene Kommentare bearbeiten',array('startwert'=>$data['datum'],'user_id'=>$data['poster_id']))===true) ) {
?>	
	<span class="fr">
	<?php echo TbHtml::buttonDropdown('<i class="icon-cog cp orange fr ml5">&nbsp;</i>', array(
    	array('label' => Yii::t('global','bearbeiten'), 'url' => Yii::app()->createUrl('kommentarZuweisung/update',array('id'=>$data['kommentar_id'],'seo'=>'default')), 'class'=>'updateKommentarButton', 'data-id'=> $data['kommentar_id'],'icon'=>'edit'),
    	array('label' => Yii::t('global','loeschen'), 'url' => Yii::app()->createUrl('kommentarZuweisung/delete',array('id'=>$data['kommentar_id'],'seo'=>'default')), 'class'=>'deleteKommentarButton', 'data-id'=> $data['kommentar_id'],'icon'=>'trash'),
    	#TbHtml::menuDivider(),
    	#array('label' => Yii::t('global','user_sperren'), 'url' => '#'),
    	),array('class'=>'nobutton')); 
    ?>	
	</span>
<?php 
}
?>	
<?php echo CHtml::tag('span',array('class'=>'fr'),Yii::app()->dateFormatter->formatDateTimeAnzeige($data['datum'],'medium','short',' - ',true));?>	
</h2>

<?php 
	echo nl2br($data['kommentar']); 
?>
</div>