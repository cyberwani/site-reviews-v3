<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Contracts\HooksContract;
use GeminiLabs\SiteReviews\Controllers\AdminController;
use GeminiLabs\SiteReviews\Controllers\EditorController;
use GeminiLabs\SiteReviews\Controllers\ListTableController;
use GeminiLabs\SiteReviews\Controllers\MainController;
use GeminiLabs\SiteReviews\Controllers\MenuController;
use GeminiLabs\SiteReviews\Controllers\PublicController;
use GeminiLabs\SiteReviews\Controllers\SettingsController;
use GeminiLabs\SiteReviews\Router;

class Actions implements HooksContract
{
	protected $admin;
	protected $app;
	protected $editor;
	protected $listtable;
	protected $menu;
	protected $main;
	protected $public;
	protected $router;
	protected $settings;

	public function __construct( Application $app ) {
		$this->app = $app;
		$this->admin = $app->make( AdminController::class );
		$this->editor = $app->make( EditorController::class );
		$this->listtable = $app->make( ListTableController::class );
		$this->main = $app->make( MainController::class );
		$this->menu = $app->make( MenuController::class );
		$this->public = $app->make( PublicController::class );
		$this->router = $app->make( Router::class );
		$this->settings = $app->make( SettingsController::class );
	}

	/**
	 * @return void
	 */
	public function run()
	{
		add_action( 'admin_enqueue_scripts',                        [$this->admin, 'enqueueAssets'] );
		add_action( 'admin_init',                                   [$this->admin, 'registerShortcodeButtons'] );
		add_action( 'edit_form_after_title',                        [$this->admin, 'renderReviewEditor'] );
		add_action( 'edit_form_top',                                [$this->admin, 'renderReviewNotice'] );
		add_action( 'media_buttons',                                [$this->admin, 'renderTinymceButton'], 11 );
		add_action( 'plugins_loaded',                               [$this->app, 'getDefaults'], 11 );
		add_action( 'plugins_loaded',                               [$this->app, 'registerAddons'] );
		add_action( 'plugins_loaded',                               [$this->app, 'registerLanguages'] );
		add_action( 'plugins_loaded',                               [$this->app, 'registerReviewTypes'] );
		add_action( 'upgrader_process_complete',                    [$this->app, 'upgraded'], 10, 2 );
		add_action( 'admin_enqueue_scripts',                        [$this->editor, 'customizePostStatusLabels'] );
		add_action( 'site-reviews/create/review',                   [$this->editor, 'onCreateReview'], 10, 3 );
		add_action( 'before_delete_post',                           [$this->editor, 'onDeleteReview'] );
		add_action( 'save_post_'.Application::POST_TYPE,            [$this->editor, 'onSaveReview'], 20, 2 );
		add_action( 'add_meta_boxes',                               [$this->editor, 'registerMetaBoxes'] );
		add_action( 'admin_print_scripts',                          [$this->editor, 'removeAutosave'], 999 );
		add_action( 'admin_menu',                                   [$this->editor, 'removeMetaBoxes'] );
		add_action( 'post_submitbox_misc_actions',                  [$this->editor, 'renderPinnedInPublishMetaBox'] );
		add_action( 'admin_action_revert',                          [$this->editor, 'revertReview'] );
		add_action( 'save_post_'.Application::POST_TYPE,            [$this->editor, 'saveMetaboxes'] );
		add_action( 'admin_action_approve',                         [$this->listtable, 'approve'] );
		add_action( 'bulk_edit_custom_box',                         [$this->listtable, 'renderBulkEditFields'], 10, 2 );
		add_action( 'restrict_manage_posts',                        [$this->listtable, 'renderColumnFilters'] );
		add_action( 'manage_posts_custom_column',                   [$this->listtable, 'renderColumnValues'], 10, 2 );
		add_action( 'save_post_'.Application::POST_TYPE,            [$this->listtable, 'saveBulkEditFields'] );
		add_action( 'pre_get_posts',                                [$this->listtable, 'setQueryForColumn'] );
		add_action( 'admin_action_unapprove',                       [$this->listtable, 'unapprove'] );
		add_action( 'init',                                         [$this->main, 'registerPostType'], 8 );
		add_action( 'init',                                         [$this->main, 'registerShortcodes'] );
		add_action( 'init',                                         [$this->main, 'registerTaxonomy'] );
		add_action( 'widgets_init',                                 [$this->main, 'registerWidgets'] );
		add_action( 'wp_footer',                                    [$this->main, 'renderSchema'] );
		add_action( 'admin_menu',                                   [$this->menu, 'registerMenuCount'] );
		add_action( 'admin_menu',                                   [$this->menu, 'registerSubMenus'] );
		add_action( 'admin_init',                                   [$this->menu, 'setCustomPermissions'], 999 );
		add_action( 'wp_enqueue_scripts',                           [$this->public, 'enqueueAssets'], 999 );
		add_action( 'admin_init',                                   [$this->router, 'routeAdminPostRequest'] );
		add_action( 'wp_ajax_'.Application::PREFIX.'action',        [$this->router, 'routeAjaxRequest'] );
		add_action( 'wp_ajax_nopriv_'.Application::PREFIX.'action', [$this->router, 'routeAjaxRequest'] );
		add_action( 'init',                                         [$this->router, 'routePublicPostRequest'] );
		add_action( 'admin_init',                                   [$this->router, 'routeWebhookRequest'] );
		add_action( 'admin_init',                                   [$this->settings, 'registerSettings'] );
	}
}
