<?php
class ChatHandler extends YiiChatDbHandlerBase {
    //
    // IMPORTANT:
    // in any time here you can use this available methods:
    //  getData(), getIdentity(), getChatId()
    //
    protected function getDb(){
        // the application database
        return Yii::app()->db;
    }
    protected function createPostUniqueId(){
        // generates a unique id. 40 char.
        return hash('sha1',$this->getChatId().time().rand(1000,9999));      
    }
    protected function getIdentityName(){
        // find the identity name here
        // example: 
        return Yii::app()->user->name;
        //return $model->userFullName();
    }
    protected function getDateFormatted($value){
        // format the date numeric $value
        //return Yii::app()->format->formatDateTime($value);
    	return Yii::app()->dateFormatter->formatDateTimeAnzeige($value,'short','short',' - ');
    }
    protected function acceptMessage($message){
        // return false to reject it, elsewere return $message
        return $message;
    }

    /**
     retrieve posts from your database, considering the last_id argument:
     $last_id is the ID of the last post sent by the other person:
     when -1:
     you must reetrive all posts this scenario occurs when
     the chat initialize, retriving your posts and those posted
     by the others.
     when >0:
     you must retrive thoses posts that match this criteria:
     a) having an owner distinct as $identity
     b) having an ID greater than $last_id
     this scenario occurs when the post widget refreshs using
     a timer, in order to receive the new posts since last_id.
     */
    public function yiichat_list_posts($chat_id, $identity, $last_id, $data){
    	$this->_chat_id = $chat_id;
    	$this->_identity = $identity;
    	$this->_data = $data;
    	$limit = 3;
    	$where_string='';
    	$where_params=array();
    
    	// case all posts:
    	if($last_id == -1){
    		$where_string = 'chat_id=:chat_id';
    		$where_params = array(
    				':chat_id' => $chat_id,
    		);
    		$rows = $this->db->createCommand()
    		->select()
    		->from($this->getTableName())
    		->where($where_string,$where_params)
    		->limit(10)
    		->order('created desc')
    		->queryAll();
    		foreach($rows as $k=>$v)
    			$rows[$k]['time']=$this->getDateFormatted($v['created']);
    		
    		$zeit = array();
    		
    		foreach ($rows as $k => $v) {
    			$zeit[] = $v['created'];
    		}    		
    		
    		array_multisort($zeit, SORT_ASC, $rows);
    		
    		foreach($rows as $k => $v) {
    			Yii::app()->session["lastShoutId"] = $v['auto_id'];    
    			$rows[$k]['user'] = $v['owner'];
    			if($v['post_identity'] > 0) {
    				$user = User::model()->findByPk($v['post_identity']);
    				if($user != null) {
    					$rows[$k]['user'] = $user->getHeadline();
    				}   
    			}			
    		}

    		
    		return $rows;
    	} 	else{
    		
    		// case timer, new posts since last_id, not identity
    		
    		//$where_string = '((chat_id=:chat_id) and (post_identity<>:identity))';
    		
    		//print_r($last_id);
    		
    		$lastShoutId = Yii::app()->session["lastShoutId"];
    		/*
    		$where_string = '((chat_id=:chat_id))';
    		$where_params = array(
    				':chat_id' => $chat_id,
    				//':identity' => $identity,
    		);
    		
    		*/
    		
    		//print_r($lastShoutId);
    		
    		$where_string = '((chat_id=:chat_id) and (auto_id > :last_id))';
    		$where_params = array(
    				':chat_id' => $chat_id,
    				':last_id' => $lastShoutId,
    		);
    		
    		$rows = $this->db->createCommand()
    		->select()
    		->from($this->getTableName())
    		->where($where_string,$where_params)
    		->order('auto_id desc') // in this case desc,late will be sort asc
    		->limit(10)
    		->queryAll();
    		
    		//$ar = $this->getLastPosts($rows, $limit, $last_id);
    		$ar = $this->cleanLastPosts($rows);
    		
    		foreach($ar as $k=>$v) {
    			$ar[$k]['time']=$this->getDateFormatted($v['created']);
    			
    			$ar[$k]['user'] = $v['owner'];
    			if($v['post_identity'] > 0) {
    				$user = User::model()->findByPk($v['post_identity']);
    				if($user != null) {
    					$rar[$k]['user'] = $user->getHeadline();
    				}
    			}    			
    			
    			Yii::app()->session["lastShoutId"] = $v['auto_id'];
    		}
    		
    		return $ar;
    	}
    }
    
    private function cleanLastPosts($rows) {
    	$output = array();
    	foreach($rows as $k => $v) {
    		if($v['auto_id'] > Yii::app()->session["lastShoutId"]) {
    			$output[$k] = $v;
    		}
    	}
    	return $output;
    }
    
    /**
     retrieve the last posts since the last_id, must be used
     only when the records has been filtered (case timer).
     */
    private function getLastPosts($rows, $limit, $last_id){
    	
    	if(count($rows)==0) {
    		return array();
    	} else {
    		$lastShoutId = Yii::app()->session["lastShoutId"];
    	}
    	$n=-1;
    	for($i=0;$i<count($rows);$i++) {
    		if($rows[$i]['auto_id']==$lastShoutId) {
	    		$n=$i;
    			break;
    		}
    	}
    	if($last_id=='' || $last_id==null){
    		if($n==-1) {
    			$n = $i-1;
    		}
    		if($n==0){
    // TEST CASE: 7
    			return $rows;
    		}
    	} else {
    // TEST CASES: 6 and 8
    		$cnk2 = array_chunk($rows, $limit);
    		return array_reverse($cnk2[0]);
    	}
    
    	if($n > 0){
    		$cnk = array_chunk($rows,$n);
    		$cnk2 = array_chunk($cnk[0], $limit);
    		return array_reverse($cnk2[0]);
    	}else {
    		return array();
    	}
    }    
    
}
?>