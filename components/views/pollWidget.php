<div class="row-fluid">
	<div class="span12">
		<div class="info mb10" style="padding:15px;">
<?php 
if($this->showThreadTitle == true) {
	echo CHtml::tag('p',array('class'=>'p5 fb m0'),$this->thread->thread_titel);
} else {
	echo TbHtml::tag('b',array(),Yii::t('forum','umfrage'));	
}
?>
			<table class="mt10 mb10 poll poll">
				<thead>
					<tr>
						<th style="min-width:20%;white-space:nowrap;"><?php echo $this->thread->thread_titel; ?></th>
						<th style="min-width:60%;">&nbsp;</th>
						<th class="ar">%</th>
						<th class="ar"><?php  echo Yii::t('forum','stimmen');?></th>
					</tr>
				</thead>
				<tbody>
	<?php foreach($this->optionen as $k => $v) {?>		
					<tr>
						<td class="pr10">
							<?php echo $v->option; ?>
						</td>
						<td class="pr10"><div class="bgPollResult s10 pl5 pr5" style="width:<?php echo Yii::app()->numberFormatter->format('#0',$v->getProzent($this->gesamtstimmen));?>%;">&nbsp;</div></td>
						<td class="ar">&nbsp;<?php echo Yii::app()->numberFormatter->format('#,#00.0',$v->getProzent($this->gesamtstimmen)); ?>&nbsp;</td>
						<td class="ar"><?php echo Yii::app()->numberFormatter->formatDecimal($v->count_votes); ?></td>
					</tr>
	<?php }?>
					<tr class="bggrau">
						<td>
			
						</td>
						<td class="ar" style="white-space:nowrap;" colspan="3">
							<?php echo Yii::t('forum','stimmen_gesamt',array('{stimmen}'=>Yii::app()->numberFormatter->formatDecimal($this->gesamtstimmen))); ?>
						</td>
					</tr>
				</tbody>		
			</table>
<?php 
	if($this->enddatum) {
		echo CHtml::tag('p',array('class'=>'p5'),$this->enddatum);
	}
?>		

	
	</div>
</div>
<?php 
if($this->showThreadTitle == true) {
?>
<div class="box-footer">
	<?php echo ForumThread::getHtmlLinkStatic($this->thread,array(),Yii::t('global','zur_diskussion')); ?>
</div>
<?php 
}
?>