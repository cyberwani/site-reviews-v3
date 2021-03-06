<?php

namespace GeminiLabs\SiteReviews\Modules;

use DateTime;
use GeminiLabs\SiteReviews\Application;
use ReflectionClass;

class Logger
{
	const ALERT = 'alert';
	const CRITICAL  = 'critical';
	const DEBUG = 'debug';
	const EMERGENCY = 'emergency';
	const ERROR = 'error';
	const INFO = 'info';
	const NOTICE = 'notice';
	const WARNING = 'warning';

	protected $file;
	protected $log;

	public function __construct( Application $app )
	{
		$this->file = $app->path( 'debug.log' );
		$this->log = file_exists( $this->file )
			? file_get_contents( $this->file )
			: '';
		$this->reset();
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->get();
	}

	/**
	 * Action must be taken immediately
	 * Example: Entire website down, database unavailable, etc. This should trigger the SMS alerts and wake you up
	 * @param mixed $message
	 * @param array $context
	 * @return static
	 */
	public function alert( $message, array $context = [] )
	{
		return $this->log( static::ALERT, $message, $context );
	}

	/**
	 * @return void
	 */
	public function clear()
	{
		$this->log = '';
		file_put_contents( $this->file, $this->log );
	}

	/**
	 * Critical conditions
	 * Example: Application component unavailable, unexpected exception
	 * @param mixed $message
	 * @param array $context
	 * @return static
	 */
	public function critical( $message, array $context = [] )
	{
		return $this->log( static::CRITICAL, $message, $context );
	}

	/**
	 * Detailed debug information
	 * @param mixed $message
	 * @param array $context
	 * @return static
	 */
	public function debug( $message, array $context = [] )
	{
		return $this->log( static::DEBUG, $message, $context );
	}

	/**
	 * System is unusable
	 * @param mixed $message
	 * @param array $context
	 * @return static
	 */
	public function emergency( $message, array $context = [] )
	{
		return $this->log( static::EMERGENCY, $message, $context );
	}

	/**
	 * Runtime errors that do not require immediate action but should typically be logged and monitored
	 * @param mixed $message
	 * @param array $context
	 * @return static
	 */
	public function error( $message, array $context = [] )
	{
		return $this->log( static::ERROR, $message, $context );
	}

	/**
	 * @return string
	 */
	public function get()
	{
		return empty( $this->log )
			? __( 'Log is empty', 'site-reviews' )
			: $this->log;
	}

	/**
	 * Interesting events
	 * Example: User logs in, SQL logs
	 * @param mixed $message
	 * @param array $context
	 * @return static
	 */
	public function info( $message, array $context = [] )
	{
		return $this->log( static::INFO, $message, $context );
	}

	/**
	 * @param mixed $level
	 * @param mixed $message
	 * @return static
	 */
	public function log( $level, $message, array $context = [] )
	{
		$constants = (new ReflectionClass( __CLASS__ ))->getConstants();
		$constants = (array)apply_filters( 'site-reviews/log-levels', $constants );
		if( in_array( $level, $constants, true )) {
			$entry = $this->buildLogEntry( $level, $message, $context );
			file_put_contents( $this->file, $entry, FILE_APPEND|LOCK_EX );
			$this->reset();
		}
		return $this;
	}

	/**
	 * Normal but significant events
	 * @param mixed $message
	 * @param array $context
	 * @return static
	 */
	public function notice( $message, array $context = [] )
	{
		return $this->log( static::NOTICE, $message, $context );
	}

	/**
	 * Exceptional occurrences that are not errors
	 * Example: Use of deprecated APIs, poor use of an API, undesirable things that are not necessarily wrong
	 * @param mixed $message
	 * @param array $context
	 * @return static
	 */
	public function warning( $message, array $context = [] )
	{
		return $this->log( static::WARNING, $message, $context );
	}

	/**
	 * @param string $level
	 * @param mixed $message
	 * @return string
	 */
	protected function buildLogEntry( $level, $message, array $context = [] )
	{
		return sprintf( '[%s] %s: %s%s'.PHP_EOL,
			current_time( 'mysql' ),
			strtoupper( $level ),
			$this->getDebugInformation(),
			$this->interpolate( $message, $context )
		);
	}

	/**
	 * @return void|string
	 */
	protected function getDebugInformation()
	{
		$caller = debug_backtrace( 0, 6 );
		$index = array_search( 'log', array_column( $caller, 'function' ));
		if( $index === false
			|| !isset( $caller[$index+2]['class'] )
			|| !isset( $caller[$index+2]['function'] )
		)return;
		return sprintf( '[%s()->%s:%s] ',
			$caller[$index+2]['class'],
			$caller[$index+2]['function'],
			$caller[$index+1]['line']
		);
	}

	/**
	 * Interpolates context values into the message placeholders
	 * @param mixed $message
	 * @param array $context
	 * @return string
	 */
	protected function interpolate( $message, $context = [] )
	{
		if( $this->isObjectOrArray( $message ) || !is_array( $context )) {
			return print_r( $message, true );
		}
		$replace = [];
		foreach( $context as $key => $value ) {
			$replace['{'.$key.'}'] = $this->normalizeValue( $value );
		}
		return strtr( $message, $replace );
	}

	/**
	 * @param mixed $value
	 * @return bool
	 */
	protected function isObjectOrArray( $value )
	{
		return is_object( $value ) || is_array( $value );
	}

	/**
	 * @param mixed $value
	 * @return string
	 */
	protected function normalizeValue( $value )
	{
		if( $value instanceof DateTime ) {
			$value = $value->format( 'Y-m-d H:i:s' );
		}
		else if( $this->isObjectOrArray( $value )) {
			$value = json_encode( $value );
		}
		return (string)$value;
	}

	/**
	 * @return void
	 */
	protected function reset()
	{
		if( file_exists( $this->file )
			&& filesize( $this->file ) > pow( 1024, 2 ) / 2 ) {
			$this->clear();
			file_put_contents(
				$this->file,
				$this->buildLogEntry( 'info', __( 'Log has been automatically reset (512 KB max size)', 'site-reviews' ))
			);
		}
	}
}
