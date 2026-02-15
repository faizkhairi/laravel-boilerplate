# Security Policy

## Reporting a Vulnerability

If you discover a security vulnerability in this project, please report it responsibly.

**Do NOT open a public GitHub issue for security vulnerabilities.**

Instead, please email **faiz@faizkhairi.my** with:

1. A description of the vulnerability
2. Steps to reproduce the issue
3. Any potential impact

You will receive acknowledgment within 48 hours and a detailed response within 5 business days.

## Supported Versions

| Version | Supported |
|---------|-----------|
| Latest  | Yes       |

## Security Best Practices

When using this boilerplate, ensure you:

- Never commit `.env` files or `APP_KEY` to version control
- Set `APP_DEBUG=false` in production
- Use HTTPS in production with correct `APP_URL`
- Keep dependencies updated (`composer audit`, `npm audit`)
- Validate all inputs using Form Requests
- Use Eloquent or Query Builder (never raw SQL)
- Enable throttle on auth routes (Breeze default)
- Use `LOG_STACK=daily` in production for log rotation
