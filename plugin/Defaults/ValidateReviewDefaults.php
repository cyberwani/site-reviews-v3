<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;
use GeminiLabs\SiteReviews\Helper;

class ValidateReviewDefaults extends Defaults
{
	/**
	 * @return array
	 */
	public function defaults()
	{
		$user = wp_get_current_user();
		return [
			'assign_to' => '',
			'category' => '',
			'content' => '',
			'email' => $user->exists() ? $user->user_email : '',
			'form_id' => '',
			'ip_address' => glsr( Helper::class )->getIpAddress(),
			'name' => $user->exists() ? $user->display_name : '',
			'rating' => '0',
			'terms' => '',
			'title' => '',
		];
	}
}
