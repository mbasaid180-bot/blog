<?php
/**
 * One-time script: Debug IndexNow 422 for qivato.com
 * Token: qivato2026debug
 * DELETE this file after execution.
 */

if (!isset($_GET['token']) || $_GET['token'] !== 'qivato2026debug') {
    die('Unauthorized');
}

require_once('wp-load.php');

$key     = 'ZTQ2MjI5NjU0NTY4NGMyZWExNmE3NzIxMWYwZjVmNzU=';
$host    = 'qivato.com';
$key_url = 'https://' . $host . '/' . $key . '.txt';

echo '<style>body{font-family:monospace;padding:20px} .ok{color:green} .err{color:red} pre{background:#f4f4f4;padding:10px;overflow:auto}</style>';
echo '<h2>IndexNow Debug — Qivato</h2>';

$test_urls = ['https://qivato.com/best-ai-tools-for-freelancers-scale-your-business-in-2026/'];

$endpoints = [
    'api.indexnow.org'  => 'https://api.indexnow.org/indexnow',
    'www.bing.com'      => 'https://www.bing.com/indexnow',
];

foreach ($endpoints as $name => $endpoint) {
    echo '<h3>Test → ' . $name . '</h3>';

    // Method 1: GET request (simple)
    $get_url = $endpoint . '?url=' . urlencode($test_urls[0]) . '&key=' . urlencode($key) . '&keyLocation=' . urlencode($key_url);
    $get_resp = wp_remote_get($get_url, ['timeout' => 15]);
    $get_code = is_wp_error($get_resp) ? 'ERROR: ' . $get_resp->get_error_message() : wp_remote_retrieve_response_code($get_resp);
    $get_body = is_wp_error($get_resp) ? '' : wp_remote_retrieve_body($get_resp);

    echo '<p><strong>GET :</strong> ';
    echo ($get_code == 200 || $get_code == 202) ? '<span class="ok">✅ ' . $get_code . '</span>' : '<span class="err">❌ ' . $get_code . '</span>';
    echo '</p>';
    if ($get_body) echo '<pre>' . esc_html(substr($get_body, 0, 300)) . '</pre>';

    // Method 2: POST request (batch)
    $payload = json_encode([
        'host'        => $host,
        'key'         => $key,
        'keyLocation' => $key_url,
        'urlList'     => $test_urls,
    ]);

    $post_resp = wp_remote_post($endpoint, [
        'headers' => ['Content-Type' => 'application/json; charset=utf-8'],
        'body'    => $payload,
        'timeout' => 15,
    ]);
    $post_code = is_wp_error($post_resp) ? 'ERROR: ' . $post_resp->get_error_message() : wp_remote_retrieve_response_code($post_resp);
    $post_body = is_wp_error($post_resp) ? '' : wp_remote_retrieve_body($post_resp);

    echo '<p><strong>POST :</strong> ';
    echo ($post_code == 200 || $post_code == 202) ? '<span class="ok">✅ ' . $post_code . '</span>' : '<span class="err">❌ ' . $post_code . '</span>';
    echo '</p>';
    if ($post_body) echo '<pre>' . esc_html(substr($post_body, 0, 300)) . '</pre>';
}

echo '<p style="color:red"><strong>SUPPRIMER CE FICHIER via cPanel après lecture.</strong></p>';
