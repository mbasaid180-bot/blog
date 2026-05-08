<?php
/**
 * One-time script: Download AI images + insert into pillar article 2
 * Token: qivato2026images
 * DELETE this file after execution.
 */

if (!isset($_GET['token']) || $_GET['token'] !== 'qivato2026images') {
    die('Unauthorized');
}

require_once('wp-load.php');
require_once(ABSPATH . 'wp-admin/includes/media.php');
require_once(ABSPATH . 'wp-admin/includes/file.php');
require_once(ABSPATH . 'wp-admin/includes/image.php');

$slug = 'automate-your-freelance-business-complete-guide-2026';
$post = get_page_by_path($slug, OBJECT, 'post');

if (!$post) {
    die('Article not found.');
}

echo '<style>body{font-family:monospace;padding:20px} .ok{color:green} .err{color:red} table{border-collapse:collapse;width:100%} td,th{border:1px solid #ccc;padding:8px}</style>';
echo '<h2>Images Insert — Pillar Article 2</h2>';

$images = [
    [
        'url'      => 'https://d8j0ntlcm91z4.cloudfront.net/user_3AoczSHvk6rR1J5DXaKSxcJuOuL/hf_20260508_191914_b802ab49-73f0-472f-9874-1fdf93388425.png',
        'filename' => 'automate-freelance-business-hero-2026.png',
        'alt'      => 'Freelancer automating business workflows with AI tools 2026',
        'title'    => 'Automate Freelance Business Hero 2026',
        'role'     => 'featured',
        'insert_after' => null,
    ],
    [
        'url'      => 'https://d8j0ntlcm91z4.cloudfront.net/user_3AoczSHvk6rR1J5DXaKSxcJuOuL/hf_20260508_191917_557585c5-a829-4b8f-8b64-39636fabb7a1.png',
        'filename' => 'freelance-automation-workflow-diagram-zapier.png',
        'alt'      => 'Freelance business automation workflow diagram with Zapier integrations',
        'title'    => 'Freelance Automation Workflow Diagram Zapier',
        'role'     => 'content',
        'insert_after' => 'Section 7: Workflow Automation',
    ],
    [
        'url'      => 'https://d8j0ntlcm91z4.cloudfront.net/user_3AoczSHvk6rR1J5DXaKSxcJuOuL/hf_20260508_192036_712c5993-1cb2-4d01-a4df-5d1c0065f7f5.png',
        'filename' => 'best-freelance-automation-tools-2026.png',
        'alt'      => 'Best freelance automation tools stack 2026 comparison',
        'title'    => 'Best Freelance Automation Tools 2026',
        'role'     => 'content',
        'insert_after' => 'Best Freelance Automation Tools 2026',
    ],
];

$attachment_ids = [];
$content = $post->post_content;

foreach ($images as $img) {
    echo '<h3>' . esc_html($img['title']) . '</h3>';

    // Download image
    $tmp = download_url($img['url']);

    if (is_wp_error($tmp)) {
        echo '<p class="err">❌ Download failed: ' . esc_html($tmp->get_error_message()) . '</p>';
        continue;
    }

    $file_array = [
        'name'     => $img['filename'],
        'tmp_name' => $tmp,
    ];

    // Sideload into media library
    $att_id = media_handle_sideload($file_array, $post->ID, $img['title']);

    if (is_wp_error($att_id)) {
        @unlink($tmp);
        echo '<p class="err">❌ Upload failed: ' . esc_html($att_id->get_error_message()) . '</p>';
        continue;
    }

    // Set alt text
    update_post_meta($att_id, '_wp_attachment_image_alt', $img['alt']);

    $attachment_url = wp_get_attachment_url($att_id);
    echo '<p class="ok">✅ Uploaded (ID: ' . $att_id . ') — <a href="' . esc_url($attachment_url) . '" target="_blank">View</a></p>';

    // Set as featured image
    if ($img['role'] === 'featured') {
        set_post_thumbnail($post->ID, $att_id);
        echo '<p class="ok">✅ Set as featured image</p>';
    }

    // Insert into content
    if ($img['role'] === 'content' && !empty($img['insert_after'])) {
        $img_html = "\n" . wp_get_attachment_image($att_id, 'large', false, [
            'alt'   => $img['alt'],
            'class' => 'wp-image-' . $att_id . ' size-large aligncenter',
        ]) . "\n";

        // Find the H2 heading and insert image after the next closing </p>
        $search = $img['insert_after'];
        $pos    = strpos($content, $search);

        if ($pos !== false) {
            // Find the end of the next paragraph after the heading
            $para_end = strpos($content, '</p>', $pos);
            if ($para_end !== false) {
                $content = substr($content, 0, $para_end + 4) . $img_html . substr($content, $para_end + 4);
                echo '<p class="ok">✅ Inserted into article after "' . esc_html($search) . '"</p>';
            }
        } else {
            echo '<p class="err">⚠️ Section "' . esc_html($search) . '" not found in content — image uploaded but not inserted</p>';
        }
    }

    $attachment_ids[] = $att_id;
}

// Update post content with inserted images
$update = wp_update_post([
    'ID'           => $post->ID,
    'post_content' => $content,
]);

if (!is_wp_error($update)) {
    $word_count = str_word_count(wp_strip_all_tags($content));
    echo '<h3 class="ok">✅ Article updated with images</h3>';
    echo '<p>Word count: ~' . $word_count . ' | Images inserted: ' . count($attachment_ids) . '</p>';
    echo '<p><a href="' . get_permalink($post->ID) . '" target="_blank">→ View article live</a></p>';
} else {
    echo '<p class="err">❌ Content update failed: ' . esc_html($update->get_error_message()) . '</p>';
}

echo '<p style="color:red"><strong>SUPPRIMER CE FICHIER via cPanel après lecture.</strong></p>';
