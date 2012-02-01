<?php
class UsersController extends AppController {

	var $name = 'Users';
	var $uses = array('Star', 'User');
	var $components = array('Email','Otp','Auth');
	
////////////////////////////////////////////////////////////////////////////////////////////

	function index() {
		$this->User->recursive = 0;
		$this->set('users', $this->paginate());
	}
////////////////////////////////////////////////////////////////////////////////////////////

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid user', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('user', $this->User->read(null, $id));
	}
////////////////////////////////////////////////////////////////////////////////////////////

	function add() {
		if (!empty($this->data)) {
			$this->data['User']['group_id'] = 3;
			
			$this->User->create();
			if ($this->User->save($this->data)) {
				$this->Auth->login($this->data);
				$this->redirect(array('action' => 'success'));
				//$this->Session->setFlash(__('The user has been saved', true));
				//$this->redirect(array('action' => 'add'));
			} else {
				//$this->Session->setFlash(__('The user could not be saved. Please, try again.', true));
				$this->redirect(array('action' => 'fail'));
			}
		}
		$groups = $this->User->Group->find('list');
		$this->set(compact('groups'));
	}
	
	
	
////////////////////////////////////////////////////////////////////////////////////////////

/*
	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid user', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->User->save($this->data)) {
				$this->Session->setFlash(__('The user has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->User->read(null, $id);
		}
		$groups = $this->User->Group->find('list');
		$this->set(compact('groups'));
	}
*/
////////////////////////////////////////////////////////////////////////////////////////////

/*
	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for user', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->User->delete($id)) {
			$this->Session->setFlash(__('User deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('User was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
*/
////////////////////////////////////////////////////////////////////////////////////////////
	function get_stars() {
		$user_id = $this->Auth->user('id');
	
		$this->view = 'Json';
		
		//First, check to see if the user has already starred the post
		$arguments = array('conditions' => array("Star.user_id" => $user_id),
						   'fields' 	=> array("Star.post_id"),
						   'recursive'	=> 0,
						  );
		
		$stars = $this->Star->find('list',$arguments);
		

			    

	    $json = array(
			  'stars' => $stars,
			  );
	    $this->set(compact('json'));

		
		
			
	
	}
////////////////////////////////////////////////////////////////////////////////////////////

	function login() {
		if ($this->Session->read('Auth.User')) {
			$this->Session->setFlash('You are logged in!');
			$this->redirect('/', null, false);
		}
	}     
  
////////////////////////////////////////////////////////////////////////////////////////////

	function authenticated() {
		
		$this->redirect(array('action' => 'success'));		
	}
////////////////////////////////////////////////////////////////////////////////////////////

	function success() {
	    $this->view = 'Data';
	    $json = "YES";
	    $this->set(compact('json'));	
	}
////////////////////////////////////////////////////////////////////////////////////////////

	function fail() {
	    $this->view = 'Data';
	    $json = "NO";
	    $this->set(compact('json'));	
	}
////////////////////////////////////////////////////////////////////////////////////////////

	function logout() {
	    $this->Session->setFlash('Good-Bye');
		$this->redirect($this->Auth->logout());

	}


////////////////////////////////////////////////////////////////////////////////////////////

function good_username($username) {  //Returns SUCCESS if username is available

	$user = $this->User->FindByUsername($username);
	
	if (!$user) {
		$this->redirect(array('action' => 'success'));
	} else {
		$this->redirect(array('action' => 'fail'));
	}
	

}


////////////////////////////////////////////////////////////////////////////////////////////

function good_email($email) {  //Returns SUCCESS if username is available

	$user = $this->User->FindByEmail($email);
	
	if (!$user) {
		$this->redirect(array('action' => 'success'));
	} else {
		$this->redirect(array('action' => 'fail'));
	}
	

}


////////////////////////////////////////////////////////////////////////////////////////////
	
	function forgot() {
		if (!empty($this->data)) {
		
			$username = $this->data["User"]["username"];
			$user = $this->User->FindByUsername($username);
			
			if (!$user) {
			
			$user = $this->User->FindByEmail($username);
			
			}
			
			
			if($user){ // TODO Check for email or username
				
				
				$userEmail = $user['User']['email'];
				$userID = $user['User']['id'];
				
				
				$this->User->read(null, $user['User']['id']);			    
			
				// create an non-active user (with random password) 
				$randomPass = $this->Auth->password($this->generatePassword()); 
	
				$this->User->set('random_pass',$randomPass); 
				$this->User->save(); 
			
	        	
	  			// setup the TIME TO LIVE (valid until date) for the next day
				$now = microtime(true); 
				$ttl = intval($now + 24*3600); // the invitation is good for the next day 
				
				// create the OTP - TTL = time to live 
				$otp = $this->Otp->createOTP(array('user'=>$username,'password'=>$randomPass,'ttl'=> $ttl) ); 
				
				$link = "http://" . $_SERVER['SERVER_NAME'] . Dispatcher::baseUrl()."/users/otpreset/".$username."/".$ttl."/".$otp."";
						
				 // send mail 
				$this->Email->from    = "Starworld <reset@starworlddata.com>"; 
				$this->Email->to      = $userEmail; 
				
				$this->Email->subject = "Password Reset"; 
				$this->Email->sendAs = 'html'; 
				
				
				$body = "Please click the following link to reset your Starworld password:<br><br>"; 

				$body .= $link;
				$body .= "<br><br>If you did not request to change your password, please ignore this email.";
				
				//$this->Email->delivery = 'debug';
				
				$this->Email->send($body); 
				
				//pr($this->Session->read('Message.email'));
				$this->Session->setFlash("Password reset email has been sent. Please check your email."); 
				$this->redirect("/"); 
			} else {
			
			$this->Session->setFlash("Oh no! There was no account with that information."); 
				$this->redirect("/"); 
			}
        
        }
	
	}
	
