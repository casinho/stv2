<?php
class Alarm extends CFormModel
{
        public $nachricht;
        public $absender_name;
        public $absender_id;
        public $link;


        /**
         * Declares the validation rules.
         */
        public function rules()
        {
        	return array(
            	array('nachricht', 'required'),
           	);
        }

        /**
         * Declares customized attribute labels.
         * If not declared here, an attribute would have a label that is
         * the same as its name with the first letter in upper case.
         */
        public function attributeLabels()
        {
                return array(
                	'nachricht'			=> 'Nachricht',
                	'absender_name' 	=> 'Dein Name',
                	'absender_id' 		=> 'Deine UserID',
                	'link' 				=> 'Link',
                );
        }
        
	public function getAdminMails() {
		$attributes['kontroll_flag'] = 1;

		$user = User::model()->findAllByAttributes($attributes);
		$mails = array();

		foreach($user as $k => $v) {
			if(!empty($v->user_mail)) {
				$mails[] = $v->user_mail;
			}
		}

		return $mails;
	}        

}
?>