# Security Policy

## Supported Versions

We release security updates for the following versions:

| Version | Supported          |
| ------- | ------------------ |
| 2.0.x   | :white_check_mark: |
| < 2.0   | :x:                |

## Reporting a Vulnerability

Security is a top priority for this project. If you discover a security vulnerability, please follow these steps:

### How to Report

**Please do not report security vulnerabilities through public GitHub issues.**

Instead, please report security vulnerabilities by emailing: **contact@skynets.fr**

Include the following information in your report:

- Type of vulnerability
- Full paths of source file(s) related to the vulnerability
- Location of the affected source code (tag/branch/commit or direct URL)
- Step-by-step instructions to reproduce the issue
- Proof-of-concept or exploit code (if possible)
- Impact of the vulnerability, including how an attacker might exploit it

### What to Expect

- **Acknowledgment**: You will receive an acknowledgment of your report within 48 hours
- **Assessment**: We will assess the vulnerability and determine its severity
- **Fix Development**: We will work on a fix and keep you informed of progress
- **Disclosure**: Once the fix is ready, we will:
  - Release a security update
  - Credit you in the release notes (if desired)
  - Publish a security advisory

### Disclosure Policy

- Security issues are handled with the highest priority
- We follow responsible disclosure practices
- Vulnerabilities will be patched as quickly as possible
- Security updates will be released immediately upon fix verification

## Security Measures

This plugin implements several security best practices:

- **CSRF Protection**: All AJAX handlers verify nonces
- **Capability Checks**: Operations require appropriate WordPress permissions
- **Input Sanitization**: All user inputs are sanitized using WordPress functions
- **Output Escaping**: All outputs are properly escaped to prevent XSS
- **SQL Injection Prevention**: Database queries use prepared statements
- **Path Traversal Protection**: File operations validate paths

## Security Updates

Security updates are released as needed and announced via:

- GitHub Security Advisories
- Plugin changelog
- GitHub releases

Thank you for helping keep Hreflang Tags WP and its users safe!
