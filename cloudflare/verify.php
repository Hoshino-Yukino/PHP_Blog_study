<?php
// cloudflare_turnstile.php

/**
 * 验证 Cloudflare Turnstile
 * 
 * @param string $responseToken 从前端提交的 cf-turnstile-response
 * @param string|null $remoteIp 用户的 IP 地址（可选）
 * 
 * @return array 验证结果数组，包含 `success` 和 `message`
 */



function verifyTurnstile($responseToken,$remoteIp = null) {
    require_once "../config.php";
    $secretKey = $cloudflareSecretKey;
    if (!$responseToken) {
        return ['success' => false, 'message' => 'Missing response token.'];
    }
    $url = "https://challenges.cloudflare.com/turnstile/v0/siteverify";
    $data = [
        'secret' => $secretKey,
        'response' => $responseToken,
    ];

    if ($remoteIp) {
        $data['remoteip'] = $remoteIp;
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    if ($response) {
        return json_decode($response, true);
    }

    return ['success' => false, 'message' => 'Verification failed.'];
}
