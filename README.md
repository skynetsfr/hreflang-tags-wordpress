# Hreflang Tags WP

![Version](https://img.shields.io/badge/version-2.0.1-blue.svg)
![License](https://img.shields.io/badge/license-GPL--2.0-green.svg)
![WordPress](https://img.shields.io/badge/wordpress-5.0%2B-blue.svg)

Smart implementation of HREFLANG meta tags into the head section of your WordPress site. Essential for multilingual and multi-regional websites to help search engines understand which language and regional variations of your content to serve to users.

## Features

- **Easy hreflang Management** - Add and manage hreflang tags directly from the WordPress post editor
- **Bulk Editor** - Manage hreflang tags for multiple posts at once
- **Taxonomy Support** - Add hreflang tags to categories and custom taxonomies
- **XML Sitemap** - Generate hreflang-enabled XML sitemaps
- **Validator** - Built-in validation tool to check your hreflang implementation
- **Tag Generator** - Generate hreflang HTML code snippets
- **REST API Support** - Access and update hreflang data via WordPress REST API
- **Security Hardened** - CSRF protection, input sanitization, and permission checks throughout
- **Free** - No license keys, no restrictions

## Installation

### From GitHub

1. Download the latest release from the [releases page](https://github.com/skynetsfr/hreflang-tags-wordpress/releases)
2. Upload the plugin folder to `/wp-content/plugins/`
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Configure the plugin settings under the 'Hreflang Tags WP' menu

### Manual Installation

1. Clone this repository:
   ```bash
   git clone https://github.com/skynetsfr/hreflang-tags-wordpress.git
   ```
2. Upload to your `/wp-content/plugins/` directory
3. Activate the plugin in WordPress

## Usage

### Basic Setup

1. Navigate to **Hreflang Tags WP** in your WordPress admin menu
2. Select which post types should have hreflang support
3. Configure your default settings

### Adding hreflang Tags to Posts

1. Edit any post or page
2. Scroll to the **HREFLANG Tags** meta box
3. Add your alternate language URLs:
   - Select the language code (e.g., `en`, `fr`, `de`)
   - Optionally select a region code (e.g., `US`, `GB`, `CA`)
   - Enter the alternate URL
4. Click the + button to add more languages
5. Save your post

### Using the Bulk Editor

1. Go to **Hreflang Tags WP → Bulk Editor**
2. View all posts with their hreflang tags
3. Edit multiple entries at once
4. Save changes in bulk

### XML Sitemap Generation

1. Go to **Hreflang Tags WP → Sitemap**
2. Enable XML sitemap generation
3. Click "Generate Sitemap"
4. Your sitemap will be available at `yoursite.com/hreflang-tags-pro-sitemap.xml`

### REST API Usage

The plugin exposes hreflang data via the WordPress REST API:

#### GET Request
```bash
GET /wp-json/wp/v2/posts/{id}
```

Response includes:
```json
{
  "id": 123,
  "title": "...",
  "hreflang_tags": {
    "en": "https://example.com/en/page",
    "fr": "https://example.com/fr/page",
    "es-ES": "https://example.com/es/page"
  },
  "html_lang": "en",
  "html_region": "US"
}
```

#### POST/PUT Request
```bash
POST /wp-json/wp/v2/posts/{id}
Authorization: Bearer {token}

{
  "hreflang_tags": {
    "en": "https://example.com/en/new-page",
    "fr": "https://example.com/fr/nouvelle-page"
  }
}
```

## Requirements

- WordPress 5.0 or higher
- PHP 7.0 or higher

## What's New in 2.0.0

- **Enhanced Security** - Complete security audit with CSRF protection and input sanitization
- **REST API Support** - Full integration with WordPress REST API
- **Improved Performance** - Optimized code and reduced overhead
- **Modern Standards** - Updated to follow current WordPress development best practices

See [CHANGELOG.md](CHANGELOG.md) for detailed version history.

## Security

Security is a top priority. Version 2.0.0 includes:

- CSRF token verification on all AJAX handlers
- Capability checks with `current_user_can()`
- Input sanitization with WordPress sanitization functions
- Output escaping to prevent XSS
- Path traversal protection on file operations
- SQL injection prevention with prepared statements

If you discover a security vulnerability, please email the details to the repository maintainer. Security issues will be addressed promptly.

## Contributing

Contributions are welcome! Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on our code of conduct and the process for submitting pull requests.

## Support

- **Documentation**: Check this README and the [Wiki](https://github.com/skynetsfr/hreflang-tags-wordpress/wiki)
- **Issues**: Report bugs or request features via [GitHub Issues](https://github.com/skynetsfr/hreflang-tags-wordpress/issues)
- **Discussions**: Ask questions in [GitHub Discussions](https://github.com/skynetsfr/hreflang-tags-wordpress/discussions)

## License

This project is licensed under the GPL v2 or later - see the [LICENSE](LICENSE) file for details.

## Credits

Originally created by Vagary Digital. Now maintained and improved by [Skynets](https://skynets.fr/) as an open-source project.

---

Made with ❤️ for the WordPress multilingual community
