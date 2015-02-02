<h2 class="mt10">
<?php echo TbHtml::tag('i',array('class'=>'icon-smile orange mr2 s13 mt1'),'&nbsp;');?>
<?php echo Yii::t('global','smilies'); ?>
</h2>
<div class="kommentar mt1">
	<?php echo Yii::t('global','smiley_info'); ?>
		<table class="auflistung">
<?php 
	$i = 0;
	$anzahl = count($this->smilies)-1;
	foreach($this->smilies as $k => $v) {
		if($i == 0) {
			echo '<tr>';
		} elseif($i%5==0 && $i!=$anzahl) {
			echo '</tr>';
			echo '<tr>';
		} elseif($i==$anzahl && $i%5==0) {
			echo '<tr>';
		}
		
		if(isset($v['folder'])) {
			$path = $this->path.$v['folder'].'/';
		} else {
			$path = $this->path;
		}

		
		if(isset($v['ext'])) {
			$value = $path.$v['name'].'.'.$v['ext'];
		} else {
			$value = $path.$v['name'].'.gif';
		}

		

?>
		<td class="ac tb" style="height:30px;vertical-align:middle;width:20%">
			<input type="image" src="<?php echo $value; ?>" name="<?php echo $v['name']; ?>" title="<?php echo $v['title']; ?>" alt="<?php echo $v['title']; ?>" class="smilie" />
		</td>
<?php 
	if($i==$anzahl) {
		echo '</tr>';
	}
		$i+=1;
	}
?>		
	</table>				
</div>
<script type="text/javascript">
$('.smilie').click(function() {

	var ed = tinyMCE.get('<?php echo $this->editorId; ?>');
	var dom = ed.dom;

	file = $(this).attr('src');
	title= $(this).attr('title');
	
	ed.execCommand('mceInsertContent', false, dom.createHTML('img', {
		src : file,
		alt : title,
		title : title,
		border : 0,
        aspectratio: 1,		
	}));
	return false;
});
		
</script>
