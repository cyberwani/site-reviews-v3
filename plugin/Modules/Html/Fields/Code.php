<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Fields;

use GeminiLabs\SiteReviews\Modules\Html\Fields\Field;

class Code extends Field
{
	/**
	 * @return string|void
	 */
	public function build()
	{
		$this->builder->tag = 'textarea';
		$this->mergeFieldArgs();
		return $this->builder->buildTag();
	}

	/**
	 * @return array
	 */
	public static function defaults()
	{
		return [
			'class' => 'large-text code',
		];
	}

	/**
	 * @return array
	 */
	public static function required()
	{
		return [
			'type' => 'textarea',
		];
	}
}
