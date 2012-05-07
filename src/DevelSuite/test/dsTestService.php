<?php
namespace DevelSuite\test;

/**
 * This is a bla bla ;)
 *
 * @author develman
 */
class dsTestService {
	/**
	 * Bla
	 *
	 * @param string $dsn
	 * 		DSN
	 * @param string $user
	 * 		DB User
	 * @param string $password
	 * 		DB Password
	 */
	public function __construct() {
		echo "instantiated ;) <br/>";
	}

	/**
	 * @Inject
	 *
	 * @param string $test
	 * 		A test value
	 */
	public function setTest(dsTestObj $test) {
		echo "got test data test = ";
		print_r($test);
		echo "<br/>";
	}
	
	public function sayHello() {
		echo "Hello World<br/>";
	}
}