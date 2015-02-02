<?php
class UserLoginWidget extends CWidget {
    public $title='User login';
    public $visible=true; 
 
    public function init()
    {
        if($this->visible)
        {
 
        }
    }
 
    public function run()
    {
        if($this->visible)
        {
            $this->renderContent();
        }
    }
 
    protected function renderContent()
    {
        $form=new User;
        if(isset($_POST['User']))
        {
            $form->attributes=$_POST['User'];
            if($form->validate() && $form->login()){
                $url = $this->controller->createUrl('user/index');
                $this->controller->redirect($url);
            }
        }
        $this->render('UserLogin',array('form'=>$form));
    }   
}
?>