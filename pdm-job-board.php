<?php
/*
Plugin Name: PDM Job Board
Description: Modular job board plugin with a custom post type, secure custom fields, and a [jobs] shortcode that renders an Apply Now modal with your form shortcode.
Version: 1.0.4
Author: PDM team and Omar
Author URI: https://www.purelydigitalmarketing.com/
Plugin URI: https://www.purelydigitalmarketing.com/
Text Domain: pdm-job-board
Domain Path: /languages

Usage — [jobs] shortcode
- posts_per_page: Number of jobs to show (default: 10)
- department: Department taxonomy slug(s), comma‑separated (optional)
- job_type: One of Full-time, Part-time, Contract, Internship (optional)
- workspace: One of On-site, Remote, Hybrid, In-field (optional)
Example: [jobs posts_per_page="6" department="engineering,marketing" job_type="Full-time" workspace="Hybrid"]
*/

if (!defined('ABSPATH')) {
	exit;
}

define('PDMJB_VERSION', '1.0.4');
define('PDMJB_PLUGIN_FILE', __FILE__);
define('PDMJB_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('PDMJB_PLUGIN_URL', plugin_dir_url(__FILE__));

// Auto-update mechanism
if (file_exists(PDMJB_PLUGIN_DIR . 'includes/plugin-update-checker/plugin-update-checker.php')) {
	require_once PDMJB_PLUGIN_DIR . 'includes/plugin-update-checker/plugin-update-checker.php';
	$myUpdateChecker = \YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
		'https://github.com/iamOmarFaruk/pdm-job-board',
		__FILE__,
		'pdm-job-board'
	);
	// We do NOT set setBranch('main') so it defaults to checking GitHub Releases (Tags), which is the safe way.
}

// Bootstrap the plugin classes
require_once PDMJB_PLUGIN_DIR . 'includes/class-pdm-job-board.php';

// Activation/deactivation
register_activation_hook(__FILE__, ['\\PDMJB\\Plugin', 'activate']);
register_deactivation_hook(__FILE__, ['\\PDMJB\\Plugin', 'deactivate']);

// Initialize
add_action('plugins_loaded', function () {
	\PDMJB\Plugin::instance();
});

// End of file


