<?php 
	if($this->relevant === true) {
		$holder = 'bgholder';
?>
	<h2 class="mt1"><?php echo Yii::t('news','relevante_news'); ?></h2>
	
<?php } else {
		$holder = 'bgholder';
?>		
		<h2 class="mt1"><?php echo Yii::t('news','aktuelle_news'); ?></h2>
<?php }?>
		<div class="kommentar">

<?php

$this->widget('zii.widgets.CListView', array(
				'id'				=> 'news-list2',
				'dataProvider' 		=> $dataProvider,
				'itemView'			=> '_newsTeaser',
				'ajaxUpdate'		=> false,    
				'emptyText'			=> Yii::t('global','keine_relevanten_daten_vorhanden'),
				'template'			=> '{items}',
				'pager' 			=> false,
				/*
				'afterAjaxUpdate'		=> 'function() {
												initBGRows();
												checkBGRows();
												$(".reveal-button").on("click", function(event) {
													initModal(event);
												});
											}'
				*/							
				//'summaryText' 			=> '',
)); 	
?>
		</div>		

