# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a PHP backend API for a quiz application with admin functionality. It handles participant registration, quiz answer storage, admin authentication with JWT tokens, and winner selection. The backend uses SQLite for data storage and implements a custom JWT authentication system without external dependencies.

## Development Setup

```bash
# Initialize database and create default admin user
php database/setup-admin.php

# Default admin credentials (MUST be changed in production):
# Username: admin
# Password: admin123
```

**Environment**: Designed to run on MAMP with PHP 7.4+. The SQLite database file is auto-created at `database/quiz.db` on first API call.

**Important**: Change the JWT secret key in `utils/JWT.php` line 9 before production deployment.

## Architecture

### Directory Structure

- `api/` - API endpoints (PHP files handle specific operations)
- `api/auth/` - Authentication endpoints (login, verify)
- `config/` - Database configuration and connection management
- `database/` - SQLite database file and setup scripts
- `middleware/` - Reusable middleware (authentication)
- `utils/` - Utility classes (JWT handling)

### Database Schema

**participants table**:
- `id` - Auto-incrementing primary key
- `email` - Unique email address (validated on submission)
- `quiz_answers` - JSON-encoded array of quiz answers
- `is_winner` - Boolean flag (0 or 1)
- `created_at` - Timestamp of submission

**admin_users table**:
- `id` - Auto-incrementing primary key
- `username` - Unique username
- `password` - Bcrypt-hashed password
- `created_at` - Account creation timestamp

### Authentication System

**JWT Implementation** (`utils/JWT.php`):
- Custom JWT library using HS256 algorithm with HMAC-SHA256 signing
- No external dependencies (pure PHP implementation)
- Token structure: `{header}.{payload}.{signature}`
- Default expiration: 24 hours (86400 seconds)
- Secret key stored as static property in JWT class

**Authentication Flow**:
1. Admin logs in via `api/auth/login.php` with username/password
2. Password verified using `password_verify()` against bcrypt hash
3. JWT token generated with `user_id` and `username` payload
4. Token returned to frontend in response JSON
5. Frontend includes token in `Authorization: Bearer {token}` header
6. Protected endpoints use `requireAuth()` middleware to validate token

**Middleware Pattern** (`middleware/auth.php`):
- Call `requireAuth()` at the start of protected endpoints
- Extracts token from `HTTP_AUTHORIZATION` or `REDIRECT_HTTP_AUTHORIZATION` header
- Validates token signature and expiration
- Returns payload on success or sends 401 response and exits on failure

### API Endpoints

**Public Endpoints**:
- `POST /api/submit-email.php` - Submit participant email and quiz answers
  - Validates email format with `FILTER_VALIDATE_EMAIL`
  - Returns 23000 error code for duplicate email entries
  - Stores answers as JSON string

**Authentication Endpoints**:
- `POST /api/auth/login.php` - Admin login
  - Returns JWT token and user object
  - Sets CORS origin to `http://localhost:8888`
- `POST /api/auth/verify.php` - Verify JWT token validity
  - Used for session validation on frontend

**Protected Endpoints** (require JWT):
- `GET /api/get-participants.php` - Get all participants ordered by creation date DESC
- `GET /api/select-winners.php?count=N` - Randomly select N winners
  - Resets all previous winners before selecting new ones
  - Uses SQLite `RANDOM()` function for randomization
- `POST /api/mark-winner.php` - Toggle winner status for specific participant
  - Accepts `id` and `is_winner` (boolean) in request body

### CORS Configuration

- Public API endpoints use `Access-Control-Allow-Origin: *` for broad access
- Auth endpoints restrict to `http://localhost:8888` for security
- All endpoints handle OPTIONS preflight requests
- Common headers: `Content-Type`, `Authorization`

### Database Connection Pattern

All endpoints follow this pattern:
```php
require_once '../config/database.php';
$db = new Database();
$conn = $db->getConnection();
```

The `Database` class:
- Auto-creates SQLite database file and directory if missing
- Sets PDO error mode to exceptions
- Automatically creates `participants` table on instantiation
- Returns PDO connection object via `getConnection()`

### Error Handling

- PDO exceptions caught with try-catch blocks
- Duplicate email constraint violation returns user-friendly message (error code 23000)
- HTTP status codes: 200 (success), 400 (bad request), 401 (unauthorized), 405 (method not allowed), 500 (server error)
- All responses return JSON with `success` boolean flag

## Key Implementation Notes

### Password Hashing
- Uses PHP's `password_hash()` with `PASSWORD_BCRYPT` algorithm
- Admin passwords are never stored in plaintext
- Verification uses `password_verify()` for timing-attack resistance

### JSON Handling
- Quiz answers stored as JSON string in SQLite TEXT column
- Input data parsed with `json_decode(file_get_contents('php://input'), true)`
- All responses encoded with `json_encode()`

### Winner Selection Algorithm
- `/select-winners.php` uses two-step process:
  1. Reset all participants: `UPDATE participants SET is_winner = 0`
  2. Select random winners: `UPDATE participants SET is_winner = 1 WHERE id IN (SELECT id FROM participants ORDER BY RANDOM() LIMIT ?)`
- Ensures no stale winner data persists between selections

### Security Considerations
- JWT secret MUST be changed from default value in production
- Default admin credentials MUST be changed after initial setup
- CORS origins should be restricted in production (remove wildcard)
- Authorization header forwarding configured in root `.htaccess`