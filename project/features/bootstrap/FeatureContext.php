<?php

require_once __DIR__ . '/../../vendor/phpunit/phpunit/src/Framework/Assert/Functions.php';
require_once __DIR__ . '/Utils.php';

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;
use Behat\MinkExtension\Context\RawMinkContext;

use UBC\BehatUtils as Utils;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends RawMinkContext implements Context, SnippetAcceptingContext {

	/** @var GuestContext */
	private $guestContext;

	private $parameters;


	/**
	 * Initializes context.
	 *
	 * Every scenario gets its own context instance.
	 * You can also pass arbitrary arguments to the
	 * context constructor through behat.yml.
	 */

	public function __construct( $parameters ) {

		$this->parameters = $parameters;

	}/* __construct() */


	/** @BeforeScenario */
	public function before( $scope ) {

	}/* before() */

	/**
	 * @AfterScenario
	 *
	 * After a scenario fails, send a message to slack detailing which scenario failed
	 *
	 * @since 1.0.0
	 *
	 * @param (object) $scope - The Scenario Scope object
	 * @return null
	 */

	public static function reportResultsToSlackIfScenarioFailed( $scope ) {

		// Find out if this suite passed
		if ( Utils::scenarioHasPassed( $scope ) ) {
			return;
		}

		$scenarioName = Utils::getScenarioName( $scope );

		// Scenario failed, so send message
		$settings = [
			'link_names' => true,
		];

		$client = new \Maknz\Slack\Client( 'https://hooks.slack.com/services/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', $settings );

		$client->to( '@richard.tape' )->attach([
			'fallback' => 'Scenario failed: ' . $scenarioName,
			'text' => 'Scenario failed: ' . $scenarioName,
			'color' => 'danger',
		])->send( 'Scenario failed: ' . $scenarioName );

	}/* reportResultsToSlackIfScenarioFailed() */


	/**
	 * @AfterScenario
	 *
	 * After a scenario fails, take a screenshot
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function takeScreenshotIfScenarioFailed( $scope ) {

		// Find out if this suite passed
		if ( Utils::scenarioHasPassed( $scope ) ) {
			return;
		}

		$driver = $this->getSession()->getDriver();
		$scenarioName = Utils::getScenarioName( $scope );
		$usable_file_name = Utils::getUsableFileNameFromScenarioName( $scenarioName );

		// GouteDriver means we have the HTML content
		if ( is_a( $driver, '\\Behat\\Mink\\Driver\\GoutteDriver' ) ) {
			$data = $driver->getContent();
			$file_and_path = dirname( dirname( dirname( __FILE__ ) ) ) . "/screenshots/$usable_file_name.html";
		} else {
			$data = $driver->getScreenshot();
			$file_and_path = dirname( dirname( dirname( __FILE__ ) ) ) . "/screenshots/$usable_file_name.jpg";
		}

		file_put_contents( $file_and_path, $data );

	}/* takeScreenshotIfScenarioFailed() */


	/**
	 * @Then I should see :urlString in the URL
	 * @And I should see :urlString in the URL
	 *
	 * Search for a specific string within the current URL
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function iShouldSeeInTheUrl( $urlString ) {

		$currentURL = $this->getSession()->getCurrentUrl();

		assertContains( $urlString, $currentURL, "The URL does not contain $urlString" );

	}/* iShouldSeeInTheUrl() */


	/**
	 * @Then I wait to see :text for no more than :numberOfSeconds seconds
	 *
	 * @since 1.0.0
	 *
	 * @param (string) $text - The text we wish to see
	 * @param (int) $numberOfSeconds - The maximum time we'll wait
	 * @return null
	 */

	public function iWaitToSeeForNoMoreThanSeconds( $text, $numberOfSeconds ) {

		$this->assertPageContainsText( $text, 1, $numberOfSeconds );

	}/* iWaitToSeeForNoMoreThanSeconds() */


	/**
	 * Allows us to handle slow tests (AJAX call backs for example)
	 *
	 * @since 1.0.0
	 *
	 * @param (function) $lambda - The callback to try - must return boolean
	 * @param (int) $interval - How often do we try? In seconds.
	 * @param (int) $wait - The maximum wait time. In seconds.
	 * @return null
	 */

	public function spin( $lambda, $interval = 1, $wait = 10 ) {

		for ( $i = 0; $i < $wait; $i++ ) {

			try {
				if ( $lambda( $this ) ) {
					// Give the page time to sort itself out
					sleep( 1 );
					echo '... success';
					return true;
				}
			} catch ( \Exception $e ) {
				echo '... waiting';
			}

			sleep( $interval );
		}

		$backtrace = debug_backtrace();

		throw new Exception(
			"Timeout thrown by " . $backtrace[1]['class'] . "::" . $backtrace[1]['function'] . "()\n" . $backtrace[1]['file'] . ", line " . $backtrace[1]['line']
		);

	}/* spin() */


	/**
	 * Overrides MinkContext method by adding a spin
	 *
	 * {@inheritdoc}
	 */
	public function assertPageContainsText( $text ) {

		return $this->spin(
			function ( $context ) use ( $text ) {
				$text = str_replace( '\\"', '"', $text );
				$context->assertSession()->pageTextContains( $text );
				return true;
			}
		);

	}/* assertPageContainsText() */

	/**
	 * @Then I wait :seconds seconds
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function waitSeconds( $seconds ) {
		sleep( $seconds );
	}

	/**
	 * @When I scroll :elementId into view
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public function scrollIntoView( $elementId ) {

		$this->getSession()->executeScript( 'var elem = document.getElementById( "' . $elementId . '" ); elem.scrollIntoView( false );' );

	}/* scrollIntoView() */

}
