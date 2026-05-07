<?php
/**
 * Category Descriptions Fix — Qivato.com
 * Adds SEO-optimized descriptions to all 5 categories.
 * Upload to WordPress root, run ONCE, then DELETE immediately.
 * Access: https://qivato.com/seo-categories-qivato.php?token=qivato2026cat
 */

define('SECRET', 'qivato2026cat');
if (!isset($_GET['token']) || $_GET['token'] !== SECRET) {
    die('Access denied. Add ?token=qivato2026cat to the URL.');
}

$wp_load = dirname(__FILE__) . '/wp-load.php';
if (!file_exists($wp_load)) die('wp-load.php not found.');
require_once($wp_load);
if (!function_exists('wp_update_term')) die('WordPress not loaded.');

echo '<html><head><meta charset="UTF-8"><title>Category Fix - Qivato</title>';
echo '<style>body{font-family:sans-serif;max-width:900px;margin:40px auto;padding:20px;}
.ok{color:green;font-weight:bold;} .err{color:red;} .skip{color:orange;}
table{width:100%;border-collapse:collapse;margin-top:20px;}
th,td{padding:10px;text-align:left;border-bottom:1px solid #eee;font-size:13px;}
th{background:#f0f0f0;}</style></head><body>';
echo '<h1>Descriptions Catégories — Qivato SEO Fix</h1>';
echo '<table><tr><th>Catégorie</th><th>Slug</th><th>Statut</th></tr>';

$updated = 0;
$errors  = 0;

// [slug, name, description]
$categories = [

    [
        'ai-tools-saas',
        'AI Tools & SaaS',
        'Discover the best AI tools and SaaS platforms for freelancers and small businesses. We review, compare and rank the top artificial intelligence software to help you automate your workflow, boost productivity and grow your income. From writing assistants to CRM tools, project management platforms and automation software — find the right AI tools to build a smarter, more efficient business in 2026.'
    ],

    [
        'trends-innovation',
        'Trends Innovation',
        'Stay ahead of the curve with the latest AI and tech trends reshaping freelancing and online business. We analyze emerging technologies, market shifts and innovation breakthroughs that matter to independent professionals. From autonomous AI agents to no-code platforms and the gig economy evolution — discover the trends you need to know to future-proof your business and seize new opportunities in 2026 and beyond.'
    ],

    [
        'growth-hacking',
        'Growth Hacking',
        'Proven growth hacking strategies powered by AI to help freelancers and small businesses acquire more clients, increase revenue and scale faster. Explore data-driven tactics for lead generation, customer retention, email marketing automation, retargeting and personalization at scale. Learn how to use artificial intelligence to grow your business smarter, not harder — with real-world strategies that deliver measurable results.'
    ],

    [
        'guides-tutoriels',
        'Guides & Tutoriels',
        'Step-by-step guides and practical tutorials for freelancers looking to master AI tools, automation workflows and digital business strategies. Whether you want to automate your invoicing, set up a CRM, build your personal brand or create a content strategy — our hands-on guides walk you through every step. Learn at your own pace with actionable tutorials designed for independent professionals in 2026.'
    ],

    [
        'ecomstack',
        'EcomStack',
        'AI-powered strategies and tools for e-commerce businesses and online sellers. Explore how artificial intelligence is transforming inventory management, demand forecasting, warehouse automation, last-mile delivery and reverse logistics. From reducing stockouts to optimizing fulfillment and personalizing the customer experience — EcomStack covers everything you need to build a smarter, more profitable e-commerce operation with AI.'
    ],
];

foreach ($categories as [$slug, $name, $description]) {
    $term = get_term_by('slug', $slug, 'category');

    if (!$term) {
        // Try by name
        $term = get_term_by('name', $name, 'category');
    }

    if (!$term) {
        echo "<tr><td>{$name}</td><td>{$slug}</td><td class='err'>Catégorie introuvable</td></tr>";
        $errors++;
        continue;
    }

    if (!empty($term->description)) {
        echo "<tr><td>{$name}</td><td>{$slug}</td><td class='skip'>Description déjà présente — ignorée</td></tr>";
        continue;
    }

    $result = wp_update_term($term->term_id, 'category', [
        'description' => $description,
    ]);

    if (is_wp_error($result)) {
        echo "<tr><td>{$name}</td><td>{$slug}</td><td class='err'>Erreur : " . $result->get_error_message() . "</td></tr>";
        $errors++;
    } else {
        $preview = mb_substr($description, 0, 80) . '...';
        echo "<tr><td><strong>{$name}</strong></td><td>{$slug}</td><td class='ok'>✓ Description ajoutée — \"{$preview}\"</td></tr>";
        $updated++;
    }
}

echo '</table>';
echo '<br><hr><h2>Résumé</h2>';
echo "<p><strong class='ok'>Catégories mises à jour : {$updated}</strong> | ";
echo "<strong class='err'>Erreurs : {$errors}</strong></p>";
echo "<p style='color:red;font-weight:bold;'>⚠️ SUPPRIMEZ CE FICHIER IMMÉDIATEMENT.</p>";
echo '</body></html>';
