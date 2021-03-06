<?php

namespace GeminiLabs\SiteReviews\Tests;

trait Setup
{
	public function setUp()
	{
		parent::setUp();
		$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
		$_SERVER['SERVER_NAME'] = '';
		$PHP_SELF = $GLOBALS['PHP_SELF'] = $_SERVER['PHP_SELF'] = '/index.php';
		glsr()->activate();
		$this->review = [
			'action' => 'submit-review',
			'content' => 'abcdefg',
			'email' => 'jane@doee.com',
			'excluded' => "[]",
			'form_id' => 'abcdef',
			'name' => 'Jane doe',
			'rating' => '5',
			'terms' => '1',
			'title' => 'Test Review',
			'_wp_http_referer' => $PHP_SELF,
		];
		// save initial plugin settings here if needed
	}
}
