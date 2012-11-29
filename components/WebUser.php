<?php

class WebUser extends CWebUser
{
	// Store model to not repeat query.
	private $_model;


	// This is a function that checks the field 'role'
	// in the User model to be equal to 1, that means it's admin
	// access it by Yii::app()->user->isAdmin()
	function getRole(){
        
		if (Yii::app()->user->isGuest) {
			
            return "";
		} else {
			$user = $this->loadUser(Yii::app()->user->id);
			
            return $user->role;
		}
	}

	// Load user model.
	protected function loadUser($id=null)
	{
		if($this->_model===null)
		{
			if($id!==null)
				$this->_model=User::model()->findByPk($id);
		}
		return $this->_model;
	}
	
	//Bypass Yii authManager, use simple role checking instead.
	public function checkAccess($operation,$params=array(),$allowCaching=true)
	{
		return $this->getRole() == $operation;
	}
	
	public function reloadStates($state) {
		$this->loadIdentityStates($state);
	}
    
    public function getId()
    {
        return $this->getState('__id') ? $this->getState('__id') : 0;
    }

//    protected function beforeLogin($id, $states, $fromCookie)
//    {
//        parent::beforeLogin($id, $states, $fromCookie);
//
//        $model = new UserLoginStats();
//        $model->attributes = array(
//            'user_id' => $id,
//            'ip' => ip2long(Yii::app()->request->getUserHostAddress())
//        );
//        $model->save();
//
//        return true;
//    }

    protected function afterLogin($fromCookie)
	{
        parent::afterLogin($fromCookie);
        $this->updateSession();
	}

    public function updateSession() {
        $user = Yii::app()->getModule('user')->user($this->id);
        $userAttributes = CMap::mergeArray(array(
                                                'email'=>$user->email,
                                                'username'=>$user->username,
                                                'create_at'=>$user->create_at,
                                                'lastvisit_at'=>$user->lastvisit_at,
                                           ),$user->profile->getAttributes());
        foreach ($userAttributes as $attrName=>$attrValue) {
            $this->setState($attrName,$attrValue);
        }
    }

    public function model($id=0) {
        return Yii::app()->getModule('user')->user($id);
    }

    public function user($id=0) {
        return $this->model($id);
    }

    public function getUserByName($username) {
        return Yii::app()->getModule('user')->getUserByName($username);
    }

    public function getAdmins() {
        return Yii::app()->getModule('user')->getAdmins();
    }

    public function isAdmin() {
        return Yii::app()->getModule('user')->isAdmin();
    }

}