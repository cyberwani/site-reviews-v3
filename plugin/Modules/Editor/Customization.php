<?php

namespace GeminiLabs\SiteReviews\Modules\Editor;

use GeminiLabs\SiteReviews\Application;

class Customization
{
	/**
	 * @return array
	 */
	public function filterEditorSettings( array $settings )
	{
		if( $this->isReviewEditable() ) {
			$settings = [
				'media_buttons' => false,
				'quicktags' => false,
				'textarea_rows' => 12,
				'tinymce' => false,
			];
		}
		return $settings;
	}

	/**
	 * @param string $html
	 * @return string
	 */
	public function filterEditorTextarea( $html )
	{
		if( $this->isReviewEditable() ) {
			$html = str_replace( '<textarea', '<div id="ed_toolbar"></div><textarea', $html );
		}
		return $html;
	}

	/**
	 * @return void
	 */
	public function removeAutosave()
	{
		if( $this->isReviewEditor() && !$this->isReviewEditable() ) {
			wp_deregister_script( 'autosave' );
		}
	}

	/**
	 * @return void
	 */
	public function removeMetaBoxes()
	{
		remove_meta_box( 'slugdiv', Application::POST_TYPE, 'advanced' );
	}

	/**
	 * @return bool
	 */
	protected function isReviewEditable()
	{
		$postId = intval( filter_input( INPUT_GET, 'post' ));
		return $postId > 0
			&& get_post_meta( $postId, 'review_type', true ) == 'local'
			&& $this->isReviewEditor();
	}

	/**
	 * @return bool
	 */
	protected function isReviewEditor()
	{
		$screen = glsr_current_screen();
		return $screen->base == 'post'
			&& $screen->id == Application::POST_TYPE
			&& $screen->post_type == Application::POST_TYPE;
	}
}
