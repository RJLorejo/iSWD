
#!/bin/sh
set -e

PORT="${PORT:-80}"

echo "========================================"
echo "iSWD Apache Startup Diagnostics"
echo "PORT=${PORT}"
echo "========================================"

echo "Updating Apache listening port..."
sed -i "s/^Listen 80$/Listen ${PORT}/" /etc/apache2/ports.conf

echo "Updating VirtualHost port..."
sed -i "s|<VirtualHost \*:80>|<VirtualHost *:${PORT}>|" /etc/apache2/sites-available/000-default.conf

echo "Current Apache VirtualHost configuration:"
cat -n /etc/apache2/sites-available/000-default.conf

echo "Current Apache listening configuration:"
cat /etc/apache2/ports.conf

echo "Checking Apache configuration..."
apache2ctl -t

echo "Starting Apache..."
exec apache2-foreground
