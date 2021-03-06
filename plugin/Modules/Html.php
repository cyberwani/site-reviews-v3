<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Html\Partial;
use GeminiLabs\SiteReviews\Modules\Html\Settings;
use GeminiLabs\SiteReviews\Modules\Html\Template;
use GeminiLabs\SiteReviews\Modules\Notice;

class Html
{
	/**
	 * @var Builder
	 */
	protected $builder;

	public function __construct( Builder $builder )
	{
		$this->builder = $builder;
	}

	/**
	 * @return Builder
	 */
	public function build()
	{
		$this->builder->render = false;
		return $this->builder;
	}

	/**
	 * @param string $partialPath
	 * @return string
	 */
	public function buildPartial( $partialPath, array $args = [] )
	{
		return glsr( Partial::class )->build( $partialPath, $args );
	}

	/**
	 * @param string $id
	 * @return void|string
	 */
	public function buildSettings( $id )
	{
		return glsr( Settings::class )->buildFields( $id );
	}

	/**
	 * @param string $templatePath
	 * @return void|string
	 */
	public function buildTemplate( $templatePath, array $args = [] )
	{
		return glsr( Template::class )->build( $templatePath, $args );
	}

	/**
	 * @return Builder
	 */
	public function render()
	{
		$this->builder->render = true;
		return $this->builder;
	}

	/**
	 * @return void
	 */
	public function renderNotices()
	{
		$this->render()->div( glsr( Notice::class )->get(), [
			'id' => 'glsr-notices',
		]);
	}

	/**
	 * @param string $partialPath
	 * @return void
	 */
	public function renderPartial( $partialPath, array $args = [] )
	{
		echo $this->buildPartial( $partialPath, $args );
	}

	/**
	 * @param string $id
	 * @return void
	 */
	public function renderSettings( $id )
	{
		echo $this->buildSettings( $id );
	}

	/**
	 * @param string $templatePath
	 * @return void
	 */
	public function renderTemplate( $templatePath, array $args = [] )
	{
		echo $this->buildTemplate( $templatePath, $args );
	}
}
