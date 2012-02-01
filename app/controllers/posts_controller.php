<?php
class PostsController extends AppController {
	
	//Are you adding a new function? Be sure to update the ACL!
	
	var $name = 'Posts';
	var $uses = array('Star', 'Post');

	function index($y = null,$x = null) {
		$this->Post->recursive = 0;
		$this->set('posts', $this->paginate());
	}
	
/*OLD OLD OLD
	function posts_json($y = null,$x = null) {
	    $this->view = 'Json';

	    $info =  $this->Post->find('all',array(
	    									"order" => array("Post.created DESC")
	    									  )
	    						   );
	    $json = array(
			  'posts' => $info,
			  'extra' => "cheeseSteak, EXTRA Extra Pickles, no mayo",
			  );
	    $this->set(compact('json'));
  	}
*/
  	
	
	function add_star($post_id = null) {
	
		$user_id = $this->Auth->user('id');
		
		
		
		//First, check to see if the user has already starred the post
		$arguments = array('conditions' => array(
												"Star.post_id" => $post_id,
												"Star.user_id" => $user_id,
											  ),
						  );
		
		$postExists = $this->Star->find('first',$arguments);	
		
		if (!$postExists) {
		
			//Insert Star in to Star Table
			$new_star = array('Star' => array(
									"post_id" => $post_id,
									"user_id" => $user_id,
									)
								);
			
			$this->Star->create();
			$this->Star->save($new_star);
			
			//Update Post count on the post
			if ($this->Post->updateAll(array('Post.star_count'=>'Post.star_count+1'), array('Post.id'=>$post_id))) {
				$this->redirect(array('action' => 'success'));
			}		
		
		} else {
		
			$this->redirect(array('action' => 'fail'));
		}
	}
	
	function remove_star($post_id = null) {
	
		$user_id = $this->Auth->user('id');
		
		
		
		//First, check to see if the user has already starred the post
		$arguments = array(conditions => array(
												"Star.post_id" => $post_id,
												"Star.user_id" => $user_id,
											  ),
						  );
		
		$star = $this->Star->find('first',$arguments);	
		
		if ($star) {
			
			$star_id = $star['Star']['id'];
			
			$this->Star->delete($star_id,false);
			
			//Update Post count on the post
			if ($this->Post->updateAll(array('Post.star_count'=>'Post.star_count-1'), array('Post.id'=>$post_id))) {
				$this->redirect(array('action' => 'success'));
			}		
		
		} else {
		
			$this->redirect(array('action' => 'fail'));
		}
	}
	
	
	function test($left = null,$right = null,$top = null, $bottom = null) {
	
				
		//Latitude = y
		//Longitude = x
		
		$this->Post->recursive = 0;
				
		$posts = array();
			
		
		//debug($x,$y,$insideCoordRight,$insideCoordLeft,$insideCoordTop,$insideCoordBottom);
					
					$info =  $this->Post->find('all',array(
    									"order" => array("Post.created DESC"),
    									"conditions" => array(
    										"Post.y >" => $bottom,
    										"Post.y <" => $top,
    										"Post.x >" => $left,
    										"Post.x <" => $right,
											
										),
										"limit" => 35,
									)
								);
		
		array_push($posts,$info);
		
		


			
			
		
		
			    										
	    //pr($posts);
		
		
		
		$this->set('posts',$posts);
		
	}
	
	
	function mapquery_json($left = null,$right = null,$top = null, $bottom = null) {
		$this->view = 'Json';

				
		//Latitude = y
		//Longitude = x
		
		$this->Post->recursive = 0;
				
		$posts = array();
			

		//debug($x,$y,$insideCoordRight,$insideCoordLeft,$insideCoordTop,$insideCoordBottom);
					
					$info =  $this->Post->find('all',array(
    									"order" => array("Post.created DESC"),
    									"conditions" => array(
    										"Post.y >" => $bottom,
    										"Post.y <" => $top,
    										"Post.x >" => $left,
    										"Post.x <" => $right,
											
										),
										"limit" => 35,
									)
								);
		
		array_push($posts,$info);
		
		


			
			
		
		
			    										
	    //pr($posts);
		
		$json = array(
			  'posts' => $posts,
			  'extra' => "cheeseSteak",
			  );
	    $this->set(compact('json'));
		
	}
	
