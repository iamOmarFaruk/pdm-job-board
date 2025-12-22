<?php
namespace PDMJB;

if (!defined('ABSPATH')) {
	exit;
}

require_once PDMJB_PLUGIN_DIR . 'includes/post-types/class-pdm-job-cpt.php';
require_once PDMJB_PLUGIN_DIR . 'includes/frontend/class-pdm-job-shortcode.php';
require_once PDMJB_PLUGIN_DIR . 'includes/admin/class-pdm-job-metaboxes.php';
require_once PDMJB_PLUGIN_DIR . 'includes/admin/class-pdm-job-settings.php';

use PDMJB\Post_Types\Job_CPT;
use PDMJB\Frontend\Job_Shortcode;
use PDMJB\Admin\Job_Metaboxes;
use PDMJB\Admin\Job_Settings;

final class Plugin
{
	/** @var Plugin */
	private static $instance;

	/** @var Job_CPT */
	private $job_cpt;

	/** @var Job_Shortcode */
	private $shortcode;

	/** @var Job_Metaboxes */
	private $metaboxes;

	/** @var Job_Settings */
	private $settings;

	public static function instance()
	{
		if (!isset(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct()
	{
		add_action('init', [$this, 'load_textdomain']);

		$this->job_cpt = new Job_CPT();
		$this->shortcode = new Job_Shortcode();

		if (is_admin()) {
			$this->metaboxes = new Job_Metaboxes();
			$this->settings = new Job_Settings();
		}

		// Frontend: prevent direct single job pages; use global modal UX instead
		if (!is_admin()) {
			add_action('template_redirect', [$this, 'maybe_redirect_job_single']);
		}

		add_filter('plugin_row_meta', [$this, 'plugin_row_meta'], 10, 2);
	}

	public function maybe_redirect_job_single(): void
	{
		if (is_singular('job')) {
			$target = wp_get_referer();
			if (!$target) {
				$target = home_url('/');
			}
			wp_safe_redirect($target);
			exit;
		}
	}

	public function load_textdomain(): void
	{
		load_plugin_textdomain('pdm-job-board', false, dirname(plugin_basename(PDMJB_PLUGIN_FILE)) . '/languages');
	}

	public static function activate(): void
	{
		// Ensure CPT and taxonomy are registered before flushing
		$cpt = new Job_CPT();
		$cpt->register();
		flush_rewrite_rules();
	}

	public static function deactivate(): void
	{
		flush_rewrite_rules();
	}

	public function plugin_row_meta($links, $file)
	{
		if (strpos($file, 'pdm-job-board.php') !== false) {
			$links[] = '<a href="https://www.purelydigitalmarketing.com/" target="_blank" rel="noopener">' . esc_html__('Visit PDM', 'pdm-job-board') . '</a>';
		}
		return $links;
	}
}


