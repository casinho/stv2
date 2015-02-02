<?php
date_default_timezone_set('Europe/Berlin');

class testCommand extends CConsoleCommand {

	public function actionIndicateTest() {
 	
		$empfaenger = "carsten-tetzlaff@web.de";
		$betreff = "Cron-Test";
		$from = "From: daRth <darth@santitan.de>";
		$text = "Dies ist ein Crontest";

		mail($empfaenger, $betreff, $text, $from);
		
		//echo "hjahfasdfjasdkfjasf";
	}
}

?>