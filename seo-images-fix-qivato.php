<?php
/**
 * One-time script: Check headings + insert images at correct positions
 * Token: qivato2026imagesfix
 * DELETE this file after execution.
 */

if (!isset($_GET['token']) || $_GET['token'] !== 'qivato2026imagesfix') {
    die('Unauthorized');
}

require_once('wp-load.php');

$slug = 'automate-your-freelance-business-complete-guide-2026';
$post = get_page_by_path($slug, OBJECT, 'post');

if (!$post) die('Article not found.');

echo '<style>body{font-family:monospace;padding:20px} .ok{color:green} .err{color:red} pre{background:#f4f4f4;padding:10px;font-size:12px}</style>';
echo '<h2>Images Fix — Pillar Article 2</h2>';

$content = $post->post_content;

// Show all headings found
preg_match_all('/<h[1-6][^>]*>(.*?)<\/h[1-6]>/is', $content, $matches);
echo '<h3>Headings found in article:</h3><pre>';
foreach ($matches[1] as $h) {
    echo esc_html(wp_strip_all_tags($h)) . "\n";
}
echo '</pre>';

// Image IDs already uploaded
$img_workflow = 2337;
$img_tools    = 2338;

if (!isset($_GET['fix']) || $_GET['fix'] !== '1') {
    echo '<p><a href="?token=qivato2026imagesfix&fix=1"><strong>→ Clique ici pour insérer les images</strong></a></p>';
    echo '<p style="color:red"><strong>SUPPRIMER CE FICHIER via cPanel après utilisation.</strong></p>';
    exit;
}

// Build image HTML
$img_workflow_html = "\n" . wp_get_attachment_image($img_workflow, 'large', false, [
    'alt'   => 'Freelance business automation workflow diagram with Zapier integrations',
    'class' => 'wp-image-' . $img_workflow . ' size-large aligncenter',
]) . "\n";

$img_tools_html = "\n" . wp_get_attachment_image($img_tools, 'large', false, [
    'alt'   => 'Best freelance automation tools stack 2026 comparison',
    'class' => 'wp-image-' . $img_tools . ' size-large aligncenter',
]) . "\n";

// Insert workflow image after first H2 that contains "Workflow" or "Section 7"
$inserted_workflow = false;
$inserted_tools    = false;

// Find positions using regex
$content = preg_replace_callback(
    '/(<h2[^>]*>.*?(?:Workflow|Section 7).*?<\/h2>)(.*?)(<p>)/is',
    function($m) use ($img_workflow_html, &$inserted_workflow) {
        if (!$inserted_workflow) {
            $inserted_workflow = true;
            // Insert after first paragraph following the heading
            $first_para_end = strpos($m[2] . $m[3], '</p>');
            if ($first_para_end !== false) {
                $combined = $m[2] . $m[3];
                return $m[1] . substr($combined, 0, $first_para_end + 4) . $img_workflow_html . substr($combined, $first_para_end + 4);
            }
        }
        return $m[0];
    },
    $content
);

// Insert tools image before the comparison table <table>
$table_pos = strpos($content, '<table>');
if ($table_pos !== false) {
    // Find the H2 before the table
    $before_table = substr($content, 0, $table_pos);
    $last_h2_pos  = strrpos($before_table, '<h2');
    if ($last_h2_pos !== false) {
        $content = substr($content, 0, $table_pos) . $img_tools_html . substr($content, $table_pos);
        $inserted_tools = true;
    }
}

// Fallback: append at end of section 7 if regex didn't match
if (!$inserted_workflow) {
    // Insert after 7th H2
    $h2_count = 0;
    $content = preg_replace_callback('/<h2[^>]*>.*?<\/h2>/is', function($m) use (&$h2_count, $img_workflow_html, &$inserted_workflow) {
        $h2_count++;
        if ($h2_count === 7 && !$inserted_workflow) {
            $inserted_workflow = true;
            return $m[0] . $img_workflow_html;
        }
        return $m[0];
    }, $content);
}

// Update post
$result = wp_update_post(['ID' => $post->ID, 'post_content' => $content]);

echo '<h3>Results:</h3>';
echo '<p ' . ($inserted_workflow ? 'class="ok"' : 'class="err"') . '>' . ($inserted_workflow ? '✅' : '❌') . ' Workflow diagram image inserted</p>';
echo '<p ' . ($inserted_tools ? 'class="ok"' : 'class="err"') . '>' . ($inserted_tools ? '✅' : '❌') . ' Tools comparison image inserted</p>';
echo '<p ' . (!is_wp_error($result) ? 'class="ok"' : 'class="err"') . '>' . (!is_wp_error($result) ? '✅ Article updated' : '❌ Update failed') . '</p>';

if (!is_wp_error($result)) {
    echo '<p><a href="' . get_permalink($post->ID) . '" target="_blank">→ View article live</a></p>';
}

echo '<p style="color:red"><strong>SUPPRIMER CE FICHIER via cPanel après lecture.</strong></p>';
