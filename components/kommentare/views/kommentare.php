		<h1><?php echo Yii::t('global','kommentare')?></h1>
		<div class="info mt1 pt5" id="kommentare">
<?php 

if(Yii::app()->user->checkAccess('Freigeschaltet')===true) {

	$form = $this->beginWidget('CActiveForm', array(
    							'action'				=> Yii::app()->createUrl('kommentarZuweisung/create'),
								'id'					=> 'kommentar-form',
    							'enableAjaxValidation'	=> true,
     							'enableClientValidation'=> true,
						        'clientOptions'			=> array(
									'validatingCssClass'=>'loading',
							        'validateOnSubmit'	=> true,
										/*
							        	'beforValidate'			=> 'js:function(form){
																	alert("dgf");
																	$("#kommentare").addClass("loading");
																	alert("ok");
																	return true;	
																}',
										*/																
            							'afterValidate'		=> 'js:function(form,data,hasError){
            														if(!hasError){
						        										$("#kommentar-form > .clearValue").val("");
																		$("#kommentar-form > .hideMe").hide(500);
            															$("#kommentar-success").toggle(500);
                                   										$("#kommentare").removeClass("loading");
                                   										reloadContent();
						        										$("#kommentar-success").text(data.msg);
																		setTimeout(showForm,4000);						        										
                                									}
                        										}'
        						),
								'htmlOptions' => array('class'=>'mt5'),
));

?>
<?php 
//if(Yii::app()->user->isGuest) {
if(1==0) {
?>
<div class="row-fluid hideMe">
    <?php echo $form->labelEx($model,'name',array('class'=>'span4 s10')); ?>
    <?php echo $form->textField($model,'name',array('class'=>'span8 clearValue')); ?>
    <?php echo $form->error($model,'name'); ?>
</div>
<?php 
}
?>
<div class="row-fluid hideMe">
    <?php echo $form->labelEx($model,'kommentar',array('class'=>'s10')); ?>
    <?php echo $form->textArea($model,'kommentar',array('rows'=>6,'class'=>'span12 clearValue')); ?>
    <?php echo $form->error($model,'kommentar'); ?>
</div>
<?php echo $form->hiddenField($model,'zuweisung',array('value'=>$this->zuweisung)); ?>
<?php echo $form->hiddenField($model,'fremd_id',array('value'=>$this->fremd_id)); ?>
<?php echo TbHtml::submitButton(Yii::t('global','speichern'),array('class'=>'hideMe')); ?>
<?php echo TbHtml::button(Yii::t('global','ok'),array('class'=>'btn dnu fr okbtn')); ?>
<?php $this->endWidget(); ?>
<?php 
	} else {
		echo TbHtml::tag('p',array(),Yii::t('global','kommentar_verfassen_txt'));
		echo TbHtml::link(Yii::t('global','kommentar_verfassen'),Yii::app()->createUrl('user/login'),array('class'=>'btn loginBtn mt5 mb5','data-toggle' => 'modal', 'data-target' => '#myModal'));
	}
?>
<?php echo TbHtml::alert(TbHtml::ALERT_COLOR_SUCCESS, 'x', array('class'=>'dnu','id'=>'kommentar-success')); ?>
</div>
<?php
$this->widget('zii.widgets.CListView', array(
				'id'				=> 'kommentar_list',
				'dataProvider' 		=> $kommentare,
				'itemView'			=> '_kommentar',
				'ajaxUpdate'		=> true,    
				'emptyText'			=> Yii::t('global','noch_keine_kommentare'),

				//'viewData'			=> array('thread' => $this->thread, 'forum' => $this->forum),
				//'template'			=> "{items}\n{pager}",
				'sortableAttributes'=> array(
										//'erstellt_zeit'
										),
				'template'				=> '{summary}{pager}{items}{pager}',
				 										
				'pager' => array(
					'class'=>'CLinkPager',
					'cssFile' => false,
					'header' => false,
					'firstPageLabel' => '<span class="icon-double-angle-left"></span>',
					'prevPageLabel' => '<span class="icon-angle-left"></span>',
					'nextPageLabel' => '<span class="icon-angle-right"></span>',
					'lastPageLabel' => '<span class="icon-double-angle-right"></span>',
					'firstPageCssClass' => 'show',
					'previousPageCssClass' => 'show',
					'nextPageCssClass' => 'show',
					'lastPageCssClass' => 'show',
					'maxButtonCount'		=>7,
				),
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

<?php 

$jsButtonArray = array('data-dismiss' => 'modal',
						'color' => TbHtml::BUTTON_COLOR_WARNING,            			
						'type' => 'POST',
            			'data' => 'js:$("#deleteForm").serialize()',
						'beforeSend' 	=> 'function(){
							$("#modalKommentarContent").addClass("loading");
						}',
						'complete' 		=> 'function(){
							$("#modalKommentarContent").removeClass("loading");
						}',
						
						'success'=>'function(data){
							$("#modalKommentarContent").addClass("loading");
							if(data.status=="success") {
								$("#modalKommentarContent").html(data.info);
								$("#modalKommentarFooter").html(data.button);
								$("#modalKommentarContent").removeClass("loading");
								reloadContent();
			       			} else {
			        			formErrors("#login-form",data);
			       			}
			 		}');
 

