# My API Gateway

## Setup Instructions
1. Place in `htdocs`.
2. Ensure Apache mod_rewrite is enabled.
3. Start Apache via XAMPP.

## API Keys
- key123 (UserA)
- key456 (UserB)

## Testing (using curl)
```bash
curl -H "X-API-Key: key123" http://localhost/my_api_gateway/api/users
