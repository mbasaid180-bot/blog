<?php
/**
 * One-time script: Full content quality audit — E-E-A-T + thin content
 * Token: qivato2026contentaudit
 * DELETE this file after execution.
 */

if (!isset($_GET['token']) || $_GET['token'] !== 'qivato2026contentaudit') {
    die('Unauthorized');
}

require_once('wp-load.php');

echo '<style>
body{font-family:monospace;padding:20px;font-size:13px;max-width:1400px;margin:0 auto}
table{border-collapse:collapse;width:100%;margin-bottom:30px}
td,th{border:1px solid #ccc;padding:6px 8px;text-align:left;vertical-align:top}
th{background:#f0f0f0;font-weight:bold}
.ok{color:#2a7a2a;font-weight:bold} .warn{color:#b36200;font-weight:bold} .err{color:#cc0000;font-weight:bold}
.score-good{background:#d4edda} .score-ok{background:#fff3cd} .score-bad{background:#f8d7da}
h2{border-bottom:2px solid #333;padding-bottom:6px;margin-top:30px}
.badge{display:inline-block;padding:2px 7px;border-radius:10px;font-size:11px;font-weight:bold;color:#fff}
.badge-red{background:#cc3333} .badge-orange{background:#cc7700} .badge-green{background:#2a7a2a}
.priority-1{background:#ffe0e0} .priority-2{background:#fff3cd} .priority-3{background:#e0f4e0}
</style>';

echo '<h1>Audit Contenu Complet — Qivato.com</h1>';
echo '<p style="color:#555">Date : ' . date('Y-m-d H:i') . ' | Critères : E-E-A-T, word count, structure, linking, freshness, AI-readiness</p>';

$posts = get_posts([
    'post_type'      => 'post',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'orderby'        => 'date',
    'order'          => 'DESC',
]);

$total = count($posts);
echo '<p>Total articles analysés : <strong>' . $total . '</strong></p>';

// Pillar slugs
$pillar_slugs = [
    'best-ai-tools-for-freelancers-scale-your-business-in-2026',
    'automate-your-freelance-business-complete-guide-2026',
];
$pillar_p1 = 'best-ai-tools-for-freelancers-scale-your-business-in-2026';
$pillar_p2 = 'automate-your-freelance-business-complete-guide-2026';

$results = [];

foreach ($posts as $post) {
    $content  = $post->post_content;
    $plain    = wp_strip_all_tags($content);
    $words    = str_word_count($plain);
    $slug     = $post->post_name;
    $title    = $post->post_title;
    $date_pub = strtotime($post->post_date);
    $date_mod = strtotime($post->post_modified);
    $age_days = (time() - $date_pub) / 86400;
    $mod_days = (time() - $date_mod) / 86400;
    $is_pillar = in_array($slug, $pillar_slugs);

    // --- Word count score ---
    if ($words >= 2500)      $wc_score = 25; // excellent
    elseif ($words >= 1500)  $wc_score = 18;
    elseif ($words >= 800)   $wc_score = 10;
    elseif ($words >= 400)   $wc_score = 5;
    else                     $wc_score = 0;  // thin

    // --- Structure score ---
    preg_match_all('/<h2[^>]*>/i', $content, $h2);
    preg_match_all('/<h3[^>]*>/i', $content, $h3);
    preg_match_all('/<ul[^>]*>|<ol[^>]*>/i', $content, $lists);
    preg_match_all('/<table[^>]*>/i', $content, $tables);
    preg_match_all('/<img[^>]*>/i', $content, $imgs);
    $has_faq     = stripos($content, 'FAQ') !== false || stripos($content, 'Frequently Asked') !== false;
    $has_table   = count($tables[0]) > 0;
    $h2_count    = count($h2[0]);
    $h3_count    = count($h3[0]);
    $list_count  = count($lists[0]);
    $img_count   = count($imgs[0]);

    $struct_score = 0;
    if ($h2_count >= 3) $struct_score += 8;
    elseif ($h2_count >= 1) $struct_score += 4;
    if ($h3_count >= 2) $struct_score += 5;
    if ($list_count >= 1) $struct_score += 4;
    if ($has_table) $struct_score += 4;
    if ($has_faq) $struct_score += 4;
    $struct_score = min($struct_score, 25);

    // --- E-E-A-T score ---
    $eeat_score = 0;
    // External links (authority)
    preg_match_all('/href=["\']https?:\/\/(?!qivato\.com)[^"\']+["\']/', $content, $ext_links);
    $ext_count = count($ext_links[0]);
    if ($ext_count >= 3) $eeat_score += 8;
    elseif ($ext_count >= 1) $eeat_score += 4;
    // Stats / numbers (experience signals)
    preg_match_all('/\b\d+[\.,]?\d*\s*%/', $content, $stats);
    if (count($stats[0]) >= 2) $eeat_score += 5;
    elseif (count($stats[0]) >= 1) $eeat_score += 3;
    // Specific tools/brands mentioned (expertise)
    $brands = ['ChatGPT','Claude','Jasper','Canva','Grammarly','Notion','Zapier','Surfer','Fiverr','Upwork','HoneyBook','Loom','Otter','Perplexity','Midjourney','Make.com','n8n','Trello','Asana','Slack','Todoist'];
    $brand_count = 0;
    foreach ($brands as $b) {
        if (stripos($content, $b) !== false) $brand_count++;
    }
    if ($brand_count >= 4) $eeat_score += 7;
    elseif ($brand_count >= 2) $eeat_score += 4;
    elseif ($brand_count >= 1) $eeat_score += 2;
    // Author / first-person signals
    if (preg_match('/\b(I |my |we |our |I\'ve|I\'m)\b/i', $plain)) $eeat_score += 5;
    $eeat_score = min($eeat_score, 25);

    // --- Internal linking score ---
    preg_match_all('/href=["\']https?:\/\/qivato\.com\/([^"\']+)["\']/', $content, $int_links);
    $int_count   = count(array_unique($int_links[1]));
    $has_pillar1 = strpos($content, $pillar_p1) !== false;
    $has_pillar2 = strpos($content, $pillar_p2) !== false;

    $link_score = 0;
    if ($int_count >= 5) $link_score += 10;
    elseif ($int_count >= 3) $link_score += 7;
    elseif ($int_count >= 1) $link_score += 3;
    if ($has_pillar1 || $has_pillar2) $link_score += 10;
    elseif ($is_pillar) $link_score += 15; // pillars are OK
    $link_score = min($link_score, 25);

    // --- Total score ---
    $total_score = $wc_score + $struct_score + $eeat_score + $link_score;

    // --- Freshness flag ---
    $freshness = '';
    if ($age_days > 365 && $mod_days > 180) $freshness = '⚠️ Stale';
    elseif ($age_days > 180 && $mod_days > 90) $freshness = '📅 Aging';
    else $freshness = '✅ Fresh';

    // --- Priority ---
    if ($total_score < 30 && !$is_pillar) $priority = 1;
    elseif ($total_score < 55 && !$is_pillar) $priority = 2;
    else $priority = 3;

    // --- Issues list ---
    $issues = [];
    if ($words < 800 && !$is_pillar) $issues[] = 'Thin content (' . $words . ' mots)';
    if ($words < 1500 && !$is_pillar) $issues[] = 'Court (' . $words . ' mots)';
    if ($h2_count < 2) $issues[] = 'Peu de H2 (' . $h2_count . ')';
    if (!$has_faq) $issues[] = 'Pas de FAQ';
    if (!$has_table && $words > 600) $issues[] = 'Pas de tableau';
    if ($ext_count === 0) $issues[] = 'Aucun lien externe';
    if ($int_count === 0) $issues[] = 'Aucun lien interne';
    if (!$has_pillar1 && !$has_pillar2 && !$is_pillar) $issues[] = 'Pas de lien pilier';
    if ($img_count === 0) $issues[] = 'Aucune image';
    if ($brand_count === 0) $issues[] = 'Aucun outil cité';

    $results[] = [
        'id'          => $post->ID,
        'title'       => $title,
        'slug'        => $slug,
        'words'       => $words,
        'h2'          => $h2_count,
        'h3'          => $h3_count,
        'lists'       => $list_count,
        'has_table'   => $has_table,
        'has_faq'     => $has_faq,
        'img_count'   => $img_count,
        'int_links'   => $int_count,
        'ext_links'   => $ext_count,
        'brands'      => $brand_count,
        'has_pillar'  => ($has_pillar1 || $has_pillar2),
        'freshness'   => $freshness,
        'is_pillar'   => $is_pillar,
        'score'       => $total_score,
        'wc_score'    => $wc_score,
        'struct'      => $struct_score,
        'eeat'        => $eeat_score,
        'links_score' => $link_score,
        'priority'    => $priority,
        'issues'      => $issues,
        'date_pub'    => date('Y-m-d', $date_pub),
        'age_days'    => round($age_days),
    ];
}

// Sort by score ascending (worst first)
usort($results, fn($a, $b) => $a['score'] - $b['score']);

// --- SUMMARY STATS ---
$thin      = array_filter($results, fn($r) => $r['words'] < 800 && !$r['is_pillar']);
$medium    = array_filter($results, fn($r) => $r['words'] >= 800 && $r['words'] < 1500 && !$r['is_pillar']);
$good_wc   = array_filter($results, fn($r) => $r['words'] >= 1500);
$no_faq    = array_filter($results, fn($r) => !$r['has_faq'] && !$r['is_pillar']);
$no_pillar = array_filter($results, fn($r) => !$r['has_pillar'] && !$r['is_pillar']);
$no_ext    = array_filter($results, fn($r) => $r['ext_links'] === 0 && !$r['is_pillar']);
$p1_count  = array_filter($results, fn($r) => $r['priority'] === 1);
$p2_count  = array_filter($results, fn($r) => $r['priority'] === 2);
$p3_count  = array_filter($results, fn($r) => $r['priority'] === 3);

echo '<h2>Vue d\'ensemble</h2>';
echo '<table style="width:auto">';
echo '<tr><th>Métrique</th><th>Valeur</th><th>Statut</th></tr>';
echo '<tr><td>Total articles</td><td><strong>' . $total . '</strong></td><td></td></tr>';
echo '<tr class="score-bad"><td>🔴 Thin content (&lt;800 mots)</td><td><strong>' . count($thin) . '</strong></td><td class="err">Action immédiate</td></tr>';
echo '<tr class="score-ok"><td>🟡 Contenu court (800–1500 mots)</td><td><strong>' . count($medium) . '</strong></td><td class="warn">À enrichir</td></tr>';
echo '<tr class="score-good"><td>🟢 Contenu suffisant (1500+ mots)</td><td><strong>' . count($good_wc) . '</strong></td><td class="ok">OK</td></tr>';
echo '<tr><td>❌ Articles sans FAQ</td><td><strong>' . count($no_faq) . '</strong></td><td class="warn">À ajouter</td></tr>';
echo '<tr><td>❌ Articles sans lien pilier</td><td><strong>' . count($no_pillar) . '</strong></td><td class="warn">Maillage</td></tr>';
echo '<tr><td>❌ Articles sans lien externe</td><td><strong>' . count($no_ext) . '</strong></td><td class="warn">E-E-A-T</td></tr>';
echo '</table>';

echo '<h2>Priorités de traitement</h2>';
echo '<table style="width:auto">';
echo '<tr><th>Priorité</th><th>Nb articles</th><th>Score</th><th>Action</th></tr>';
echo '<tr class="priority-1"><td>🔴 P1 — Urgent</td><td><strong>' . count($p1_count) . '</strong></td><td>&lt;30/100</td><td>Réécriture complète</td></tr>';
echo '<tr class="priority-2"><td>🟡 P2 — À améliorer</td><td><strong>' . count($p2_count) . '</strong></td><td>30–54/100</td><td>Enrichissement ciblé</td></tr>';
echo '<tr class="priority-3"><td>🟢 P3 — Bon</td><td><strong>' . count($p3_count) . '</strong></td><td>55+/100</td><td>Maintenir / optimiser</td></tr>';
echo '</table>';

// --- DETAILED TABLE ---
echo '<h2>Tableau détaillé — tous les articles</h2>';
echo '<table>';
echo '<tr>';
echo '<th>Priorité</th>';
echo '<th>Article</th>';
echo '<th>Mots</th>';
echo '<th>H2/H3</th>';
echo '<th>Images</th>';
echo '<th>Int. Links</th>';
echo '<th>Ext. Links</th>';
echo '<th>Marques</th>';
echo '<th>FAQ</th>';
echo '<th>Tableau</th>';
echo '<th>Pilier</th>';
echo '<th>Score<br>/100</th>';
echo '<th>Problèmes</th>';
echo '</tr>';

foreach ($results as $r) {
    $score_class = $r['score'] >= 55 ? 'score-good' : ($r['score'] >= 30 ? 'score-ok' : 'score-bad');
    $pri_label   = $r['is_pillar'] ? '<span class="badge badge-green">⭐ Pilier</span>' :
                   ($r['priority'] === 1 ? '<span class="badge badge-red">P1</span>' :
                   ($r['priority'] === 2 ? '<span class="badge badge-orange">P2</span>' :
                    '<span class="badge badge-green">P3</span>'));
    $title_short = mb_substr($r['title'], 0, 52);
    $url         = 'https://qivato.com/' . $r['slug'] . '/';

    echo '<tr class="' . $score_class . '">';
    echo '<td>' . $pri_label . '</td>';
    echo '<td><a href="' . esc_url($url) . '" target="_blank">' . esc_html($title_short) . '</a><br><small style="color:#777">' . $r['date_pub'] . ' · ' . $r['age_days'] . 'j · ' . $r['freshness'] . '</small></td>';
    echo '<td>' . ($r['words'] < 800 ? '<span class="err">' . $r['words'] . '</span>' : ($r['words'] < 1500 ? '<span class="warn">' . $r['words'] . '</span>' : '<span class="ok">' . $r['words'] . '</span>')) . '</td>';
    echo '<td>' . $r['h2'] . '/' . $r['h3'] . '</td>';
    echo '<td>' . ($r['img_count'] === 0 ? '<span class="err">0</span>' : $r['img_count']) . '</td>';
    echo '<td>' . ($r['int_links'] === 0 ? '<span class="err">0</span>' : ($r['int_links'] < 3 ? '<span class="warn">' . $r['int_links'] . '</span>' : '<span class="ok">' . $r['int_links'] . '</span>')) . '</td>';
    echo '<td>' . ($r['ext_links'] === 0 ? '<span class="err">0</span>' : ($r['ext_links'] < 2 ? '<span class="warn">' . $r['ext_links'] . '</span>' : '<span class="ok">' . $r['ext_links'] . '</span>')) . '</td>';
    echo '<td>' . ($r['brands'] === 0 ? '<span class="err">0</span>' : $r['brands']) . '</td>';
    echo '<td>' . ($r['has_faq'] ? '<span class="ok">✅</span>' : '<span class="err">❌</span>') . '</td>';
    echo '<td>' . ($r['has_table'] ? '<span class="ok">✅</span>' : '<span class="err">❌</span>') . '</td>';
    echo '<td>' . ($r['is_pillar'] ? '⭐' : ($r['has_pillar'] ? '<span class="ok">✅</span>' : '<span class="err">❌</span>')) . '</td>';
    echo '<td><strong>' . $r['score'] . '</strong>/100<br><small>' . $r['wc_score'] . '+' . $r['struct'] . '+' . $r['eeat'] . '+' . $r['links_score'] . '</small></td>';
    echo '<td><small>' . implode('<br>', array_map('esc_html', $r['issues'])) . '</small></td>';
    echo '</tr>';
}

echo '</table>';

// --- TOP PRIORITY LIST ---
echo '<h2>🔴 Articles priorité 1 — Réécriture urgente</h2>';
$urgent = array_filter($results, fn($r) => $r['priority'] === 1 && !$r['is_pillar']);
if (empty($urgent)) {
    echo '<p class="ok">✅ Aucun article en priorité 1 !</p>';
} else {
    echo '<ol>';
    foreach ($urgent as $r) {
        echo '<li><a href="https://qivato.com/' . esc_html($r['slug']) . '/" target="_blank">' . esc_html($r['title']) . '</a>';
        echo ' — <strong>' . $r['words'] . ' mots</strong> | Score: ' . $r['score'] . '/100';
        echo '<br><small style="color:#cc0000">' . implode(' · ', $r['issues']) . '</small></li>';
    }
    echo '</ol>';
}

echo '<h2>🟡 Articles priorité 2 — Enrichissement ciblé</h2>';
$medium_pri = array_filter($results, fn($r) => $r['priority'] === 2 && !$r['is_pillar']);
if (empty($medium_pri)) {
    echo '<p class="ok">✅ Aucun article en priorité 2 !</p>';
} else {
    echo '<ol>';
    foreach ($medium_pri as $r) {
        echo '<li><a href="https://qivato.com/' . esc_html($r['slug']) . '/" target="_blank">' . esc_html($r['title']) . '</a>';
        echo ' — <strong>' . $r['words'] . ' mots</strong> | Score: ' . $r['score'] . '/100';
        if (!empty($r['issues'])) echo '<br><small style="color:#b36200">' . implode(' · ', $r['issues']) . '</small>';
        echo '</li>';
    }
    echo '</ol>';
}

echo '<p style="color:red;margin-top:40px"><strong>SUPPRIMER CE FICHIER via cPanel après lecture.</strong></p>';