$modalButtons1 = array();
$modalButtons1[] = TbHtml::ajaxSubmitButton(Yii::t('global','loeschen'),Yii::app()->createUrl('kommentarZuweisung/loeschen'),$jsButtonArray);
$modalButtons1[] = TbHtml::button(Yii::t('global','abbrechen'), array('data-dismiss' => 'modal')); 
    				
$this->widget('bootstrap.widgets.TbModal', array(
		'id' 		=> 'deleteKommentarModal',
    	'header' 	=> Yii::t('global','kommentar_loeschen'),
    	'content' 	=> '<div id="modalKommentarContent">inhalt ...</div>',
    	'footer' 	=> '<div id="modalKommentarFooter">'.implode(' ',$modalButtons1).'</div>',
));

$jsButtonArray = array('data-dismiss' => 'modal',
		'color' => TbHtml::BUTTON_COLOR_WARNING,
		'type' => 'POST',
		'data' => 'js:$("#update-kommentar-form").serialize()',
		'beforeSend' 	=> 'function(){
							$("#modalKommentarContent").addClass("loading");
						}',
		'complete' 		=> 'function(){
							$("#modalKommentarContent").removeClass("loading");
						}',

		'success'=>'function(data){
							$("#modalKommentarContent").addClass("loading");
							if(data.status=="success") {
								$("#updateModalKommentarContent").html(data.msg);
								$("#updateModalKommentarFooter").html(data.button);
								$("#updateModalKommentarContent").removeClass("loading");
								reloadContent();
			       			} else {
								formErrors("#update-kommentar-form",data);
			       			}
			 		}');


$modalButtons2 = array();
$modalButtons2[] = TbHtml::ajaxSubmitButton(Yii::t('global','speichern'),Yii::app()->createUrl('kommentarZuweisung/update'),$jsButtonArray);
$modalButtons2[] = TbHtml::button(Yii::t('global','abbrechen'), array('data-dismiss' => 'modal'));

$this->widget('bootstrap.widgets.TbModal', array(
		'id' 		=> 'updateKommentarModal',
		'header' 	=> Yii::t('global','kommentar_bearbeiten'),
		'content' 	=> '<div id="updateModalKommentarContent">inhalt ...</div>',
		'footer' 	=> '<div id="updateModalKommentarFooter">'.implode(' ',$modalButtons2).'</div>',
));


?>

<script type="text/javascript"> 

function setDefaultButtons() {
	$("#modalKommentarFooter").html('<?php echo implode(' ',$modalButtons1); ?>');
	$("#updateModalKommentarFooter").html('<?php echo implode(' ',$modalButtons2); ?>');
}

function reloadContent() {
	href = '<?php echo Yii::app()->request->requestUri; ?>';
	$.fn.yiiListView.update("kommentar_list", {url: href});
}

$("#deleteKommentarModal").on("hidden", function () {
	$("#modalKommentarFooter").html('<?php echo implode(' ',$modalButtons1); ?>');
});

function initDeleteButtons() {
	$(".deleteKommentarButton").live('click', function(event) {
		event.preventDefault();
		
		var li = $(this).closest("li");
		kommentar_id = li.attr('data-id');

		$.ajax({
			type: 'POST',
			data:  { kommentar_id : kommentar_id },
			url: "<?php echo Yii::app()->createUrl('kommentarZuweisung/delete'); ?>",
			success: function(r){
				$("#modalKommentarContent").html(r);
				$("#deleteKommentarModal").modal("show");
			}
			
		});
	});
	  	
}

$("#updateKommentarModal").on("hidden", function () {
	$("#updateModalKommentarFooter").html('<?php echo implode(' ',$modalButtons2); ?>');
});

function showForm() {
	$("#kommentar-success").toggle();
	$("#kommentar-form > .clearValue").val("");
	$("#KommentarZuweisung_kommentar").val('');
	$("#kommentar-form > .hideMe").show(500);
}


function initUpdateButtons() {
	$(".updateKommentarButton").live('click', function(event) {

		event.preventDefault();
		
		var li = $(this).closest("li");
		kommentar_id = li.attr('data-id');

		$.ajax({
			type: 'POST',
			data:  { kommentar_id : kommentar_id },
			url: "<?php echo Yii::app()->createUrl('kommentarZuweisung/update'); ?>",
			success: function(r){
				$("#updateModalKommentarContent").html(r);
				$("#updateKommentarModal").modal("show");
			}
			
		});
	});
	  	
}

$(document).ready(function() { 
	initDeleteButtons();
	initUpdateButtons();
});

</script>
