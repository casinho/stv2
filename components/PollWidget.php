<?php
class PollWidget extends CWidget {
	
	public $thread = null;
	public $forum = null;
	
	public $thread_id = false;
	public $forum_id = false;
	
	
	public $optionen;
	public $gesamtstimmen;
	public $showThreadTitle = false;
	public $abgelaufen = false;
	public $enddatum;
	
	public $view;
	public $uForm;
	
	private $template = 'pollWidget';
	
	public function init() {
		//$this->id = $this->getId();
		
		if(!isset($this->forum->forum_id,$this->thread->thread_id)) {
			$this->forum = ForumModel::model()->findByPk($this->forum_id);
			$this->thread = ForumThread::model(null,$this->forum_id)->findByPk($this->thread_id);
		}
				
		if($this->thread->poll_flag == 0) {
			return false;
		} else {
			
			if(strtotime($this->thread->poll_end_datum) > time() || $this->thread['closed_flag']==1) {
				$this->abgelaufen = true;
			} 
			
			if($this->thread->poll_end_datum > 0) {
				$this->enddatum = Yii::t('forum','teilnahme_moeglich_bis',array('{datum}'=> Yii::app()->dateFormatter->formatDateTime($this->thread->poll_end_datum,'medium',null)));
				if($this->thread->poll_end_datum < time()) {
					$this->enddatum = Yii::t('forum','umfrage_abgelaufen');
				}
			}
			
			if(!Yii::app()->user->isGuest) {
				$sql = "SELECT COUNT(user_id) FROM user2poll WHERE forum_id = '".$this->forum->forum_id."' AND thread_id = ".$this->thread->thread_id." AND user_id = ".Yii::app()->user->getId();
				$abgegebeneStimmen = $count=Yii::app()->db->createCommand($sql)->queryScalar();
			} else {
				$abgegebeneStimmen = 0;
			}

			if($abgegebeneStimmen == 0 || $this->abgelaufen = false) {
				$this->view = 'pollFormWidget';
			} else {
				$this->view = 'pollWidget';
			}

			$attributes['thread_id'] 	= $this->thread->thread_id;
			$attributes['forum_id'] 	= $this->forum->forum_id;

			$condition = array('order' => 'sort ASC');

			$this->optionen = Polls::model()->findAllByAttributes($attributes,$condition);
			
			foreach($this->optionen as $k => $v) {
				$this->gesamtstimmen += $v->count_votes;
			}				
		}
			
		
		if(!empty($this->view)) {
			$this->template = $this->view;
		}
		
	}
	
    public function run() {
    	if($this->thread->poll_flag == 0) { 
    		return false; 
    	} 
    	$this->render($this->template);
    }
}
?>