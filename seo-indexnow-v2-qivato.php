<?php
/**
 * One-time script: IndexNow setup with valid key for qivato.com
 * Token: qivato2026indexnowv2
 * DELETE this file after execution.
 * Keep the key file permanently.
 */

if (!isset($_GET['token']) || $_GET['token'] !== 'qivato2026indexnowv2') {
    die('Unauthorized');
}

require_once('wp-load.php');

$key     = 'qivato2026bing-indexnow-key-secure';
$host    = 'qivato.com';
$key_url = 'https://' . $host . '/' . $key . '.txt';

echo '<style>body{font-family:monospace;padding:20px} .ok{color:green} .err{color:red} pre{background:#f4f4f4;padding:10px}</style>';
echo '<h2>IndexNow v2 — Qivato</h2>';

// Step 1: Delete old key file
$old_key_file = ABSPATH . 'ZTQ2MjI5NjU0NTY4NGMyZWExNmE3NzIxMWYwZjVmNzU=.txt';
if (file_exists($old_key_file)) {
    unlink($old_key_file);
    echo '<p class="ok">✅ Ancien fichier clé supprimé</p>';
}

// Step 2: Create new key file
$new_key_file = ABSPATH . $key . '.txt';
$created = file_put_contents($new_key_file, $key);
echo '<p>' . ($created !== false ? '<span class="ok">✅ Nouveau fichier clé créé : ' . esc_html($key) . '.txt</span>' : '<span class="err">❌ Erreur création fichier</span>') . '</p>';

// Step 3: Verify key file accessible
$check = wp_remote_get($key_url, ['timeout' => 10]);
$check_code = is_wp_error($check) ? 'ERROR' : wp_remote_retrieve_response_code($check);
$check_body = is_wp_error($check) ? '' : trim(wp_remote_retrieve_body($check));
echo '<p>Fichier accessible : ' . ($check_code == 200 && $check_body === $key ? '<span class="ok">✅ OK</span>' : '<span class="err">❌ HTTP ' . $check_code . '</span>') . '</p>';

// Step 4: Collect all published URLs
$posts = get_posts([
    'post_type'      => ['post', 'page'],
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'fields'         => 'ids',
]);

$urls = ['https://qivato.com/'];
foreach ($posts as $id) {
    $url = get_permalink($id);
    if (strpos($url, 'https://qivato.com') === 0) {
        $urls[] = $url;
    }
}
echo '<p>URLs collectées : <strong>' . count($urls) . '</strong></p>';

// Step 5: Submit to IndexNow
$payload = json_encode([
    'host'        => $host,
    'key'         => $key,
    'keyLocation' => $key_url,
    'urlList'     => array_values($urls),
]);

$response  = wp_remote_post('https://api.indexnow.org/indexnow', [
    'headers' => ['Content-Type' => 'application/json; charset=utf-8'],
    'body'    => $payload,
    'timeout' => 30,
]);
$code = is_wp_error($response) ? 'ERROR: ' . $response->get_error_message() : wp_remote_retrieve_response_code($response);
$body = is_wp_error($response) ? '' : wp_remote_retrieve_body($response);

echo '<p>IndexNow API : ';
echo ($code == 200 || $code == 202) ? '<span class="ok">✅ ' . $code . ' — Accepté !</span>' : '<span class="err">❌ ' . $code . '</span>';
echo '</p>';
if ($body) echo '<pre>' . esc_html(substr($body, 0, 300)) . '</pre>';

echo '<h3>URLs soumises :</h3><ul>';
foreach ($urls as $url) echo '<li>' . esc_html($url) . '</li>';
echo '</ul>';
echo '<p style="color:red"><strong>SUPPRIMER CE FICHIER via cPanel. Garder le fichier ' . esc_html($key) . '.txt</strong></p>';
