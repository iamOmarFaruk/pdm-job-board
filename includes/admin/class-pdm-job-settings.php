<?php
namespace PDMJB\Admin;

if (!defined('ABSPATH')) {
    exit;
}

class Job_Settings
{

    public function __construct()
    {
        add_action('admin_menu', [$this, 'register_menu_page']);
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

    public function render_settings_page()
    {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <p><?php esc_html_e('Settings coming soon.', 'pdm-job-board'); ?></p>
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