////////////////////////////////////////////////////////////////////////////////////////////
// after clicking the link in the registration email the user will be sent here 
    function otpreset($username,$ttl,$otp) { 
            if($username){ 
                $user = $this->User->FindByUsername($username); 
                if($user){ 
                    $randomPass = $user["User"]["random_pass"]; 

                    $now = microtime(true); 
                    // check expiration date. the experation date should be greater them now. 
                    if($now <  $ttl){ 
                        // validate OTP 
                        if($this->Otp->authenticateOTP($otp,array('user'=>$username,'password'=>$randomPass,'ttl'=> $ttl)) ){ 
                               if($this->data){ 
									// activate the account by setting the password 
									$password = $this->data["User"]["password"]; 
									$this->User->id =  $user["User"]["id"]; 
        

									$this->User->saveField('password',   $this->Auth->password($password)); 
									$this->Session->setFlash( 'Yay! Your password has been changed!<br><br>Please log in to StarWorld using your new password.'); 
									$this->redirect('/');                                   

                               } 
                                
                               $this->set('ttime',$ttl); 
                               $this->set('hash',$otp); 
                               $this->set('username',$username);
                               

                        }else{ 
                            $this->Session->setFlash("Invalid request. Please contact the website administration."); 
                            // send to a error view 
                            $this->redirect('/'); 

                        } 
                    }else{ 
                        $this->Session->setFlash("Your invitation has expired. Please contact the website administration."); 
                        // send to a error view 
                       $this->redirect('/'); 
                    } 
                } 
            } 
            
    } 	
////////////////////////////////////////////////////////////////////////////////////////////
     function generatePassword($length=6, $strength=0){
		$vowels = 'aeuy';
		$consonants = 'bdghjmnpqrstvz';
		if ($strength & 1) {
			$consonants .= 'BDGHJLMNPQRSTVWXZ';
		}
		if ($strength & 2) {
			$vowels .= "AEUY";
		}
		if ($strength & 4) {
			$consonants .= '23456789';
		}
		if ($strength & 8) {
			$consonants .= '@#$%';
		}

		$password = '';
		$alt = time() % 2;
		for ($i = 0; $i < $length; $i++) {
			if ($alt == 1) {
				$password .= $consonants[(rand() % strlen($consonants))];
				$alt = 0;
			} else {
				$password .= $vowels[(rand() % strlen($vowels))];
				$alt = 1;
			}
		}
		return $password;
	}
	
	
	
	
	
////////////////////////////////////////////////////////////////////////////////////////////

	function beforeFilter() {
	    parent::beforeFilter(); 
	    $this->Auth->allowedActions = array('success','fail','get_stars','forgot','otpreset','add','good_username','good_email');
	}



////////////////////////////////////////////////////////////////////////////////////////////

/*

	function initDB() {
	    $group =& $this->User->Group;
	    //Allow admins to everything
	    $group->id = 1;     
	    $this->Acl->allow($group, 'controllers');
	 
	    //allow managers to posts and widgets
	    $group->id = 2;
	    $this->Acl->deny($group, 'controllers');
	    $this->Acl->allow($group, 'controllers/Posts');
	    $this->Acl->deny($group, 'controllers/Widgets');
	 
	    //allow users to only add and edit on posts and widgets
	    $group->id = 3;
	    $this->Acl->deny($group, 'controllers');        
	    $this->Acl->allow($group, 'controllers/Posts/add');
	    $this->Acl->deny($group, 'controllers/Posts/edit');        
	    $this->Acl->deny($group, 'controllers/Widgets/add');
	    $this->Acl->deny($group, 'controllers/Widgets/edit');
	    $this->Acl->allow($group, 'controllers/Users/logout');
	    $this->Acl->allow($group, 'controllers/Users/authenticated');
	    $this->Acl->allow($group, 'controllers/Users/success');
	    //we add an exit to avoid an ugly "missing views" error message
	    echo "all done";
	    exit;
	}


*/
	
	
	

}
