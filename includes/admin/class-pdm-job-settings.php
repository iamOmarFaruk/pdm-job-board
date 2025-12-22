<?php
namespace PDMJB\Admin;

if (!defined('ABSPATH')) {
    exit;
}

class Job_Settings
{
	private $option_name = 'pdmjb_settings';

	public function __construct()
	{
		add_action('admin_menu', [$this, 'register_menu_page']);
		add_action('admin_init', [$this, 'register_settings']);
	}

	public function register_menu_page()
	{
		add_submenu_page(
			'edit.php?post_type=job',
			__('Settings', 'pdm-job-board'),
			__('Settings', 'pdm-job-board'),
			'manage_options',
			'pdm-job-board-settings',
			[$this, 'render_settings_page']
		);
	}

	public function register_settings()
	{
		register_setting(
			'pdmjb_settings_group',
			$this->option_name,
			[$this, 'sanitize_settings']
		);

		add_settings_section(
			'pdmjb_general_section',
			__('General Settings', 'pdm-job-board'),
			null,
			'pdm-job-board-settings'
		);

		add_settings_field(
			'layout_view',
			__('Job Listing Layout', 'pdm-job-board'),
			[$this, 'render_layout_field'],
			'pdm-job-board-settings',
			'pdmjb_general_section'
		);
	}

	public function render_layout_field()
	{
		$options = get_option($this->option_name);
		$value = isset($options['layout_view']) ? $options['layout_view'] : 'grid';
		?>
		<select name="<?php echo esc_attr($this->option_name); ?>[layout_view]">
			<option value="grid" <?php selected($value, 'grid'); ?>><?php esc_html_e('Grid View', 'pdm-job-board'); ?></option>
			<option value="list" <?php selected($value, 'list'); ?>><?php esc_html_e('List View', 'pdm-job-board'); ?></option>
		</select>
		<p class="description"><?php esc_html_e('Select the default layout for the job listings page.', 'pdm-job-board'); ?></p>
		<?php
	}

	public function sanitize_settings($input)
	{
		$new_input = [];
		if (isset($input['layout_view'])) {
			$new_input['layout_view'] = sanitize_text_field($input['layout_view']);
		}
		return $new_input;
	}

	public function render_settings_page()
	{
		?>
		<div class="wrap">
			<h1><?php echo esc_html(get_admin_page_title()); ?></h1>
			<form action="options.php" method="post">
				<?php
				settings_fields('pdmjb_settings_group');
				do_settings_sections('pdm-job-board-settings');
				submit_button();
				?>
			</form>
		</div>
		<?php
	}
}
/*
 * ┌── o m a r ──┐
 * │ gh@iamOmarFaruk
 * │ omarfaruk.dev
 * │ Created: 2025-12-22
 * │ Updated: 2025-12-22
 * └─ pdm-job-board ───┘
 */
