# WordPress Plugin Development Environment

A Docker-based WordPress development environment optimized for plugin development and testing, especially for conversion tracking and GTM integration.

## Features

- **WordPress** (Latest version)
- **MySQL 8.0** database
- **MySQL CLI** for database management
- **WP-CLI** for WordPress management
- **MailCatcher** for email testing
- **Debug logging** enabled
- **Plugin mounting** for live development
- **GTM-friendly** configuration

## Quick Start

```bash
# Start the environment
./scripts/start.sh

# Stop the environment
./scripts/stop.sh

# Reset everything (clean install)
./scripts/reset.sh

# Install a plugin from WordPress repository
./scripts/install-plugin.sh contact-form-7
```

## Access Points

- **WordPress**: http://localhost:8080
  - Username: `admin`
  - Password: `admin`
- **MailCatcher**: http://localhost:1080
- **MySQL**: localhost:3306

## Directory Structure

```
wp-pdev/
├── docker-compose.yml      # Docker configuration
├── .env                    # Environment variables
├── plugins/               # Mount your plugins here
├── themes/                # Custom themes
├── uploads/               # Media uploads
├── scripts/               # Utility scripts
└── debug.log             # WordPress debug log
```

## Plugin Development

### Mounting Local Plugins

1. Copy or symlink your plugin to the `plugins/` directory:
   ```bash
   ln -s ../choice-uft plugins/choice-uft
   ```

2. The plugin will automatically appear in WordPress admin

3. Any changes to plugin files are immediately reflected

### Installing Test Plugins

For testing with form builders:
```bash
# Contact Form 7
./scripts/install-plugin.sh contact-form-7

# Ninja Forms
./scripts/install-plugin.sh ninja-forms

# WPForms Lite
./scripts/install-plugin.sh wpforms-lite

# Elementor
./scripts/install-plugin.sh elementor
```

### WP-CLI Commands

Run WP-CLI commands using:
```bash
docker-compose exec wpcli wp [command]

# Examples:
docker-compose exec wpcli wp plugin list
docker-compose exec wpcli wp plugin activate choice-uft
docker-compose exec wpcli wp user create test test@example.com --role=editor
```

## GTM Testing

1. Install Google Tag Manager plugin or add container manually
2. Use browser developer tools to monitor dataLayer
3. MailCatcher captures all form submission emails
4. Debug log shows PHP-side tracking events

## Database Management

### MySQL CLI Access
```bash
# Interactive MySQL shell
./scripts/db-cli.sh

# Run SQL query directly
./scripts/db-cli.sh -e "SELECT * FROM wp_users;"

# Direct container access
docker-compose exec db mysql -u wordpress -pwordpress wordpress
```

### Export Database
```bash
./scripts/export-db.sh
```

### Import Database
```bash
./scripts/import-db.sh backup.sql
```

## Debugging

### View Debug Log
```bash
tail -f debug.log
```

### Enable Query Monitor
```bash
./scripts/install-plugin.sh query-monitor
```

### Container Logs
```bash
docker-compose logs -f wordpress
docker-compose logs -f db
```

## Configuration

### Environment Variables

Edit `.env` file to customize:
- Port numbers
- Database credentials
- Debug settings

### Adding PHP Extensions

Edit `docker-compose.yml` to add a custom Dockerfile for WordPress with additional PHP extensions.

## Troubleshooting

### Permission Issues
```bash
# Fix permissions
sudo chown -R $(id -u):$(id -g) plugins/ themes/ uploads/
```

### Container Won't Start
```bash
# Check ports
lsof -i :8080
lsof -i :3306

# Reset everything
./scripts/reset.sh
```

### Slow Performance on Mac
Add volume mount options in docker-compose.yml:
```yaml
volumes:
  - ./plugins:/var/www/html/wp-content/plugins:cached
```

## Tips

1. **GTM Container**: Add your GTM container ID in the choice-uft plugin settings
2. **Form Testing**: Create test forms with different builders to ensure compatibility
3. **Email Testing**: All emails go to MailCatcher - check http://localhost:1080
4. **Performance**: Use `docker-compose exec` instead of `docker exec` for better performance
5. **Backup**: Regular backups with `./scripts/export-db.sh`

## Common Tasks

### Create Test Content
```bash
# Create sample pages
docker-compose exec wpcli wp post create --post_type=page --post_title="Contact" --post_status=publish
docker-compose exec wpcli wp post create --post_type=page --post_title="About" --post_status=publish
```

### Install Theme
```bash
# Install and activate a theme
docker-compose exec wpcli wp theme install twentytwentyfour --activate
```

### Update WordPress
```bash
docker-compose exec wpcli wp core update
docker-compose exec wpcli wp plugin update --all
```

## Requirements

- Docker Desktop
- Docker Compose
- 4GB RAM minimum
- 10GB free disk space