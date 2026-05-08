<?php
/**
 * One-time script: Inject authoritative external links into articles missing them
 * Token: qivato2026extlinks
 * DELETE this file after execution.
 */

if (!isset($_GET['token']) || $_GET['token'] !== 'qivato2026extlinks') {
    die('Unauthorized');
}

require_once('wp-load.php');

$dry_run = !isset($_GET['apply']) || $_GET['apply'] !== '1';

echo '<style>
body{font-family:monospace;padding:20px;font-size:13px;max-width:1300px}
table{border-collapse:collapse;width:100%;margin-bottom:20px}
td,th{border:1px solid #ccc;padding:6px;text-align:left}
.ok{color:green} .warn{color:orange} .err{color:red} .skip{color:#999}
.dry{background:#fff3cd;padding:10px;border:1px solid #ffc107;margin-bottom:15px}
</style>';

echo '<h2>Injection Liens Externes — Qivato</h2>';

if ($dry_run) {
    echo '<div class="dry">⚠️ MODE SIMULATION. <a href="?token=qivato2026extlinks&apply=1"><strong>→ Appliquer les changements</strong></a></div>';
} else {
    echo '<div style="background:#e0f4e0;padding:10px;border:1px solid green;margin-bottom:15px">✅ MODE APPLICATION</div>';
}

/**
 * External link library — keyed by topic keywords
 * Format: ['anchor text' => 'https://...']
 */
$link_library = [
    // AI General
    'ai_general' => [
        'keywords' => ['artificial intelligence', 'ai tool', 'machine learning', 'ai software', 'ai platform', 'ai solution', 'ai technology'],
        'sources'  => [
            ['anchor' => 'McKinsey Global AI Report', 'url' => 'https://www.mckinsey.com/capabilities/quantumblack/our-insights/the-state-of-ai'],
            ['anchor' => 'Stanford AI Index 2024', 'url' => 'https://aiindex.stanford.edu/report/'],
            ['anchor' => 'Gartner AI Trends', 'url' => 'https://www.gartner.com/en/topics/artificial-intelligence'],
        ],
    ],
    // Freelancing
    'freelance' => [
        'keywords' => ['freelancer', 'freelance business', 'solopreneur', 'independent contractor', 'gig worker', 'self-employed'],
        'sources'  => [
            ['anchor' => 'Upwork Future of Work Report', 'url' => 'https://www.upwork.com/research/future-workforce-report'],
            ['anchor' => 'Freelancers Union research', 'url' => 'https://www.freelancersunion.org/resources/freelancing-in-america/'],
            ['anchor' => 'MBO Partners State of Independence', 'url' => 'https://www.mbopartners.com/state-of-independence/'],
        ],
    ],
    // Automation / Workflow
    'automation' => [
        'keywords' => ['automat', 'workflow', 'zapier', 'make.com', 'n8n', 'process automat', 'task automat', 'business automat'],
        'sources'  => [
            ['anchor' => 'Zapier Automation Guide', 'url' => 'https://zapier.com/blog/what-is-automation/'],
            ['anchor' => 'Harvard Business Review on Automation', 'url' => 'https://hbr.org/topic/subject/automation'],
            ['anchor' => 'McKinsey Automation Potential Report', 'url' => 'https://www.mckinsey.com/featured-insights/future-of-work/jobs-lost-jobs-gained-what-the-future-of-work-will-mean-for-jobs-skills-and-wages'],
        ],
    ],
    // E-commerce / Logistics
    'ecommerce' => [
        'keywords' => ['ecommerce', 'e-commerce', 'online store', 'shopify', 'dropshipping', 'logistics', 'fulfillment', 'warehouse', 'inventory', 'supply chain', 'last-mile', 'delivery'],
        'sources'  => [
            ['anchor' => 'Shopify Commerce Trends', 'url' => 'https://www.shopify.com/research/future-of-commerce'],
            ['anchor' => 'Statista E-Commerce Statistics', 'url' => 'https://www.statista.com/topics/871/online-shopping/'],
            ['anchor' => 'Forbes E-Commerce Insights', 'url' => 'https://www.forbes.com/advisor/business/ecommerce-statistics/'],
        ],
    ],
    // CRM / Marketing
    'crm_marketing' => [
        'keywords' => ['crm', 'customer relationship', 'hubspot', 'salesforce', 'lead generation', 'email marketing', 'customer retention', 'personalization', 'retargeting', 'marketing automat'],
        'sources'  => [
            ['anchor' => 'HubSpot Marketing Statistics', 'url' => 'https://www.hubspot.com/marketing-statistics'],
            ['anchor' => 'Salesforce State of Marketing Report', 'url' => 'https://www.salesforce.com/resources/research-reports/state-of-marketing/'],
            ['anchor' => 'Mailchimp Email Marketing Benchmarks', 'url' => 'https://mailchimp.com/resources/email-marketing-benchmarks/'],
        ],
    ],
    // Productivity / Tools
    'productivity' => [
        'keywords' => ['productivity', 'time management', 'time tracking', 'project management', 'scheduling', 'calendar', 'task management', 'focus', 'efficiency'],
        'sources'  => [
            ['anchor' => 'Asana Anatomy of Work Report', 'url' => 'https://asana.com/resources/anatomy-of-work'],
            ['anchor' => 'RescueTime Productivity Data', 'url' => 'https://www.rescuetime.com/resources/productivity-report'],
            ['anchor' => 'Forbes Productivity Statistics', 'url' => 'https://www.forbes.com/advisor/business/remote-work-statistics/'],
        ],
    ],
    // Content / Writing
    'content_writing' => [
        'keywords' => ['content creat', 'writing tool', 'copywriting', 'content strateg', 'blog', 'seo content', 'jasper', 'grammarly', 'content market'],
        'sources'  => [
            ['anchor' => 'Content Marketing Institute Research', 'url' => 'https://contentmarketinginstitute.com/articles/content-marketing-statistics/'],
            ['anchor' => 'SEMrush Content Marketing Statistics', 'url' => 'https://www.semrush.com/blog/content-marketing-statistics/'],
            ['anchor' => 'HubSpot State of Marketing', 'url' => 'https://www.hubspot.com/state-of-marketing'],
        ],
    ],
    // Design / Creative
    'design' => [
        'keywords' => ['design tool', 'canva', 'midjourney', 'image generat', 'graphic design', 'visual content', 'creative tool', 'ai design', 'video edit'],
        'sources'  => [
            ['anchor' => 'Adobe Creative Economy Report', 'url' => 'https://www.adobe.com/creativecloud/business/teams/resources/reports/creative-economy.html'],
            ['anchor' => 'Canva Design Statistics', 'url' => 'https://www.canva.com/learn/design-statistics/'],
            ['anchor' => 'Statista Digital Design Market', 'url' => 'https://www.statista.com/topics/3947/graphic-design/'],
        ],
    ],
    // Branding / Personal Brand
    'branding' => [
        'keywords' => ['personal brand', 'brand identity', 'brand strateg', 'brand voice', 'social media brand', 'online presence', 'linkedin'],
        'sources'  => [
            ['anchor' => 'Forbes Personal Branding Guide', 'url' => 'https://www.forbes.com/advisor/business/what-is-personal-branding/'],
            ['anchor' => 'LinkedIn Marketing Solutions', 'url' => 'https://business.linkedin.com/marketing-solutions/blog/linkedin-b2b-marketing/2024/personal-branding-tips'],
            ['anchor' => 'Harvard Business Review on Personal Branding', 'url' => 'https://hbr.org/2005/01/managing-oneself'],
        ],
    ],
    // Translation / Multilingual
    'translation' => [
        'keywords' => ['translat', 'multilingual', 'language', 'locali', 'deepl', 'international'],
        'sources'  => [
            ['anchor' => 'CSA Research Language Industry Report', 'url' => 'https://csa-research.com/Blogs-Events/CSA-in-the-Media/Press-Releases/Consumers-Prefer-their-Own-Language'],
            ['anchor' => 'Statista Translation Services Market', 'url' => 'https://www.statista.com/statistics/257656/size-of-the-global-language-services-market/'],
            ['anchor' => 'Nimdzi Language Industry Report', 'url' => 'https://www.nimdzi.com/nimdzi-100/'],
        ],
    ],
    // Predictive Analytics / Data
    'analytics' => [
        'keywords' => ['predictive analytic', 'data analytic', 'business intelligence', 'data-driven', 'analytics tool', 'ai forecast', 'demand forecast'],
        'sources'  => [
            ['anchor' => 'Gartner Predictive Analytics', 'url' => 'https://www.gartner.com/en/topics/predictive-analytics'],
            ['anchor' => 'Forrester Analytics Report', 'url' => 'https://www.forrester.com/bold'],
            ['anchor' => 'IBM Analytics Insights', 'url' => 'https://www.ibm.com/think/topics/predictive-analytics'],
        ],
    ],
    // Digital Nomad / Remote Work
    'remote_work' => [
        'keywords' => ['digital nomad', 'remote work', 'work from home', 'remote team', 'distributed team', 'nomad'],
        'sources'  => [
            ['anchor' => 'Buffer State of Remote Work', 'url' => 'https://buffer.com/state-of-remote-work'],
            ['anchor' => 'Owl Labs Remote Work Statistics', 'url' => 'https://owllabs.com/state-of-remote-work'],
            ['anchor' => 'Forbes Remote Work Statistics 2024', 'url' => 'https://www.forbes.com/advisor/business/remote-work-statistics/'],
        ],
    ],
    // Gig Economy
    'gig_economy' => [
        'keywords' => ['gig economy', 'gig worker', 'platform economy', 'on-demand', 'sharing economy'],
        'sources'  => [
            ['anchor' => 'Statista Gig Economy Statistics', 'url' => 'https://www.statista.com/topics/4891/gig-economy/'],
            ['anchor' => 'Pew Research Gig Economy', 'url' => 'https://www.pewresearch.org/internet/2016/11/17/gig-work-online-selling-and-home-sharing/'],
            ['anchor' => 'McKinsey Independent Work Report', 'url' => 'https://www.mckinsey.com/featured-insights/employment-and-growth/independent-work-choice-necessity-and-the-gig-economy'],
        ],
    ],
    // SaaS / Tech
    'saas_tech' => [
        'keywords' => ['saas', 'software as a service', 'no-code', 'low-code', 'tech tool', 'cloud software', 'subscription software', 'ai saas'],
        'sources'  => [
            ['anchor' => 'Gartner SaaS Market Forecast', 'url' => 'https://www.gartner.com/en/information-technology/insights/cloud-strategy'],
            ['anchor' => 'Statista SaaS Statistics', 'url' => 'https://www.statista.com/topics/1999/software-as-a-service-saas/'],
            ['anchor' => 'G2 Software Market Data', 'url' => 'https://learn.g2.com/trends'],
        ],
    ],
];

// Get all published posts
$posts = get_posts([
    'post_type'      => 'post',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
]);

$stats = ['updated' => 0, 'already_has' => 0, 'no_match' => 0, 'error' => 0];

echo '<table>';
echo '<tr><th>Article</th><th>Liens ajoutés</th><th>Résultat</th></tr>';

foreach ($posts as $post) {
    $content     = $post->post_content;
    $plain       = strtolower(wp_strip_all_tags($content) . ' ' . strtolower($post->post_title));
    $slug        = $post->post_name;
    $title_short = mb_substr($post->post_title, 0, 55);

    // Check if article already has external links
    preg_match_all('/href=["\']https?:\/\/(?!qivato\.com)[^"\']+["\']/', $content, $existing_ext);
    $existing_count = count($existing_ext[0]);

    if ($existing_count >= 2) {
        echo '<tr><td>' . esc_html($title_short) . '</td><td class="skip">Déjà ' . $existing_count . ' lien(s)</td><td class="skip">Ignoré</td></tr>';
        $stats['already_has']++;
        continue;
    }

    // Score each topic
    $topic_scores = [];
    foreach ($link_library as $topic => $data) {
        $score = 0;
        foreach ($data['keywords'] as $kw) {
            $score += substr_count($plain, $kw);
        }
        if ($score > 0) $topic_scores[$topic] = $score;
    }

    if (empty($topic_scores)) {
        // Fallback to ai_general + freelance
        $topic_scores = ['ai_general' => 1, 'freelance' => 1];
    }

    arsort($topic_scores);
    $top_topics = array_slice(array_keys($topic_scores), 0, 2); // top 2 topics

    // Collect links to add (2-3 links, avoiding duplicates)
    $links_to_add = [];
    $already_linked_urls = [];

    // Extract already-linked URLs
    preg_match_all('/href=["\']([^"\']+)["\']/', $content, $all_links);
    $already_linked_urls = $all_links[1];

    foreach ($top_topics as $topic) {
        foreach ($link_library[$topic]['sources'] as $src) {
            if (count($links_to_add) >= 3) break 2;
            if (!in_array($src['url'], $already_linked_urls)) {
                $links_to_add[] = $src;
                $already_linked_urls[] = $src['url'];
            }
        }
    }

    if (empty($links_to_add)) {
        echo '<tr><td>' . esc_html($title_short) . '</td><td class="err">Aucune source trouvée</td><td>—</td></tr>';
        $stats['no_match']++;
        continue;
    }

    // Build the "Sources & References" block to append
    $sources_html = "\n<h3>Sources &amp; References</h3>\n<ul>\n";
    $added_anchors = [];
    foreach ($links_to_add as $lnk) {
        $sources_html   .= '<li><a href="' . esc_url($lnk['url']) . '" target="_blank" rel="noopener noreferrer">' . esc_html($lnk['anchor']) . '</a></li>' . "\n";
        $added_anchors[] = $lnk['anchor'];
    }
    $sources_html .= "</ul>\n";

    // Inject before the last </p> or at the very end of content
    $last_p = strrpos($content, '</p>');
    if ($last_p !== false) {
        $new_content = substr($content, 0, $last_p + 4) . $sources_html . substr($content, $last_p + 4);
    } else {
        $new_content = $content . $sources_html;
    }

    if (!$dry_run) {
        $result = wp_update_post(['ID' => $post->ID, 'post_content' => $new_content]);
        if (!is_wp_error($result)) {
            echo '<tr><td>' . esc_html($title_short) . '</td><td class="ok">' . esc_html(implode(', ', $added_anchors)) . '</td><td class="ok">✅ Mis à jour</td></tr>';
            $stats['updated']++;
        } else {
            echo '<tr><td>' . esc_html($title_short) . '</td><td class="err">Erreur update</td><td class="err">❌ ' . esc_html($result->get_error_message()) . '</td></tr>';
            $stats['error']++;
        }
    } else {
        echo '<tr><td>' . esc_html($title_short) . '</td><td class="warn">🔍 ' . esc_html(implode(' | ', $added_anchors)) . '</td><td class="warn">Simulé</td></tr>';
        $stats['updated']++;
    }
}

echo '</table>';

echo '<h2>Résumé</h2>';
echo '<p ' . ($dry_run ? 'class="warn"' : 'class="ok"') . '>' . ($dry_run ? '🔍 Simulation' : '✅ Appliqué') . ' — Articles traités : <strong>' . $stats['updated'] . '</strong></p>';
echo '<p class="skip">Déjà avec liens externes : <strong>' . $stats['already_has'] . '</strong></p>';
echo '<p class="err">Aucune correspondance : <strong>' . $stats['no_match'] . '</strong></p>';

if ($dry_run) {
    echo '<p><a href="?token=qivato2026extlinks&apply=1" style="background:green;color:white;padding:10px 20px;text-decoration:none;font-weight:bold;display:inline-block;margin-top:10px">→ Appliquer maintenant</a></p>';
}

echo '<p style="color:red;margin-top:30px"><strong>SUPPRIMER CE FICHIER via cPanel après lecture.</strong></p>';
