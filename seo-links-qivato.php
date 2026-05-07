<?php
/**
 * Internal Linking Fix — Qivato.com (v2 — Natural In-Text Links)
 * Finds keyword phrases inside article content and wraps them with links.
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

echo '<html><head><meta charset="UTF-8"><title>Internal Links Fix v2 - Qivato</title>';
echo '<style>body{font-family:sans-serif;max-width:1100px;margin:40px auto;padding:20px;}
.ok{color:green;font-weight:bold;} .err{color:red;} .skip{color:orange;} .warn{color:#e67e00;}
table{width:100%;border-collapse:collapse;margin-top:20px;}
th,td{padding:7px 10px;text-align:left;border-bottom:1px solid #eee;font-size:12px;}
th{background:#f0f0f0;font-size:13px;}</style></head><body>';
echo '<h1>Maillage Interne v2 — Liens Naturels dans le Texte</h1>';
echo '<table><tr><th>ID</th><th>Article</th><th>Lien</th><th>Phrase trouvée</th><th>Statut</th></tr>';

$updated_total = 0;
$errors_total  = 0;

/**
 * For each post: list of [search_phrases[], url, fallback_anchor]
 * search_phrases: ordered list of phrases to look for in the content (most specific first).
 * The first phrase found in the text will be wrapped with the link.
 * fallback_anchor: used only if nothing is found (inserted at end of 2nd paragraph).
 */
