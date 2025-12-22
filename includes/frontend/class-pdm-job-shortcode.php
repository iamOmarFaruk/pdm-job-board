<?php
namespace PDMJB\Frontend;

use PDMJB\Post_Types\Job_CPT;

if (!defined('ABSPATH')) {
	exit;
}

class Job_Shortcode
{
	public function __construct()
	{
		add_action('wp_enqueue_scripts', [$this, 'register_assets']);
		add_shortcode('jobs', [$this, 'render']);
	}

	private function render_global_modal(string $modal_id, int $post_id, string $job_title, string $thumb_url, array $dept_names, string $location, string $job_type, string $workspace_type, string $salary, string $deadline, string $resps, string $quals, string $benefits, string $contact, string $form_shortcode): void
	{
		echo '<div class="pdmjb-modal pdmjb-modal--global" id="' . esc_attr($modal_id) . '" role="dialog" aria-modal="true" aria-hidden="true" aria-label="' . esc_attr__('Job details for', 'pdm-job-board') . ' ' . esc_attr($job_title) . '">';
		echo '<div class="pdmjb-modal__overlay"></div>';
		echo '<div class="pdmjb-modal__dialog pdmjb-modal__dialog--wide" role="document">';
		echo '<div class="pdmjb-modal__header">';
		echo '<h3 class="pdmjb-modal__title">' . esc_html($job_title) . '</h3>';
		echo '<button class="pdmjb-modal__close" type="button" aria-label="' . esc_attr__('Close', 'pdm-job-board') . '" data-pdmjb-close>&times;</button>';
		echo '</div>';
		echo '<div class="pdmjb-modal__two-col">';
		// Left: details
		echo '<div class="pdmjb-modal__left">';
		if ($thumb_url) {
			echo '<div class="pdmjb-detail__cover"><img src="' . esc_url($thumb_url) . '" alt="' . esc_attr($job_title) . '" /></div>';
		}
		// Title now in sticky header
		// Meta grid
		echo '<div class="pdmjb-detail__meta">';
		if ($location) {
			echo '<div class="pdmjb-detail__meta-item"><i class="pdmjb-icon fa-solid fa-location-dot" aria-hidden="true"></i><span class="pdmjb-meta__label">' . esc_html__('Location:', 'pdm-job-board') . '</span> ' . esc_html($location) . '</div>';
		}
		if (!empty($dept_names)) {
			echo '<div class="pdmjb-detail__meta-item"><i class="pdmjb-icon fa-solid fa-layer-group" aria-hidden="true"></i><span class="pdmjb-meta__label">' . esc_html__('Department:', 'pdm-job-board') . '</span> ' . esc_html(implode(', ', $dept_names)) . '</div>';
		}
		if ($job_type) {
			echo '<div class="pdmjb-detail__meta-item"><i class="pdmjb-icon fa-solid fa-briefcase" aria-hidden="true"></i><span class="pdmjb-meta__label">' . esc_html__('Type:', 'pdm-job-board') . '</span> ' . esc_html($job_type) . '</div>';
		}
		if ($workspace_type) {
			echo '<div class="pdmjb-detail__meta-item"><i class="pdmjb-icon fa-solid fa-building" aria-hidden="true"></i><span class="pdmjb-meta__label">' . esc_html__('Workspace:', 'pdm-job-board') . '</span> ' . esc_html($workspace_type) . '</div>';
		}
		if ($salary) {
			echo '<div class="pdmjb-detail__meta-item"><i class="pdmjb-icon fa-solid fa-money-bill-wave" aria-hidden="true"></i><span class="pdmjb-meta__label">' . esc_html__('Salary:', 'pdm-job-board') . '</span> ' . esc_html($salary) . '</div>';
		}
		if ($deadline) {
			$ts = strtotime($deadline);
			$display_deadline = $ts ? date_i18n(get_option('date_format'), $ts) : $deadline;
			echo '<div class="pdmjb-detail__meta-item"><i class="pdmjb-icon fa-solid fa-calendar-days" aria-hidden="true"></i><span class="pdmjb-meta__label">' . esc_html__('Apply By:', 'pdm-job-board') . '</span> ' . esc_html($display_deadline) . '</div>';
		}
		echo '</div>';

		// Description and sections
		$content = get_post_field('post_content', $post_id);
		if ($content) {
			echo '<div class="pdmjb-detail__section">';
			echo '<div class="pdmjb-detail__heading">' . esc_html__('Job Description', 'pdm-job-board') . '</div>';
			// Image displayed above description (see pdmjb-detail__cover)
			echo apply_filters('the_content', $content);
			echo '</div>';
		}
		$render_card_section = function (string $heading, string $raw_text, string $icon_class): void {
			$lines = array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $raw_text)));
			if (empty($lines)) {
				return;
			}
			echo '<div class="pdmjb-detail__card">';
			echo '<div class="pdmjb-detail__heading">' . esc_html($heading) . '</div>';
			echo '<ul class="pdmjb-detail__list">';
			foreach ($lines as $line) {
				echo '<li><i class="pdmjb-detail__list-icon fa-solid fa-circle-check" aria-hidden="true"></i><span>' . esc_html($line) . '</span></li>';
			}
			echo '</ul>';
			echo '</div>';
		};
		// Group into horizontal cards
		echo '<div class="pdmjb-detail__cards">';
		if ($resps) {
			$render_card_section(__('Responsibilities', 'pdm-job-board'), $resps, 'fa-solid fa-circle-check');
		}
		if ($quals) {
			$render_card_section(__('Qualifications', 'pdm-job-board'), $quals, 'fa-solid fa-clipboard-list');
		}
		if ($benefits) {
			$render_card_section(__('Benefits', 'pdm-job-board'), $benefits, 'fa-solid fa-gift');
		}
		echo '</div>';
		echo '</div>'; // end left

		// Right: application form
		echo '<div class="pdmjb-modal__right">';
		echo '<div class="pdmjb-apply__heading">' . esc_html__('Apply Now', 'pdm-job-board') . '</div>';
		if (!empty($contact)) {
			echo '<div class="pdmjb-modal__contact">' . esc_html__('Contact:', 'pdm-job-board') . ' ' . esc_html($contact) . '</div>';
		}
		echo '<div class="pdmjb-apply__box">';
		if (!empty($form_shortcode)) {
			// Intentionally unescaped to render the actual form HTML
			echo do_shortcode($form_shortcode);
		} else {
			echo '<div>' . esc_html__('No application form attached for this job.', 'pdm-job-board') . '</div>';
		}
		echo '</div>';
		echo '</div>'; // end right

		echo '</div>'; // end two-col
		echo '</div>';
		echo '</div>';
	}

	public function register_assets(): void
	{
		wp_register_style('pdmjb-job-board', PDMJB_PLUGIN_URL . 'assets/css/pdm-job-board.css', [], PDMJB_VERSION);
		// Icon library (Font Awesome via CDN)
		wp_register_style('pdmjb-icons', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css', [], '6.6.0');
		wp_register_script('pdmjb-job-board', PDMJB_PLUGIN_URL . 'assets/js/pdm-job-board.js', [], PDMJB_VERSION, true);
	}

	public function render($atts = []): string
	{
		$atts = shortcode_atts([
			'posts_per_page' => 10,
			'department' => '', // taxonomy slug filter (optional)
			'job_type' => '', // meta filter (optional)
			'workspace' => '', // meta filter (optional)
		], $atts, 'jobs');

		wp_enqueue_style('pdmjb-icons');
		wp_enqueue_style('pdmjb-job-board');
		wp_enqueue_script('pdmjb-job-board');

		$args = [
			'post_type' => 'job',
			'posts_per_page' => intval($atts['posts_per_page']),
			'orderby' => 'date',
			'order' => 'DESC',
		];

		$tax_query = [];
		if (!empty($atts['department'])) {
			$tax_query[] = [
				'taxonomy' => 'job_department',
				'field' => 'slug',
				'terms' => array_map('sanitize_title', array_map('trim', explode(',', (string) $atts['department']))),
			];
		}
		if (!empty($tax_query)) {
			$args['tax_query'] = $tax_query;
		}

		$meta_query = [];
		if (!empty($atts['job_type'])) {
			$meta_query[] = [
				'key' => 'pdmjb_job_type',
				'value' => sanitize_text_field((string) $atts['job_type']),
			];
		}
		if (!empty($atts['workspace'])) {
			$meta_query[] = [
				'key' => 'pdmjb_workspace_type',
				'value' => sanitize_text_field((string) $atts['workspace']),
			];
		}
		if (!empty($meta_query)) {
			$args['meta_query'] = $meta_query;
		}

		$q = new \WP_Query($args);

		if (!$q->have_posts()) {
			return '<div class="pdmjb-jobs pdmjb-jobs--empty">
				<div class="pdmjb-empty-state">
					<div class="pdmjb-empty-state__icon">
						<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
					</div>
					<h3 class="pdmjb-empty-state__title">' . esc_html__('No Job Openings Available', 'pdm-job-board') . '</h3>
					<p class="pdmjb-empty-state__message">' . esc_html__('There are no job openings available at the moment. Please check back later for new opportunities.', 'pdm-job-board') . '</p>
				</div>
			</div>';
		}

		$settings = get_option('pdmjb_settings');
		$layout_view = isset($settings['layout_view']) ? $settings['layout_view'] : 'grid';

		ob_start();

		$container_class = 'pdmjb-jobs';
		if ($layout_view === 'list') {
			$container_class .= ' pdmjb-jobs--list';
		}

		echo '<div class="' . esc_attr($container_class) . '">';
		while ($q->have_posts()):
			$q->the_post();
			$this->render_job_card(get_the_ID());
		endwhile;
		echo '</div>';
		wp_reset_postdata();

		return (string) ob_get_clean();
	}

	private function render_job_card(int $post_id): void
	{
		$title = get_the_title($post_id);
		$link = get_permalink($post_id);
		$thumb = get_the_post_thumbnail_url($post_id, 'large');
		$excerpt = has_excerpt($post_id) ? get_the_excerpt($post_id) : wp_trim_words(wp_kses_post(get_post_field('post_content', $post_id)), 32);

		$location = get_post_meta($post_id, 'pdmjb_location', true);
		$job_type = get_post_meta($post_id, 'pdmjb_job_type', true);
		$workspace_type = get_post_meta($post_id, 'pdmjb_workspace_type', true);
		$salary = get_post_meta($post_id, 'pdmjb_salary', true);
		$deadline = get_post_meta($post_id, 'pdmjb_deadline', true);
		$contact = get_post_meta($post_id, 'pdmjb_contact', true);
		$form_shortcode = get_post_meta($post_id, 'pdmjb_application_form_shortcode', true);
		$resps = get_post_meta($post_id, 'pdmjb_responsibilities', true);
		$quals = get_post_meta($post_id, 'pdmjb_qualifications', true);
		$benefits = get_post_meta($post_id, 'pdmjb_benefits', true);

		// Fallback: if no explicit form shortcode is set, try to detect one in content
		if (empty($form_shortcode)) {
			$content_for_detection = get_post_field('post_content', $post_id);
			$detected = $this->detect_form_shortcode_in_content((string) $content_for_detection);
			if (!empty($detected)) {
				$form_shortcode = $detected;
			}
		}

		$dept_terms = get_the_terms($post_id, 'job_department');
		$dept_names = [];
		if ($dept_terms && !is_wp_error($dept_terms)) {
			foreach ($dept_terms as $t) {
				$dept_names[] = $t->name;
			}
		}

		$global_modal_id = 'pdmjb-modal-global-' . $post_id;

		echo '<article class="pdmjb-card" data-pdmjb-open="#' . esc_attr($global_modal_id) . '">';
		if ($thumb) {
			echo '<a class="pdmjb-card__media" href="#" aria-label="' . esc_attr($title) . '" data-pdmjb-open="#' . esc_attr($global_modal_id) . '">';
			echo '<img src="' . esc_url($thumb) . '" alt="' . esc_attr($title) . '" loading="lazy" />';
			echo '</a>';
		}
		echo '<div class="pdmjb-card__body">';
		echo '<div class="pdmjb-card__info">';
		echo '<h3 class="pdmjb-card__title"><a href="#" data-pdmjb-open="#' . esc_attr($global_modal_id) . '">' . esc_html($title) . '</a></h3>';

		echo '<ul class="pdmjb-card__meta">';
		if ($location) {
			echo '<li class="pdmjb-card__meta-item"><i class="pdmjb-icon fa-solid fa-location-dot" aria-hidden="true"></i><span class="pdmjb-meta__label">' . esc_html__('Location:', 'pdm-job-board') . '</span> ' . esc_html($location) . '</li>';
		}
		if ($job_type) {
			echo '<li class="pdmjb-card__meta-item"><i class="pdmjb-icon fa-solid fa-briefcase" aria-hidden="true"></i><span class="pdmjb-meta__label">' . esc_html__('Type:', 'pdm-job-board') . '</span> ' . esc_html($job_type) . '</li>';
		}
		if ($workspace_type) {
			echo '<li class="pdmjb-card__meta-item"><i class="pdmjb-icon fa-solid fa-building" aria-hidden="true"></i><span class="pdmjb-meta__label">' . esc_html__('Workspace:', 'pdm-job-board') . '</span> ' . esc_html($workspace_type) . '</li>';
		}
		if ($salary) {
			echo '<li class="pdmjb-card__meta-item"><i class="pdmjb-icon fa-solid fa-money-bill-wave" aria-hidden="true"></i><span class="pdmjb-meta__label">' . esc_html__('Salary:', 'pdm-job-board') . '</span> ' . esc_html($salary) . '</li>';
		}
		if (!empty($dept_names)) {
			echo '<li class="pdmjb-card__meta-item"><i class="pdmjb-icon fa-solid fa-layer-group" aria-hidden="true"></i><span class="pdmjb-meta__label">' . esc_html__('Department:', 'pdm-job-board') . '</span> ' . esc_html(implode(', ', $dept_names)) . '</li>';
		}
		if ($deadline) {
			$ts = strtotime($deadline);
			$display_date = $ts ? date_i18n(get_option('date_format'), $ts) : $deadline;
			echo '<li class="pdmjb-card__meta-item"><i class="pdmjb-icon fa-solid fa-calendar-days" aria-hidden="true"></i><span class="pdmjb-meta__label">' . esc_html__('Apply By:', 'pdm-job-board') . '</span> ' . esc_html($display_date) . '</li>';
		}
		echo '</ul>';

		if ($excerpt) {
			echo '<div class="pdmjb-card__excerpt">' . esc_html($excerpt) . '</div>';
		}
		echo '</div>'; // end info

		echo '<div class="pdmjb-card__actions">';
		echo '<button class="pdmjb-btn" type="button" data-pdmjb-open="#' . esc_attr($global_modal_id) . '">' . esc_html__('View Details', 'pdm-job-board') . '</button>';
		echo '<button class="pdmjb-btn pdmjb-btn--primary" type="button" data-pdmjb-open="#' . esc_attr($global_modal_id) . '">' . esc_html__('Apply Now', 'pdm-job-board') . '</button>';
		echo '</div>';
		echo '</div>';
		echo '</article>';

		// Global modal containing details (left) and form (right)
		$this->render_global_modal(
			$global_modal_id,
			$post_id,
			$title,
			(string) $thumb,
			$dept_names,
			(string) $location,
			(string) $job_type,
			(string) $workspace_type,
			(string) $salary,
			(string) $deadline,
			(string) $resps,
			(string) $quals,
			(string) $benefits,
			(string) $contact,
			(string) $form_shortcode
		);
	}

	/**
	 * Detect the first likely form shortcode within given content.
	 * Supports popular form plugins and any shortcode whose tag contains "form".
	 */
	private function detect_form_shortcode_in_content(string $content): string
	{
		if ($content === '') {
			return '';
		}
		$likely_form_tags = [
			'wpforms',
			'contact-form-7',
			'contact-form',
			'gravityform',
			'ninja_form',
			'ninja_forms',
			'ninja_forms_display_form',
			'formidable',
			'fluentform',
			'everest_forms',
			'forminator_form',
			'happyforms',
			'weforms',
			'quform',
			'caldera_form',
			'mc4wp_form',
			'wpuf_form'
		];
		$pattern = get_shortcode_regex();
		if (preg_match_all('/' . $pattern . '/s', $content, $matches, PREG_SET_ORDER)) {
			foreach ($matches as $m) {
				$tag = isset($m[2]) ? (string) $m[2] : '';
				if ($tag === '') {
					continue;
				}
				if (in_array($tag, $likely_form_tags, true) || stripos($tag, 'form') !== false) {
					return (string) $m[0];
				}
			}
		}
		return '';
	}

	private function render_details_modal(string $modal_id, int $post_id, string $job_title, array $dept_names, string $location, string $job_type, string $workspace_type, string $salary, string $deadline, string $resps, string $quals, string $benefits, string $apply_modal_selector): void
	{
		echo '<div class="pdmjb-modal" id="' . esc_attr($modal_id) . '" role="dialog" aria-modal="true" aria-hidden="true" aria-label="' . esc_attr__('Job details for', 'pdm-job-board') . ' ' . esc_attr($job_title) . '">';
		echo '<div class="pdmjb-modal__overlay"></div>';
		echo '<div class="pdmjb-modal__dialog" role="document">';
		echo '<button class="pdmjb-modal__close" type="button" aria-label="' . esc_attr__('Close', 'pdm-job-board') . '" data-pdmjb-close>&times;</button>';
		echo '<h3 class="pdmjb-modal__title">' . esc_html($job_title) . '</h3>';
		echo '<div class="pdmjb-modal__content">';

		// Meta grid with icons
		echo '<div class="pdmjb-detail__meta">';
		if ($location) {
			echo '<div class="pdmjb-detail__meta-item"><i class="pdmjb-icon fa-solid fa-location-dot" aria-hidden="true"></i><span class="pdmjb-meta__label">' . esc_html__('Location:', 'pdm-job-board') . '</span> ' . esc_html($location) . '</div>';
		}
		if (!empty($dept_names)) {
			echo '<div class="pdmjb-detail__meta-item"><i class="pdmjb-icon fa-solid fa-layer-group" aria-hidden="true"></i><span class="pdmjb-meta__label">' . esc_html__('Department:', 'pdm-job-board') . '</span> ' . esc_html(implode(', ', $dept_names)) . '</div>';
		}
		if ($job_type) {
			echo '<div class="pdmjb-detail__meta-item"><i class="pdmjb-icon fa-solid fa-briefcase" aria-hidden="true"></i><span class="pdmjb-meta__label">' . esc_html__('Type:', 'pdm-job-board') . '</span> ' . esc_html($job_type) . '</div>';
		}
		if ($workspace_type) {
			echo '<div class="pdmjb-detail__meta-item"><i class="pdmjb-icon fa-solid fa-building" aria-hidden="true"></i><span class="pdmjb-meta__label">' . esc_html__('Workspace:', 'pdm-job-board') . '</span> ' . esc_html($workspace_type) . '</div>';
		}
		if ($salary) {
			echo '<div class="pdmjb-detail__meta-item"><i class="pdmjb-icon fa-solid fa-money-bill-wave" aria-hidden="true"></i><span class="pdmjb-meta__label">' . esc_html__('Salary:', 'pdm-job-board') . '</span> ' . esc_html($salary) . '</div>';
		}
		if ($deadline) {
			$ts = strtotime($deadline);
			$display_deadline = $ts ? date_i18n(get_option('date_format'), $ts) : $deadline;
			echo '<div class="pdmjb-detail__meta-item"><i class="pdmjb-icon fa-solid fa-calendar-days" aria-hidden="true"></i><span class="pdmjb-meta__label">' . esc_html__('Apply By:', 'pdm-job-board') . '</span> ' . esc_html($display_deadline) . '</div>';
		}
		echo '</div>';

		// Description (full content)
		$content = get_post_field('post_content', $post_id);
		if ($content) {
			echo '<div class="pdmjb-detail__section">';
			echo '<div class="pdmjb-detail__heading">' . esc_html__('Job Description', 'pdm-job-board') . '</div>';
			echo apply_filters('the_content', $content);
			echo '</div>';
		}

		// Helper to render list sections
		$render_list_section = function (string $heading, string $raw_text): void {
			$lines = array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $raw_text)));
			if (empty($lines)) {
				return;
			}
			echo '<div class="pdmjb-detail__section">';
			echo '<div class="pdmjb-detail__heading">' . esc_html($heading) . '</div>';
			echo '<ul class="pdmjb-detail__list">';
			foreach ($lines as $line) {
				echo '<li>' . esc_html($line) . '</li>';
			}
			echo '</ul>';
			echo '</div>';
		};

		if ($resps) {
			$render_list_section(__('Responsibilities', 'pdm-job-board'), $resps);
		}
		if ($quals) {
			$render_list_section(__('Qualifications', 'pdm-job-board'), $quals);
		}
		if ($benefits) {
			$render_list_section(__('Benefits', 'pdm-job-board'), $benefits);
		}

		// Footer actions
		if ($apply_modal_selector) {
			echo '<div class="pdmjb-modal__footer">';
			echo '<button class="pdmjb-btn pdmjb-btn--primary" type="button" data-pdmjb-open="' . esc_attr($apply_modal_selector) . '">' . esc_html__('Apply Now', 'pdm-job-board') . '</button>';
			echo '</div>';
		}

		echo '</div>';
		echo '</div>';
		echo '</div>';
	}

	private function render_modal(string $modal_id, string $job_title, string $form_shortcode, string $contact): void
	{
		echo '<div class="pdmjb-modal" id="' . esc_attr($modal_id) . '" role="dialog" aria-modal="true" aria-hidden="true" aria-label="' . esc_attr__('Apply for', 'pdm-job-board') . ' ' . esc_attr($job_title) . '">';
		echo '<div class="pdmjb-modal__overlay"></div>';
		echo '<div class="pdmjb-modal__dialog" role="document">';
		echo '<button class="pdmjb-modal__close" type="button" aria-label="' . esc_attr__('Close', 'pdm-job-board') . '" data-pdmjb-close>&times;</button>';
		echo '<h3 class="pdmjb-modal__title">' . esc_html__('Apply for', 'pdm-job-board') . ' ' . esc_html($job_title) . '</h3>';
		if (!empty($contact)) {
			echo '<div class="pdmjb-modal__contact">' . esc_html__('Contact:', 'pdm-job-board') . ' ' . esc_html($contact) . '</div>';
		}
		echo '<div class="pdmjb-modal__content">';
		// Shortcode rendering is intentionally not escaped; it must output form HTML.
		echo do_shortcode($form_shortcode);
		echo '</div>';
		echo '</div>';
		echo '</div>';
	}
}


