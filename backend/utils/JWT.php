<?php
/**
 * Simple JWT Helper Class
 * Handles JWT token generation and validation without external dependencies
 */

class JWT {
    // Secret key for signing tokens - CHANGE THIS IN PRODUCTION!
    private static $secret = 'your-secret-key-change-this-in-production-2024';

    /**
     * Generate a JWT token
     * @param array $payload The data to encode in the token
     * @param int $expiration Token expiration time in seconds (default 24 hours)
     * @return string The JWT token
     */
    public static function encode($payload, $expiration = 86400) {
        // Add expiration time to payload
        $payload['exp'] = time() + $expiration;
        $payload['iat'] = time();

        // Create header
        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256'
        ];

        // Encode header and payload
        $headerEncoded = self::base64UrlEncode(json_encode($header));
        $payloadEncoded = self::base64UrlEncode(json_encode($payload));

        // Create signature
        $signature = hash_hmac('sha256', "$headerEncoded.$payloadEncoded", self::$secret, true);
        $signatureEncoded = self::base64UrlEncode($signature);

        // Return complete token
        return "$headerEncoded.$payloadEncoded.$signatureEncoded";
    }

    /**
     * Decode and validate a JWT token
     * @param string $token The JWT token to decode
     * @return array|false The decoded payload or false if invalid
     */
    public static function decode($token) {
        // Split token into parts
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return false;
        }

        list($headerEncoded, $payloadEncoded, $signatureEncoded) = $parts;

        // Verify signature
        $signature = hash_hmac('sha256', "$headerEncoded.$payloadEncoded", self::$secret, true);
        $signatureCheck = self::base64UrlEncode($signature);

        if ($signatureEncoded !== $signatureCheck) {
            return false; // Invalid signature
        }

        // Decode payload
        $payload = json_decode(self::base64UrlDecode($payloadEncoded), true);

        // Check expiration
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            return false; // Token expired
        }

        return $payload;
    }

    /**
     * Extract token from Authorization header
     * @param string $authHeader The Authorization header value
     * @return string|false The token or false if not found
     */
    public static function extractFromHeader($authHeader) {
        if (empty($authHeader)) {
            return false;
        }

        // Check for "Bearer TOKEN" format
        if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            return $matches[1];
        }

        return false;
    }

    /**
     * Base64 URL encode
     * @param string $data Data to encode
     * @return string Encoded string
     */
    private static function base64UrlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Base64 URL decode
     * @param string $data Data to decode
     * @return string Decoded string
     */
    private static function base64UrlDecode($data) {
        return base64_decode(strtr($data, '-_', '+/'));
    }
}