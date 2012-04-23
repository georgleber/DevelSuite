<?php
namespace DevelSuite\routing;

use DevelSuite\config\dsConfig;

use DevelSuite\dsApp;

class dsRouterTest extends \PHPUnit_Framework_TestCase {
	private $router;
	
	public function setUp() {
		dsConfig::write('app.path', '');
		$this->router = new dsRouter();
	}

	public function testMapRoute() {
		assertSame("/", $this->router->generateUrl("home::action"));
	}
}