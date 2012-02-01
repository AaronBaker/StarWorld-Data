<?php
/* Widget Test cases generated on: 2011-08-29 15:39:03 : 1314646743*/
App::import('Model', 'Widget');

class WidgetTestCase extends CakeTestCase {
	var $fixtures = array('app.widget');

	function startTest() {
		$this->Widget =& ClassRegistry::init('Widget');
	}

	function endTest() {
		unset($this->Widget);
		ClassRegistry::flush();
	}

}
