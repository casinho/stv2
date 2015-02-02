<div class="row">
	
	<div class="span12">
	
		<div class="mt3 mb10" style="padding:0 10px;">
			<marquee behavior="scroll" direction="left" scrollamount="2">
<?php 
 
$string = implode(' +++ ',$output);

echo ' +++ '.$string.' +++ ';
?>
			</marquee>
		</div>
	
	</div>
</div>
<script type="text/javascript">
$(document).ready(function(){
	$('marquee').marquee('pointer').mouseover(function () {
        $(this).trigger('stop');
    }).mouseout(function () {
        $(this).trigger('start');
    }).mousemove(function (event) {
        if ($(this).data('drag') == true) {
            this.scrollLeft = $(this).data('scrollX') + ($(this).data('x') - event.clientX);
        }
    }).mousedown(function (event) {
        $(this).data('drag', true).data('x', event.clientX).data('scrollX', this.scrollLeft);
    }).mouseup(function () {
        $(this).data('drag', false);
    });
});
</script>