$linking_data = [

    645 => [
        [['AI automation tools', 'automation tools', 'automate', 'freelancers'],
         'https://qivato.com/the-best-ai-tools-for-freelancers-in-2025-complete-guide/',
         'AI tools guide for freelancers'],
        [['save time', 'boost income', 'boost productivity', 'time-saving'],
         'https://qivato.com/ai-automation-tools-for-freelancers-to-save-time-and-boost-income/',
         'AI tools to save time and boost income'],
        [['productivity tools', 'productivity', 'most productive'],
         'https://qivato.com/best-productivity-tools-for-freelancers/',
         'best productivity tools for freelancers'],
    ],

    1480 => [
        [['multilingual', 'multiple languages', 'language'],
         'https://qivato.com/ai-tools-for-translation-and-multilingual-clients/',
         'AI tools for multilingual clients'],
        [['freelancers', 'freelance professionals', 'independent'],
         'https://qivato.com/the-best-ai-tools-for-freelancers-in-2025-complete-guide/',
         'complete AI tools guide for freelancers'],
        [['productivity', 'efficient', 'workflow'],
         'https://qivato.com/best-productivity-tools-for-freelancers/',
         'best productivity tools'],
    ],

    1698 => [
        [['scale', 'grow', 'scaling your'],
         'https://qivato.com/best-ai-tools-for-freelancers-scale-your-business-in-2026/',
         'best AI tools to scale your freelance business'],
        [['must-have', 'essential tools', '7 tools', 'seven tools'],
         'https://qivato.com/ai-tools-for-freelancers-develop-your-business-with-7-must-have-tools/',
         '7 must-have AI tools for freelancers'],
        [['automate', 'automation guide', 'automated'],
         'https://qivato.com/automate-your-freelance-business-complete-guide-2026/',
         'complete freelance automation guide'],
    ],

    1739 => [
        [['sustainable', 'long-term', 'stability'],
         'https://qivato.com/ai-tools-growth-for-freelancers-build-a-sustainable-business/',
         'build a sustainable freelance business'],
        [['free tools', 'free AI', 'no cost', 'budget'],
         'https://qivato.com/best-free-ai-tools-for-freelancers/',
         'free AI tools for freelancers'],
        [['pricing', 'cost', 'price', 'affordable', 'budget'],
         'https://qivato.com/how-much-do-ai-tools-really-cost-in-2026-a-practical-pricing-breakdown-for-u-s-freelancers/',
         'AI tools pricing breakdown 2026'],
    ],

    673 => [
        [['transforming', 'transform', 'changing', 'reshape'],
         'https://qivato.com/ai-and-saas-technology-are-transforming-the-freelance-ecosystem/',
         'how AI and SaaS are transforming freelancing'],
        [['compare', 'comparison', 'vs', 'versus', 'best tool'],
         'https://qivato.com/ai-automation-tools-comparison/',
         'comparison of top AI automation tools'],
        [['SaaS', 'software stack', 'tools stack', 'guide 2026'],
         'https://qivato.com/best-ai-tools-saas-2026-the-complete-guide-to-choose-compare-adopt/',
         'complete AI and SaaS guide 2026'],
    ],

    723 => [
        [['essential', 'necessary', 'indispensable', 'crucial'],
         'https://qivato.com/automation-for-freelancers/',
         'why automation is essential for freelancers'],
        [['workflow', 'connect apps', 'connect your tools', 'automate tasks'],
         'https://qivato.com/workflow-automation-tools-for-freelancers-connect-and-save-hours/',
         'workflow automation tools'],
        [['complete guide', 'step-by-step', 'how to automate', 'full guide'],
         'https://qivato.com/automate-your-freelance-business-complete-guide-2026/',
         'complete freelance automation guide 2026'],
    ],

    791 => [
        [['future opportunities', 'opportunities', 'future of'],
         'https://qivato.com/ai-tools-for-freelancers-trends-tools-and-future-opportunities-2025/',
         'AI tools trends and future opportunities'],
        [['redefine', 'redefining', '2026', 'next year'],
         'https://qivato.com/ai-tech-trends-freelancing-online-business/',
         'AI and tech trends redefining freelancing 2026'],
        [['no-code', 'no code', 'solopreneur', 'solo'],
         'https://qivato.com/ai-no-code-the-biggest-innovation-trend-for-solopreneurs/',
         'AI and no-code: the biggest trend for solopreneurs'],
    ],

    1474 => [
        [['SaaS trends', 'software trends', 'tech trends'],
         'https://qivato.com/the-future-of-freelance-ai-and-saas-trends-to-watch-in-2025/',
         'future of freelance and SaaS trends'],
        [['opportunities', 'future', 'upcoming'],
         'https://qivato.com/ai-tools-for-freelancers-trends-tools-and-future-opportunities-2025/',
         'AI tools and future opportunities for freelancers'],
        [['autonomous', 'AI agents', 'agentic', 'agent'],
         'https://qivato.com/autonomous-ai-agents-for-freelancers/',
         'how autonomous AI agents transform freelancers'],
    ],

    1516 => [
        [['pricing', 'cost', 'how much', 'price'],
         'https://qivato.com/how-much-do-ai-tools-really-cost-in-2026-a-practical-pricing-breakdown-for-u-s-freelancers/',
         'complete AI tools pricing guide 2026'],
        [['free', 'freemium', 'no cost', 'free plan'],
         'https://qivato.com/best-free-ai-tools-for-freelancers/',
         'best free AI tools for freelancers'],
        [['beginner', 'getting started', 'start with', 'learning curve', 'easy'],
         'https://qivato.com/ai-tools-learning-curve-in-2026-the-easiest-tools-for-beginner-freelancers-in-the-us/',
         'easiest AI tools for beginner freelancers'],
    ],

    1796 => [
        [['writing assistant', 'AI writer', 'write faster', 'content generation'],
         'https://qivato.com/best-ai-writing-assistants-for-faster-content-creation/',
         'best AI writing assistants for content creation'],
        [['productivity', 'efficient', 'work faster', 'save time'],
         'https://qivato.com/best-productivity-tools-for-freelancers/',
         'top productivity tools for freelancers'],
        [['workflow', 'automate', 'automation', 'connect'],
         'https://qivato.com/workflow-automation-tools-for-freelancers-connect-and-save-hours/',
         'workflow automation tools to save hours'],
    ],

    1805 => [
        [['automate your business', 'full automation', 'entire business', 'complete automation'],
         'https://qivato.com/automate-your-freelance-business-complete-guide-2026/',
         'automate your entire freelance business'],
        [['productivity', 'stay productive', 'most productive'],
         'https://qivato.com/best-productivity-tools-for-freelancers/',
         'best productivity tools for freelancers'],
        [['grow', 'growth', 'accelerate', 'skyrocket'],
         'https://qivato.com/advanced-automation-10-ai-workflows-that-skyrocket-freelancers-growth/',
         'AI workflows that accelerate freelance growth'],
    ],

    1807 => [
        [['workflow', 'automate tasks', 'connect apps', 'save hours'],
         'https://qivato.com/workflow-automation-tools-for-freelancers-connect-and-save-hours/',
         'workflow automation tools for freelancers'],
        [['time tracking', 'track time', 'billable hours', 'hours worked'],
         'https://qivato.com/smart-time-tracking-tools-for-high-performing-us-freelancers/',
         'smart time tracking tools'],
        [['project management', 'manage projects', 'manage clients', 'deadlines'],
         'https://qivato.com/ai-assistants-for-project-management-and-productivity/',
         'AI assistants for project management'],
    ],

    1778 => [
        [['SaaS trends', 'AI trends to watch', 'watch in 2025', 'watch in 2026'],
         'https://qivato.com/the-future-of-freelance-ai-and-saas-trends-to-watch-in-2025/',
         'AI and SaaS trends to watch'],
        [['autonomous', 'AI agents', 'agentic AI', 'agent'],
         'https://qivato.com/autonomous-ai-agents-for-freelancers/',
         'how autonomous AI agents are changing freelancing'],
        [['no-code', 'no code', 'without coding', 'code-free'],
         'https://qivato.com/ai-no-code-the-biggest-innovation-trend-for-solopreneurs/',
         'AI and no-code innovation trend'],
    ],

    1818 => [
        [['no-code', 'no code', 'build without code', 'without coding'],
         'https://qivato.com/ai-no-code-the-biggest-innovation-trend-for-solopreneurs/',
         'AI and no-code tools for solopreneurs'],
        [['automate', 'complete automation', 'fully automated', 'entire business'],
         'https://qivato.com/automate-your-freelance-business-complete-guide-2026/',
         'automate your freelance business completely'],
        [['workflow', 'advanced automation', '10 workflows', 'AI workflow'],
         'https://qivato.com/advanced-automation-10-ai-workflows-that-skyrocket-freelancers-growth/',
         'advanced AI workflows for freelancers'],
    ],

    1942 => [
        [['autonomous', 'AI agents', 'agentic', 'agents'],
         'https://qivato.com/autonomous-ai-agents-for-freelancers/',
         'how autonomous AI agents transform freelance work'],
        [['business ideas', 'new business', 'business model', 'economic model'],
         'https://qivato.com/ai-business-ideas-for-freelancers-new-economic-models-you-can-start/',
         'AI business ideas for freelancers'],
        [['ecosystem', 'scalable business', 'revenue streams', 'passive income'],
         'https://qivato.com/ai-business-model-for-freelancers-ecosystem/',
         'AI business model for freelancers'],
    ],

    2028 => [
        [['scale', 'grow', 'scaling', '2026'],
         'https://qivato.com/best-ai-tools-for-freelancers-scale-your-business-in-2026/',
         'best AI tools for freelancers in 2026'],
        [['compare', 'comparison', 'Zapier', 'HubSpot', 'Make', 'Jasper'],
         'https://qivato.com/ai-automation-tools-comparison/',
         'AI automation tools comparison'],
        [['pricing', 'cost', 'price', 'budget', 'how much'],
         'https://qivato.com/how-much-do-ai-tools-really-cost-in-2026-a-practical-pricing-breakdown-for-u-s-freelancers/',
         'AI tools pricing breakdown'],
    ],

    937 => [
        [['design', 'visual', 'graphics', 'creative'],
         'https://qivato.com/ai-design-tools-every-us-freelancer-should-master/',
         'AI design tools for freelancers'],
        [['writing', 'AI writer', 'content', 'copy'],
         'https://qivato.com/best-ai-writing-assistants-for-faster-content-creation/',
         'best AI writing assistants'],
        [['content creation', 'create content', 'freelance writer', 'writers'],
         'https://qivato.com/content-creation-tools-for-freelance-writers-write-faster-better/',
         'content creation tools for freelancers'],
    ],

    618 => [
        [['future', 'future of freelance', 'upcoming trends', 'what\'s next'],
         'https://qivato.com/the-future-of-freelance-ai-and-saas-trends-to-watch-in-2025/',
         'future AI and SaaS trends for freelancers'],
        [['strategies', 'monetize', 'monetization', 'earn more'],
         'https://qivato.com/ai-automation-for-freelancers-strategies-tools-and-monetization-in-2025/',
         'AI automation strategies and monetization'],
        [['redefine', 'redefining', '2026', 'new era'],
         'https://qivato.com/ai-tech-trends-freelancing-online-business/',
         'top AI trends redefining freelancing in 2026'],
    ],

    622 => [
        [['opportunities', 'future opportunities', 'growing trends'],
         'https://qivato.com/ai-tools-for-freelancers-trends-tools-and-future-opportunities-2025/',
         'AI tools trends and future opportunities'],
        [['complete guide', 'step by step', 'full guide', 'how to automate'],
         'https://qivato.com/automate-your-freelance-business-complete-guide-2026/',
         'complete freelance automation guide'],
        [['10 workflows', 'advanced workflows', 'skyrocket', 'grow faster'],
         'https://qivato.com/advanced-automation-10-ai-workflows-that-skyrocket-freelancers-growth/',
         '10 AI workflows that skyrocket growth'],
    ],

    689 => [
        [['business model', 'revenue model', 'monetization model', 'ecosystem'],
         'https://qivato.com/ai-business-model-for-freelancers-ecosystem/',
         'AI business model for freelancers'],
        [['white label', 'resell', 'reseller', 'rebrand'],
         'https://qivato.com/white-label-ai-solutions-for-freelancers/',
         'white label AI solutions for freelancers'],
        [['consulting', 'consultant', 'advise', 'advisory'],
         'https://qivato.com/ai-consulting-services-for-small-business-growth/',
         'AI consulting services for business growth'],
    ],

    1169 => [
        [['workflow', 'connect apps', 'connect tools', 'automation tools'],
         'https://qivato.com/workflow-automation-tools-for-freelancers-connect-and-save-hours/',
         'workflow automation tools for freelancers'],
        [['complete guide', 'full guide', 'step by step', 'comprehensive'],
         'https://qivato.com/automate-your-freelance-business-complete-guide-2026/',
         'complete freelance automation guide 2026'],
        [['get started', 'start automating', 'beginner', 'first steps'],
         'https://qivato.com/freelance-automation-guide-get-started-in-2026/',
         'get started with freelance automation'],
    ],

    1701 => [
        [['invoicing', 'invoice', 'billing', 'get paid'],
         'https://qivato.com/how-to-automate-invoicing-freelancer-tutorial-2026/',
         'how to automate invoicing as a freelancer'],
        [['complete guide', 'full guide', 'step by step', 'comprehensive guide'],
         'https://qivato.com/automate-your-freelance-business-complete-guide-2026/',
         'complete freelance automation guide 2026'],
        [['CRM', 'client management', 'customer relationship', 'contacts'],
         'https://qivato.com/best-ai-crm-tools-for-small-businesses-in-2025/',
         'best AI CRM tools for small businesses'],
    ],

    1960 => [
        [['CRM', 'client management', 'customer relationship', 'manage clients'],
         'https://qivato.com/crm-automation-for-freelancers-video-tutorial-2026/',
         'CRM automation for freelancers tutorial'],
        [['automate your business', 'full automation', 'entire business', 'complete guide'],
         'https://qivato.com/automate-your-freelance-business-complete-guide-2026/',
         'automate your freelance business completely'],
        [['workflow', 'connect apps', 'save hours', 'automate tasks'],
         'https://qivato.com/workflow-automation-tools-for-freelancers-connect-and-save-hours/',
         'workflow automation tools for freelancers'],
    ],
];

