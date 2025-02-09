# API Documentation

## Authentication Endpoints

### Register
```http
POST /api/register
Content-Type: application/json

Request Body:
{
    "name": "string",
    "email": "string",
    "password": "string"
}

Response (201):
{
    "user": {
        "id": "integer",
        "name": "string",
        "email": "string"
    },
    "access_token": "string",
    "token_type": "Bearer"
}
```

### Login
```http
POST /api/login
Content-Type: application/json

Request Body:
{
    "email": "string",
    "password": "string"
}

Response (200):
{
    "user": {
        "id": "integer",
        "name": "string",
        "email": "string"
    },
    "access_token": "string",
    "token_type": "Bearer"
}
```

### Logout
```http
POST /api/logout
Authorization: Bearer {token}

Response (200):
{
    "message": "Successfully logged out"
}
```

## TV Authentication Endpoints

### Generate TV Code
```http
POST /api/generate-tv-code
Content-Type: application/json

Response (200):
{
    "code": "string (6 characters)",
    "expires_in": 300
}
```

### Activate TV Code
```http
POST /api/active-tv-code
Authorization: Bearer {token}
Content-Type: application/json

Request Body:
{
    "code": "string (6 characters)"
}

Response (200):
{
    "message": "Code activated successfully"
}
```

### Poll TV Code
```http
POST /api/poll-tv-code
Content-Type: application/json

Request Body:
{
    "code": "string (6 characters)"
}

Response (200):
{
    "access_token": "string",
    "token_type": "Bearer",
    "expires_in": 3600
}

Response (Pending):
{
    "status": "pending"
}
```

## Error Responses

```http
401 Unauthorized:
{
    "message": "Unauthenticated"
}

400 Bad Request:
{
    "message": "Invalid or expired code"
}

429 Too Many Attempts:
{
    "message": "Too many attempts. Please try again later."
}

500 Server Error:
{
    "message": "Error processing request"
}