	function posts_json($y = null,$x = null) {
	
		$this->view = 'Json';
		
		//Latitude = y
		//Longitude = x
		
		$this->Post->recursive = 0;
		
		$metersTable = array(300,500,1000,3000,5000,10000,25000);
		
		$feetTable = array(500,1000,2640,5280,10560,26400,105600);
		
		
		$posts = array();

		$PrevXDegreesDistancePlus = 0;
		$PrevYDegreesDistancePlus = 0;
		
		
		$insideDistanceTop = 0;
		$insideDistanceRight = 0;
		$insideDistanceBottom = 0;
		$insideDistanceLeft = 0;

		
		$insideCoordTop = $y;
		$insideCoordRight = $x;
		$insideCoordBottom = $y;
		$insideCoordLeft = $x;
		
		
					
		foreach ($feetTable as $feetDistance) {
			
			
			$xDegrees = $this->longitudeDegreesFromFeet($feetDistance);	
			$yDegrees = $this->latitudeDegreesFromFeet($feetDistance);
			
			$outsideDistanceTop = $this->latitudeDegreesFromFeet($feetDistance);
			$outsideDistanceRight = $this->longitudeDegreesFromFeet($feetDistance);
			$outsideDistanceBottom = $this->latitudeDegreesFromFeet($feetDistance);
			$outsideDistanceLeft = $this->longitudeDegreesFromFeet($feetDistance);
			
			
			
			
			$outsideCoordTop = $y + $outsideDistanceTop;
			$outsideCoordRight = $x + $outsideDistanceRight;
			$outsideCoordBottom = $y - $outsideDistanceBottom;
			$outsideCoordLeft = $x - $outsideDistanceLeft;
			
			$xDegreesDistancePlus = $x + $xDegrees;
			$xDegreesDistanceMinus = $x - $xDegrees;
			$yDegreesDistancePlus = $y + $yDegrees;
			$yDegreesDistanceMinus = $y - $yDegrees;
			
			//debug($x,$y,$insideCoordRight,$insideCoordLeft,$insideCoordTop,$insideCoordBottom);
						
						$info =  $this->Post->find('all',array(
	    									"order" => array("Post.created DESC"),
	    									"conditions" => array(
	    										array("OR" => array( 
	    										
	    										array(
	    									
	    											//First do top and bottom rows:
		    										array(  
			    										"OR" => array(
			    											array("Post.y BETWEEN ? AND ?" => array($insideCoordTop,$outsideCoordTop)),
			    											array("Post.y BETWEEN ? AND ?" => array($outsideCoordBottom,$insideCoordBottom))
			    										),
			    									), //AND
			    									array(
			    										
			    										array("Post.x BETWEEN ? AND ?" => array($outsideCoordLeft,$outsideCoordRight)),
	    											),
    											
    											),
	    										array(  //OR
	    									
	    											//Then right and left columns
		    										
		    										
		    										array(
			    										"OR" => array(
			    											array("Post.x BETWEEN ? AND ?" => array($insideCoordRight,$outsideCoordRight)),
			    											array("Post.x BETWEEN ? AND ?" => array($outsideCoordLeft,$insideCoordLeft))
			    										),
	    											),  //AND  										
		    										array(
			    										
			    										array("Post.y BETWEEN ? AND ?" => array($outsideCoordBottom,$outsideCoordTop)),
			    											
			    										
			    									),
	
    											
    											),
   											
    											
    											
    											)
    											
    											)
    											
    										),
    									)
    								);
			
			array_push($posts,$info);
			
			
			$PrevXDegreesDistancePlus = $xDegreesDistancePlus;
			$PrevYDegreesDistancePlus = $yDegreesDistancePlus;
			
			
			$insideCoordTop = $outsideCoordTop;
			$insideCoordRight = $outsideCoordRight;
			$insideCoordBottom = $outsideCoordBottom;
			$insideCoordLeft = $outsideCoordLeft;
			
			
			
		}
		
		
			    										
	    //pr($posts);
		
		
		
		$json = array(
			  'posts' => $posts,
			  'extra' => "cheeseSteak",
			  );
	    $this->set(compact('json'));
		
	}




