<?php
/**
 * One-time script: Delete 12 cannibalized posts on qivato.com
 * Token: qivato2026delete
 * DELETE this file after execution.
 */

if (!isset($_GET['token']) || $_GET['token'] !== 'qivato2026delete') {
    die('Unauthorized');
}

require_once('wp-load.php');

$slugs = [
    'the-best-ai-tools-for-freelancers-in-2025-complete-guide',
    'ai-tools-for-freelancers-trends-tools-and-future-opportunities-2025',
    'ai-tools-for-freelancers-boost-your-solo-business',
    'the-ai-tools-freelancers-actually-use-in-2026-a-practical-selection-guide',
    'ai-tools-growth-for-freelancers-build-a-sustainable-business',
    'ai-tools-for-freelancers-develop-your-business-with-7-must-have-tools',
    'freelance-automation-guide-get-started-in-2026',
    'ai-automation-tools-for-freelancers-to-save-time-and-boost-income',
    'ai-automation-for-freelancers-strategies-tools-and-monetization-in-2025',
    'workflow-automation-tools-for-freelancers-connect-and-save-hours',
    'intelligent-automation-is-becoming-essential-for-modern-freelancers',
    'advanced-automation-10-ai-workflows-that-skyrocket-freelancers-growth',
];

$results = [];

foreach ($slugs as $slug) {
    $post = get_page_by_path($slug, OBJECT, 'post');

    if (!$post) {
        $results[] = ['slug' => $slug, 'status' => 'NOT FOUND'];
        continue;
    }

    $deleted = wp_delete_post($post->ID, true);

    if ($deleted) {
        $results[] = ['slug' => $slug, 'title' => $post->post_title, 'status' => 'DELETED (ID: ' . $post->ID . ')'];
    } else {
        $results[] = ['slug' => $slug, 'title' => $post->post_title, 'status' => 'ERROR'];
    }
}

echo '<style>body{font-family:monospace;padding:20px} table{border-collapse:collapse;width:100%} td,th{border:1px solid #ccc;padding:8px} .ok{color:green} .err{color:red} .warn{color:orange}</style>';
echo '<h2>Delete Cannibalized Posts — Qivato</h2>';
echo '<table><tr><th>Slug</th><th>Title</th><th>Status</th></tr>';
foreach ($results as $r) {
    $class = $r['status'] === 'NOT FOUND' ? 'warn' : (str_starts_with($r['status'], 'DELETED') ? 'ok' : 'err');
    echo '<tr><td>' . esc_html($r['slug']) . '</td><td>' . esc_html($r['title'] ?? '-') . '</td><td class="' . $class . '">' . esc_html($r['status']) . '</td></tr>';
}
echo '</table>';
echo '<p style="color:red"><strong>SUPPRIMER CE FICHIER MAINTENANT via cPanel File Manager.</strong></p>';
