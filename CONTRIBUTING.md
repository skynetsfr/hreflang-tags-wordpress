# Contributing to Hreflang Tags WP

First off, thank you for considering contributing to Hreflang Tags WP! It's people like you that make this plugin better for everyone.

## Code of Conduct

This project and everyone participating in it is expected to follow basic principles of respect and professionalism. Be kind, be respectful, and help us create a welcoming environment for all contributors.

## How Can I Contribute?

### Reporting Bugs

Before creating bug reports, please check the existing issues to avoid duplicates. When you create a bug report, include as many details as possible:

- **Use a clear and descriptive title**
- **Describe the exact steps to reproduce the problem**
- **Provide specific examples**
- **Describe the behavior you observed and what you expected**
- **Include screenshots if relevant**
- **Include your environment details**:
  - WordPress version
  - PHP version
  - Plugin version
  - Theme name
  - Other active plugins

### Suggesting Enhancements

Enhancement suggestions are tracked as GitHub issues. When creating an enhancement suggestion:

- **Use a clear and descriptive title**
- **Provide a detailed description of the suggested enhancement**
- **Explain why this enhancement would be useful**
- **List some examples of how it would be used**

### Pull Requests

1. **Fork the repository** and create your branch from `main`
2. **Make your changes** following the coding standards below
3. **Test your changes** thoroughly
4. **Update documentation** if needed
5. **Write clear commit messages**
6. **Submit a pull request**

## Development Setup

```bash
# Clone your fork
git clone https://github.com/YOUR-USERNAME/hreflang-tags-for-wordpress.git

# Create a branch
git checkout -b feature/your-feature-name

# Make your changes and commit
git add .
git commit -m "Add your feature"

# Push to your fork
git push origin feature/your-feature-name
```

## Coding Standards

### PHP

- Follow [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/)
- Use tabs for indentation, not spaces
- Always sanitize input and escape output
- Add security checks (nonces, capability checks)
- Comment your code where necessary
- Use meaningful variable and function names

### JavaScript

- Follow [WordPress JavaScript Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/javascript/)
- Use modern ES6+ syntax where appropriate
- Comment complex logic
- Keep functions small and focused

### CSS

- Follow [WordPress CSS Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/css/)
- Use meaningful class names
- Organize properties logically

## Security

- **Never** commit sensitive data (API keys, passwords, etc.)
- Always use WordPress nonces for form submissions
- Always check user capabilities before performing actions
- Sanitize all input data
- Escape all output data
- Use prepared statements for database queries

## Testing

Before submitting a pull request:

1. Test on a clean WordPress installation
2. Test with common themes (Twenty Twenty-Four, etc.)
3. Test with WP_DEBUG enabled
4. Check for PHP errors and warnings
5. Test REST API endpoints if you modified them
6. Test with different post types if relevant

## Documentation

- Update README.md if you add new features
- Update CHANGELOG.md following the existing format
- Add inline comments for complex logic
- Update code examples if you change functionality

## Commit Messages

- Use the present tense ("Add feature" not "Added feature")
- Use the imperative mood ("Move cursor to..." not "Moves cursor to...")
- Limit the first line to 72 characters
- Reference issues and pull requests when relevant

Examples:
```
Add REST API support for taxonomies
Fix security vulnerability in AJAX handler
Update README with installation instructions
Refactor meta box code for clarity
```

## Questions?

Feel free to open an issue with the label "question" if you need help or clarification.

## License

By contributing, you agree that your contributions will be licensed under the GPL v2 or later license.

---

Thank you for contributing! 🎉
