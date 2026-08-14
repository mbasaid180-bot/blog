<?php
/**
 * Tag Cleanup — Qivato.com
 * Deletes 129 single-use tags that dilute crawl budget.
 * Upload to WordPress root, run ONCE, then DELETE immediately.
 * Access: https://qivato.com/seo-tags-qivato.php?token=qivato2026tags
 */

define('SECRET', 'qivato2026tags');
if (!isset($_GET['token']) || $_GET['token'] !== SECRET) {
    die('Access denied. Add ?token=qivato2026tags to the URL.');
}

$wp_load = dirname(__FILE__) . '/wp-load.php';
if (!file_exists($wp_load)) die('wp-load.php not found.');
require_once($wp_load);
if (!function_exists('wp_delete_term')) die('WordPress not loaded.');

echo '<html><head><meta charset="UTF-8"><title>Tag Cleanup - Qivato</title>';
echo '<style>body{font-family:sans-serif;max-width:900px;margin:40px auto;padding:20px;}
.ok{color:green;} .err{color:red;} .skip{color:gray;}
table{width:100%;border-collapse:collapse;margin-top:20px;}
th,td{padding:6px 10px;text-align:left;border-bottom:1px solid #eee;font-size:12px;}
th{background:#f0f0f0;}</style></head><body>';
echo '<h1>Nettoyage des Tags — Qivato SEO</h1>';
echo '<p>Suppression des 129 tags utilisés une seule fois...</p>';
echo '<table><tr><th>Slug</th><th>Nom</th><th>Statut</th></tr>';

$deleted = 0;
$skipped = 0;
$errors  = 0;

$slugs_to_delete = ['ai-saas','automatisation','hubspot-ai','intelligence-artificielle','jasper-ai','outils-dautomatisation','ai-translation-tools','business-translation','chatgpt-translation','deepl','enterprise-translation','google-translate','language-translation','live-meeting-translation','machine-translation','microsoft-translator','multilingual-business','multilingual-communication','real-time-translation','translation-accuracy','translation-api','translation-automation','translation-comparison','translation-pricing','translation-software','freelance-growth','workflow-optimization','2026-tools','design-tools','ai-tools-list','top-ai-software','freelance-workflow','connectivity','digital-nomad','content-writing','freelance-writers','freelance-writing-tools','grammar-tools','plagiarism-checker','writing-productivity','writing-software','airtable','business-automation','categorie-ai-tools-saas-tags-workflow-automation','freelance-efficiency','ifttt','no-code-automation','time-saving-tools','time-tracking','easy-ai-tools-for-beginners','ai-tools-for-business','saas-ai-guide','ai-logistics','customer-experience','e-commerce','demand-forecasting','ecommerce','artificial-intelligence-in-logistics','inventory-management-technologies','smart-fulfillment-solutions','warehouse-automation','ai-project-management','ai-workflow-tools','client-collaboration-tools','client-workflow-management','project-management-software','task-automation','best-ai-tools-for-content-creators','best-ai-tools-for-freelancers','best-ai-writing-assistants','freelance-automation-tools','content-marketing-for-professionals','content-planning-and-creation','personal-brand-content-strategy','personal-branding-strategy','ai-design-tools','ai-video-editing-tools','ai-writing-assistants','content-repurposing','shorts-reels-tools','video-ai-tools','video-automation','youtube-growth','brand-identity-development','brand-positioning-guide','brand-strategy-fundamentals','visual-brand-design','digital-brand-building','online-reputation-management','professional-social-media-strategy','social-media-personal-branding','ai-contract-tools','freelance-legal','proposal-generators','upwork-tips','ai-personal-branding','content-automation','linkedin-tools','online-presence','social-media-growth','brand-impact-assessment','career-development-metrics','personal-brand-measurement','professional-reputation-management','client-work-management','freelance-time-management','us-freelancers','content-marketing-best-practices','content-strategy-planning','digital-marketing-strategy','seo-content-optimization','ai-route-optimization','last-mile-delivery-solutions','logistics-technology','supply-chain-automation','ai-in-logistics','customer-support','order-tracking','return-management','reverse-logistics','supply-chain-optimization','business-systems','freelance-business-management','crm-automation','productivity-automation','freelance-invoicing','freelancer-finance','get-paid-faster','invoice-automation'];

foreach ($slugs_to_delete as $slug) {
    $term = get_term_by('slug', $slug, 'post_tag');

    if (!$term) {
        echo "<tr><td>{$slug}</td><td>—</td><td class='skip'>Introuvable (déjà supprimé ?)</td></tr>";
        $skipped++;
        continue;
    }

    $result = wp_delete_term($term->term_id, 'post_tag');

    if (is_wp_error($result) || $result === false) {
        echo "<tr><td>{$slug}</td><td>{$term->name}</td><td class='err'>Erreur suppression</td></tr>";
        $errors++;
    } else {
        echo "<tr><td>{$slug}</td><td>{$term->name}</td><td class='ok'>✓ Supprimé</td></tr>";
        $deleted++;
    }
}

echo '</table>';
echo '<br><hr><h2>Résumé</h2>';
echo "<p><strong class='ok'>Tags supprimés : {$deleted}</strong> | ";
echo "<strong class='skip'>Introuvables : {$skipped}</strong> | ";
echo "<strong class='err'>Erreurs : {$errors}</strong></p>";
echo '<p>Tags conservés (3+ utilisations) : AI tools for freelancers, AI productivity tools, Software as a Service, AI automation, AI business tools, Artificial intelligence, best AI tools 2025, AI Tools, freelance automation, AI productivity tools for freelancers, Freelance Productivity, zapier, Productivity, SaaS tools for freelancers, freelancer business, Free AI tools, Workflow automation</p>';
echo "<p style='color:red;font-weight:bold;'>⚠️ SUPPRIMEZ CE FICHIER IMMÉDIATEMENT.</p>";
echo '</body></html>';
