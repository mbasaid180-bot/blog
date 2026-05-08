<?php
/**
 * One-time script: Auto-add internal links to pillar articles
 * Token: qivato2026maillage
 * DELETE this file after execution.
 */

if (!isset($_GET['token']) || $_GET['token'] !== 'qivato2026maillage') {
    die('Unauthorized');
}

require_once('wp-load.php');

$dry_run = !isset($_GET['apply']) || $_GET['apply'] !== '1';

echo '<style>
body{font-family:monospace;padding:20px;font-size:13px}
table{border-collapse:collapse;width:100%;margin-bottom:20px}
td,th{border:1px solid #ccc;padding:6px;text-align:left}
.ok{color:green} .warn{color:orange} .err{color:red} .skip{color:#999}
h2{border-bottom:2px solid #333;padding-bottom:5px}
.dry{background:#fff3cd;padding:10px;border:1px solid #ffc107;margin-bottom:15px}
</style>';

echo '<h2>Maillage Interne — Qivato</h2>';

if ($dry_run) {
    echo '<div class="dry">⚠️ MODE SIMULATION — aucune modification. <a href="?token=qivato2026maillage&apply=1"><strong>→ Cliquer ici pour appliquer les changements</strong></a></div>';
} else {
    echo '<div style="background:#e0f4e0;padding:10px;border:1px solid green;margin-bottom:15px">✅ MODE APPLICATION — modifications en cours...</div>';
}

// Pillar definitions
$pillars = [
    'p1' => [
        'slug'    => 'best-ai-tools-for-freelancers-scale-your-business-in-2026',
        'url'     => 'https://qivato.com/best-ai-tools-for-freelancers-scale-your-business-in-2026/',
        'anchor'  => 'best AI tools for freelancers',
        'anchor2' => 'top AI tools for freelancers in 2026',
        'keywords' => [
            'ai tool', 'artificial intelligence', 'chatgpt', 'claude', 'jasper', 'midjourney',
            'canva', 'grammarly', 'notion', 'surfer', 'freelancer tool', 'productivity tool',
            'freelance tool', 'writing tool', 'design tool', 'content tool', 'ai assistant',
            'ai software', 'best tool', 'top tool', 'must-have tool', 'honeybook',
        ],
    ],
    'p2' => [
        'slug'    => 'automate-your-freelance-business-complete-guide-2026',
        'url'     => 'https://qivato.com/automate-your-freelance-business-complete-guide-2026/',
        'anchor'  => 'automate your freelance business',
        'anchor2' => 'freelance business automation guide',
        'keywords' => [
            'automat', 'workflow', 'zapier', 'make.com', 'integromat', 'n8n', 'trigger',
            'schedule', 'batch', 'recurring', 'system', 'process', 'streamline', 'efficiency',
            'save time', 'passive', 'scale', 'outsourc', 'delegat', 'automation tool',
        ],
    ],
];

// Get all published posts
$posts = get_posts([
    'post_type'      => 'post',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'orderby'        => 'date',
    'order'          => 'DESC',
]);

$stats = ['updated' => 0, 'skipped' => 0, 'already_ok' => 0, 'no_spot' => 0];

echo '<table>';
echo '<tr><th>Article</th><th>Action</th><th>Lien ajouté</th><th>Résultat</th></tr>';

foreach ($posts as $post) {
    $slug    = $post->post_name;
    $content = $post->post_content;
    $title   = mb_substr($post->post_title, 0, 55);

    // Skip pillar articles themselves
    if (in_array($slug, [$pillars['p1']['slug'], $pillars['p2']['slug']])) {
        echo '<tr><td>' . esc_html($title) . '</td><td colspan="3" class="skip">⭐ Pilier — ignoré</td></tr>';
        continue;
    }

    // Check current pillar links
    $has_p1 = strpos($content, $pillars['p1']['slug']) !== false;
    $has_p2 = strpos($content, $pillars['p2']['slug']) !== false;

    if ($has_p1 && $has_p2) {
        echo '<tr><td>' . esc_html($title) . '</td><td colspan="3" class="ok">✅ Déjà bien maillé</td></tr>';
        $stats['already_ok']++;
        continue;
    }

    $content_lower = strtolower($content . ' ' . strtolower($post->post_title));
    $actions = [];

    // Score each pillar based on keyword matches
    foreach (['p1', 'p2'] as $pid) {
        if (($pid === 'p1' && $has_p1) || ($pid === 'p2' && $has_p2)) {
            continue; // already links to this pillar
        }

        $score = 0;
        foreach ($pillars[$pid]['keywords'] as $kw) {
            $score += substr_count($content_lower, $kw);
        }
        if ($score > 0) {
            $actions[$pid] = $score;
        }
    }

    if (empty($actions)) {
        // No keyword match — add the most relevant based on slug keywords
        $slug_lower = strtolower($slug . ' ' . strtolower($post->post_title));
        $auto_p = null;
        if (!$has_p1 && (strpos($slug_lower, 'tool') !== false || strpos($slug_lower, 'ai') !== false || strpos($slug_lower, 'freelanc') !== false)) {
            $auto_p = 'p1';
        } elseif (!$has_p2 && (strpos($slug_lower, 'automat') !== false || strpos($slug_lower, 'workflow') !== false)) {
            $auto_p = 'p2';
        }
        if ($auto_p) {
            $actions[$auto_p] = 1;
        }
    }

    if (empty($actions)) {
        // Default: add p1 if missing (most articles relate to AI tools)
        if (!$has_p1) $actions['p1'] = 1;
        elseif (!$has_p2) $actions['p2'] = 1;
    }

    // Sort by score descending
    arsort($actions);

    $new_content    = $content;
    $added          = [];
    $insertion_log  = [];

    foreach (array_keys($actions) as $pid) {
        $pillar    = $pillars[$pid];
        $link_url  = $pillar['url'];
        $anchor    = $pillar['anchor'];
        $anchor2   = $pillar['anchor2'];

        // Try to find a natural insertion point:
        // Strategy 1 — find a paragraph containing relevant keyword and wrap it
        $best_pos    = false;
        $best_kw     = '';
        $keyword_map = [
            'p1' => ['ai tool', 'freelancer tool', 'productivity', 'writing tool', 'design tool', 'content creation'],
            'p2' => ['automat', 'workflow', 'save time', 'streamline', 'efficiency', 'scale your'],
        ];
        $search_kws = $keyword_map[$pid] ?? [];

        // Try to find paragraph with keyword and insert link inline
        $inserted = false;

        // Strategy 1: wrap an existing phrase in the content with a link
        $phrases_to_wrap = [
            'p1' => [
                'AI tools'              => $anchor,
                'ai tools'              => $anchor,
                'freelance tools'       => $anchor,
                'best tools'            => $anchor,
                'productivity tools'    => $anchor,
                'AI software'           => $anchor,
            ],
            'p2' => [
                'automate'              => $anchor2,
                'automation'            => $anchor2,
                'workflow automation'   => $anchor2,
                'save time'             => $anchor2,
                'streamline'            => $anchor2,
            ],
        ];

        foreach ($phrases_to_wrap[$pid] as $phrase => $display_anchor) {
            // Case-insensitive search — find occurrence not already inside an <a> tag
            $pattern = '/(?<!["\'])(' . preg_quote($phrase, '/') . ')(?![^<]*<\/a>)/i';
            if (preg_match($pattern, $new_content, $match, PREG_OFFSET_CAPTURE)) {
                $match_pos   = $match[1][1];
                $match_text  = $match[1][0];
                // Check it's not already inside an anchor
                $before = substr($new_content, 0, $match_pos);
                $open_a  = substr_count($before, '<a ');
                $close_a = substr_count($before, '</a>');
                if ($open_a <= $close_a) {
                    $link_html   = '<a href="' . $link_url . '">' . $match_text . '</a>';
                    $new_content = substr($new_content, 0, $match_pos) . $link_html . substr($new_content, $match_pos + strlen($match_text));
                    $inserted    = true;
                    $insertion_log[] = $pid . ': wrapped "' . $match_text . '"';
                    break;
                }
            }
        }

        // Strategy 2: append a sentence in the first matching paragraph
        if (!$inserted) {
            // Find first <p> that contains relevant content keywords
            $paragraphs = [];
            preg_match_all('/<p>(.*?)<\/p>/is', $new_content, $para_matches, PREG_OFFSET_CAPTURE);

            foreach ($para_matches[0] as $idx => $para_match) {
                $para_text  = strtolower(strip_tags($para_match[0]));
                $para_score = 0;
                foreach ($search_kws as $kw) {
                    $para_score += substr_count($para_text, $kw);
                }
                if ($para_score > 0 && strlen($para_text) > 100) {
                    $paragraphs[] = ['idx' => $idx, 'offset' => $para_match[1], 'length' => strlen($para_match[0]), 'score' => $para_score];
                }
            }

            // Sort by score
            usort($paragraphs, fn($a, $b) => $b['score'] - $a['score']);

            if (!empty($paragraphs)) {
                $best_para = $paragraphs[0];
                $para_end  = $best_para['offset'] + $best_para['length'] - strlen('</p>');
                $cta_sentence = ' For a complete overview, check out our guide to the <a href="' . $link_url . '">' . $anchor . '</a>.';
                $new_content = substr($new_content, 0, $para_end) . $cta_sentence . substr($new_content, $para_end);
                $inserted    = true;
                $insertion_log[] = $pid . ': appended sentence in paragraph';
            }
        }

        // Strategy 3: Add a "Related" paragraph before the last H2 or at end of content
        if (!$inserted) {
            $anchor_text = ($pid === 'p1') ? $anchor : $anchor2;
            $related_html = "\n<p><strong>Related:</strong> <a href=\"" . $link_url . "\">" . ucfirst($anchor_text) . "</a> — our complete guide for freelancers.</p>\n";

            // Insert before last </p> tag
            $last_p = strrpos($new_content, '</p>');
            if ($last_p !== false) {
                $new_content = substr($new_content, 0, $last_p + 4) . $related_html . substr($new_content, $last_p + 4);
                $inserted    = true;
                $insertion_log[] = $pid . ': added Related block at end';
            }
        }

        if ($inserted) {
            $added[] = $pid;
        }
    }

    if (empty($added)) {
        echo '<tr><td>' . esc_html($title) . '</td><td class="err">❌ Aucun point d\'insertion</td><td>—</td><td>—</td></tr>';
        $stats['no_spot']++;
        continue;
    }

    $added_labels = array_map(fn($p) => $p === 'p1' ? 'Pilier 1' : 'Pilier 2', $added);
    $log_str      = implode(' | ', $insertion_log);

    if (!$dry_run) {
        $result = wp_update_post(['ID' => $post->ID, 'post_content' => $new_content]);
        if (!is_wp_error($result)) {
            echo '<tr><td>' . esc_html($title) . '</td><td class="ok">✅ Mis à jour</td><td>' . implode(', ', $added_labels) . '</td><td>' . esc_html($log_str) . '</td></tr>';
            $stats['updated']++;
        } else {
            echo '<tr><td>' . esc_html($title) . '</td><td class="err">❌ Échec update</td><td>' . implode(', ', $added_labels) . '</td><td>' . esc_html($result->get_error_message()) . '</td></tr>';
            $stats['skipped']++;
        }
    } else {
        echo '<tr><td>' . esc_html($title) . '</td><td class="warn">🔍 Simulé</td><td>' . implode(', ', $added_labels) . '</td><td>' . esc_html($log_str) . '</td></tr>';
        $stats['updated']++;
    }
}

echo '</table>';

echo '<h2>Résumé</h2>';
if ($dry_run) {
    echo '<p class="warn">⚠️ Simulation — aucune modification appliquée</p>';
    echo '<p>Articles qui seraient mis à jour : <strong>' . $stats['updated'] . '</strong></p>';
    echo '<p>Articles déjà bien maillés : <strong>' . $stats['already_ok'] . '</strong></p>';
    echo '<p>Articles sans point d\'insertion : <strong>' . $stats['no_spot'] . '</strong></p>';
    echo '<p><a href="?token=qivato2026maillage&apply=1" style="background:green;color:white;padding:10px 20px;text-decoration:none;font-weight:bold;display:inline-block;margin-top:10px">→ Appliquer maintenant</a></p>';
} else {
    echo '<p class="ok">✅ Articles mis à jour : <strong>' . $stats['updated'] . '</strong></p>';
    echo '<p class="skip">⏭️ Ignorés/Erreurs : <strong>' . $stats['skipped'] . '</strong></p>';
    echo '<p class="ok">✅ Déjà bien maillés : <strong>' . $stats['already_ok'] . '</strong></p>';
    echo '<p>Sans point d\'insertion : <strong>' . $stats['no_spot'] . '</strong></p>';
}

echo '<p style="color:red;margin-top:30px"><strong>SUPPRIMER CE FICHIER via cPanel après lecture.</strong></p>';
