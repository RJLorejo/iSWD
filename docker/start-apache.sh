
#!/bin/sh
set -e

PORT="${PORT:-80}"

echo "========================================"
echo "iSWD Apache Startup"
echo "PORT=${PORT}"
echo "========================================"

echo "Removing conflicting Apache MPM modules..."

a2dismod mpm_event mpm_worker || true

rm -f \
    /etc/apache2/mods-enabled/mpm_event.load \
    /etc/apache2/mods-enabled/mpm_event.conf \
    /etc/apache2/mods-enabled/mpm_worker.load \
    /etc/apache2/mods-enabled/mpm_worker.conf

echo "Enabling required Apache MPM..."
a2enmod mpm_prefork

echo "Verifying enabled MPM modules..."
ls -la /etc/apache2/mods-enabled/ | grep mpm || true

echo "Verifying Apache loaded modules..."
apache2ctl -M

echo "Updating Apache listening port..."
sed -i "s/^Listen 80$/Listen ${PORT}/" /etc/apache2/ports.conf

echo "Updating VirtualHost port..."
sed -i "s|<VirtualHost \*:80>|<VirtualHost *:${PORT}>|" /etc/apache2/sites-available/000-default.conf

echo "Testing Apache configuration..."
apache2ctl -t

echo "Starting Apache..."
exec apache2-foreground
