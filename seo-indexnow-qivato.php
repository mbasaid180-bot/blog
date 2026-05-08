<?php
/**
 * One-time script: Setup IndexNow + submit all URLs for qivato.com
 * Token: qivato2026indexnow
 * DELETE this file after execution.
 */

if (!isset($_GET['token']) || $_GET['token'] !== 'qivato2026indexnow') {
    die('Unauthorized');
}

require_once('wp-load.php');

$key      = 'ZTQ2MjI5NjU0NTY4NGMyZWExNmE3NzIxMWYwZjVmNzU=';
$host     = 'qivato.com';
$key_file = ABSPATH . $key . '.txt';

// Step 1: Create key verification file
$file_created = file_put_contents($key_file, $key);

// Step 2: Get all published post URLs
$posts = get_posts([
    'post_type'      => 'post',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'fields'         => 'ids',
]);

$pages = get_posts([
    'post_type'      => 'page',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'fields'         => 'ids',
]);

$all_ids = array_merge($posts, $pages);
$urls = [];
foreach ($all_ids as $id) {
    $urls[] = get_permalink($id);
}
$urls[] = 'https://qivato.com/';

// Step 3: Submit to IndexNow API
$payload = json_encode([
    'host'        => $host,
    'key'         => $key,
    'keyLocation' => 'https://' . $host . '/' . $key . '.txt',
    'urlList'     => array_values($urls),
]);

$response = wp_remote_post('https://api.indexnow.org/indexnow', [
    'headers' => ['Content-Type' => 'application/json; charset=utf-8'],
    'body'    => $payload,
    'timeout' => 30,
]);

$status_code = is_wp_error($response) ? 'ERROR: ' . $response->get_error_message() : wp_remote_retrieve_response_code($response);

// Output
echo '<style>body{font-family:monospace;padding:20px} .ok{color:green} .err{color:red}</style>';
echo '<h2>IndexNow Setup — Qivato</h2>';
echo '<p>Key file created : <strong>' . ($file_created !== false ? '<span class="ok">✅ ' . $key . '.txt</span>' : '<span class="err">❌ FAILED</span>') . '</strong></p>';
echo '<p>URLs collected : <strong>' . count($urls) . '</strong></p>';
echo '<p>IndexNow API response : <strong>';
echo ($status_code == 200 || $status_code == 202) ? '<span class="ok">✅ ' . $status_code . ' — Accepted</span>' : '<span class="err">⚠️ ' . $status_code . '</span>';
echo '</strong></p>';
echo '<h3>URLs soumises :</h3><ul>';
foreach ($urls as $url) {
    echo '<li>' . esc_html($url) . '</li>';
}
echo '</ul>';
echo '<p style="color:red"><strong>SUPPRIMER CE FICHIER MAINTENANT via cPanel. Ne pas supprimer le fichier ' . esc_html($key) . '.txt</strong></p>';
