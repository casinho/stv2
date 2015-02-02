	<h1 class="mb1"><?php echo Yii::t('global', 'Aktuelle Rollen'); ?> </h1>
	<div class="kommentar">
	<?php
	$this->widget('zii.widgets.grid.CGridView', array(	  	
			'cssFile'				=> false,
			'dataProvider'			=> $this->dataProvider,
			'emptyText'				=> '',
			'showTableOnEmpty' 		=> false,
			'enablePagination' 		=> false,
	   		'summaryText' 			=> '',
		  	'columns'				=> $this->columns,
			));
	?>
	<br />
	<i class="icon icon-chevron-right"></i> <?php echo CHtml::link(Yii::t('global','rollenzuweisung_bearbeiten'),'/grbac/authZuweisung/update?id='.$this->user_id.'&lang=de')?>
	</div>