<?php
/**
 * One-time script: Download AI images + insert into pillar article 1
 * Token: qivato2026pillar1images
 * DELETE this file after execution.
 */

if (!isset($_GET['token']) || $_GET['token'] !== 'qivato2026pillar1images') {
    die('Unauthorized');
}

require_once('wp-load.php');
require_once(ABSPATH . 'wp-admin/includes/media.php');
require_once(ABSPATH . 'wp-admin/includes/file.php');
require_once(ABSPATH . 'wp-admin/includes/image.php');

$slug = 'best-ai-tools-for-freelancers-scale-your-business-in-2026';
$post = get_page_by_path($slug, OBJECT, 'post');

if (!$post) die('Article not found.');

echo '<style>body{font-family:monospace;padding:20px} .ok{color:green} .err{color:red}</style>';
echo '<h2>Images Insert — Pillar Article 1</h2>';

$images = [
    [
        'url'          => 'https://d8j0ntlcm91z4.cloudfront.net/user_3AoczSHvk6rR1J5DXaKSxcJuOuL/hf_20260508_224638_8fea60c3-b25e-49d0-b339-0ef00db5ed43.png',
        'filename'     => 'best-ai-tools-freelancers-2026-hero.png',
        'alt'          => 'Best AI tools for freelancers 2026 complete guide',
        'title'        => 'Best AI Tools for Freelancers 2026 Hero',
        'role'         => 'featured',
        'insert_after' => null,
    ],
    [
        'url'          => 'https://d8j0ntlcm91z4.cloudfront.net/user_3AoczSHvk6rR1J5DXaKSxcJuOuL/hf_20260508_224714_d066221e-112a-4304-8792-3df1b2000a06.png',
        'filename'     => 'ai-tools-freelancer-stack-layers-2026.png',
        'alt'          => 'AI tool stack layers for freelancers foundation specialty scale 2026',
        'title'        => 'AI Tools Freelancer Stack Layers 2026',
        'role'         => 'content',
        'insert_after' => 'How to Choose the Right AI Tools',
    ],
];

$content = $post->post_content;

foreach ($images as $img) {
    echo '<h3>' . esc_html($img['title']) . '</h3>';

    $tmp = download_url($img['url']);
    if (is_wp_error($tmp)) {
        echo '<p class="err">❌ Download failed: ' . esc_html($tmp->get_error_message()) . '</p>';
        continue;
    }

    $file_array = ['name' => $img['filename'], 'tmp_name' => $tmp];
    $att_id = media_handle_sideload($file_array, $post->ID, $img['title']);

    if (is_wp_error($att_id)) {
        @unlink($tmp);
        echo '<p class="err">❌ Upload failed: ' . esc_html($att_id->get_error_message()) . '</p>';
        continue;
    }

    update_post_meta($att_id, '_wp_attachment_image_alt', $img['alt']);
    echo '<p class="ok">✅ Uploaded (ID: ' . $att_id . ')</p>';

    if ($img['role'] === 'featured') {
        set_post_thumbnail($post->ID, $att_id);
        echo '<p class="ok">✅ Set as featured image</p>';
    }

    if ($img['role'] === 'content' && !empty($img['insert_after'])) {
        $img_html = "\n" . wp_get_attachment_image($att_id, 'large', false, [
            'alt'   => $img['alt'],
            'class' => 'wp-image-' . $att_id . ' size-large aligncenter',
        ]) . "\n";

        $pos = strpos($content, $img['insert_after']);
        if ($pos !== false) {
            $para_end = strpos($content, '</p>', $pos);
            if ($para_end !== false) {
                $content = substr($content, 0, $para_end + 4) . $img_html . substr($content, $para_end + 4);
                echo '<p class="ok">✅ Inserted after "' . esc_html($img['insert_after']) . '"</p>';
            }
        } else {
            echo '<p class="err">⚠️ Section not found — image uploaded to media library only</p>';
        }
    }
}

$result = wp_update_post(['ID' => $post->ID, 'post_content' => $content]);

echo '<h3>' . (!is_wp_error($result) ? '<span class="ok">✅ Article updated</span>' : '<span class="err">❌ Update failed</span>') . '</h3>';
echo '<p><a href="' . get_permalink($post->ID) . '" target="_blank">→ View article live</a></p>';
echo '<p style="color:red"><strong>SUPPRIMER CE FICHIER via cPanel après lecture.</strong></p>';
