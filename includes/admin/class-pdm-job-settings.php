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

        add_settings_field(
            'grid_columns',
            __('Grid Columns', 'pdm-job-board'),
            [$this, 'render_grid_columns_field'],
            'pdm-job-board-settings',
            'pdmjb_general_section'
        );

        add_settings_field(
            'primary_color',
            __('Primary Color', 'pdm-job-board'),
            [$this, 'render_primary_color_field'],
            'pdm-job-board-settings',
            'pdmjb_general_section'
        );
    }

    public function render_layout_field()
    {
        $options = get_option($this->option_name);
        $value = isset($options['layout_view']) ? $options['layout_view'] : 'grid';
        ?>
        <select name="<?php echo esc_attr($this->option_name); ?>[layout_view]" id="pdmjb_layout_view">
            <option value="grid" <?php selected($value, 'grid'); ?>><?php esc_html_e('Grid View', 'pdm-job-board'); ?></option>
            <option value="list" <?php selected($value, 'list'); ?>><?php esc_html_e('List View', 'pdm-job-board'); ?></option>
        </select>
        <p class="description"><?php esc_html_e('Select the default layout for the job listings page.', 'pdm-job-board'); ?></p>
        <?php
    }

    public function render_grid_columns_field()
    {
        $options = get_option($this->option_name);
        $value = isset($options['grid_columns']) ? intval($options['grid_columns']) : 3;
        ?>
        <select name="<?php echo esc_attr($this->option_name); ?>[grid_columns]" id="pdmjb_grid_columns">
            <option value="2" <?php selected($value, 2); ?>>2</option>
            <option value="3" <?php selected($value, 3); ?>>3</option>
            <option value="4" <?php selected($value, 4); ?>>4</option>
        </select>
        <p class="description"><?php esc_html_e('Select how many posts per line to display in Grid View.', 'pdm-job-board'); ?>
        </p>
        <script>
            jQuery(document).ready(function ($) {
                function toggleGridInputs() {
                    const layout = $('#pdmjb_layout_view').val();
                    const $gridRow = $('#pdmjb_grid_columns').closest('tr');
                    if (layout === 'grid') {
                        $gridRow.show();
                    } else {
                        $gridRow.hide();
                    }
                }
                $('#pdmjb_layout_view').on('change', toggleGridInputs);
                toggleGridInputs(); // Initial check
            });
        </script>
        <?php
    }

    public function render_primary_color_field()
    {
        $options = get_option($this->option_name);
        $value = isset($options['primary_color']) ? $options['primary_color'] : '#000000';
        ?>
        <input type="color" name="<?php echo esc_attr($this->option_name); ?>[primary_color]" value="<?php echo esc_attr($value); ?>">
        <p class="description"><?php esc_html_e('Select the primary color for the job board (default is #000000).', 'pdm-job-board'); ?></p>
        <?php
    }

    public function sanitize_settings($input)
    {
        $new_input = [];
        if (isset($input['layout_view'])) {
            $new_input['layout_view'] = sanitize_text_field($input['layout_view']);
        }
        if (isset($input['grid_columns'])) {
            $new_input['grid_columns'] = intval($input['grid_columns']);
        }
        if (isset($input['primary_color'])) {
            $new_input['primary_color'] = sanitize_hex_color($input['primary_color']);
        }
        return $new_input;
    }

    public function render_settings_page()
    {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <?php settings_errors(); ?>
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