// ============================================================
// HELPER: inject link around first matching phrase in content
// Skips content inside existing <a> tags and HTML attributes
// ============================================================
function inject_link_natural($content, $phrases, $url, $fallback_anchor) {
    // Strip tags for text-only search but work on original HTML
    foreach ($phrases as $phrase) {
        // Case-insensitive search outside of HTML tags
        // We use a regex that skips content inside tags
        $pattern = '/(?<!["\'=>])(?<!href=")(?<!<[^>]{0,200})(' . preg_quote($phrase, '/') . ')(?![^<]*>)(?![^<]*<\/a>)/iu';

        // Simpler approach: split on HTML tags, find phrase in text nodes
        $result = inject_in_text_nodes($content, $phrase, $url);
        if ($result !== null) {
            return [$result, $phrase];
        }
    }
    return [null, null];
}

function inject_in_text_nodes($html, $phrase, $url) {
    // Split HTML into text nodes and tag nodes
    $parts = preg_split('/(<[^>]+>)/i', $html, -1, PREG_SPLIT_DELIM_CAPTURE);

    $inside_link = 0;
    $replaced    = false;
    $result      = [];

    foreach ($parts as $part) {
        if (preg_match('/^<a[\s>]/i', $part)) {
            $inside_link++;
            $result[] = $part;
        } elseif (preg_match('/^<\/a>/i', $part)) {
            $inside_link = max(0, $inside_link - 1);
            $result[] = $part;
        } elseif (preg_match('/^</i', $part)) {
            // Other tag
            $result[] = $part;
        } else {
            // Text node
            if (!$replaced && $inside_link === 0 && mb_stripos($part, $phrase) !== false) {
                // Replace first occurrence only
                $pos = mb_stripos($part, $phrase);
                $matched = mb_substr($part, $pos, mb_strlen($phrase));
                $linked  = '<a href="' . esc_url($url) . '">' . esc_html($matched) . '</a>';
                $part    = mb_substr($part, 0, $pos) . $linked . mb_substr($part, $pos + mb_strlen($phrase));
                $replaced = true;
            }
            $result[] = $part;
        }
    }

    return $replaced ? implode('', $result) : null;
}

