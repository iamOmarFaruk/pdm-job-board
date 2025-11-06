<?php
namespace PDMJB\Post_Types;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Job_CPT {
	public function __construct() {
		add_action( 'init', [ $this, 'register' ] );
	}

	public static function get_job_types() : array {
		return [ 'Full-time', 'Part-time', 'Contract', 'Internship' ];
	}

	public static function get_workspace_types() : array {
		return [ 'On-site', 'Remote', 'Hybrid', 'In-field' ];
	}

	public function register() : void {
		$this->register_post_type();
		$this->register_taxonomy();
		$this->register_meta_fields();
	}

	private function register_post_type() : void {
		$labels = [
			'name'               => _x( 'Jobs', 'post type general name', 'pdm-job-board' ),
			'singular_name'      => _x( 'Job', 'post type singular name', 'pdm-job-board' ),
			'menu_name'          => _x( 'Jobs', 'admin menu', 'pdm-job-board' ),
			'name_admin_bar'     => _x( 'Job', 'add new on admin bar', 'pdm-job-board' ),
			'add_new'            => _x( 'Add New', 'job', 'pdm-job-board' ),
			'add_new_item'       => __( 'Add New Job', 'pdm-job-board' ),
			'new_item'           => __( 'New Job', 'pdm-job-board' ),
			'edit_item'          => __( 'Edit Job', 'pdm-job-board' ),
			'view_item'          => __( 'View Job', 'pdm-job-board' ),
			'all_items'          => __( 'All Jobs', 'pdm-job-board' ),
			'search_items'       => __( 'Search Jobs', 'pdm-job-board' ),
			'parent_item_colon'  => __( 'Parent Jobs:', 'pdm-job-board' ),
			'not_found'          => __( 'No jobs found.', 'pdm-job-board' ),
			'not_found_in_trash' => __( 'No jobs found in Trash.', 'pdm-job-board' ),
		];

		$args = [
			'labels'             => $labels,
			'public'             => true,
			'has_archive'        => true,
			'rewrite'            => [ 'slug' => 'jobs' ],
			'show_in_rest'       => true,
			'supports'           => [ 'title', 'editor', 'thumbnail', 'excerpt', 'author', 'revisions' ],
			'menu_icon'          => 'dashicons-id',
		];

		register_post_type( 'job', $args );
	}

	private function register_taxonomy() : void {
		$labels = [
			'name'              => _x( 'Departments', 'taxonomy general name', 'pdm-job-board' ),
			'singular_name'     => _x( 'Department', 'taxonomy singular name', 'pdm-job-board' ),
			'search_items'      => __( 'Search Departments', 'pdm-job-board' ),
			'all_items'         => __( 'All Departments', 'pdm-job-board' ),
			'parent_item'       => __( 'Parent Department', 'pdm-job-board' ),
			'parent_item_colon' => __( 'Parent Department:', 'pdm-job-board' ),
			'edit_item'         => __( 'Edit Department', 'pdm-job-board' ),
			'update_item'       => __( 'Update Department', 'pdm-job-board' ),
			'add_new_item'      => __( 'Add New Department', 'pdm-job-board' ),
			'new_item_name'     => __( 'New Department Name', 'pdm-job-board' ),
			'menu_name'         => __( 'Departments', 'pdm-job-board' ),
		];

		$args = [
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => [ 'slug' => 'department' ],
			'show_in_rest'      => true,
		];

		register_taxonomy( 'job_department', [ 'job' ], $args );
	}

	private function register_meta_fields() : void {
		$meta_fields = [
			'pdmjb_location' => [ 'type' => 'string' ],
			'pdmjb_job_type' => [ 'type' => 'string' ],
			'pdmjb_workspace_type' => [ 'type' => 'string' ],
			'pdmjb_salary' => [ 'type' => 'string' ],
			'pdmjb_application_form_shortcode' => [ 'type' => 'string' ],
			'pdmjb_responsibilities' => [ 'type' => 'string' ],
			'pdmjb_qualifications' => [ 'type' => 'string' ],
			'pdmjb_benefits' => [ 'type' => 'string' ],
			'pdmjb_deadline' => [ 'type' => 'string' ], // Y-m-d
			'pdmjb_contact' => [ 'type' => 'string' ],
		];

		foreach ( $meta_fields as $key => $schema ) {
			register_post_meta( 'job', $key, [
				'single'            => true,
				'type'              => $schema['type'],
				'show_in_rest'      => true,
				'auth_callback'     => function () { return current_user_can( 'edit_posts' ); },
				'sanitize_callback' => function ( $value ) use ( $key ) {
					if ( in_array( $key, [ 'pdmjb_responsibilities', 'pdmjb_qualifications', 'pdmjb_benefits', 'pdmjb_application_form_shortcode' ], true ) ) {
						return sanitize_textarea_field( (string) $value );
					}
					if ( $key === 'pdmjb_job_type' && ! in_array( (string) $value, self::get_job_types(), true ) ) {
						return '';
					}
					if ( $key === 'pdmjb_workspace_type' && ! in_array( (string) $value, self::get_workspace_types(), true ) ) {
						return '';
					}
					if ( $key === 'pdmjb_deadline' ) {
						$value = sanitize_text_field( (string) $value );
						if ( preg_match( '/^\d{4}-\d{2}-\d{2}$/', $value ) ) {
							list( $y, $m, $d ) = array_map( 'intval', explode( '-', $value ) );
							return checkdate( $m, $d, $y ) ? $value : '';
						}
						return '';
					}
					if ( $key === 'pdmjb_contact' ) {
						$value = (string) $value;
						return strpos( $value, '@' ) !== false ? sanitize_email( $value ) : sanitize_text_field( $value );
					}
					return sanitize_text_field( (string) $value );
				},
			] );
		}
	}
}


