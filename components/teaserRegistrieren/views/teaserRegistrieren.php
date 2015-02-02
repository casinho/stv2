<?php
return false;
if($this->headline === true) {?>
<h3><?php echo Yii::t('global','jetzt_registrieren'); ?></h3>
<?php } ?>
<dl>
	<dt><?php echo Yii::t('global','noch_keinen_account'); ?></dt>
	<dd class="mt5"><i class="icon-ok fa-green"></i> <?php echo Yii::t('global','neue_daten_vorschlagen'); ?></dd>
	<dd><i class="icon-ok fa-green"></i> <?php echo Yii::t('global','beitraege_verfassen'); ?></dd>
	<dd><i class="icon-ok fa-green"></i> <?php echo Yii::t('global','news_vorschlagen'); ?></dd>
	<dd><i class="icon-ok fa-green"></i> <?php echo Yii::t('global','alben_bewerten'); ?></dd>
	<dd><i class="icon-ok fa-green"></i> <?php echo Yii::t('global','reviews_schreiben'); ?></dd>
</dl>
<?php if($this->link === true) {?>
<hr />
<i class="icon-chevron-right fa-orange"></i><?php  echo CHtml::link(Yii::t('global','jetzt_registrieren'),Yii::app()->createUrl('user/register')); ?>
<?php } ?>
