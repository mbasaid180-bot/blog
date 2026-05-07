<?php
/**
 * Internal Linking Fix — Qivato.com
 * Adds 3 relevant internal links to each of the 23 orphan posts.
 * Upload to WordPress root, run ONCE, then DELETE immediately.
 * Access: https://qivato.com/seo-links-qivato.php?token=qivato2026links
 */

define('SECRET', 'qivato2026links');
if (!isset($_GET['token']) || $_GET['token'] !== SECRET) {
    die('Access denied. Add ?token=qivato2026links to the URL.');
}

$wp_load = dirname(__FILE__) . '/wp-load.php';
if (!file_exists($wp_load)) die('wp-load.php not found.');
require_once($wp_load);
if (!function_exists('get_post')) die('WordPress not loaded.');

echo '<html><head><meta charset="UTF-8"><title>Internal Links Fix - Qivato</title>';
echo '<style>body{font-family:sans-serif;max-width:1000px;margin:40px auto;padding:20px;}
.ok{color:green;font-weight:bold;} .err{color:red;} .skip{color:orange;}
table{width:100%;border-collapse:collapse;margin-top:20px;}
th,td{padding:8px;text-align:left;border-bottom:1px solid #eee;font-size:12px;}
th{background:#f0f0f0;font-size:13px;}</style></head><body>';
echo '<h1>Maillage Interne — Qivato SEO Fix</h1>';
echo '<p>Ajout de 3 liens internes pertinents sur les 23 articles orphelins...</p>';
echo '<table><tr><th>ID</th><th>Article</th><th>Liens ajoutés</th><th>Statut</th></tr>';

$updated = 0;
$skipped = 0;
$errors  = 0;

/**
 * linking_data: [
 *   post_id => [
 *     [anchor_text, target_url],
 *     ...
 *   ]
 * ]
 */
$linking_data = [

    645 => [
        ['AI automation guide for freelancers',          'https://qivato.com/the-best-ai-tools-for-freelancers-in-2025-complete-guide/'],
        ['AI automation tools to save time',             'https://qivato.com/ai-automation-tools-for-freelancers-to-save-time-and-boost-income/'],
        ['best productivity tools for freelancers',      'https://qivato.com/best-productivity-tools-for-freelancers/'],
    ],

    1480 => [
        ['AI tools for multilingual clients',            'https://qivato.com/ai-tools-for-translation-and-multilingual-clients/'],
        ['complete AI tools guide for freelancers',      'https://qivato.com/the-best-ai-tools-for-freelancers-in-2025-complete-guide/'],
        ['top productivity tools for freelancers',       'https://qivato.com/best-productivity-tools-for-freelancers/'],
    ],

    1698 => [
        ['best AI tools to scale your freelance business', 'https://qivato.com/best-ai-tools-for-freelancers-scale-your-business-in-2026/'],
        ['7 must-have AI tools for freelancers',           'https://qivato.com/ai-tools-for-freelancers-develop-your-business-with-7-must-have-tools/'],
        ['freelance automation tools guide',               'https://qivato.com/automate-your-freelance-business-complete-guide-2026/'],
    ],

    1739 => [
        ['AI tools to build a sustainable freelance business', 'https://qivato.com/ai-tools-growth-for-freelancers-build-a-sustainable-business/'],
        ['free AI tools for freelancers',                      'https://qivato.com/best-free-ai-tools-for-freelancers/'],
        ['AI tools pricing breakdown 2026',                    'https://qivato.com/how-much-do-ai-tools-really-cost-in-2026-a-practical-pricing-breakdown-for-u-s-freelancers/'],
    ],

    673 => [
        ['how AI and SaaS are transforming freelancing',  'https://qivato.com/ai-and-saas-technology-are-transforming-the-freelance-ecosystem/'],
        ['comparison of top AI automation tools',         'https://qivato.com/ai-automation-tools-comparison/'],
        ['complete AI and SaaS guide 2026',               'https://qivato.com/best-ai-tools-saas-2026-the-complete-guide-to-choose-compare-adopt/'],
    ],

    723 => [
        ['why automation is essential for modern freelancers', 'https://qivato.com/automation-for-freelancers/'],
        ['workflow automation tools that save hours',          'https://qivato.com/workflow-automation-tools-for-freelancers-connect-and-save-hours/'],
        ['complete freelance automation guide 2026',           'https://qivato.com/automate-your-freelance-business-complete-guide-2026/'],
    ],

    791 => [
        ['AI tools trends and future opportunities',        'https://qivato.com/ai-tools-for-freelancers-trends-tools-and-future-opportunities-2025/'],
        ['AI and tech trends redefining freelancing 2026',  'https://qivato.com/ai-tech-trends-freelancing-online-business/'],
        ['AI and no-code: the biggest trend for solopreneurs', 'https://qivato.com/ai-no-code-the-biggest-innovation-trend-for-solopreneurs/'],
    ],

    1474 => [
        ['future of freelance and SaaS trends',          'https://qivato.com/the-future-of-freelance-ai-and-saas-trends-to-watch-in-2025/'],
        ['AI tools and future opportunities for freelancers', 'https://qivato.com/ai-tools-for-freelancers-trends-tools-and-future-opportunities-2025/'],
        ['how autonomous AI agents transform freelancers', 'https://qivato.com/autonomous-ai-agents-for-freelancers/'],
    ],

    1516 => [
        ['complete AI tools pricing guide 2026',      'https://qivato.com/how-much-do-ai-tools-really-cost-in-2026-a-practical-pricing-breakdown-for-u-s-freelancers/'],
        ['best free AI tools for freelancers',         'https://qivato.com/best-free-ai-tools-for-freelancers/'],
        ['easiest AI tools for beginner freelancers',  'https://qivato.com/ai-tools-learning-curve-in-2026-the-easiest-tools-for-beginner-freelancers-in-the-us/'],
    ],

    1796 => [
        ['best AI writing assistants for content creation', 'https://qivato.com/best-ai-writing-assistants-for-faster-content-creation/'],
        ['top productivity tools for freelancers',          'https://qivato.com/best-productivity-tools-for-freelancers/'],
        ['workflow automation tools to save hours',         'https://qivato.com/workflow-automation-tools-for-freelancers-connect-and-save-hours/'],
    ],

    1805 => [
        ['automate your entire freelance business',     'https://qivato.com/automate-your-freelance-business-complete-guide-2026/'],
        ['best productivity tools for freelancers',     'https://qivato.com/best-productivity-tools-for-freelancers/'],
        ['AI workflows that accelerate freelance growth', 'https://qivato.com/advanced-automation-10-ai-workflows-that-skyrocket-freelancers-growth/'],
    ],

    1807 => [
        ['workflow automation tools for freelancers',   'https://qivato.com/workflow-automation-tools-for-freelancers-connect-and-save-hours/'],
        ['smart time tracking tools',                   'https://qivato.com/smart-time-tracking-tools-for-high-performing-us-freelancers/'],
        ['AI assistants for project management',        'https://qivato.com/ai-assistants-for-project-management-and-productivity/'],
    ],

    1778 => [
        ['AI and SaaS trends to watch',                   'https://qivato.com/the-future-of-freelance-ai-and-saas-trends-to-watch-in-2025/'],
        ['how autonomous AI agents are changing freelancing', 'https://qivato.com/autonomous-ai-agents-for-freelancers/'],
        ['AI and no-code innovation trend',                'https://qivato.com/ai-no-code-the-biggest-innovation-trend-for-solopreneurs/'],
    ],

    1818 => [
        ['AI and no-code tools for solopreneurs',       'https://qivato.com/ai-no-code-the-biggest-innovation-trend-for-solopreneurs/'],
        ['automate your freelance business completely',  'https://qivato.com/automate-your-freelance-business-complete-guide-2026/'],
        ['advanced AI workflows for freelancers',        'https://qivato.com/advanced-automation-10-ai-workflows-that-skyrocket-freelancers-growth/'],
    ],

    1942 => [
        ['how autonomous AI agents transform freelance work', 'https://qivato.com/autonomous-ai-agents-for-freelancers/'],
        ['AI business ideas for freelancers',                 'https://qivato.com/ai-business-ideas-for-freelancers-new-economic-models-you-can-start/'],
        ['AI business model for freelancers',                 'https://qivato.com/ai-business-model-for-freelancers-ecosystem/'],
    ],

    2028 => [
        ['best AI tools for freelancers in 2026',  'https://qivato.com/best-ai-tools-for-freelancers-scale-your-business-in-2026/'],
        ['AI automation tools comparison',          'https://qivato.com/ai-automation-tools-comparison/'],
        ['AI tools pricing breakdown',              'https://qivato.com/how-much-do-ai-tools-really-cost-in-2026-a-practical-pricing-breakdown-for-u-s-freelancers/'],
    ],

    937 => [
        ['AI design tools for freelancers',          'https://qivato.com/ai-design-tools-every-us-freelancer-should-master/'],
        ['best AI writing assistants',               'https://qivato.com/best-ai-writing-assistants-for-faster-content-creation/'],
        ['content creation tools for freelancers',   'https://qivato.com/content-creation-tools-for-freelance-writers-write-faster-better/'],
    ],

    618 => [
        ['future AI and SaaS trends for freelancers',    'https://qivato.com/the-future-of-freelance-ai-and-saas-trends-to-watch-in-2025/'],
        ['AI automation strategies and monetization',    'https://qivato.com/ai-automation-for-freelancers-strategies-tools-and-monetization-in-2025/'],
        ['top AI trends redefining freelancing in 2026', 'https://qivato.com/ai-tech-trends-freelancing-online-business/'],
    ],

    622 => [
        ['AI tools trends and future opportunities',  'https://qivato.com/ai-tools-for-freelancers-trends-tools-and-future-opportunities-2025/'],
        ['complete freelance automation guide',       'https://qivato.com/automate-your-freelance-business-complete-guide-2026/'],
        ['10 AI workflows that skyrocket growth',     'https://qivato.com/advanced-automation-10-ai-workflows-that-skyrocket-freelancers-growth/'],
    ],

    689 => [
        ['AI business model for freelancers',         'https://qivato.com/ai-business-model-for-freelancers-ecosystem/'],
        ['white label AI solutions for freelancers',  'https://qivato.com/white-label-ai-solutions-for-freelancers/'],
        ['AI consulting services for business growth','https://qivato.com/ai-consulting-services-for-small-business-growth/'],
    ],

    1169 => [
        ['workflow automation tools for freelancers',    'https://qivato.com/workflow-automation-tools-for-freelancers-connect-and-save-hours/'],
        ['complete freelance automation guide 2026',     'https://qivato.com/automate-your-freelance-business-complete-guide-2026/'],
        ['get started with freelance automation',        'https://qivato.com/freelance-automation-guide-get-started-in-2026/'],
    ],

    1701 => [
        ['how to automate invoicing as a freelancer', 'https://qivato.com/how-to-automate-invoicing-freelancer-tutorial-2026/'],
        ['complete freelance automation guide 2026',  'https://qivato.com/automate-your-freelance-business-complete-guide-2026/'],
        ['best AI CRM tools for small businesses',    'https://qivato.com/best-ai-crm-tools-for-small-businesses-in-2025/'],
    ],

    1960 => [
        ['CRM automation for freelancers tutorial',    'https://qivato.com/crm-automation-for-freelancers-video-tutorial-2026/'],
        ['automate your freelance business completely','https://qivato.com/automate-your-freelance-business-complete-guide-2026/'],
        ['workflow automation tools for freelancers',  'https://qivato.com/workflow-automation-tools-for-freelancers-connect-and-save-hours/'],
    ],
];

// ============================================================
// APPLY LINKS
// ============================================================
foreach ($linking_data as $post_id => $links) {
    $post = get_post($post_id);
    if (!$post) {
        echo "<tr><td>{$post_id}</td><td>—</td><td>Introuvable</td><td class='err'>ERREUR</td></tr>";
        $errors++;
        continue;
    }

    $title_short = mb_substr($post->post_title, 0, 45);
    $content = $post->post_content;

    // Build the link block to append
    $link_block = "\n\n<p><strong>Related articles:</strong> ";
    $link_parts = [];
    foreach ($links as $link) {
        $anchor = esc_html($link[0]);
        $url    = esc_url($link[1]);
        $link_parts[] = "<a href=\"{$url}\">{$anchor}</a>";
    }
    $link_block .= implode(' &mdash; ', $link_parts) . "</p>";

    // Check if links already present (avoid duplicates)
    $already_linked = false;
    foreach ($links as $link) {
        if (strpos($content, $link[1]) !== false) {
            $already_linked = true;
            break;
        }
    }

    if ($already_linked) {
        echo "<tr><td>{$post_id}</td><td>{$title_short}</td><td>Liens déjà présents</td><td class='skip'>IGNORÉ</td></tr>";
        $skipped++;
        continue;
    }

    // Append link block before closing </p> or at end
    $new_content = $content . $link_block;

    $result = wp_update_post([
        'ID'           => $post_id,
        'post_content' => $new_content,
    ]);

    if (is_wp_error($result) || $result === 0) {
        echo "<tr><td>{$post_id}</td><td>{$title_short}</td><td>Erreur mise à jour</td><td class='err'>ERREUR</td></tr>";
        $errors++;
    } else {
        $links_html = implode(', ', array_map(fn($l) => "<em>{$l[0]}</em>", $links));
        echo "<tr><td>{$post_id}</td><td>{$title_short}</td><td>{$links_html}</td><td class='ok'>✓ OK</td></tr>";
        $updated++;
    }
}

echo '</table>';
echo '<br><hr><h2>Résumé</h2>';
echo "<p><strong class='ok'>Mis à jour : {$updated}</strong> | ";
echo "<strong class='skip'>Ignorés : {$skipped}</strong> | ";
echo "<strong class='err'>Erreurs : {$errors}</strong></p>";
echo "<p style='color:red;font-weight:bold;'>⚠️ SUPPRIMEZ CE FICHIER IMMÉDIATEMENT après vérification.</p>";
echo '</body></html>';
