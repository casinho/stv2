<?php
class TinyMceSmilieWidget extends CWidget {

	public $editorId;
	public $smilies;
	
	public $path;
	
	public function init() {
		
		/*
		$smilies = array();
		$smilies[] = array('name' => 'smiley-cool', 'title' => Yii::t('global','smiley_cool'));
		$smilies[] = array('name' => 'smiley-cry', 'title' => Yii::t('global','smiley_cry'));
		$smilies[] = array('name' => 'smiley-embarassed', 'title' => Yii::t('global','smiley_embarassed'));
		$smilies[] = array('name' => 'smiley-foot-in-mouth', 'title' => Yii::t('global','smiley_foot_in_mouth'));
		$smilies[] = array('name' => 'smiley-frown', 'title' => Yii::t('global','smiley_frown'));
		$smilies[] = array('name' => 'smiley-innocent', 'title' => Yii::t('global','smiley_innocent'));
		$smilies[] = array('name' => 'smiley-kiss', 'title' => Yii::t('global','smiley_kiss'));
		$smilies[] = array('name' => 'smiley-laughing', 'title' => Yii::t('global','smiley_laughing'));
		$smilies[] = array('name' => 'smiley-money-mouth', 'title' => Yii::t('global','smiley_money_mouth'));
		$smilies[] = array('name' => 'smiley-sealed', 'title' => Yii::t('global','smiley_sealed'));
		$smilies[] = array('name' => 'smiley-smile', 'title' => Yii::t('global','smiley_smile'));
		$smilies[] = array('name' => 'smiley-surprised', 'title' => Yii::t('global','smiley_surprised'));
		$smilies[] = array('name' => 'smiley-tongue-out', 'title' => Yii::t('global','smiley_tongue_out'));
		$smilies[] = array('name' => 'smiley-undecided', 'title' => Yii::t('global','smiley_undecided'));
		$smilies[] = array('name' => 'smiley-wink', 'title' => Yii::t('global','smiley_wink'));
		$smilies[] = array('name' => 'smiley-yell', 'title' => Yii::t('global','smiley_yell'));
		
		$this->path = Yii::app()->getBaseUrl(true).'/images/smilies/';
		
		*/
/*
		$smilies[] = array('name' => 'angel', 'title' => Yii::t('smiley','angel'), 'ext'=>'png');
		$smilies[] = array('name' => 'confused', 'title' => Yii::t('smiley','smiley_cry'), 'ext'=>'png');
		$smilies[] = array('name' => 'cry', 'title' => Yii::t('smiley','smiley_embarassed'), 'ext'=>'png');
		$smilies[] = array('name' => 'devil', 'title' => Yii::t('smiley','smiley_foot_in_mouth'), 'ext'=>'png');
		$smilies[] = array('name' => 'frown', 'title' => Yii::t('smiley','smiley_frown'), 'ext'=>'png');
		$smilies[] = array('name' => 'gasp', 'title' => Yii::t('smiley','smiley_innocent'), 'ext'=>'png');
		$smilies[] = array('name' => 'glasses', 'title' => Yii::t('smiley','smiley_kiss'), 'ext'=>'png');
		$smilies[] = array('name' => 'grin', 'title' => Yii::t('smiley','smiley_laughing'), 'ext'=>'png');
		$smilies[] = array('name' => 'grumpy', 'title' => Yii::t('smiley','smiley_money_mouth'), 'ext'=>'png');
		$smilies[] = array('name' => 'heart', 'title' => Yii::t('smiley','smiley_sealed'), 'ext'=>'png');
		$smilies[] = array('name' => 'kiki', 'title' => Yii::t('smiley','smiley_smile'), 'ext'=>'png');
		$smilies[] = array('name' => 'kiss', 'title' => Yii::t('smiley','smiley_surprised'), 'ext'=>'png');
		$smilies[] = array('name' => 'pacman', 'title' => Yii::t('smiley','smiley_tongue_out'), 'ext'=>'png');

		#$smilies[] = array('name' => 'penguin', 'title' => Yii::t('smiley','smiley_undecided'));
		#$smilies[] = array('name' => 'putnam', 'title' => Yii::t('smiley','smiley_wink'));
		
		$smilies[] = array('name' => 'robot', 'title' => Yii::t('smiley','smiley_yell'));
		$smilies[] = array('name' => 'shark', 'title' => Yii::t('smiley','smiley_yell'));

		$smilies[] = array('name' => 'smile', 'title' => Yii::t('smiley','smiley_money_mouth'), 'ext'=>'png');
		$smilies[] = array('name' => 'squint', 'title' => Yii::t('smiley','smiley_sealed'), 'ext'=>'png');
		$smilies[] = array('name' => 'sunglasses', 'title' => Yii::t('smiley','smiley_smile'), 'ext'=>'png');
		$smilies[] = array('name' => 'tongue', 'title' => Yii::t('smiley','smiley_surprised'), 'ext'=>'png');
		$smilies[] = array('name' => 'unsure', 'title' => Yii::t('smiley','smiley_tongue_out'), 'ext'=>'png');		
		$smilies[] = array('name' => 'upset', 'title' => Yii::t('smiley','smiley_tongue_out'), 'ext'=>'png');		
*/		
		$smilies[] = array('name' => 'smiley-biggrin', 'title' => Yii::t('smiley','smiley_biggrin'),'folder'=>'ufd');		
		$smilies[] = array('name' => 'smiley-confused', 'title' => Yii::t('smiley','smiley_confused'),'folder'=>'ufd');
		$smilies[] = array('name' => 'smiley-cool', 'title' => Yii::t('smiley','smiley_cool'),'folder'=>'ufd');
		$smilies[] = array('name' => 'smiley-daumenhoch', 'title' => Yii::t('smiley','smiley_daumenhoch'),'folder'=>'ufd');
		$smilies[] = array('name' => 'smiley-daumenrunter', 'title' => Yii::t('smiley','smiley_daumenrunter'),'folder'=>'ufd');
		$smilies[] = array('name' => 'smiley-frown', 'title' => Yii::t('smiley','smiley_frown'),'folder'=>'ufd');
		$smilies[] = array('name' => 'smiley-embarassed', 'title' => Yii::t('smiley','smiley_embarassed'),'folder'=>'ufd');
		#$smilies[] = array('name' => 'smiley-glasses', 'title' => Yii::t('smiley','smiley_glasses'),'folder'=>'ufd');
		$smilies[] = array('name' => 'smiley-mad', 'title' => Yii::t('smiley','smiley_mad'),'folder'=>'ufd');
		$smilies[] = array('name' => 'smiley-redface', 'title' => Yii::t('smiley','smiley_redface'),'folder'=>'ufd');
		$smilies[] = array('name' => 'smiley-rolleyes', 'title' => Yii::t('smiley','smiley_rolleyes'),'folder'=>'ufd');
		$smilies[] = array('name' => 'smiley-sealed', 'title' => Yii::t('smiley','smiley_sealed'),'folder'=>'ufd');
		$smilies[] = array('name' => 'smiley-smile', 'title' => Yii::t('smiley','smiley_smile'),'folder'=>'ufd');
		$smilies[] = array('name' => 'smiley-sleep', 'title' => Yii::t('smiley','smiley_sleep'),'folder'=>'ufd');
		$smilies[] = array('name' => 'smiley-smoke', 'title' => Yii::t('smiley','smiley_smoke'),'folder'=>'ufd');
		$smilies[] = array('name' => 'smiley-surprised', 'title' => Yii::t('smiley','smiley_surprised'),'folder'=>'ufd');
		$smilies[] = array('name' => 'smiley-tjo', 'title' => Yii::t('smiley','smiley_tjo'),'folder'=>'ufd');
		$smilies[] = array('name' => 'smiley-tongue-out', 'title' => Yii::t('smiley','smiley_tongue_out'),'folder'=>'ufd');
		$smilies[] = array('name' => 'smiley-wink', 'title' => Yii::t('smiley','smiley_wink'),'folder'=>'ufd');
		//$smilies[] = array('name' => 'smiley-uglyhammer2', 'title' => Yii::t('smiley','smiley_uglyhammer2'),'folder'=>'ufd');
		
		
		
		$this->path = Yii::app()->getBaseUrl(true).'/images/smileys/';
		
		$this->smilies = $smilies;
		
		
	}

    public function run() {
    	// Teaser wird nur gerendert, wenn mindestens ein Datensatz im DataProvider vorhanden ist
        $this->render('smilies');
    }
}
?>