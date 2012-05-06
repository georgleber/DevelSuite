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
	 * @Inject
	 * @Lenght('min' = 1, 'max' = 5)
	 *
	 * @param string $dsn
	 * 		DSN
	 * @param string $user
	 * 		DB User
	 * @param string $password
	 * 		DB Password
	 */
	public function __construct($name, $dsn, $user, $password) {
		echo "got data: <br/>NAME = " . $name . ", DSN = " . $dsn . ", USER = " . $user . ", PASSWORD = " . $password . "<br/>";
	}

	/**
	 * a test method
	 *
	 * @Inject
	 *
	 * @param string $test
	 * 		A test value
	 */
	public function setTest($test) {
		echo "got test data test = " . $test . "<br/>";
	}
	
	public function sayHello() {
		echo "Hello World<br/>";
	}
}