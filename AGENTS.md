# AGENTS.md

## Project Overview

This repository contains "Marc's Custom WP Functions Plugin", a lightweight WordPress plugin that provides additional functionality without requiring a child theme.

The plugin is maintained by Marc Chiroiu.

GitHub: https://github.com/schischa/marcs-custom-wp-functions

The primary goals are:
- Keep the plugin lightweight and dependency-free.
- Provide useful WordPress enhancements and administrative options.
- Follow WordPress development standards.
- Keep the code modular, maintainable and easy to understand.

## General Working Principles

- Always inspect the existing implementation before making changes.
- Understand the existing architecture and conventions before proposing or implementing modifications.
- Prefer small, focused changes over extensive refactoring.
- Do not rewrite existing functionality unless explicitly requested.
- Preserve existing functionality and backward compatibility wherever possible.
- Do not introduce unnecessary dependencies or external services.
- Ask for approval before introducing new dependencies or significantly changing the plugin architecture.
- Explain your implementation approach before making larger changes.
- If requirements are ambiguous, ask before making assumptions.

## Code Structure and Architecture

- The main plugin file is `marcs-custom-wp-functions.php`.
- Individual features are organized in separate PHP files in the `inc/` directory.
- New functionality should generally be implemented in a separate file under `inc/`.
- Include new feature files from the main plugin file using `require_once`.
- Keep the main plugin file focused on plugin initialization and loading feature files.
- Follow the existing procedural programming style.
- Do not introduce classes, namespaces or additional architectural layers unless explicitly requested.

## Naming Conventions

- Use the `marcs_cwf_` prefix for all new functions, options, hooks and other globally scoped identifiers.
- Preserve existing function names when modifying existing functionality, even if they use older naming conventions.
- Use descriptive function and variable names.
- Avoid generic function names that could conflict with WordPress core or other plugins.

## Settings and Options

The plugin uses individual WordPress options rather than a single settings array.

When adding new settings:

- Follow the existing implementation in `inc/settings-page.php`.
- Use individual options with the `marcs_cwf_` prefix.
- Register settings using the WordPress Settings API.
- Use appropriate sanitization callbacks.
- Provide sensible defaults.
- Add new default options to the plugin activation function when necessary.
- Use `get_option()` with appropriate fallback values.
- For checkbox fields, include a hidden input with value `0` before the checkbox so unchecked values are saved correctly.
- Follow the existing settings page structure and section organization.
- Do not replace the existing settings architecture with a different approach.

## WordPress Development Standards

- Follow WordPress Coding Standards and APIs where practical.
- Use WordPress core functions instead of reinventing existing functionality.
- Sanitize all user input.
- Escape output appropriately, for example with `esc_html()`, `esc_attr()` and `esc_url()`.
- Use nonces and capability checks for actions that require authorization.
- Use appropriate hooks and filters instead of modifying WordPress core files.
- Avoid unnecessary database queries and expensive operations.
- Ensure compatibility with supported WordPress and PHP versions. Inspect the existing plugin documentation and metadata rather than assuming version requirements.

## Security and Privacy

- Apply the principle of least privilege.
- Check user capabilities before displaying or executing administrative functionality.
- Never expose sensitive server information unnecessarily.
- Do not transmit data to external services unless explicitly requested.
- Never add tracking, telemetry or external API calls without explicit approval.
- Do not introduce unnecessary cookies or collect personal data.
- Do not expose credentials, API keys, passwords or other secrets in code or documentation.

## Documentation and Versioning

When adding or changing user-facing functionality:

- Update `README.md` where appropriate.
- Add a corresponding entry to the changelog.
- Keep documentation concise and consistent with the existing style.
- Update the plugin version in the main plugin file when preparing a release.
- Follow semantic versioning:
  - PATCH: Bug fixes and minor corrections.
  - MINOR: New backward-compatible functionality.
  - MAJOR: Breaking changes.

Do not change the plugin version for every intermediate code modification. Only update it when explicitly preparing a release or when requested.

## Testing and Validation

After making changes:

- Check the PHP syntax of all modified PHP files using `php -l`.
- Review the complete Git diff.
- Check for undefined functions, variables and potential PHP warnings.
- Verify that new settings use appropriate sanitization and defaults.
- Check that existing functionality has not been unintentionally changed.
- Run available project tests if they exist.

Do not claim that functionality has been tested in WordPress unless an actual WordPress environment was available and the relevant tests were performed.

## Git and File Management

- Never create commits, tags or releases unless explicitly requested.
- Never push changes to remote repositories unless explicitly requested.
- Never deploy changes to production environments.
- Do not overwrite unrelated user changes.
- Always inspect `git status` before modifying files.
- Keep changes limited to the requested task.
- Summarize modified files and relevant Git diff details after implementation.

## Communication

- Communicate with the project owner in German.
- Keep explanations concise but include relevant technical details.
- Before implementing larger changes, provide a short implementation plan.
- Clearly mention potential compatibility issues, security implications and relevant trade-offs.
- Distinguish between implemented changes and suggestions for future improvements.