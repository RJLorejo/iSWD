
#!/bin/sh
set -e

PORT="${PORT:-80}"

echo "========================================"
echo "iSWD Apache Startup Diagnostics"
echo "PORT=${PORT}"
echo "========================================"

echo "Checking enabled MPM module files..."
ls -la /etc/apache2/mods-enabled/ | grep mpm || true

echo "Checking MPM load directives..."
grep -RniE '^[[:space:]]*LoadModule[[:space:]]+mpm_' \
    /etc/apache2/apache2.conf \
    /etc/apache2/mods-enabled \
    /etc/apache2/conf-enabled \
    /etc/apache2/sites-enabled || true

echo "Checking Apache module list..."
apache2ctl -M 2>&1 || true

echo "Updating Apache listening port..."
sed -i "s/^Listen 80$/Listen ${PORT}/" /etc/apache2/ports.conf

echo "Updating VirtualHost port..."
sed -i "s|<VirtualHost \*:80>|<VirtualHost *:${PORT}>|" /etc/apache2/sites-available/000-default.conf

echo "Checking Apache configuration..."
apache2ctl -t

echo "Starting Apache..."
exec apache2-foreground
