<?php
/**
 * One-time script: Extract pillar articles content for analysis
 * Token: qivato2026pillar
 * DELETE this file after execution.
 */

if (!isset($_GET['token']) || $_GET['token'] !== 'qivato2026pillar') {
    die('Unauthorized');
}

require_once('wp-load.php');

$slugs = [
    'best-ai-tools-for-freelancers-scale-your-business-in-2026',
    'automate-your-freelance-business-complete-guide-2026',
];

echo '<style>body{font-family:monospace;padding:20px;max-width:1200px} h2{color:#333;border-bottom:2px solid #333} h3{color:#666} pre{background:#f4f4f4;padding:10px;overflow:auto;font-size:12px} .stat{display:inline-block;margin:5px 10px 5px 0;padding:5px 10px;background:#e8f4f8;border-radius:4px}</style>';
echo '<h1>Pillar Articles Analysis — Qivato</h1>';

foreach ($slugs as $slug) {
    $post = get_page_by_path($slug, OBJECT, 'post');
    if (!$post) {
        echo '<p style="color:red">NOT FOUND: ' . esc_html($slug) . '</p>';
        continue;
    }

    $content    = apply_filters('the_content', $post->post_content);
    $text       = wp_strip_all_tags($content);
    $word_count = str_word_count($text);

    // Extract headings
    preg_match_all('/<h([1-6])[^>]*>(.*?)<\/h[1-6]>/i', $content, $headings);
    $heading_list = [];
    foreach ($headings[1] as $i => $level) {
        $heading_list[] = 'H' . $level . ': ' . wp_strip_all_tags($headings[2][$i]);
    }

    // Extract images
    preg_match_all('/<img[^>]+>/i', $content, $imgs);

    // Extract links
    preg_match_all('/<a[^>]+href=["\']([^"\']+)["\'][^>]*>(.*?)<\/a>/i', $content, $links);
    $internal = array_filter($links[1], fn($l) => strpos($l, 'qivato.com') !== false);
    $external = array_filter($links[1], fn($l) => strpos($l, 'http') === 0 && strpos($l, 'qivato.com') === false);

    // SEOPress meta
    $seo_title = get_post_meta($post->ID, '_seopress_titles_title', true);
    $seo_desc  = get_post_meta($post->ID, '_seopress_titles_desc', true);

    echo '<h2>' . esc_html($post->post_title) . '</h2>';
    echo '<p>URL: <a href="' . get_permalink($post->ID) . '" target="_blank">' . get_permalink($post->ID) . '</a></p>';
    echo '<span class="stat">📝 ' . $word_count . ' mots</span>';
    echo '<span class="stat">🖼️ ' . count($imgs[0]) . ' images</span>';
    echo '<span class="stat">🔗 ' . count($internal) . ' liens internes</span>';
    echo '<span class="stat">🌐 ' . count($external) . ' liens externes</span>';
    echo '<span class="stat">📋 ' . count($heading_list) . ' titres</span>';

    echo '<h3>Structure des titres :</h3><pre>';
    echo esc_html(implode("\n", $heading_list));
    echo '</pre>';

    echo '<h3>SEO Meta :</h3>';
    echo '<p><strong>Title :</strong> ' . esc_html($seo_title ?: '(non défini)') . '</p>';
    echo '<p><strong>Description :</strong> ' . esc_html($seo_desc ?: '(non définie)') . '</p>';

    echo '<h3>Contenu complet :</h3>';
    echo '<pre>' . esc_html(substr($text, 0, 8000)) . '</pre>';
    echo '<hr>';
}

echo '<p style="color:red"><strong>SUPPRIMER CE FICHIER via cPanel après lecture.</strong></p>';
