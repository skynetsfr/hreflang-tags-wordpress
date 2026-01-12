# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.0.0] - 2026-01-12

### 🎉 Major Release - Community Edition

This release marks the transition to a fully open-source, community-driven project.

### Added
- **REST API Support**: Full WordPress REST API integration
  - GET endpoints to retrieve hreflang data
  - POST/PUT endpoints to update hreflang tags
  - Authentication and permission checks
  - Fields: `hreflang_tags`, `html_lang`, `html_region`
- Modern WordPress coding standards throughout
- Improved code organization and documentation

### Removed
- **License System**: Removed all license check requirements
  - No more license keys needed
  - No update server dependencies  
  - Removed GitHub token exposure
  - Plugin is now completely free and open

### Security
- **Complete Security Audit**:
  - CSRF protection on all AJAX handlers
  - Nonce verification throughout
  - Capability checks with `current_user_can()`
  - Input sanitization (`sanitize_text_field`, `esc_url_raw`, `absint`)
  - Output escaping to prevent XSS
  - Path traversal protection on file operations
  - SQL injection prevention with prepared statements
  - Removed nopriv AJAX handlers

### Changed
- Authorship transferred to Skynets
- Repository moved to github.com/skynetsfr
- Support now via GitHub Issues
- Simplified plugin architecture (merged main files)
- Cleaned up variable definitions
- Updated TinyMCE stylesheet URL to CDN
- Version bumped to 2.0.0 to reflect major changes

### Fixed
- Security vulnerabilities in AJAX handlers
- Exposed GitHub token removed
- Input validation improvements
- Permission bypass issues

## [1.9.13] - Previous Release

Last version under original maintenance. See git history for details.

---

[2.0.0]: https://github.com/skynetsfr/hreflang-tags-wordpress/releases/tag/v2.0.0
[1.9.13]: https://github.com/skynetsfr/hreflang-tags-wordpress/releases/tag/v1.9.13
