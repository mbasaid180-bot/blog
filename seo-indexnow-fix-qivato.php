<?php
/**
 * One-time script: Fix & resubmit IndexNow for qivato.com
 * Token: qivato2026indexnowfix
 * DELETE this file after execution.
 */

if (!isset($_GET['token']) || $_GET['token'] !== 'qivato2026indexnowfix') {
    die('Unauthorized');
}

require_once('wp-load.php');

$key      = 'ZTQ2MjI5NjU0NTY4NGMyZWExNmE3NzIxMWYwZjVmNzU=';
$host     = 'qivato.com';
$key_url  = 'https://' . $host . '/' . $key . '.txt';

echo '<style>body{font-family:monospace;padding:20px} .ok{color:green} .err{color:red} .warn{color:orange} ul{font-size:12px}</style>';
echo '<h2>IndexNow Diagnostic — Qivato</h2>';

// Step 1: Verify key file is publicly accessible
$check = wp_remote_get($key_url, ['timeout' => 10]);
$check_code = is_wp_error($check) ? 'ERROR' : wp_remote_retrieve_response_code($check);
$check_body = is_wp_error($check) ? '' : trim(wp_remote_retrieve_body($check));

echo '<h3>1. Key file accessible ?</h3>';
if ($check_code == 200 && $check_body === $key) {
    echo '<p class="ok">✅ Accessible — contenu correct</p>';
} else {
    echo '<p class="err">❌ Problème — HTTP ' . $check_code . ' | contenu: ' . esc_html(substr($check_body, 0, 100)) . '</p>';
}

// Step 2: Get only published posts with valid URLs
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

echo '<h3>2. URLs valides collectées : ' . count($urls) . '</h3>';

// Step 3: Submit in batch of 100
$chunks = array_chunk($urls, 100);
$all_ok = true;

foreach ($chunks as $i => $chunk) {
    $payload = json_encode([
        'host'        => $host,
        'key'         => $key,
        'keyLocation' => $key_url,
        'urlList'     => array_values($chunk),
    ]);

    $response = wp_remote_post('https://api.indexnow.org/indexnow', [
        'headers' => [
            'Content-Type' => 'application/json; charset=utf-8',
            'Host'         => 'api.indexnow.org',
        ],
        'body'    => $payload,
        'timeout' => 30,
    ]);

    $code = is_wp_error($response) ? 'ERROR: ' . $response->get_error_message() : wp_remote_retrieve_response_code($response);
    $ok   = ($code == 200 || $code == 202);
    if (!$ok) $all_ok = false;

    echo '<p>Batch ' . ($i + 1) . ' (' . count($chunk) . ' URLs) : ';
    echo $ok ? '<span class="ok">✅ ' . $code . ' Accepted</span>' : '<span class="err">❌ ' . $code . '</span>';
    echo '</p>';
}

echo '<h3>3. Résultat final : ' . ($all_ok ? '<span class="ok">✅ Soumission réussie</span>' : '<span class="err">❌ Erreur — voir détails ci-dessus</span>') . '</h3>';

echo '<ul>';
foreach ($urls as $url) echo '<li>' . esc_html($url) . '</li>';
echo '</ul>';

echo '<p style="color:red"><strong>SUPPRIMER CE FICHIER via cPanel après lecture.</strong></p>';
