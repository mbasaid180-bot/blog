<?php
/**
 * One-time script: Inject FAQ sections into articles missing them
 * Token: qivato2026faqinject
 * DELETE this file after execution.
 */

if (!isset($_GET['token']) || $_GET['token'] !== 'qivato2026faqinject') {
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

echo '<h2>Injection FAQ — Qivato</h2>';

if ($dry_run) {
    echo '<div class="dry">⚠️ MODE SIMULATION. <a href="?token=qivato2026faqinject&apply=1"><strong>→ Appliquer les changements</strong></a></div>';
} else {
    echo '<div style="background:#e0f4e0;padding:10px;border:1px solid green;margin-bottom:15px">✅ MODE APPLICATION</div>';
}

/**
 * FAQ library — keyed by topic
 * Each topic has 4-5 Q&A pairs
 */
$faq_library = [
    'ai_tools_freelancer' => [
        'keywords' => ['ai tool', 'freelancer tool', 'ai for freelancer', 'freelance tool', 'ai software freelanc'],
        'faqs' => [
            ['q' => 'What are the best AI tools for freelancers in 2026?', 'a' => 'The best AI tools for freelancers in 2026 include Claude and ChatGPT for writing and research, Canva and Midjourney for design, Grammarly for editing, Surfer SEO for content optimization, and HoneyBook for client management. The right choice depends on your niche and workflow.'],
            ['q' => 'Are AI tools worth the investment for freelancers?', 'a' => 'Yes — most freelancers report saving 5–15 hours per week after adopting AI tools. At an average freelance rate of $50–100/hour, even a $50/month AI subscription pays for itself quickly. Start with free tiers to validate the value before committing to paid plans.'],
            ['q' => 'Can AI tools replace freelancers?', 'a' => 'AI tools augment freelancers rather than replace them. They handle repetitive tasks (drafting, formatting, scheduling), freeing freelancers to focus on strategy, relationships, and creative work that requires human judgment. Freelancers who use AI effectively can take on more clients and charge higher rates.'],
            ['q' => 'How do I choose the right AI tools for my freelance business?', 'a' => 'Identify your biggest time drains first, then look for AI tools that solve those specific problems. Key criteria: ease of use, integration with your existing stack, pricing model, and quality of output. Most tools offer free trials — test before you commit.'],
        ],
    ],
    'automation' => [
        'keywords' => ['automat', 'workflow automat', 'business automat', 'task automat', 'process automat', 'zapier', 'make.com'],
        'faqs' => [
            ['q' => 'What is business automation for freelancers?', 'a' => 'Business automation for freelancers means using software to handle repetitive tasks automatically — sending invoices, following up with clients, scheduling social posts, onboarding new clients, and managing contracts. Tools like Zapier, Make.com, and HoneyBook connect your apps and trigger actions without manual input.'],
            ['q' => 'How much time can automation save a freelancer?', 'a' => 'Studies show freelancers can save 10–20 hours per week by automating routine tasks. Common time-savers include automated invoicing (saves ~2h/week), client onboarding sequences (saves ~3h/client), and social media scheduling (saves ~4h/week).'],
            ['q' => 'Is automation difficult to set up for non-technical freelancers?', 'a' => 'No. Modern no-code automation platforms like Zapier and Make.com use drag-and-drop interfaces with thousands of pre-built templates. Most freelancers can set up their first automation in under 30 minutes with no coding required.'],
            ['q' => 'What should I automate first in my freelance business?', 'a' => 'Start with your highest-volume repetitive tasks: invoice generation and follow-ups, client onboarding emails, appointment scheduling, and social media posting. These four areas typically deliver the fastest ROI and biggest time savings.'],
        ],
    ],
    'crm_lead_gen' => [
        'keywords' => ['crm', 'lead generation', 'lead gen', 'customer acquisition', 'client onboarding', 'client management', 'hubspot', 'salesforce'],
        'faqs' => [
            ['q' => 'What is the best CRM for freelancers and small businesses?', 'a' => 'The best CRMs for freelancers include HoneyBook (all-in-one for creative freelancers), HubSpot Free (powerful for lead management), and Notion (flexible for project-based work). For small businesses needing more power, Salesforce and Zoho CRM offer robust features with AI capabilities.'],
            ['q' => 'How can AI improve lead generation for freelancers?', 'a' => 'AI improves lead generation by automating prospect research, personalizing outreach at scale, scoring leads based on conversion likelihood, and identifying the best time to follow up. Tools like Clay, Apollo.io, and HubSpot AI can reduce the time spent on manual prospecting by 60–70%.'],
            ['q' => 'What is client onboarding automation?', 'a' => 'Client onboarding automation sends the right information to new clients at the right time — welcome emails, contract signing links, questionnaires, project kickoff documents, and payment instructions — all triggered automatically when a client signs a contract or pays an invoice.'],
            ['q' => 'How do I retain clients using AI tools?', 'a' => 'AI tools help with client retention by automating check-in emails, tracking project milestones, generating personalized reports, and identifying at-risk clients before they churn. Consistent, proactive communication is the #1 factor in client retention.'],
        ],
    ],
    'ecommerce_logistics' => [
        'keywords' => ['ecommerce', 'e-commerce', 'logistics', 'fulfillment', 'warehouse', 'inventory', 'supply chain', 'delivery', 'shipping', 'last-mile'],
        'faqs' => [
            ['q' => 'How is AI transforming e-commerce logistics?', 'a' => 'AI is transforming e-commerce logistics through demand forecasting (reducing overstock by 20–30%), route optimization for last-mile delivery (cutting costs by 10–15%), automated warehouse picking (improving throughput by 25%), and real-time inventory management across multiple warehouses.'],
            ['q' => 'What is AI demand forecasting in e-commerce?', 'a' => 'AI demand forecasting uses machine learning to predict future product demand based on historical sales, seasonality, market trends, and external signals (weather, events, social media). Accurate forecasts reduce stockouts and prevent costly overstock situations.'],
            ['q' => 'How can AI reduce return rates in e-commerce?', 'a' => 'AI reduces return rates by improving product recommendations (better fit = fewer returns), generating more accurate product descriptions, detecting fraudulent return patterns, and providing predictive sizing tools. Retailers using AI-powered recommendations report 15–30% lower return rates.'],
            ['q' => 'What is last-mile delivery optimization with AI?', 'a' => 'Last-mile delivery optimization uses AI to calculate the most efficient delivery routes in real time, accounting for traffic, delivery windows, vehicle capacity, and driver availability. This can reduce delivery costs by 10–20% while improving on-time delivery rates.'],
        ],
    ],
    'content_writing' => [
        'keywords' => ['content creat', 'writing tool', 'copywriting', 'content strateg', 'seo content', 'blog content', 'content market', 'content writer'],
        'faqs' => [
            ['q' => 'What are the best AI writing tools for freelancers?', 'a' => 'The top AI writing tools for freelancers are Claude (best for long-form, nuanced writing), ChatGPT (versatile for any content type), Jasper (marketing copy), and Grammarly (editing and tone). For SEO-optimized content specifically, Surfer SEO + Claude is the most powerful combination.'],
            ['q' => 'Can AI writing tools produce publication-ready content?', 'a' => 'AI writing tools generate strong first drafts, but publication-ready content requires human editing for accuracy, brand voice, and original insights. The best workflow: use AI to draft and structure, then add your expertise, real examples, and personal perspective. This hybrid approach is both faster and higher-quality than writing from scratch.'],
            ['q' => 'How do I create a content strategy with AI tools?', 'a' => 'Start by using AI tools to analyze your top competitors, identify keyword gaps, and cluster topics into content pillars. Then create a publishing calendar based on search volume and business priority. Tools like Semrush, Surfer SEO, and Claude can automate most of the research phase.'],
            ['q' => 'Are AI-generated articles penalized by Google?', 'a' => 'Google does not penalize AI-generated content per se — it penalizes low-quality content regardless of how it was created. AI content that demonstrates genuine expertise, provides original value, and is edited by a human expert can rank as well as manually written content. Focus on quality and E-E-A-T signals.'],
        ],
    ],
    'productivity' => [
        'keywords' => ['productivity', 'time management', 'time tracking', 'project management', 'scheduling', 'efficiency', 'focus', 'task management'],
        'faqs' => [
            ['q' => 'What are the best productivity tools for freelancers in 2026?', 'a' => 'The top productivity tools for freelancers in 2026 include Notion AI (all-in-one workspace), Toggl or Harvest (time tracking), Calendly (scheduling), Asana or Trello (project management), and RescueTime (productivity analytics). The best stack depends on your work style and client needs.'],
            ['q' => 'How can AI tools improve freelancer productivity?', 'a' => 'AI tools boost productivity by automating routine tasks, generating first drafts, summarizing long documents, transcribing meetings, and providing smart scheduling suggestions. Freelancers using AI tools report being 30–40% more productive on average.'],
            ['q' => 'What is the best time tracking method for freelancers?', 'a' => 'The most effective time tracking for freelancers combines a dedicated app (Toggl, Harvest, or Clockify) with a clear project-client structure. Track time in real time rather than reconstructing it at the end of the day. Regular time audits reveal where your hours actually go — often surprising.'],
            ['q' => 'How do I avoid burnout as a freelancer using productivity tools?', 'a' => 'Use productivity tools to set clear work boundaries: block focus time in your calendar, automate low-value tasks, and use time tracking data to spot when you\'re overworked. AI scheduling tools can also help distribute workload more evenly across the week.'],
        ],
    ],
    'design_creative' => [
        'keywords' => ['design tool', 'canva', 'midjourney', 'image generat', 'graphic design', 'visual content', 'creative tool', 'ai design', 'video edit', 'video tool'],
        'faqs' => [
            ['q' => 'What are the best AI design tools for freelancers?', 'a' => 'The best AI design tools for freelancers are Canva (all-purpose design with AI features), Midjourney (AI image generation), Adobe Firefly (integrated with Creative Cloud), and Figma (UI/UX design). For video, CapCut and Adobe Premiere Pro with AI features lead the market.'],
            ['q' => 'Can non-designers use AI design tools effectively?', 'a' => 'Yes. Modern AI design tools like Canva\'s Magic Design, Adobe Express, and Looka generate professional-quality designs from simple text prompts. Non-designers can create social media graphics, presentations, logos, and marketing materials in minutes without prior design training.'],
            ['q' => 'How much do AI design tools cost for freelancers?', 'a' => 'AI design tools range from free (Canva free tier, Adobe Express free) to $20–60/month for pro plans. Midjourney costs $10–60/month depending on usage. Most freelancers find that a $20–30/month budget covers their core design needs across 2–3 tools.'],
            ['q' => 'How is AI changing video editing for freelancers?', 'a' => 'AI is automating the most time-consuming video editing tasks: auto-cutting on speaking pauses, background removal, color correction, audio cleanup, and subtitle generation. Tools like CapCut, Descript, and Adobe Premiere Pro\'s AI features can cut editing time by 50–70%.'],
        ],
    ],
    'branding' => [
        'keywords' => ['personal brand', 'brand identity', 'brand strateg', 'brand voice', 'online presence', 'linkedin', 'social media'],
        'faqs' => [
            ['q' => 'Why is personal branding important for freelancers?', 'a' => 'Strong personal branding allows freelancers to attract clients instead of constantly pitching. Freelancers with a clear brand identity and online presence report earning 20–35% more than peers with similar skills but no brand. Your brand is your reputation made visible.'],
            ['q' => 'How do I build a personal brand as a freelancer?', 'a' => 'Start by defining your niche, unique value proposition, and target audience. Then create consistent content on 1–2 platforms where your clients spend time (LinkedIn, Twitter, YouTube). Document your process, share case studies, and engage genuinely. Consistency over 6–12 months builds authority.'],
            ['q' => 'How can AI help build my personal brand?', 'a' => 'AI tools help with personal branding by generating content ideas, drafting social posts, repurposing long content into multiple formats, analyzing what resonates with your audience, and creating consistent visual assets. This lets you maintain a high-volume content output without burning out.'],
            ['q' => 'What is a brand identity and why does it matter?', 'a' => 'Brand identity is the visual and verbal language that represents who you are: your logo, colors, fonts, tone of voice, and core message. A consistent brand identity makes you instantly recognizable, builds trust with potential clients, and communicates your professionalism before you even speak.'],
        ],
    ],
    'ai_general' => [
        'keywords' => ['artificial intelligence', 'ai trend', 'future of ai', 'ai technology', 'ai adoption', 'ai strategy', 'ai impact'],
        'faqs' => [
            ['q' => 'How is AI changing the way freelancers work?', 'a' => 'AI is fundamentally reshaping freelance work by automating routine tasks, enhancing creative capabilities, improving client communication, and enabling solo operators to compete with larger agencies. Freelancers who embrace AI can deliver higher-quality work faster, take on more clients, and focus on higher-value strategic work.'],
            ['q' => 'What AI trends should freelancers watch in 2026?', 'a' => 'Key AI trends for freelancers in 2026 include: agentic AI (AI that takes multi-step actions autonomously), multimodal AI (text, image, audio, video in one tool), AI-powered search (changing how clients find freelancers), and specialized vertical AI tools designed for specific professions.'],
            ['q' => 'Is AI making it harder or easier to be a freelancer?', 'a' => 'Both. AI lowers barriers to entry, increasing competition for basic tasks. But it also creates new opportunities: freelancers who master AI tools can deliver at a higher level than before, and entirely new niches are emerging (AI prompt engineering, AI content strategy, AI tool consulting).'],
            ['q' => 'How do I stay competitive as a freelancer in the AI era?', 'a' => 'Focus on skills AI can\'t replicate: strategic thinking, deep client relationships, creative direction, and domain expertise. Use AI to handle execution while you handle judgment. Continuously experiment with new AI tools and position yourself as an AI-native professional in your field.'],
        ],
    ],
    'saas_nocode' => [
        'keywords' => ['saas', 'no-code', 'low-code', 'software as a service', 'cloud tool', 'subscription software', 'tech stack'],
        'faqs' => [
            ['q' => 'What SaaS tools do freelancers need in 2026?', 'a' => 'A solid freelancer SaaS stack includes: a project management tool (Notion, Asana), a CRM (HoneyBook, HubSpot Free), accounting software (FreshBooks, Wave), a communication tool (Slack, Loom), and 1–2 AI tools relevant to your niche. Keep your stack lean and integrated.'],
            ['q' => 'What is no-code and how can freelancers use it?', 'a' => 'No-code platforms allow you to build apps, automations, and workflows without writing code. Freelancers use no-code tools to create client portals, automate business processes, build simple web apps for clients, and integrate their SaaS tools — all without a developer.'],
            ['q' => 'How much should a freelancer spend on software subscriptions?', 'a' => 'Most freelancers spend $100–300/month on their software stack. A good rule: if a tool saves you more time in a month than it costs in money (at your hourly rate), it pays for itself. Audit your subscriptions every quarter and eliminate tools you use less than weekly.'],
            ['q' => 'What is AI SaaS and how is it different from traditional software?', 'a' => 'AI SaaS refers to cloud software that uses machine learning to adapt, learn, and improve over time. Unlike traditional software with fixed rules, AI SaaS tools improve with use, personalize to your behavior, and can make predictions and recommendations based on your data.'],
        ],
    ],
];

// Get all published posts missing FAQs
$posts = get_posts([
    'post_type'      => 'post',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
]);

$stats = ['updated' => 0, 'already_has' => 0, 'no_match' => 0, 'error' => 0];

echo '<table>';
echo '<tr><th>Article</th><th>FAQ injectée</th><th>Résultat</th></tr>';

foreach ($posts as $post) {
    $content     = $post->post_content;
    $plain_lower = strtolower(wp_strip_all_tags($content) . ' ' . strtolower($post->post_title));
    $title_short = mb_substr($post->post_title, 0, 55);

    // Check if article already has FAQ
    $has_faq = stripos($content, 'FAQ') !== false
            || stripos($content, 'Frequently Asked') !== false
            || stripos($content, 'Common Questions') !== false;

    if ($has_faq) {
        echo '<tr><td>' . esc_html($title_short) . '</td><td class="skip">Déjà une FAQ</td><td class="skip">Ignoré</td></tr>';
        $stats['already_has']++;
        continue;
    }

    // Score each FAQ topic
    $topic_scores = [];
    foreach ($faq_library as $topic => $data) {
        $score = 0;
        foreach ($data['keywords'] as $kw) {
            $score += substr_count($plain_lower, $kw);
        }
        if ($score > 0) $topic_scores[$topic] = $score;
    }

    if (empty($topic_scores)) {
        $topic_scores = ['ai_general' => 1]; // fallback
    }

    arsort($topic_scores);
    $best_topic = array_key_first($topic_scores);
    $faq_set    = $faq_library[$best_topic]['faqs'];

    // Build FAQ HTML (4 Q&As max)
    $faq_html = "\n<h2>Frequently Asked Questions</h2>\n";
    foreach (array_slice($faq_set, 0, 4) as $qa) {
        $faq_html .= '<h3>' . esc_html($qa['q']) . '</h3>' . "\n";
        $faq_html .= '<p>' . esc_html($qa['a']) . '</p>' . "\n";
    }

    // Inject before the last </p> (so it's before the closing content)
    $last_p = strrpos($content, '</p>');
    if ($last_p !== false) {
        $new_content = substr($content, 0, $last_p + 4) . "\n" . $faq_html . substr($content, $last_p + 4);
    } else {
        $new_content = $content . "\n" . $faq_html;
    }

    if (!$dry_run) {
        $result = wp_update_post(['ID' => $post->ID, 'post_content' => $new_content]);
        if (!is_wp_error($result)) {
            echo '<tr><td>' . esc_html($title_short) . '</td><td class="ok">Topic: ' . esc_html($best_topic) . ' (' . count($faq_set) . ' Q&As)</td><td class="ok">✅ Mis à jour</td></tr>';
            $stats['updated']++;
        } else {
            echo '<tr><td>' . esc_html($title_short) . '</td><td class="err">Erreur</td><td class="err">❌ ' . esc_html($result->get_error_message()) . '</td></tr>';
            $stats['error']++;
        }
    } else {
        echo '<tr><td>' . esc_html($title_short) . '</td><td class="warn">🔍 Topic: ' . esc_html($best_topic) . '</td><td class="warn">Simulé</td></tr>';
        $stats['updated']++;
    }
}

echo '</table>';

echo '<h2>Résumé</h2>';
echo '<p ' . ($dry_run ? 'class="warn"' : 'class="ok"') . '>' . ($dry_run ? '🔍 Simulation' : '✅ Appliqué') . ' — Articles traités : <strong>' . $stats['updated'] . '</strong></p>';
echo '<p class="skip">Déjà avec FAQ : <strong>' . $stats['already_has'] . '</strong></p>';
echo '<p class="err">Aucune correspondance : <strong>' . $stats['no_match'] . '</strong></p>';

if ($dry_run) {
    echo '<p><a href="?token=qivato2026faqinject&apply=1" style="background:green;color:white;padding:10px 20px;text-decoration:none;font-weight:bold;display:inline-block;margin-top:10px">→ Appliquer maintenant</a></p>';
}

echo '<p style="color:red;margin-top:30px"><strong>SUPPRIMER CE FICHIER via cPanel après lecture.</strong></p>';
