<?php 
/*
 * Comments....
 */

?>

<div class="row-fluid">
	
	<div class="span12">
	
		<div class="info mb10" style="padding:15px;" id="infoMargin">
<?php 
/*
 * Ich muss hier eine ZufallsID erstellen, da dieses Widget mehrfach auf einer
 * Seite vorkommen kann
 */
$pn = '';
$mail = '';
$note = 'x';

$random = rand(0,5000) + time();
$randomId = 'umfrage_'.$random;
echo CHtml::beginForm(Yii::app()->createUrl('poll/speichern'),'post',array('id'=>$randomId,'style'=>'margin:0'));
?>	
	<?php echo TbHtml::tag('b',array(),Yii::t('forum','umfrage')); ?>
<?php 
if($this->showThreadTitle == true) {
	echo CHtml::tag('p',array('class'=>'p5 fb m0'),$this->thread->thread_titel);
}
?>
	<table class="mt10 mb10">
<?php foreach($this->optionen as $k => $v) {?>		
	<tr>
		<td class="vt">
<?php 
		if($this->thread->poll_flag > 1) {
			echo CHtml::checkbox('option[]',array(),array('value'=>$v['option_id'],'class'=>'vt m0 multiple'));
		} else {
			$radioArray[$k] = $v; 
			echo CHtml::radioButton('option[]',array(),array('autocomplete'=>'off','class'=>'vt m0','value'=>$v['option_id']));
		}		
?>
		</td>
		<td style="width:100%;" class="vt">
			<?php echo $v->option; ?>
		</td>
	</tr>
<?php }?>
</table>
		<div class="alert alert-error dnu" id="ajax-error<?php echo $randomId;?>"></div>
<?php 
		if($this->enddatum) {
			echo CHtml::tag('p',array('class'=>'p5'),$this->enddatum);
		}


		$umfrageUrl = Yii::app()->createUrl('forum/poll',array('id'=>$this->forum->forum_id,'thread_id'=>$this->thread->thread_id));

		echo CHtml::hiddenField('thread_id',$this->thread->thread_id);
		echo CHtml::hiddenField('forum_id',$this->forum->forum_id);
		
		if(!Yii::app()->user->isGuest) {
		
			echo CHtml::ajaxButton(
				Yii::t('global','abstimmen'),
				Yii::app()->createUrl('poll/save'),
				array(
						'dataType'	=> 'json',
						'type' 		=> 'POST',
						'data' 		=> "js:$('#$randomId').serialize()",
						'beforeSend'=> 'function() {
											$("#'.$randomId.'").addClass("loading");
										}',
						'success'	=> 'function(data){
											if(data.case == "success") {
												$("#'.$randomId.'").load("'.$umfrageUrl.' #umfrage", function(response, status, xhr) {
													if(status == "success") {
														$("#'.$randomId.'").removeAttr("class");
														$("#'.$randomId.'").attr("class","alert-box success");
														$("#infoMargin").removeAttr("style");	
														$("#'.$randomId.'").html(response);
														$("#'.$randomId.'").removeClass("loading");
														
													}
												});
											} else {
												$("#'.$randomId.'").removeAttr("class");
												$("#'.$randomId.'").attr("class","alert-box alert");
												$("#'.$randomId.'").html(data.info);
												$("#'.$randomId.'").removeClass("loading");
												showErrors(data,"#ajax-error'.$randomId.'");
						                    }
				                        }',
				),
				array('class'=>'btn','selected'=>null, 'id' => $this->getId().'_btn0')
			);
		} else {
			echo TbHtml::button(Yii::t('global','abstimmen'),array('class'=>'loginLink'));
		}
				
?>		

<?php echo CHtml::endForm(); ?>			
		</div>
	
	</div>

</div>
<?php 
if($this->thread->poll_flag > 1) {
?>
<script type="text/javascript">
$(document).ready(function() {
	$('.multiple').on('click', function() {
		checkMultiple('multiple',<?php echo $this->thread['poll_flag']; ?>);
	});
	
});


function checkMultiple(klasse,limit) {

	var n = $('.'+klasse+':checked').length;
	if(n >= limit) {
		$('.'+klasse).each( function() {
			if(this.checked == false) {
				$(this).attr('disabled','disabled');	
				//alert('this is sparta');
			}
		});
	} else {
		$('.'+klasse).each( function() {
			if(this.checked == false) {
				$(this).removeAttr('disabled');	
			}
		});		
	}
//	alert(n);
}
</script>
<?php }?>