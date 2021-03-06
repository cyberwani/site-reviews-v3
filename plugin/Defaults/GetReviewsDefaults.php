<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class GetReviewsDefaults extends Defaults
{
	/**
	 * @return array
	 */
	public function defaults()
	{
		return [
			'assigned_to' => '',
			'category' => '',
			'count' => 10,
			'offset' => '',
			'order' => 'DESC',
			'orderby' => 'date',
			'pagination' => false,
			'post__in' => [],
			'post__not_in' => [],
			'rating' => '',
			'type' => '',
		];
	}
}
