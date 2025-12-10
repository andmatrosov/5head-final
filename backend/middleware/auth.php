<?php
/**
 * Authentication Middleware
 * Include this file in protected endpoints to require JWT authentication
 */

require_once __DIR__ . '/../utils/JWT.php';

function requireAuth() {
    // Get Authorization header
    $authHeader = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';

    if (empty($authHeader)) {
        // Check alternative header format
        $authHeader = isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']) ? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] : '';
    }

    // Extract token from header
    $token = JWT::extractFromHeader($authHeader);

    if (!$token) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Authentication required']);
        exit();
    }

    // Verify token
    $payload = JWT::decode($token);

    if (!$payload) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Invalid or expired token']);
        exit();
    }

    // Return user data from token
    return $payload;
}