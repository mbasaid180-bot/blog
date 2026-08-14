<?php
/**
 * One-time script: Add 12 SEOPress 301 redirects for qivato.com
 * Token: qivato2026redirects
 * DELETE this file after execution.
 */

if (!isset($_GET['token']) || $_GET['token'] !== 'qivato2026redirects') {
    die('Unauthorized');
}

define('ABSPATH_CHECK', true);
require_once('wp-load.php');

$redirects = [
    // Pilier 1 — AI tools for freelancers
    ['/the-best-ai-tools-for-freelancers-in-2025-complete-guide/', '/best-ai-tools-for-freelancers-scale-your-business-in-2026/'],
    ['/ai-tools-for-freelancers-trends-tools-and-future-opportunities-2025/', '/best-ai-tools-for-freelancers-scale-your-business-in-2026/'],
    ['/ai-tools-for-freelancers-boost-your-solo-business/', '/best-ai-tools-for-freelancers-scale-your-business-in-2026/'],
    ['/the-ai-tools-freelancers-actually-use-in-2026-a-practical-selection-guide/', '/best-ai-tools-for-freelancers-scale-your-business-in-2026/'],
    ['/ai-tools-growth-for-freelancers-build-a-sustainable-business/', '/best-ai-tools-for-freelancers-scale-your-business-in-2026/'],
    ['/ai-tools-for-freelancers-develop-your-business-with-7-must-have-tools/', '/best-ai-tools-for-freelancers-scale-your-business-in-2026/'],
    // Pilier 2 — Automation freelancers
    ['/freelance-automation-guide-get-started-in-2026/', '/automate-your-freelance-business-complete-guide-2026/'],
    ['/ai-automation-tools-for-freelancers-to-save-time-and-boost-income/', '/automate-your-freelance-business-complete-guide-2026/'],
    ['/ai-automation-for-freelancers-strategies-tools-and-monetization-in-2025/', '/automate-your-freelance-business-complete-guide-2026/'],
    ['/workflow-automation-tools-for-freelancers-connect-and-save-hours/', '/automate-your-freelance-business-complete-guide-2026/'],
    ['/intelligent-automation-is-becoming-essential-for-modern-freelancers/', '/automate-your-freelance-business-complete-guide-2026/'],
    ['/advanced-automation-10-ai-workflows-that-skyrocket-freelancers-growth/', '/automate-your-freelance-business-complete-guide-2026/'],
];

$results = [];

foreach ($redirects as $r) {
    [$source, $target] = $r;

    $post_id = wp_insert_post([
        'post_type'   => 'seopress_redirections',
        'post_status' => 'publish',
        'post_title'  => $source,
    ]);

    if (is_wp_error($post_id)) {
        $results[] = ['source' => $source, 'status' => 'ERROR: ' . $post_id->get_error_message()];
        continue;
    }

    update_post_meta($post_id, '_seopress_redirections_value', $source);
    update_post_meta($post_id, '_seopress_redirections_url', $target);
    update_post_meta($post_id, '_seopress_redirections_type', '301');
    update_post_meta($post_id, '_seopress_redirections_enabled', '1');

    $results[] = ['source' => $source, 'target' => $target, 'status' => 'OK (ID: ' . $post_id . ')'];
}

echo '<style>body{font-family:monospace;padding:20px} table{border-collapse:collapse;width:100%} td,th{border:1px solid #ccc;padding:8px} .ok{color:green} .err{color:red}</style>';
echo '<h2>SEOPress Redirects — Qivato</h2>';
echo '<table><tr><th>Source</th><th>Target</th><th>Status</th></tr>';
foreach ($results as $r) {
    $class = str_starts_with($r['status'], 'OK') ? 'ok' : 'err';
    echo '<tr><td>' . esc_html($r['source']) . '</td><td>' . esc_html($r['target'] ?? '') . '</td><td class="' . $class . '">' . esc_html($r['status']) . '</td></tr>';
}
echo '</table>';
echo '<p style="color:red"><strong>SUPPRIMER CE FICHIER MAINTENANT via cPanel File Manager.</strong></p>';
