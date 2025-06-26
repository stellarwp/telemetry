# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is the StellarWP Telemetry Library - a PHP library for WordPress plugins that provides opt-in telemetry data collection. The library is designed to be included via Composer (preferably with Strauss for namespace isolation) and provides a complete telemetry solution with user privacy controls.

## Common Development Commands

### Testing
```bash
# Run unit tests using slic (StellarWP's testing framework)
slic run wpunit

# Run static analysis
composer test:analysis
```

### Code Quality
```bash
# Check coding standards
phpcs ./src

# Auto-fix coding standard issues
phpcbf ./src

# Check PHP compatibility for all supported versions (7.1-8.3)
composer compatibility

# Check specific PHP version compatibility
composer compatibility:php-8.0
```

### Local Development
```bash
# Start local development environment
lando start

# Install dependencies via Lando
lando composer install

# Local site will be available at: https://telemetry-library.lndo.site/
```

## High-Level Architecture

### Core Components

1. **Dependency Injection Container**: The library requires a DI container compatible with `stellarwp/container-contract`. The container must be configured before initializing the library.

2. **Opt-In System**: 
   - `Opt_In/` - Manages user consent through modal interfaces
   - `Exit_Interview/` - Collects feedback when plugins are deactivated
   - Privacy-first approach with explicit user consent required

3. **Data Collection**:
   - `Data_Providers/` - Abstract data collection with providers like `Debug_Data` for Site Health integration
   - `Events/` - Event tracking system for capturing user actions
   - `Telemetry/` - Core functionality for sending telemetry data to the server

4. **Integration Points**:
   - `Admin/` - WordPress admin integration and resource management
   - `Last_Send/` - Tracks when telemetry was last sent
   - Uses subscriber pattern (`Abstract_Subscriber`) for WordPress hooks

### Key Design Patterns

- **Subscriber Pattern**: All WordPress hook integrations use the subscriber pattern through `Abstract_Subscriber`
- **Template System**: UI components use template interfaces for rendering
- **Modular Design**: Clear separation between opt-in, telemetry, events, and exit interview functionality
- **Multi-Plugin Support**: Designed to be shared across multiple plugins using `Config::add_stellar_slug()`

### Integration Requirements

1. Initialize with a compatible DI container
2. Configure server URL and hook prefix
3. Set a unique stellar slug for plugin identification
4. Call `Telemetry::instance()->init(__FILE__)` to start

### Testing Approach

- Uses Codeception with WordPress browser testing
- Tests run via `slic` command in CI/CD
- PHPStan level 5 for static analysis
- PHPCS with WordPress VIP Go and TEC standards