<?php
/**
 * One-time script: Audit internal linking across all posts
 * Token: qivato2026linking
 * DELETE this file after execution.
 */

if (!isset($_GET['token']) || $_GET['token'] !== 'qivato2026linking') {
    die('Unauthorized');
}

require_once('wp-load.php');

echo '<style>
body{font-family:monospace;padding:20px;font-size:13px}
table{border-collapse:collapse;width:100%}
td,th{border:1px solid #ccc;padding:6px;text-align:left}
.ok{color:green} .warn{color:orange} .err{color:red}
.zero{background:#ffe0e0} .low{background:#fff3cd} .good{background:#e0f4e0}
</style>';
echo '<h2>Internal Linking Audit — Qivato</h2>';

$pillar1 = 'https://qivato.com/best-ai-tools-for-freelancers-scale-your-business-in-2026/';
$pillar2 = 'https://qivato.com/automate-your-freelance-business-complete-guide-2026/';

$posts = get_posts([
    'post_type'      => 'post',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'orderby'        => 'date',
    'order'          => 'DESC',
]);

$total        = count($posts);
$no_links     = 0;
$no_pillar    = 0;
$good         = 0;

echo '<p>Total articles analysés : <strong>' . $total . '</strong></p>';
echo '<table>';
echo '<tr><th>#</th><th>Article</th><th>Liens internes</th><th>→ Pilier 1</th><th>→ Pilier 2</th><th>Statut</th></tr>';

$rows = [];

foreach ($posts as $i => $post) {
    $content = $post->post_content;

    // Extract all internal links
    preg_match_all('/href=["\']https?:\/\/qivato\.com\/([^"\']+)["\']/', $content, $matches);
    $internal_links = array_unique($matches[1]);
    $count = count($internal_links);

    $has_pillar1 = strpos($content, 'best-ai-tools-for-freelancers-scale-your-business-in-2026') !== false;
    $has_pillar2 = strpos($content, 'automate-your-freelance-business-complete-guide-2026') !== false;

    // Skip the pillar articles themselves
    $is_pillar = in_array($post->post_name, [
        'best-ai-tools-for-freelancers-scale-your-business-in-2026',
        'automate-your-freelance-business-complete-guide-2026',
    ]);

    if ($count === 0 && !$is_pillar) $no_links++;
    if (!$has_pillar1 && !$has_pillar2 && !$is_pillar) $no_pillar++;
    if ($count >= 3 && ($has_pillar1 || $has_pillar2)) $good++;

    $class = $is_pillar ? '' : ($count === 0 ? 'zero' : ($count < 3 ? 'low' : 'good'));
    $status = $is_pillar ? '⭐ Pilier' : ($count === 0 ? '❌ Aucun lien' : ($count < 3 ? '⚠️ Insuffisant' : '✅ OK'));

    $rows[] = [
        'id'       => $post->ID,
        'num'      => $i + 1,
        'title'    => mb_substr($post->post_title, 0, 50),
        'slug'     => $post->post_name,
        'count'    => $count,
        'p1'       => $has_pillar1,
        'p2'       => $has_pillar2,
        'class'    => $class,
        'status'   => $status,
        'is_pillar'=> $is_pillar,
        'links'    => $internal_links,
    ];
}

// Sort: articles without links first
usort($rows, fn($a, $b) => $a['count'] - $b['count']);

foreach ($rows as $r) {
    echo '<tr class="' . $r['class'] . '">';
    echo '<td>' . $r['num'] . '</td>';
    echo '<td><a href="https://qivato.com/' . esc_html($r['slug']) . '/" target="_blank">' . esc_html($r['title']) . '</a></td>';
    echo '<td>' . $r['count'] . '</td>';
    echo '<td>' . ($r['p1'] ? '✅' : '❌') . '</td>';
    echo '<td>' . ($r['p2'] ? '✅' : '❌') . '</td>';
    echo '<td>' . $r['status'] . '</td>';
    echo '</tr>';
}

echo '</table>';

echo '<h3>Résumé</h3>';
echo '<p class="err">❌ Articles sans aucun lien interne : <strong>' . $no_links . '</strong></p>';
echo '<p class="warn">⚠️ Articles sans lien vers un pilier : <strong>' . $no_pillar . '</strong></p>';
echo '<p class="ok">✅ Articles bien maillés (3+ liens + pilier) : <strong>' . $good . '</strong></p>';

echo '<p style="color:red"><strong>SUPPRIMER CE FICHIER via cPanel après lecture.</strong></p>';
