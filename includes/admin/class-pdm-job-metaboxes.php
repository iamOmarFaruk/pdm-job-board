<?php
namespace PDMJB\Admin;

use PDMJB\Post_Types\Job_CPT;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Job_Metaboxes {
	public function __construct() {
		add_action( 'add_meta_boxes', [ $this, 'add_boxes' ] );
		add_action( 'save_post', [ $this, 'save' ] );
	}

	public function add_boxes() : void {
		add_meta_box( 'pdmjb_job_details', __( 'Job Details', 'pdm-job-board' ), [ $this, 'render_details_box' ], 'job', 'normal', 'high' );
		add_meta_box( 'pdmjb_job_extras', __( 'Application & Additional Details', 'pdm-job-board' ), [ $this, 'render_extras_box' ], 'job', 'normal', 'default' );
	}

	public function render_details_box( \WP_Post $post ) : void {
		wp_nonce_field( 'pdmjb_save_meta', 'pdmjb_meta_nonce' );

		$location       = get_post_meta( $post->ID, 'pdmjb_location', true );
		$job_type       = get_post_meta( $post->ID, 'pdmjb_job_type', true );
		$workspace_type = get_post_meta( $post->ID, 'pdmjb_workspace_type', true );
		$salary         = get_post_meta( $post->ID, 'pdmjb_salary', true );
		$deadline       = get_post_meta( $post->ID, 'pdmjb_deadline', true );
		$contact        = get_post_meta( $post->ID, 'pdmjb_contact', true );

		$job_types       = Job_CPT::get_job_types();
		$workspace_types = Job_CPT::get_workspace_types();

		echo '<table class="form-table pdmjb-meta">';
		echo '<tbody>';
		// Location
		echo '<tr><th><label for="pdmjb_location">' . esc_html__( 'Location / Address', 'pdm-job-board' ) . '</label></th>';
		echo '<td><input type="text" id="pdmjb_location" name="pdmjb_location" class="regular-text" value="' . esc_attr( $location ) . '" /></td></tr>';
		// Job Type
		echo '<tr><th><label for="pdmjb_job_type">' . esc_html__( 'Job Type', 'pdm-job-board' ) . '</label></th>';
		echo '<td><select id="pdmjb_job_type" name="pdmjb_job_type">';
		echo '<option value="">' . esc_html__( 'Select type', 'pdm-job-board' ) . '</option>';
		foreach ( $job_types as $opt ) {
			echo '<option value="' . esc_attr( $opt ) . '" ' . selected( $job_type, $opt, false ) . '>' . esc_html( $opt ) . '</option>';
		}
		echo '</select></td></tr>';
		// Workspace Type
		echo '<tr><th><label for="pdmjb_workspace_type">' . esc_html__( 'Workspace Type', 'pdm-job-board' ) . '</label></th>';
		echo '<td><select id="pdmjb_workspace_type" name="pdmjb_workspace_type">';
		echo '<option value="">' . esc_html__( 'Select workspace', 'pdm-job-board' ) . '</option>';
		foreach ( $workspace_types as $opt ) {
			echo '<option value="' . esc_attr( $opt ) . '" ' . selected( $workspace_type, $opt, false ) . '>' . esc_html( $opt ) . '</option>';
		}
		echo '</select></td></tr>';
		// Salary
		echo '<tr><th><label for="pdmjb_salary">' . esc_html__( 'Salary Range (optional)', 'pdm-job-board' ) . '</label></th>';
		echo '<td><input type="text" id="pdmjb_salary" name="pdmjb_salary" class="regular-text" value="' . esc_attr( $salary ) . '" placeholder="e.g. $60k–$80k / year" /></td></tr>';
		// Deadline
		echo '<tr><th><label for="pdmjb_deadline">' . esc_html__( 'Application Deadline', 'pdm-job-board' ) . '</label></th>';
		echo '<td><input type="date" id="pdmjb_deadline" name="pdmjb_deadline" value="' . esc_attr( $deadline ) . '" /></td></tr>';
		// Contact
		echo '<tr><th><label for="pdmjb_contact">' . esc_html__( 'Contact Email or HR Name (optional)', 'pdm-job-board' ) . '</label></th>';
		echo '<td><input type="text" id="pdmjb_contact" name="pdmjb_contact" class="regular-text" value="' . esc_attr( $contact ) . '" placeholder="hr@example.com or Jane Doe" /></td></tr>';
		echo '</tbody></table>';
	}

	public function render_extras_box( \WP_Post $post ) : void {
		wp_nonce_field( 'pdmjb_save_meta', 'pdmjb_meta_nonce_2' );

		$form_shortcode = get_post_meta( $post->ID, 'pdmjb_application_form_shortcode', true );
		$resps          = get_post_meta( $post->ID, 'pdmjb_responsibilities', true );
		$quals          = get_post_meta( $post->ID, 'pdmjb_qualifications', true );
		$benefits       = get_post_meta( $post->ID, 'pdmjb_benefits', true );

		echo '<table class="form-table pdmjb-meta">';
		echo '<tbody>';
		// Form Shortcode
		echo '<tr><th><label for="pdmjb_application_form_shortcode">' . esc_html__( 'Application Form Shortcode', 'pdm-job-board' ) . '</label></th>';
		echo '<td><textarea id="pdmjb_application_form_shortcode" name="pdmjb_application_form_shortcode" class="large-text" rows="2" placeholder="[wpforms id=\"123\"]">' . esc_textarea( $form_shortcode ) . '</textarea>';
		echo '<p class="description">' . esc_html__( 'Paste ANY form shortcode (e.g., WPForms, CF7, Gravity Forms). If left empty, the plugin will try to detect the first form shortcode in the job content automatically.', 'pdm-job-board' ) . '</p></td></tr>';
		// Responsibilities
		echo '<tr><th><label for="pdmjb_responsibilities">' . esc_html__( 'Responsibilities', 'pdm-job-board' ) . '</label></th>';
		echo '<td><textarea id="pdmjb_responsibilities" name="pdmjb_responsibilities" class="large-text code" rows="5" placeholder="One responsibility per line">' . esc_textarea( $resps ) . '</textarea>';
		echo '<p class="description">' . esc_html__( 'Enter one responsibility per line.', 'pdm-job-board' ) . '</p></td></tr>';
		// Qualifications
		echo '<tr><th><label for="pdmjb_qualifications">' . esc_html__( 'Qualifications / Requirements', 'pdm-job-board' ) . '</label></th>';
		echo '<td><textarea id="pdmjb_qualifications" name="pdmjb_qualifications" class="large-text code" rows="5" placeholder="One qualification per line">' . esc_textarea( $quals ) . '</textarea>';
		echo '<p class="description">' . esc_html__( 'Enter one qualification per line.', 'pdm-job-board' ) . '</p></td></tr>';
		// Benefits
		echo '<tr><th><label for="pdmjb_benefits">' . esc_html__( 'Benefits (optional)', 'pdm-job-board' ) . '</label></th>';
		echo '<td><textarea id="pdmjb_benefits" name="pdmjb_benefits" class="large-text code" rows="4" placeholder="One benefit per line">' . esc_textarea( $benefits ) . '</textarea></td></tr>';
		echo '</tbody></table>';
	}

	public function save( int $post_id ) : void {
		// Quick checks
		if ( ! isset( $_POST['post_type'] ) || $_POST['post_type'] !== 'job' ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		$nonce_ok = ( isset( $_POST['pdmjb_meta_nonce'] ) && wp_verify_nonce( (string) $_POST['pdmjb_meta_nonce'], 'pdmjb_save_meta' ) )
			|| ( isset( $_POST['pdmjb_meta_nonce_2'] ) && wp_verify_nonce( (string) $_POST['pdmjb_meta_nonce_2'], 'pdmjb_save_meta' ) );
		if ( ! $nonce_ok ) {
			return;
		}

		// Sanitize and save fields
		$location = isset( $_POST['pdmjb_location'] ) ? sanitize_text_field( (string) $_POST['pdmjb_location'] ) : '';
		$job_type = isset( $_POST['pdmjb_job_type'] ) ? sanitize_text_field( (string) $_POST['pdmjb_job_type'] ) : '';
		$workspace_type = isset( $_POST['pdmjb_workspace_type'] ) ? sanitize_text_field( (string) $_POST['pdmjb_workspace_type'] ) : '';
		$salary = isset( $_POST['pdmjb_salary'] ) ? sanitize_text_field( (string) $_POST['pdmjb_salary'] ) : '';
		$deadline = isset( $_POST['pdmjb_deadline'] ) ? sanitize_text_field( (string) $_POST['pdmjb_deadline'] ) : '';
		$contact = isset( $_POST['pdmjb_contact'] ) ? ( ( strpos( (string) $_POST['pdmjb_contact'], '@' ) !== false ) ? sanitize_email( (string) $_POST['pdmjb_contact'] ) : sanitize_text_field( (string) $_POST['pdmjb_contact'] ) ) : '';
		$form_shortcode = isset( $_POST['pdmjb_application_form_shortcode'] ) ? sanitize_textarea_field( (string) $_POST['pdmjb_application_form_shortcode'] ) : '';
		$resps = isset( $_POST['pdmjb_responsibilities'] ) ? sanitize_textarea_field( (string) $_POST['pdmjb_responsibilities'] ) : '';
		$quals = isset( $_POST['pdmjb_qualifications'] ) ? sanitize_textarea_field( (string) $_POST['pdmjb_qualifications'] ) : '';
		$benefits = isset( $_POST['pdmjb_benefits'] ) ? sanitize_textarea_field( (string) $_POST['pdmjb_benefits'] ) : '';

		// Validate enums
		if ( $job_type && ! in_array( $job_type, Job_CPT::get_job_types(), true ) ) {
			$job_type = '';
		}
		if ( $workspace_type && ! in_array( $workspace_type, Job_CPT::get_workspace_types(), true ) ) {
			$workspace_type = '';
		}
		// Validate date Y-m-d
		if ( $deadline ) {
			if ( preg_match( '/^\d{4}-\d{2}-\d{2}$/', $deadline ) ) {
				list( $y, $m, $d ) = array_map( 'intval', explode( '-', $deadline ) );
				if ( ! checkdate( $m, $d, $y ) ) {
					$deadline = '';
				}
			} else {
				$deadline = '';
			}
		}

		update_post_meta( $post_id, 'pdmjb_location', $location );
		update_post_meta( $post_id, 'pdmjb_job_type', $job_type );
		update_post_meta( $post_id, 'pdmjb_workspace_type', $workspace_type );
		update_post_meta( $post_id, 'pdmjb_salary', $salary );
		update_post_meta( $post_id, 'pdmjb_deadline', $deadline );
		update_post_meta( $post_id, 'pdmjb_contact', $contact );
		update_post_meta( $post_id, 'pdmjb_application_form_shortcode', $form_shortcode );
		update_post_meta( $post_id, 'pdmjb_responsibilities', $resps );
		update_post_meta( $post_id, 'pdmjb_qualifications', $quals );
		update_post_meta( $post_id, 'pdmjb_benefits', $benefits );
	}
}