// ============================================================
// MAIN LOOP
// ============================================================
foreach ($linking_data as $post_id => $links_config) {
    $post = get_post($post_id);
    if (!$post) {
        echo "<tr><td>{$post_id}</td><td colspan='4'>Post introuvable</td></tr>";
        $errors_total++;
        continue;
    }

    $title_short = mb_substr($post->post_title, 0, 40);
    $content     = $post->post_content;
    $modified    = false;
    $log         = [];

    foreach ($links_config as $link_def) {
        [$phrases, $url, $fallback_anchor] = $link_def;

        // Skip if URL already present in content
        if (strpos($content, $url) !== false) {
            $log[] = "<span class='skip'>déjà présent</span>";
            continue;
        }

        [$new_content, $matched_phrase] = inject_link_natural($content, $phrases, $url, $fallback_anchor);

        if ($new_content !== null) {
            $content  = $new_content;
            $modified = true;
            $log[]    = "<span class='ok'>✓ \"{$matched_phrase}\"</span>";
        } else {
            // Fallback: insert at end of second paragraph if possible
            $paragraphs = explode('</p>', $content);
            if (count($paragraphs) >= 2) {
                $insert_idx = min(2, count($paragraphs) - 2);
                $link_html  = ' <a href="' . esc_url($url) . '">' . esc_html($fallback_anchor) . '</a>';
                $paragraphs[$insert_idx] .= $link_html;
                $content   = implode('</p>', $paragraphs);
                $modified  = true;
                $log[]     = "<span class='warn'>↪ fallback: \"{$fallback_anchor}\"</span>";
            } else {
                $log[] = "<span class='err'>✗ non inséré</span>";
            }
        }
    }

    if ($modified) {
        $result = wp_update_post(['ID' => $post_id, 'post_content' => $content]);
        $status = (is_wp_error($result) || $result === 0)
            ? "<span class='err'>ERREUR DB</span>"
            : "<span class='ok'>SAUVEGARDÉ</span>";
        if (!is_wp_error($result) && $result !== 0) $updated_total++;
        else $errors_total++;
    } else {
        $status = "<span class='skip'>IGNORÉ</span>";
    }

    echo "<tr>
        <td>{$post_id}</td>
        <td>{$title_short}</td>
        <td colspan='2'>" . implode('<br>', $log) . "</td>
        <td>{$status}</td>
    </tr>";
}

echo '</table>';
echo '<br><hr><h2>Résumé</h2>';
echo "<p><strong class='ok'>Articles mis à jour : {$updated_total}</strong> | ";
echo "<strong class='err'>Erreurs : {$errors_total}</strong></p>";
echo "<p style='color:red;font-weight:bold;'>⚠️ SUPPRIMEZ CE FICHIER IMMÉDIATEMENT.</p>";
echo '</body></html>';
