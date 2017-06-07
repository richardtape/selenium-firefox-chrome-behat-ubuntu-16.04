<?php

namespace UBC;

class BehatUtils {

	/**
	 * Get the "name" of a scenario from the scope
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public static function getScenarioName( $scope ) {

		$scenario = $scope->getScenario();
		$scenarioName = $scenario->getTitle();

		return $scenarioName;

	}/* getScenarioName() */


	/**
	 * Get a usable filename from the scenario name. Replaces spaces with dashes, rawurlencodes and appends timestamp
	 *
	 * @since 1.0.0
	 *
	 * @param (string)
	 * @return (string)
	 */

	public static function getUsableFileNameFromScenarioName( $scenarioName ) {
		return str_replace( '%20', '-', strtolower( rawurlencode( $scenarioName ) . '-' . time() ) );
	}/* getUsableFileNameFromScenarioName() */


	/**
	 * Utility to test if a scenario passed given the scenario scope
	 *
	 * @since 1.0.0
	 *
	 * @param null
	 * @return null
	 */

	public static function scenarioHasPassed( $scope ) {

		// Find out if this scenario passed
		$passed = $scope->getTestResult()->isPassed();

		// if the scenario passed, we don't need a message
		if ( $passed ) {
			return true;
		}

		return false;

	}/* scenarioHasPassed() */


	/**
	 * Utility method to add the passed $message to a log file
	 *
	 * @since 1.0.0
	 *
	 * @param (string) $message - what to append to the log file
	 * @return null
	 */

	public static function addToLog( $message ) {
		file_put_contents( dirname( dirname( dirname( __FILE__ ) ) ) . '/debug.log', print_r( array( $message ), true ), FILE_APPEND );
	}/* addToLog() */

}/* BehatUtils */
