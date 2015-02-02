<?php if(isset($this->bild)) {?>		
		<div class="three columns">
			<div class="row">
				<div class="twelve columns">
					<?php echo $this->form->labelEx($this->bild,'bildtitel'); ?>
					<?php echo $this->form->textField($this->bild,'bildtitel'); ?>
					<?php echo $this->form->error($this->bild,'bildtitel'); ?>
				</div>
			</div>
			
			<div class="row">
				<div class="twelve columns">
				<?php
				if($this->useXUpload) {
					$this->widget('xupload.XUpload', array(
	                    'url' => $this->createUrl($this->bild->zuordnung.'/upload'),
	                    'model' => $this->xupload,
	                    'htmlOptions' => array('id' => $this->bild->zuordnung.'-form'),
	                    'attribute' => 'file',
	                    'multiple' => false,
					));
				} else {
					echo $this->form->labelEx($this->bild, 'bild');
					echo $this->form->fileField($this->bild, 'bild'); 
					echo $this->form->error($this->bild, 'bild');
				}
				?>
				</div>
			</div>
<!-- 			
			<div class="row">
				<div class="twelve columns">
				<?php echo $this->form->labelEx($this->bild,'beschreibung'); ?>
				<?php echo $this->form->textField($this->bild,'beschreibung'); ?>
				<?php echo $this->form->error($this->bild,'beschreibung	'); ?>
				</div>
			</div>
-->			
			<div class="row">
				<div class="twelve columns">
				<?php echo $this->form->labelEx($this->bild,'quelle_id'); ?>
                <?php 
                		echo CHtml::dropDownList('Bild[quelle_id]', 
							'', 
                            CHtml::listData(Quelle::model()->findAll(array('order' => 'quelle')),'quelle_id','quelle',''),
                            array('prompt'=>'Bitte wählen', 'class' => 'js_chosen_src', 'options' => array($this->bild->quelle_id => array('selected' => 'selected'))));
                ?>	


				<?php echo $this->form->error($this->bild,'quelle'); ?>
				</div>
			</div>
			<div class="row">
				<div class="twelve columns">
				<?php echo $this->form->hiddenField($this->bild,'zuordnung'); ?>
				</div>
			</div>			
		</div>
<script type="text/javascript"> 
	$(".js_chosen_src").chosen({
		create_option: function(term){
			var chosen = this;
			$.post('/quelle/add', {neue_quelle: term}, function(data){
				chosen.append_option({
				value: 'value-' + data.quelle_id,
				text: data.quelle
			},"json");
		});
		},
		allow_single_deselect:true,
		create_option_text: 'Bildquelle erstellen',
		// persistent_create_option decides if you can add any term, even if part
		// of the term is also found, or only unique, not overlapping terms
		persistent_create_option: true
	});
</script>	 
		
<?php } ?>		