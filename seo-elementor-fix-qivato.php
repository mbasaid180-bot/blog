<?php
/**
 * One-time script: Disable Elementor on pillar 2 and apply new content
 * Token: qivato2026elementorfix
 * DELETE this file after execution.
 */

if (!isset($_GET['token']) || $_GET['token'] !== 'qivato2026elementorfix') {
    die('Unauthorized');
}

require_once('wp-load.php');

$slug = 'automate-your-freelance-business-complete-guide-2026';
$post = get_page_by_path($slug, OBJECT, 'post');

if (!$post) {
    die('Article not found.');
}

echo '<style>body{font-family:monospace;padding:20px} .ok{color:green} .err{color:red} .warn{color:orange}</style>';
echo '<h2>Elementor Diagnostic — Pillar 2</h2>';

// Check Elementor status
$elementor_edit_mode = get_post_meta($post->ID, '_elementor_edit_mode', true);
$elementor_data      = get_post_meta($post->ID, '_elementor_data', true);
$elementor_version   = get_post_meta($post->ID, '_elementor_version', true);

echo '<p>Elementor edit mode : <strong>' . esc_html($elementor_edit_mode ?: '(empty)') . '</strong></p>';
echo '<p>Elementor data size : <strong>' . strlen($elementor_data) . ' chars</strong></p>';
echo '<p>Elementor version : <strong>' . esc_html($elementor_version ?: '(empty)') . '</strong></p>';
echo '<p>post_content size : <strong>' . strlen($post->post_content) . ' chars</strong></p>';

if (!empty($elementor_data) && strlen($elementor_data) > 100) {
    echo '<p class="warn">⚠️ Article built with Elementor — post_content is ignored by WordPress display.</p>';

    if (isset($_GET['fix']) && $_GET['fix'] === '1') {
        // Disable Elementor for this post
        update_post_meta($post->ID, '_elementor_edit_mode', '');
        delete_post_meta($post->ID, '_elementor_data');
        delete_post_meta($post->ID, '_elementor_css');

        echo '<p class="ok">✅ Elementor disabled for this post.</p>';
        echo '<p class="ok">✅ post_content will now display directly.</p>';
        echo '<p><a href="' . get_permalink($post->ID) . '" target="_blank">→ View article now</a></p>';
    } else {
        echo '<p>Pour désactiver Elementor et afficher le nouveau contenu :<br>';
        echo '<strong><a href="?token=qivato2026elementorfix&fix=1">→ Clique ici pour appliquer le fix</a></strong></p>';
    }
} else {
    echo '<p class="ok">✅ Article n\'utilise pas Elementor — post_content devrait s\'afficher.</p>';
    echo '<p>Mot count actuel : <strong>' . str_word_count(wp_strip_all_tags($post->post_content)) . ' mots</strong></p>';
    echo '<p><a href="' . get_permalink($post->ID) . '" target="_blank">→ View article</a></p>';
}

echo '<p style="color:red"><strong>SUPPRIMER CE FICHIER via cPanel après lecture.</strong></p>';
