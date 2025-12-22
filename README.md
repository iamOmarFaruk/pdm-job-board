# PDM Job Board

A modern, lightweight WordPress job board plugin. It adds a custom post type for Jobs and a `[jobs]` shortcode that renders responsive job cards. Clicking an image, title, "View Details", or "Apply Now" opens a large, accessible modal that shows full job details and an application form side‑by‑side.

- Global modal UX (no dedicated single job page shown to visitors)
- Application form via any shortcode (WPForms, CF7, Gravity Forms, etc.)
- Auto‑detects the first form shortcode found in the job content when the field is empty
- Polished, mobile‑friendly UI with icons and large call‑to‑action buttons
- Strong accessibility defaults (focus management, ARIA roles)

## Table of contents
- [Demo UX](#demo-ux)
- [Requirements](#requirements)
- [Installation](#installation)
- [Usage](#usage)
- [Job fields](#job-fields)
- [Shortcode options](#shortcode-options)
- [Styling and customization](#styling-and-customization)
- [Behavior details](#behavior-details)
- [Disable single page redirect](#disable-single-page-redirect)
- [Development](#development)
- [Folder structure](#folder-structure)
- [Changelog](#changelog)
- [License](#license)

## Demo UX
- Grid of job cards (image, meta, excerpt)
- Clicking image, title, "View Details" or "Apply Now" opens one global modal per job
- Modal layout: two columns on desktop (50/50) and stacked on small screens
  - Left: cover image, title bar, meta with icons, description, responsibilities, qualifications, benefits
  - Right: application form (your shortcode output)
- The modal overlays the page with a dark background. It only closes via the prominent Close button (red); overlay click and ESC do not close it to prevent accidental form loss.

## Requirements
- WordPress 5.8+
- PHP 7.4+
- A forms plugin if you want to collect applications (e.g., WPForms, Contact Form 7, Gravity Forms, Fluent Forms, etc.)

## Installation
1. Copy the plugin folder `pdm-job-board` into `wp-content/plugins/`.
2. Activate “PDM Job Board” from WordPress → Plugins.
3. (Optional) Flush permalinks (Settings → Permalinks → Save) after the first activation.

## Usage
1. Add new Jobs in WordPress → Jobs.
2. On a page, place the shortcode:

   ```
   [jobs]
   ```

3. Optional filters: you can scope to department(s), job type, or workspace.

### Shortcode options
- `posts_per_page` (default `10`)
- `department` — taxonomy slug(s), comma‑separated (e.g. `engineering,marketing`)
- `job_type` — one of: `Full-time`, `Part-time`, `Contract`, `Internship`
- `workspace` — one of: `On-site`, `Remote`, `Hybrid`, `In-field`

Example:

```
[jobs posts_per_page="6" department="engineering,marketing" job_type="Full-time" workspace="Hybrid"]
```

## Job fields
Each Job supports these fields (via metaboxes):
- Location / Address
- Job Type (enum)
- Workspace Type (enum)
- Salary Range (text)
- Application Deadline (date)
- Contact (email or text)
- Application Form Shortcode
- Responsibilities (one per line)
- Qualifications (one per line)
- Benefits (one per line)

### Application form
- Preferred: paste your forms plugin shortcode into “Application Form Shortcode”.
- Fallback: if you leave it empty, the plugin will auto‑detect the first likely form shortcode present in the Job content and use that inside the modal.

Supported/recognized tags include (and anything containing “form”): `wpforms`, `contact-form-7`, `gravityform`, `ninja_forms*`, `formidable`, `fluentform`, `everest_forms`, `forminator_form`, `weforms`, `quform`, `caldera_form`, `mc4wp_form`, `wpuf_form`.

## Styling and customization
Core styles live in:
- `assets/css/pdm-job-board.css`

Notable selectors you may want to adjust:
- Buttons: `.pdmjb-btn`, `.pdmjb-btn--primary` (primary uses your red `#ec2129`)
- Icons color: `.pdmjb-icon`, `.pdmjb-detail__list-icon` (uses `#ec2129`)
- Modal:
  - Container: `.pdmjb-modal` (padding top/bottom, z‑index)
  - Dialog: `.pdmjb-modal__dialog` (max‑width, height)
  - Header: `.pdmjb-modal__header` and `.pdmjb-modal__title`
  - Two‑column layout: `.pdmjb-modal__two-col`, `.pdmjb-modal__left`, `.pdmjb-modal__right`
  - Close button: `.pdmjb-modal__close` (red background, white text)
- Cards grid inside details: `.pdmjb-detail__cards` and `.pdmjb-detail__card`

To change the primary color globally, update the background/border of `.pdmjb-btn--primary` and the color of `.pdmjb-icon` / `.pdmjb-detail__list-icon`.

## Behavior details
- The plugin ships a global modal for each job on the listing page.
- Overlay and ESC do not close the modal (to protect in‑progress forms). Use the red Close button.
- For SEO/UX, single job pages are registered but front‑end requests are redirected back to the referrer (or home).
- The plugin enqueues Font Awesome 6.6 from CDN for icons.

## Disable single page redirect
If you’d like to allow normal single job pages, you can remove the redirect at runtime from a small mu‑plugin or your theme:

```php
add_action('plugins_loaded', function () {
	if ( class_exists('\\PDMJB\\Plugin') ) {
		remove_action('template_redirect', [ \\PDMJB\\Plugin::instance(), 'maybe_redirect_job_single' ]);
	}
});
```

## Development
- No build step required. PHP, CSS and vanilla JS only.
- Namespaces: `PDMJB\\Post_Types`, `PDMJB\\Frontend`, `PDMJB\\Admin`.
- Key files:
  - `includes/post-types/class-pdm-job-cpt.php` — registers CPT, taxonomy, meta
  - `includes/frontend/class-pdm-job-shortcode.php` — shortcode rendering + modals
  - `includes/admin/class-pdm-job-metaboxes.php` — metabox UI and saving
  - `assets/js/pdm-job-board.js` — modal open/close logic (ESC and overlay close disabled)
  - `assets/css/pdm-job-board.css` — all styles

### Local changes to icon set
We load Font Awesome via CDN:
```
https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css
```
If you prefer self‑hosting, dequeue `pdmjb-icons` and enqueue your own in your theme/plugin.

## Folder structure
```
pdm-job-board/
├─ assets/
│  ├─ css/pdm-job-board.css
│  └─ js/pdm-job-board.js
├─ includes/
│  ├─ admin/class-pdm-job-metaboxes.php
│  ├─ frontend/class-pdm-job-shortcode.php
│  ├─ post-types/class-pdm-job-cpt.php
│  └─ class-pdm-job-board.php
├─ pdm-job-board.php
└─ README.md
```

## Changelog
### 1.0.4 - 2025-12-22
- Added Dynamic Primary Color setting in admin
- Added Grid Columns setting (2, 3, or 4 columns)
- Added List/Grid View switcher on frontend
- Added Settings update success message

### 1.0.0
- Initial public release
- Global modal UX with details + form
- Form shortcode field + auto‑detection fallback
- Icons, responsive grid, and polished UI
- Overlay/ESC close disabled; Close button only

## License
GPL-2.0-or-later. See `LICENSE` if included, or use this snippet in your repository root.

> This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 2 of the License, or (at your option) any later version.


