<?php
/**
 * Image Alt Text Fix — Qivato.com
 * Adds contextual alt text to 72 images across 46 posts.
 * Upload to WordPress root, run ONCE, then DELETE immediately.
 * Access: https://qivato.com/seo-alttext-qivato.php?token=qivato2026alt
 */

define('SECRET', 'qivato2026alt');
if (!isset($_GET['token']) || $_GET['token'] !== SECRET) {
    die('Access denied. Add ?token=qivato2026alt to the URL.');
}

$wp_load = dirname(__FILE__) . '/wp-load.php';
if (!file_exists($wp_load)) die('wp-load.php not found.');
require_once($wp_load);
if (!function_exists('get_post')) die('WordPress not loaded.');

echo '<html><head><meta charset="UTF-8"><title>Alt Text Fix - Qivato</title>';
echo '<style>body{font-family:sans-serif;max-width:1000px;margin:40px auto;padding:20px;}
.ok{color:green;font-weight:bold;} .err{color:red;} .skip{color:gray;}
table{width:100%;border-collapse:collapse;margin-top:20px;}
th,td{padding:7px 10px;text-align:left;border-bottom:1px solid #eee;font-size:12px;}
th{background:#f0f0f0;font-size:13px;}</style></head><body>';
echo '<h1>Alt Text Images — Qivato SEO Fix</h1>';
echo '<p>Ajout des alt texts sur les 72 images sans description...</p>';
echo '<table><tr><th>ID</th><th>Article</th><th>Images traitées</th><th>Statut</th></tr>';

$updated_posts = 0;
$total_imgs    = 0;
$errors        = 0;

/**
 * Alt texts per post: [post_id => [alt1, alt2, alt3, ...]]
 * Order matches the order of img tags without alt in the content.
 */
$alt_data = [
    645  => ['Zapier vs Make vs Jasper vs HubSpot AI comparison'],
    685  => ['AI tools for freelancers to develop your business with 7 must-have tools',
             'AI tools for freelancers – essential toolkit illustration'],
    739  => ['2025 comparison of AI platforms for client management and freelance productivity',
             'AI platforms comparison for freelancers 2025 – overview'],
    909  => ['Best AI CRM tools for small businesses in 2025'],
    911  => ['AI automation tools for freelancers to save time and boost income'],
    913  => ['HubSpot vs Salesforce AI features comparison – which CRM wins in 2025'],
    915  => ['How to automate client onboarding with AI platforms'],
    917  => ['Top AI scheduling and calendar tools for busy professionals'],
    919  => ['AI platforms pricing comparison – free vs paid plans for 2025'],
    1032 => ['AI-powered lead generation tactics that actually work in 2025'],
    1051 => ['Using AI chatbots to convert website visitors into customers'],
    1105 => ['Personalization at scale – AI content strategies for customer acquisition'],
    1109 => ['Predictive analytics – how AI identifies your best customer prospects'],
    1112 => ['AI email marketing automation that doubles open rates'],
    1295 => ['Retargeting with AI – win back lost customers automatically'],
    2028 => ['AI tools and SaaS for freelancers – the 2026 complete guide',
             'Best AI and SaaS stack for freelancers in 2026 – illustration',
             'AI tools and SaaS guide 2026 – how to choose and adopt'],
    711  => ['AI-powered e-commerce engine – strategic guide to logistics and operations',
             'AI e-commerce automation – fulfillment and shipping illustration',
             'AI-powered e-commerce engine – inventory management guide',
             'AI e-commerce strategy – customer experience optimization'],
    758  => ['AI demand forecasting – eliminate stockouts and overstock',
             'AI demand forecasting tools – real-time inventory prediction',
             'AI demand forecasting for e-commerce – complete guide'],
    803  => ['AI for warehouse automation and smarter fulfillment',
             'AI-driven warehouse automation – efficiency and accuracy illustration',
             'AI warehouse management – fulfillment optimization guide'],
    851  => ['How AI project management tools transform client workflow for freelancers'],
    861  => ['Best AI writing assistants for faster content creation in 2026'],
    925  => ['Creating an optimal content strategy for personal branding',
             'Content strategy for personal branding – audience research illustration',
             'Personal branding content strategy – publishing and growth guide'],
    937  => ['Streamline video editing with AI-powered software for freelancers'],
    1007 => ['Defining your brand identity – an essential step for freelancers',
             'How to define brand identity – values and visual design illustration',
             'Brand identity creation guide – purpose values and voice'],
    1054 => ['The power of social media for personal branding',
             'Social media personal branding – platform strategy illustration'],
    1059 => ['AI contract and proposal generators for US freelancers'],
    1079 => ['Boost your online presence with AI personal branding tools'],
    1083 => ['Evaluating the impact of your personal branding with data metrics',
             'How to measure personal brand impact – engagement and visibility'],
    1149 => ['Smart time tracking tools for high-performing US freelancers'],
    1159 => ['How to create an optimal content strategy that drives results',
             'Content strategy creation – audience research and topic planning'],
    1199 => ['Solving last-mile delivery challenges with AI route optimization',
             'AI route optimization for last-mile delivery – real-time adjustments'],
    1401 => ['Automate logistics support with AI order tracking',
             'AI order tracking automation – customer service reduction',
             'AI-powered logistics support – shipping updates automation'],
    1609 => ['Automate your freelance business – complete guide 2026',
             'Freelance automation tools 2026 – CRM and invoicing illustration'],
    1669 => ['Freelance automation guide – get started in 2026'],
    618  => ['AI tools for freelancers – trends tools and future opportunities 2025'],
    622  => ['AI automation for freelancers – strategies tools and monetization 2025'],
    689  => ['AI business ideas for freelancers – new economic models to start in 2026'],
    777  => ['White label AI solutions for freelancers to build recurring revenue',
             'White label AI tools for freelancers – reseller business illustration'],
    779  => ['AI business model for freelancers ecosystem',
             'AI business model – multiple revenue streams for freelancers'],
    785  => ['AI consulting services for small business growth',
             'AI growth consulting – premium pricing for small businesses'],
    1137 => ['AI digital products for creators and freelancers',
             'AI digital products – courses and templates for creators'],
    1169 => ['Advanced automation – 10 AI workflows that skyrocket freelancers growth'],
    1363 => ['AI tools for freelancers – boost your solo business'],
    1378 => ['AI lead generation tools to grow your client base as a freelancer'],
    1413 => ['AI business automation – simplify your daily operations as a freelancer'],
    1461 => ['AI customer retention – keep clients coming back with automation'],
];

// ============================================================
// HELPER: add alt text to img tags without alt (or empty alt)
// ============================================================
function fix_img_alts($content, $alts) {
    $alt_index = 0;
    $changed   = 0;

    $new_content = preg_replace_callback(
        '/<img([^>]*)>/i',
        function($matches) use (&$alt_index, $alts, &$changed) {
            $attrs = $matches[1];

            // Check if alt is already set and non-empty
            if (preg_match('/\balt=["\']([^"\']+)["\']/', $attrs)) {
                return $matches[0]; // keep as-is
            }

            // Get the alt text for this image
            $alt = isset($alts[$alt_index]) ? $alts[$alt_index] : '';
            $alt_index++;

            if (empty($alt)) return $matches[0];

            $safe_alt = htmlspecialchars($alt, ENT_QUOTES, 'UTF-8');

            // Replace empty alt or add missing alt
            if (preg_match('/\balt=["\']["\']/', $attrs)) {
                // Has empty alt — replace it
                $new_attrs = preg_replace('/\balt=["\']["\']/', 'alt="' . $safe_alt . '"', $attrs);
            } else {
                // No alt attribute — add it
                $new_attrs = $attrs . ' alt="' . $safe_alt . '"';
            }

            $changed++;
            return '<img' . $new_attrs . '>';
        },
        $content
    );

    return [$new_content, $changed];
}

// ============================================================
// MAIN LOOP
// ============================================================
foreach ($alt_data as $post_id => $alts) {
    $post = get_post($post_id);
    if (!$post) {
        echo "<tr><td>{$post_id}</td><td>—</td><td>Introuvable</td><td class='err'>ERREUR</td></tr>";
        $errors++;
        continue;
    }

    $title_short = mb_substr($post->post_title, 0, 45);
    [$new_content, $changed] = fix_img_alts($post->post_content, $alts);

    if ($changed === 0) {
        echo "<tr><td>{$post_id}</td><td>{$title_short}</td><td>Alt texts déjà présents</td><td class='skip'>IGNORÉ</td></tr>";
        continue;
    }

    $result = wp_update_post(['ID' => $post_id, 'post_content' => $new_content]);

    if (is_wp_error($result) || $result === 0) {
        echo "<tr><td>{$post_id}</td><td>{$title_short}</td><td>{$changed} image(s)</td><td class='err'>ERREUR DB</td></tr>";
        $errors++;
    } else {
        echo "<tr><td>{$post_id}</td><td>{$title_short}</td><td class='ok'>{$changed} alt text(s) ajouté(s)</td><td class='ok'>✓ OK</td></tr>";
        $updated_posts++;
        $total_imgs += $changed;
    }
}

echo '</table>';
echo '<br><hr><h2>Résumé</h2>';
echo "<p><strong class='ok'>Articles mis à jour : {$updated_posts}</strong> | ";
echo "<strong class='ok'>Images corrigées : {$total_imgs}</strong> | ";
echo "<strong class='err'>Erreurs : {$errors}</strong></p>";
echo "<p style='color:red;font-weight:bold;'>⚠️ SUPPRIMEZ CE FICHIER IMMÉDIATEMENT.</p>";
echo '</body></html>';