	function posts_starred_json($y = null,$x = null) {
	
		$this->view = 'Json';
		
		//Latitude = y
		//Longitude = x
		
		$this->Post->recursive = 0;
		
		$metersTable = array(100,300,500,1000,3000,5000,10000,25000);
		
		$feetTable = array(250,500,1000,2640,5280,10560,26400,105600);
		
		
		$posts = array();

		$PrevXDegreesDistancePlus = 0;
		$PrevYDegreesDistancePlus = 0;
		
		
		$insideDistanceTop = 0;
		$insideDistanceRight = 0;
		$insideDistanceBottom = 0;
		$insideDistanceLeft = 0;

		
		$insideCoordTop = $y;
		$insideCoordRight = $x;
		$insideCoordBottom = $y;
		$insideCoordLeft = $x;
		
		
					
		foreach ($feetTable as $feetDistance) {
			
			
			$xDegrees = $this->longitudeDegreesFromFeet($feetDistance);	
			$yDegrees = $this->latitudeDegreesFromFeet($feetDistance);
			
			$outsideDistanceTop = $this->latitudeDegreesFromFeet($feetDistance);
			$outsideDistanceRight = $this->longitudeDegreesFromFeet($feetDistance);
			$outsideDistanceBottom = $this->latitudeDegreesFromFeet($feetDistance);
			$outsideDistanceLeft = $this->longitudeDegreesFromFeet($feetDistance);
			
			
			
			
			$outsideCoordTop = $y + $outsideDistanceTop;
			$outsideCoordRight = $x + $outsideDistanceRight;
			$outsideCoordBottom = $y - $outsideDistanceBottom;
			$outsideCoordLeft = $x - $outsideDistanceLeft;
			
			$xDegreesDistancePlus = $x + $xDegrees;
			$xDegreesDistanceMinus = $x - $xDegrees;
			$yDegreesDistancePlus = $y + $yDegrees;
			$yDegreesDistanceMinus = $y - $yDegrees;
			
			//debug($x,$y,$insideCoordRight,$insideCoordLeft,$insideCoordTop,$insideCoordBottom);
						
						$info =  $this->Post->find('all',array(
	    									"order" => array("Post.star_count DESC"),
	    									"conditions" => array(
	    										array("Post.star_count >" => 0),
	    										array("OR" => array( 
	    										
	    										array(
	    									
	    											//First do top and bottom rows:
		    										array(  
			    										"OR" => array(
			    											array("Post.y BETWEEN ? AND ?" => array($insideCoordTop,$outsideCoordTop)),
			    											array("Post.y BETWEEN ? AND ?" => array($outsideCoordBottom,$insideCoordBottom))
			    										),
			    									), //AND
			    									array(
			    										
			    										array("Post.x BETWEEN ? AND ?" => array($outsideCoordLeft,$outsideCoordRight)),
	    											),
    											
    											),
	    										array(  //OR
	    									
	    											//Then right and left columns
		    										
		    										
		    										array(
			    										"OR" => array(
			    											array("Post.x BETWEEN ? AND ?" => array($insideCoordRight,$outsideCoordRight)),
			    											array("Post.x BETWEEN ? AND ?" => array($outsideCoordLeft,$insideCoordLeft))
			    										),
	    											),  //AND  										
		    										array(
			    										
			    										array("Post.y BETWEEN ? AND ?" => array($outsideCoordBottom,$outsideCoordTop)),
			    											
			    										
			    									),
	
    											
    											),
   											
    											
    											
    											)
    											
    											)
    											
    										),
    									)
    								);
			
			array_push($posts,$info);
			
			
			$PrevXDegreesDistancePlus = $xDegreesDistancePlus;
			$PrevYDegreesDistancePlus = $yDegreesDistancePlus;
			
			
			$insideCoordTop = $outsideCoordTop;
			$insideCoordRight = $outsideCoordRight;
			$insideCoordBottom = $outsideCoordBottom;
			$insideCoordLeft = $outsideCoordLeft;
			
			
			
		}
		
		
			    										
	    //pr($posts);
		
		
		
		$json = array(
			  'posts' => $posts,
			  'extra' => "cheeseSteak",
			  );
	    $this->set(compact('json'));
		
	}



/*
	
	function test($y = null,$x = null) {
	
		//debug($x);
		//Latitude = y
		//Longitude = x
		
		$this->Post->recursive = 0;
		
		$metersTable = array(100,300,500,1000,3000,5000,10000,25000);
		
		$feetTable = array(250,500,1000,2640,5280,10560,26400,105600);
		
		
		$posts = array();

		$PrevXDegreesDistancePlus = 0;
		$PrevYDegreesDistancePlus = 0;
		
		
		$insideDistanceTop = 0;
		$insideDistanceRight = 0;
		$insideDistanceBottom = 0;
		$insideDistanceLeft = 0;

		
		$insideCoordTop = $y;
		$insideCoordRight = $x;
		$insideCoordBottom = $y;
		$insideCoordLeft = $x;
		
		
					
		foreach ($feetTable as $feetDistance) {
			
			
			$xDegrees = $this->longitudeDegreesFromFeet($feetDistance);	
			$yDegrees = $this->latitudeDegreesFromFeet($feetDistance);
			
			$outsideDistanceTop = $this->latitudeDegreesFromFeet($feetDistance);
			$outsideDistanceRight = $this->longitudeDegreesFromFeet($feetDistance);
			$outsideDistanceBottom = $this->latitudeDegreesFromFeet($feetDistance);
			$outsideDistanceLeft = $this->longitudeDegreesFromFeet($feetDistance);
			
			
			
			
			$outsideCoordTop = $y + $outsideDistanceTop;
			$outsideCoordRight = $x + $outsideDistanceRight;
			$outsideCoordBottom = $y - $outsideDistanceBottom;
			$outsideCoordLeft = $x - $outsideDistanceLeft;
			
			$xDegreesDistancePlus = $x + $xDegrees;
			$xDegreesDistanceMinus = $x - $xDegrees;
			$yDegreesDistancePlus = $y + $yDegrees;
			$yDegreesDistanceMinus = $y - $yDegrees;
			
									
			$info =  $this->Post->find('all',array(
	    									"order" => array("Post.created DESC"),
	    									"conditions" => array(
	    										array("OR" => array( 
	    										
	    										array(
	    									
	    											//First do top and bottom rows:
		    										array(  
			    										"OR" => array(
			    											array("Post.y BETWEEN ? AND ?" => array($insideCoordTop,$outsideCoordTop)),
			    											array("Post.y BETWEEN ? AND ?" => array($outsideCoordBottom,$insideCoordBottom))
			    										),
			    									), //AND
			    									array(
			    										
			    										array("Post.x BETWEEN ? AND ?" => array($outsideCoordLeft,$outsideCoordRight)),
	    											),
    											
    											),
	    										array(  //OR
	    									
	    											//Then right and left columns
		    										
		    										
		    										array(
			    										"OR" => array(
			    											array("Post.x BETWEEN ? AND ?" => array($insideCoordRight,$outsideCoordRight)),
			    											array("Post.x BETWEEN ? AND ?" => array($outsideCoordLeft,$insideCoordLeft))
			    										),
	    											),  //AND  										
		    										array(
			    										
			    										array("Post.y BETWEEN ? AND ?" => array($outsideCoordBottom,$outsideCoordTop)),
			    											
			    										
			    									),
	
    											
    											),
   											
    											
    											
    											)
    											
    											)
    											
    										),
    									)
    								);
			
			array_push($posts,$info);
			
			
			$PrevXDegreesDistancePlus = $xDegreesDistancePlus;
			$PrevYDegreesDistancePlus = $yDegreesDistancePlus;
			
			
			$insideCoordTop = $outsideCoordTop;
			$insideCoordRight = $outsideCoordRight;
			$insideCoordBottom = $outsideCoordBottom;
			$insideCoordLeft = $outsideCoordLeft;
			
			
			
		}
		
		
			    										
	    pr($posts);
		
		$this->set('posts',$posts);
	}
	
*/

