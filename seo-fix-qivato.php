<?php
/**
 * SEO Fix Script for Qivato.com — SEOPress
 * Upload to WordPress root, run once via browser, then DELETE immediately.
 * Access: https://qivato.com/seo-fix-qivato.php
 */

// Security token — change this before uploading
define('SECRET', 'qivato2026seo');
if (!isset($_GET['token']) || $_GET['token'] !== SECRET) {
    die('Access denied. Add ?token=qivato2026seo to the URL.');
}

// Load WordPress
$wp_load = dirname(__FILE__) . '/wp-load.php';
if (!file_exists($wp_load)) {
    die('wp-load.php not found. Place this file in the WordPress root directory.');
}
require_once($wp_load);

if (!function_exists('update_post_meta')) {
    die('WordPress not loaded correctly.');
}

echo '<html><head><meta charset="UTF-8"><title>SEO Fix - Qivato</title>';
echo '<style>body{font-family:sans-serif;max-width:900px;margin:40px auto;padding:20px;}
.ok{color:green;} .skip{color:gray;} .err{color:red;}
table{width:100%;border-collapse:collapse;margin-top:20px;}
th,td{padding:8px;text-align:left;border-bottom:1px solid #eee;font-size:13px;}
th{background:#f5f5f5;}</style></head><body>';

echo '<h1>SEO Fix Qivato — SEOPress Meta Update</h1>';
echo '<p>Mise à jour des SEO Titles et Meta Descriptions via SEOPress...</p>';
echo '<table><tr><th>ID</th><th>Titre</th><th>Action</th><th>Statut</th></tr>';

$updated = 0;
$skipped = 0;
$errors = 0;

// ============================================================
// DATA: [post_id, seo_title, meta_description]
// meta_description = '' means keep existing / skip
// ============================================================
$seo_data = [

    // ---- SEO TITLES + META DESCRIPTIONS (all 78 posts) ----

    [645,  'Zapier vs Make vs Jasper vs HubSpot AI: Full Comparison | Qivato',
           'Compare Zapier, Make, Jasper and HubSpot AI on features, pricing and automation power. Find the best AI tool for your business in 2025.'],

    [1480, 'AI Translation Tools 2026: Complete Guide & Best Practices | Qivato',
           'Explore the best AI translation tools of 2026. Compare accuracy, pricing and use cases for freelancers and global businesses.'],

    [1698, 'AI Tools for Freelancers: Build a Sustainable Business | Qivato',
           'Discover essential AI tools that help freelancers automate workflows, scale their income and build a sustainable independent business.'],

    [1739, 'Best AI Tools for Freelancers to Scale in 2026 | Qivato',
           'The best AI tools for freelancers in 2026: automate writing, design, invoicing and SEO. Join the 73% of freelancers already using AI.'],

    [673,  'Best AI SaaS Tools to Elevate Your Business in 2025 | Qivato',
           'Discover the essential AI SaaS tools transforming businesses in 2025. Automate tasks, boost productivity and grow faster with smart software.'],

    [723,  'AI Automation Guide for US Freelancers 2025 | Qivato',
           'The ultimate AI automation guide for US freelancers: top tools, proven workflows and strategies to save hours and boost your income in 2025.'],

    [734,  'Why Freelancers Need AI Tools in 2025 | Qivato',
           'AI tools are no longer optional for freelancers. Discover why independent professionals are adopting AI to stay competitive and work smarter.'],

    [791,  'AI & SaaS Trends Shaping Freelancing in 2025 | Qivato',
           'The top AI and SaaS trends every freelancer must watch in 2025. Stay ahead of the curve and future-proof your independent business.'],

    [797,  'Best AI Image Generators for Freelancers 2025 | Qivato',
           'Top AI image generators for freelancers: create stunning visuals in minutes. Compare the best tools for design, content and client work.'],

    [817,  'How AI & SaaS Are Transforming the Freelance Ecosystem | Qivato',
           'AI and SaaS technology are reshaping how freelancers work, win clients and scale. Learn the trends redefining the solo professional landscape.'],

    [879,  'AI Automation: Why Every Modern Freelancer Needs It | Qivato',
           'Intelligent automation is now essential for freelancers. Explore the tools and workflows that help solo professionals work smarter in 2025.'],

    [976,  'AI Assistants for Project Management & Productivity | Qivato',
           'Boost your productivity with AI assistants for project management. Discover the top tools helping freelancers manage deadlines and clients.'],

    [1065, 'Global Connectivity: Expanding Professional Opportunities | Qivato',
           'Global connectivity is opening new doors for professionals worldwide. Learn how to leverage remote work and digital tools to grow your career.'],

    [1095, 'AI Tools for Translation and Multilingual Clients | Qivato',
           'The best AI translation tools for freelancers working with multilingual clients. Compare accuracy, speed and pricing for real project use cases.'],

    [1248, 'AI-Powered Project Management Tools: 2025 Evolution | Qivato',
           'Explore how AI-powered project management tools are evolving. Find the best platforms to coordinate clients, deadlines and deliverables in 2025.'],

    [1407, 'How to Integrate AI Tools into Your Freelance Workflow | Qivato',
           'Step-by-step guide to integrating AI tools into your freelance workflow. Save time, reduce errors and deliver better results to your clients.'],

    [1435, 'Digital Nomad Productivity Solutions for the Modern Workforce | Qivato',
           'The best productivity solutions for digital nomads and remote workers. Tools and strategies to stay focused and efficient while working anywhere.'],

    [1474, 'Gig Economy Software: Innovations in Payment Processing | Qivato',
           'Discover how software innovations are transforming payment processing in the gig economy. The best platforms for US freelancers in 2025.'],

    [1516, 'AI Tools Freelancers Actually Use in 2026: Selection Guide | Qivato',
           'Cut through the noise: the AI tools US freelancers actually rely on in 2026. A practical guide to choosing tools that deliver real results.'],

    [1531, 'AI Tools Pricing 2026: A Practical Breakdown for US Freelancers | Qivato',
           'How much do AI tools really cost in 2026? A detailed pricing breakdown for US freelancers covering free plans, paid tiers and hidden fees.'],

    [1542, 'Best Free AI Tools for Freelancers in 2025 | Qivato',
           'Discover the best free AI tools for freelancers. Boost productivity, streamline your workflow and deliver great work — without spending a cent.'],

    [1796, 'Content Creation Tools for Freelance Writers: Write Faster | Qivato',
           'The best content creation tools for freelance writers: AI editors, research assistants and writing aids that help you write faster and better.'],

    [1805, 'Workflow Automation Tools for Freelancers: Save Hours Daily | Qivato',
           'Top workflow automation tools for freelancers: connect your apps, eliminate repetitive tasks and save 10+ hours every week automatically.'],

    [1807, 'Best Productivity Tools for Freelancers in 2026 | Qivato',
           'Master freelance productivity with the best tools for time tracking, project management, invoicing and focus. Your complete toolkit for 2026.'],

    [685,  'AI Tools for Freelancers: 7 Must-Have Tools to Grow Your Business | Qivato',
           'Discover 7 essential AI tools every US freelancer needs. Automate operations, boost productivity and grow your business faster in 2025.'],

    [739,  'AI Platforms for Client Management & Freelance Productivity 2025 | Qivato',
           'Compare the best AI platforms for client management and freelance productivity in 2025. Find the right stack for your solo business needs.'],

    [909,  'Best AI CRM Tools for Small Businesses in 2025 | Qivato',
           'The top AI CRM tools for small businesses in 2025. Manage contacts, automate follow-ups and close more deals without the complexity.'],

    [911,  'AI Automation Tools for Freelancers: Save Time & Boost Income | Qivato',
           'The best AI automation tools for freelancers to eliminate admin tasks, save hours weekly and significantly increase your monthly income.'],

    [913,  'HubSpot vs Salesforce AI Features: Which CRM Wins in 2025? | Qivato',
           'HubSpot vs Salesforce: a detailed comparison of AI features in 2025. Find out which CRM delivers better automation, insights and ROI.'],

    [915,  'How to Automate Client Onboarding with AI Platforms | Qivato',
           'Automate your client onboarding with AI platforms. Reduce manual work, impress new clients and set up professional processes from day one.'],

    [917,  'Top AI Scheduling and Calendar Tools for Professionals | Qivato',
           'The best AI scheduling and calendar tools for busy professionals. Eliminate back-and-forth emails and automate your meeting management.'],

    [919,  'AI Platforms Pricing: Free vs Paid Plans Comparison 2025 | Qivato',
           'A full pricing comparison of top AI platforms in 2025. Compare free vs paid plans to find the best value for your freelance or small business.'],

    [989,  'Growth Hacking with AI: Strategies to Acquire More Customers | Qivato',
           'Concrete AI-powered growth hacking strategies to acquire more customers faster. Practical tactics for freelancers and small business owners.'],

    [1032, 'AI-Powered Lead Generation Tactics That Actually Work in 2025 | Qivato',
           'Discover AI lead generation tactics that consistently deliver qualified prospects. Proven strategies for freelancers and growing businesses.'],

    [1051, 'AI Chatbots to Convert Website Visitors into Customers | Qivato',
           'Use AI chatbots to turn passive website visitors into paying customers. The best chatbot tools and strategies for conversion optimization.'],

    [1105, 'Personalization at Scale: AI Content Strategies for Acquisition | Qivato',
           'How to use AI for personalized content strategies that drive customer acquisition at scale. Practical frameworks for freelancers and marketers.'],

    [1109, 'Predictive Analytics: How AI Identifies Your Best Prospects | Qivato',
           'Use predictive analytics to identify your highest-value customer prospects. How AI helps sales and marketing teams focus on the right leads.'],

    [1112, 'AI Email Marketing Automation That Doubles Open Rates | Qivato',
           'Discover AI email marketing automation strategies that significantly boost open rates and conversions. The best tools and sequences for 2025.'],

    [1295, 'Retargeting with AI: Win Back Lost Customers Automatically | Qivato',
           'Use AI-powered retargeting to automatically win back lost customers and abandoned carts. Proven strategies that recover revenue on autopilot.'],

    [1636, 'AI Tools for Beginners in 2026: Easiest Tools to Start With | Qivato',
           'New to AI? Discover the easiest AI tools for beginner freelancers in 2026. Start automating your work without any technical background.'],

    [1778, 'Top AI & Tech Trends Redefining Freelancing in 2026 | Qivato',
           'The top AI and tech trends that will redefine freelancing and online business in 2026. Stay ahead and adapt your strategy now.'],

    [1818, 'How Autonomous AI Agents Are Transforming Freelancers\' Work | Qivato',
           'Autonomous AI agents are changing how freelancers work daily. Discover the tools and use cases reshaping independent business in 2026.'],

    [1942, 'AI + No-Code: The Biggest Innovation Trend for Solopreneurs | Qivato',
           'AI and no-code are the most powerful combination for solopreneurs in 2026. Build products, automate workflows and scale without coding.'],

    [2028, 'AI Tools & SaaS for Freelancers: The 2026 Complete Stack Guide | Qivato',
           'Build the perfect AI and SaaS stack for your freelance business in 2026. The complete guide to choosing, combining and maximizing your tools.'],

    [711,  'AI-Powered E-Commerce Engine: Transform Logistics & Operations | Qivato',
           'Build a smarter e-commerce engine with AI. Transform your inventory, fulfillment, shipping and customer experience with intelligent automation.'],

    [758,  'AI Demand Forecasting: Eliminate Stockouts and Overstock | Qivato',
           'AI demand forecasting eliminates costly stockouts and overstock by predicting inventory needs in real time. The best tools for e-commerce.'],

    [803,  'AI for Warehouse Automation and Smarter Fulfillment | Qivato',
           'Discover how AI-driven warehouse automation enhances efficiency, accuracy and fulfillment speed. The future of smart warehouse management.'],

    [851,  'AI Project Management Tools: Transform Your Client Workflow | Qivato',
           'How AI project management tools help US freelancers manage multiple clients efficiently. Cut coordination time and deliver projects on time.'],

    [861,  'Best AI Writing Assistants for Faster Content Creation 2026 | Qivato',
           'Compare the top AI writing assistants that help freelancers produce proposals, blog posts and marketing copy faster and at higher quality.'],

    [925,  'Content Strategy for Personal Branding: The Complete Guide | Qivato',
           'Create an optimal content strategy for personal branding: audience research, content pillars, consistent publishing and measurable growth.'],

    [927,  'AI Design Tools Every US Freelancer Should Master in 2026 | Qivato',
           'The best AI design tools for US freelancers: create professional visuals without design training. Master Canva AI, Adobe Firefly and more.'],

    [937,  'AI Video Editing Tools: Streamline Your Production Workflow | Qivato',
           'Discover how AI video editing tools automate transcription, remove filler words and streamline post-production for freelance video creators.'],

    [1007, 'How to Define Your Brand Identity: The Essential Guide | Qivato',
           'Master brand identity creation from scratch: define your purpose, values, visual identity and brand voice. Build a brand that attracts clients.'],

    [1054, 'Social Media for Personal Branding: The Power of Visibility | Qivato',
           'Leverage social media for personal branding: choose the right platforms, create compelling content and build an audience that trusts you.'],

    [1059, 'AI Contract Generators for US Freelancers: Protect & Win Clients | Qivato',
           'The best AI contract and proposal generators for US freelancers. Speed up client onboarding and protect your business with professional agreements.'],

    [1079, 'AI Personal Branding Tools: Boost Your Online Presence | Qivato',
           'Use AI personal branding tools to optimize your LinkedIn, generate social content and build a consistent online presence that attracts clients.'],

    [1083, 'How to Measure Your Personal Brand Impact: A Data Guide | Qivato',
           'Learn how to evaluate your personal branding impact with data-driven metrics: visibility, engagement rates, audience growth and conversion.'],

    [1149, 'Smart Time Tracking Tools for High-Performing Freelancers | Qivato',
           'Discover the best time tracking tools for freelancers: boost profitability, eliminate wasted hours and get paid accurately for every minute.'],

    [1159, 'How to Create a Content Strategy That Drives Results | Qivato',
           'Master content strategy creation with actionable steps: audience research, topic selection, content calendar and distribution for freelancers.'],

    [1199, 'AI Route Optimization: Solving Last-Mile Delivery Challenges | Qivato',
           'AI route optimization transforms last-mile delivery with smart routing and real-time adjustments. Reduce costs and delight customers consistently.'],

    [1401, 'Automate Logistics Support with AI Order Tracking | Qivato',
           'Use AI to automate logistics support and order tracking. Reduce customer service volume and deliver proactive shipping updates automatically.'],

    [1490, 'AI Reverse Logistics: Reduce Return Rates & Boost Efficiency | Qivato',
           'Discover how AI streamlines reverse logistics, reduces costly return rates and boosts operational efficiency for e-commerce businesses.'],

    [1609, 'Freelance Automation Tools 2026: Complete Guide to Save 20+ Hours | Qivato',
           'The best freelance automation tools in 2026: CRM, invoicing, client management and more. Save 20+ hours weekly and focus on billable work.'],

    [1669, 'Freelance Automation Guide 2026: Get Started Today | Qivato',
           'Start automating your freelance business in 2026. This step-by-step guide covers the best tools and workflows to eliminate repetitive tasks.'],

    [618,  'AI Tools for Freelancers: Trends & Future Opportunities 2025 | Qivato',
           'Explore the latest AI tools for freelancers in 2025: key trends, must-have tools and future opportunities to grow your independent business.'],

    [622,  'AI Automation for Freelancers: Strategies & Monetization 2025 | Qivato',
           'How to use AI automation as a freelancer in 2025: proven strategies, the best tools and ways to monetize automation for recurring income.'],

    [689,  'AI Business Ideas for Freelancers: New Models for 2026 | Qivato',
           'Discover proven AI business ideas for freelancers in 2026. Transform how you earn by building value-based offers powered by artificial intelligence.'],

    [777,  'White Label AI Solutions for Freelancers: Build Recurring Revenue | Qivato',
           'Turn AI tools into white label solutions and build recurring reseller income. The best white label AI platforms for freelancers in 2025.'],

    [779,  'AI Business Model for Freelancers: Build a Scalable Ecosystem | Qivato',
           'Design an AI business model as a freelancer combining multiple revenue streams. Move beyond hourly billing and build long-term scalable income.'],

    [785,  'AI Consulting Services for Small Business Growth | Qivato',
           'How to sell AI consulting services to small businesses: premium pricing models, high-value offers and the skills you need to get started.'],

    [1137, 'AI Digital Products for Creators and Freelancers | Qivato',
           'Build and sell AI digital products as a freelancer or creator: courses, templates and scalable tools. Stop trading time for money.'],

    [1169, '10 AI Workflows That Skyrocket Freelancers\' Growth | Qivato',
           'Advanced AI automation: 10 proven workflows that dramatically accelerate freelance business growth. Implement these systems to scale faster.'],

    [1363, 'AI Tools to Boost Your Solo Freelance Business | Qivato',
           'The best AI tools to boost your solo freelance business. Manage time, find clients and deliver better work with intelligent automation.'],

    [1378, 'AI Lead Generation Tools to Grow Your Client Base | Qivato',
           'Discover the best AI lead generation tools for freelancers. Find qualified clients consistently and grow your business without cold outreach.'],

    [1413, 'AI Business Automation: Simplify Your Daily Operations | Qivato',
           'Use AI to automate your daily business operations as a freelancer. Eliminate admin tasks, streamline client work and focus on what pays.'],

    [1461, 'AI Customer Retention: Keep Clients Coming Back | Qivato',
           'How to use AI for customer retention as a freelancer. Automate follow-ups, deliver consistent value and build long-term client relationships.'],

    [1701, 'CRM Automation for Freelancers: Video Tutorial 2026 | Qivato',
           'Learn CRM automation for freelancers with this 2026 video tutorial. Streamline client management, automate follow-ups and save hours weekly.'],

    [1960, 'How to Automate Invoicing as a Freelancer in 2026 | Qivato',
           'Learn how to automate invoicing as a freelancer in 2026. Save time, reduce errors, get paid faster and eliminate manual billing for good.'],
];

// ============================================================
// APPLY UPDATES
// ============================================================
foreach ($seo_data as $row) {
    $post_id     = intval($row[0]);
    $seo_title   = sanitize_text_field($row[1]);
    $seo_desc    = isset($row[2]) ? sanitize_text_field($row[2]) : '';

    // Verify post exists
    $post = get_post($post_id);
    if (!$post) {
        echo "<tr><td>{$post_id}</td><td>—</td><td>Post introuvable</td><td class='err'>ERREUR</td></tr>";
        $errors++;
        continue;
    }

    $title_short = mb_substr($post->post_title, 0, 50);
    $actions = [];

    // Update SEO title
    $old_title = get_post_meta($post_id, '_seopress_titles_title', true);
    if (empty($old_title) || strpos($old_title, '%%') !== false) {
        update_post_meta($post_id, '_seopress_titles_title', $seo_title);
        $actions[] = 'SEO Title ✓';
    } else {
        $actions[] = 'SEO Title (déjà défini, non écrasé)';
    }

    // Update meta description if provided and empty or broken
    if (!empty($seo_desc)) {
        $old_desc = get_post_meta($post_id, '_seopress_titles_desc', true);
        if (empty($old_desc) || strpos($old_desc, '%%') !== false) {
            update_post_meta($post_id, '_seopress_titles_desc', $seo_desc);
            $actions[] = 'Meta Desc ✓';
        } else {
            $actions[] = 'Meta Desc (déjà définie, non écrasée)';
        }
    }

    // Also migrate Yoast data if SEOPress fields are empty
    $yoast_title = get_post_meta($post_id, '_yoast_wpseo_title', true);
    $yoast_desc  = get_post_meta($post_id, '_yoast_wpseo_metadesc', true);
    $seopress_title_current = get_post_meta($post_id, '_seopress_titles_title', true);
    $seopress_desc_current  = get_post_meta($post_id, '_seopress_titles_desc', true);

    if (!empty($yoast_title) && empty($seopress_title_current)) {
        update_post_meta($post_id, '_seopress_titles_title', $yoast_title);
        $actions[] = 'Migré depuis Yoast (title)';
    }
    if (!empty($yoast_desc) && empty($seopress_desc_current)) {
        update_post_meta($post_id, '_seopress_titles_desc', $yoast_desc);
        $actions[] = 'Migré depuis Yoast (desc)';
    }

    $action_str = implode(' | ', $actions);
    echo "<tr><td>{$post_id}</td><td>{$title_short}</td><td style='font-size:12px'>{$action_str}</td><td class='ok'>OK</td></tr>";
    $updated++;
}

echo '</table>';
echo "<br><hr><h2>Résumé</h2>";
echo "<p><strong class='ok'>Mis à jour : {$updated}</strong> | ";
echo "<strong class='err'>Erreurs : {$errors}</strong></p>";
echo "<p style='color:red;font-weight:bold;'>⚠️ SUPPRIMEZ CE FICHIER IMMÉDIATEMENT après vérification.</p>";
echo "<p>Commande FTP/cPanel : supprimer <code>seo-fix-qivato.php</code> du dossier racine WordPress.</p>";
echo '</body></html>';
