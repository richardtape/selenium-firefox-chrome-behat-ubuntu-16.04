<?php

require 'vendor/autoload.php';

class BrowserStackContext extends Behat\Behat\Context\BehatContext {

	protected $config;
	protected static $driver;
	private static $bs_local;

	public function __construct( $parameters ) {

		$GLOBALS['CONFIG'] = $parameters['browserstack'];

		$GLOBALS['BROWSERSTACK_USERNAME'] = getenv( 'BROWSERSTACK_USERNAME' );
		if ( ! $GLOBALS['BROWSERSTACK_USERNAME'] ) {
			$GLOBALS['BROWSERSTACK_USERNAME'] = $GLOBALS['CONFIG']['user'];
		}

		$GLOBALS['BROWSERSTACK_ACCESS_KEY'] = getenv( 'BROWSERSTACK_ACCESS_KEY' );

		if ( ! $GLOBALS['BROWSERSTACK_ACCESS_KEY'] ) {
			$GLOBALS['BROWSERSTACK_ACCESS_KEY'] = $GLOBALS['CONFIG']['key'];
		}
	}/* __construct() */

	/** @BeforeFeature */
	public static function setup() {

		$config = $GLOBALS['CONFIG'];
		$task_id = getenv( 'TASK_ID' ) ? getenv( 'TASK_ID' ) : 0;

		$url = 'https://' . $GLOBALS['BROWSERSTACK_USERNAME'] . ':' . $GLOBALS['BROWSERSTACK_ACCESS_KEY'] . '@' . $config['server'] . '/wd/hub';
		$caps = $config['environments'][ $task_id ];

		foreach ( $config['capabilities'] as $key => $value ) {
			if ( ! array_key_exists( $key, $caps ) ) {
				$caps[ $key ] = $value;
			}
		}

		if ( array_key_exists( 'browserstack.local', $caps ) && $caps['browserstack.local'] ) {
			$bs_local_args = array( 'key' => $GLOBALS['BROWSERSTACK_ACCESS_KEY'] );
			self::$bs_local = new BrowserStack\Local();
			self::$bs_local->start( $bs_local_args );
		}

		self::$driver = RemoteWebDriver::create( $url, $caps );
	}/* setup() */

	/** @AfterFeature */
	public static function tearDown() {
		self::$driver->quit();
		if ( self::$bs_local ) {
			self::$bs_local->stop();
		}
	}/* tearDown() */

}/* class BrowserStackContext */