	private function longitudeDegreesFromFeet($feet, $theta = null) { // X
	
		$miles = 5280;
		$degree = 59.95 * $miles;
		
	
		
		return $feet / $degree;
	
	}
	
	
	private function latitudeDegreesFromFeet($feet) { // Y


		$miles = 5280;
		$degree = 68.92 * $miles;
	
		return $feet / $degree;
	}
	
	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid post', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('post', $this->Post->read(null, $id));
	}

	function add() {
			
		if (!empty($this->data)) {
			$this->Post->create();
			
			print_r($this->data);
			
			$userId = $this->Auth->user('id');
			
			$this->data['Post']['user_id'] = $userId;
			
			print_r($this->data);
					
			if ($this->Post->save($this->data)) {
				$this->Session->setFlash(__("NEW POST SAVED!", true));
				$this->redirect(array('action' => 'success'));
			} else {
				$this->Session->setFlash(__('The post could not be saved. Please, try again.', true));
			}
		}
		$users = $this->Post->User->find('list');
		$this->set(compact('users'));
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid post', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->Post->save($this->data)) {
				$this->Session->setFlash(__('The post has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The post could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Post->read(null, $id);
		}
		$users = $this->Post->User->find('list');
		$this->set(compact('users'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for post', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Post->delete($id)) {
			$this->Session->setFlash(__('Post deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Post was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
	
	function success() {
	    $this->view = 'Data';
	    $json = "YES";
	    $this->set(compact('json'));	
	}
	
	function fail() {
	    $this->view = 'Data';
	    $json = "NO";
	    $this->set(compact('json'));	
	}
		
	function beforeFilter() {
	    parent::beforeFilter(); 
	    $this->Auth->allowedActions = array('index', 'view','posts_json','posts_json2','posts_starred_json','test','success','fail','add_star','remove_star','posts_testing','mapquery_json');
	}

}
