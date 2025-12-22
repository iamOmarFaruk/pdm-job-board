<?php
// Load WordPress environment
require_once(__DIR__ . '/../../../wp-load.php');
require_once(ABSPATH . 'wp-admin/includes/media.php');
require_once(ABSPATH . 'wp-admin/includes/file.php');
require_once(ABSPATH . 'wp-admin/includes/image.php');

if (!current_user_can('install_plugins')) {
    // die('Access denied');
}

$titles = [
    "Senior Software Engineer",
    "Marketing Manager",
    "Product Designer",
    "Customer Support Specialist",
    "Data Analyst",
    "Sales Representative",
    "HR Specialist",
    "DevOps Engineer",
    "Content Writer",
    "Project Manager"
];

$departments = [
    "Engineering",
    "Marketing",
    "Design",
    "Customer Success",
    "Data",
    "Sales",
    "Human Resources",
    "Operations"
];

$locations = [
    "New York, USA",
    "London, UK",
    "Berlin, Germany",
    "San Francisco, CA",
    "Toronto, Canada",
    "Remote"
];

$salaries = [
    "$100,000 - $130,000",
    "$80,000 - $100,000",
    "$60,000 - $80,000",
    "Competitive",
    "DOE"
];

// Curated list of Unsplash images suitable for jobs/office - Forced Landscape 16:9
$image_urls = [
    'https://images.unsplash.com/photo-1497215728101-856f4ea42174?ixlib=rb-1.2.1&auto=format&fit=crop&w=1600&h=900&q=80', // Office
    'https://images.unsplash.com/photo-1542744173-8e7e53415bb0?ixlib=rb-1.2.1&auto=format&fit=crop&w=1600&h=900&q=80', // Team
    'https://images.unsplash.com/photo-1522071820081-009f0129c71c?ixlib=rb-1.2.1&auto=format&fit=crop&w=1600&h=900&q=80', // Group
    'https://images.unsplash.com/photo-1556761175-5973dc0f32e7?ixlib=rb-1.2.1&auto=format&fit=crop&w=1600&h=900&q=80', // Meeting
    'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?ixlib=rb-1.2.1&auto=format&fit=crop&w=1600&h=900&q=80', // Modern Office
    'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?ixlib=rb-1.2.1&auto=format&fit=crop&w=1600&h=900&q=80', // Working hard
    'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?ixlib=rb-1.2.1&auto=format&fit=crop&w=1600&h=900&q=80', // Buildings
    'https://images.unsplash.com/photo-1531482615713-2afd69097998?ixlib=rb-1.2.1&auto=format&fit=crop&w=1600&h=900&q=80', // Co-working
    'https://images.unsplash.com/photo-1507679799987-c73779587ccf?ixlib=rb-1.2.1&auto=format&fit=crop&w=1600&h=900&q=80', // Boss
    'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?ixlib=rb-1.2.1&auto=format&fit=crop&w=1600&h=900&q=80'  // Professional
];

$job_types = ['Full-time', 'Part-time', 'Contract', 'Internship'];
$workspace_types = ['On-site', 'Remote', 'Hybrid', 'In-field'];

echo "Starting to insert 10 dummy jobs WITH images...\n";

for ($i = 0; $i < 10; $i++) {
    $title = $titles[array_rand($titles)];
    $job_type = $job_types[array_rand($job_types)];

    // Create Post
    $post_data = [
        'post_title' => "$title - $job_type (with Image)",
        'post_content' => "This is a dummy description for the $title position. We are looking for a talented individual to join our team. \n\nLorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.",
        'post_status' => 'publish',
        'post_type' => 'job',
        'post_author' => 1,
    ];

    $post_id = wp_insert_post($post_data);

    if (is_wp_error($post_id)) {
        echo "Error creating job: " . $post_id->get_error_message() . "\n";
        continue;
    }

    echo "Created Job ID: $post_id - $title\n";

    // Set Department Taxonomy
    $dept = $departments[array_rand($departments)];
    wp_set_object_terms($post_id, $dept, 'job_department');

    // Set Meta Fields
    update_post_meta($post_id, 'pdmjb_location', $locations[array_rand($locations)]);
    update_post_meta($post_id, 'pdmjb_job_type', $job_type);
    update_post_meta($post_id, 'pdmjb_workspace_type', $workspace_types[array_rand($workspace_types)]);
    update_post_meta($post_id, 'pdmjb_salary', $salaries[array_rand($salaries)]);
    update_post_meta($post_id, 'pdmjb_application_form_shortcode', '[contact-form-7 id="123" title="Apply"]');
    update_post_meta($post_id, 'pdmjb_responsibilities', "1. Build cool things.\n2. Maintain code.\n3. Collaborate with team.");
    update_post_meta($post_id, 'pdmjb_qualifications', "1. 3+ years experience.\n2. Knowledge of PHP and React.\n3. Good communication skills.");
    update_post_meta($post_id, 'pdmjb_benefits', "1. Health insurance.\n2. Remote work options.\n3. Gym membership.");

    // Deadline: Random date in next 30 days
    $deadline = date('Y-m-d', strtotime('+' . rand(5, 30) . ' days'));
    update_post_meta($post_id, 'pdmjb_deadline', $deadline);
    update_post_meta($post_id, 'pdmjb_contact', 'hr@example.com');

    // Handle Image
    $image_url = $image_urls[$i % count($image_urls)]; // Cycle through images
    echo "  Downloading image from $image_url ...\n";

    // Custom sideload logic
    $tmp = download_url($image_url);

    if (is_wp_error($tmp)) {
        echo "  Download failed: " . $tmp->get_error_message() . "\n";
    } else {
        $file_array = [
            'name' => 'job-image-' . $post_id . '.jpg', // Force name
            'tmp_name' => $tmp,
        ];

        // Ensure we clear the temp file if anything goes wrong
        $attachment_id = media_handle_sideload($file_array, $post_id, 'Job Image');

        if (is_wp_error($attachment_id)) {
            @unlink($file_array['tmp_name']);
            echo "  Sideload failed: " . $attachment_id->get_error_message() . "\n";
        } else {
            set_post_thumbnail($post_id, $attachment_id);
            echo "  Attached image ID: $attachment_id\n";
        }
    }
}

echo "Done!\n";
