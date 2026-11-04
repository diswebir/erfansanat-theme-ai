#!/usr/bin/env bash
# Reproducible Harness Bootstrap Script for ErfanSanat WordPress Theme Development
# Installs PHP 8.4, WP-CLI, Composer, PHPCS, PHPUnit, xmllint, and WordPress 7.x + SQLite testbed.
set -euo pipefail

echo "[1/6] Checking PHP 8.4..."
if ! command -v php >/dev/null 2>&1; then
  rm -rf /tmp/php-bin-fetch /tmp/php-extracted
  git clone --depth 1 --filter=blob:none --sparse https://github.com/NativePHP/php-bin.git /tmp/php-bin-fetch
  git -C /tmp/php-bin-fetch sparse-checkout set --no-cone /bin/linux/x64/php-8.4.zip /cacert.pem
  python3 -c "import zipfile; zipfile.ZipFile('/tmp/php-bin-fetch/bin/linux/x64/php-8.4.zip').extractall('/tmp/php-extracted')"
  sudo mv /tmp/php-extracted/php /usr/local/bin/php
  sudo chmod +x /usr/local/bin/php
  sudo mkdir -p /usr/local/etc/php
  sudo cp /tmp/php-bin-fetch/cacert.pem /usr/local/etc/php/cacert.pem
  rm -rf /tmp/php-bin-fetch /tmp/php-extracted
fi

echo "[2/6] Checking WP-CLI..."
if ! command -v wp >/dev/null 2>&1; then
  rm -rf /tmp/wpcli-fetch
  git clone --depth 1 --filter=blob:none --sparse https://github.com/wp-cli/builds.git /tmp/wpcli-fetch
  git -C /tmp/wpcli-fetch sparse-checkout set --no-cone /phar/wp-cli.phar
  sudo mv /tmp/wpcli-fetch/phar/wp-cli.phar /usr/local/bin/wp
  sudo chmod +x /usr/local/bin/wp
  rm -rf /tmp/wpcli-fetch
fi

echo "[3/6] Checking Composer & PHPCS..."
if ! command -v composer >/dev/null 2>&1; then
  rm -rf /tmp/composer-fetch
  git clone --depth 1 --filter=blob:none --sparse https://github.com/composer/getcomposer.org.git /tmp/composer-fetch
  git -C /tmp/composer-fetch sparse-checkout set --no-cone /web/download/2.9.8/composer.phar
  sudo mv /tmp/composer-fetch/web/download/2.9.8/composer.phar /usr/local/bin/composer
  sudo chmod +x /usr/local/bin/composer
  rm -rf /tmp/composer-fetch
fi

if ! command -v phpcs >/dev/null 2>&1; then
  sudo rm -rf /opt/phpcs
  sudo git clone --depth 1 https://github.com/PHPCSStandards/PHP_CodeSniffer.git /opt/phpcs
  sudo sed -i "/'xmlwriter',/d" /opt/phpcs/requirements.php
  sudo ln -sf /opt/phpcs/bin/phpcs /usr/local/bin/phpcs
  sudo ln -sf /opt/phpcs/bin/phpcbf /usr/local/bin/phpcbf
fi

echo "[4/6] Checking xmllint (lxml)..."
if ! command -v xmllint >/dev/null 2>&1; then
  pip install --quiet --break-system-packages lxml beautifulsoup4 2>/dev/null || pip install --quiet lxml beautifulsoup4
fi

echo "[5/6] Checking WordPress + SQLite Testbed (/opt/wp-testbed)..."
if [ ! -f /opt/wp-testbed/wp-config.php ]; then
  sudo rm -rf /opt/wp-testbed /tmp/sqlite-repo
  sudo git clone --depth 1 https://github.com/WordPress/WordPress.git /opt/wp-testbed
  sudo chown -R "$(id -un):$(id -gn)" /opt/wp-testbed
  git clone --depth 1 https://github.com/WordPress/sqlite-database-integration.git /tmp/sqlite-repo
  cp -r /tmp/sqlite-repo/packages/plugin-sqlite-database-integration /opt/wp-testbed/wp-content/plugins/sqlite-database-integration
  rm -rf /opt/wp-testbed/wp-content/plugins/sqlite-database-integration/wp-includes/database
  cp -r /tmp/sqlite-repo/packages/mysql-on-sqlite/src /opt/wp-testbed/wp-content/plugins/sqlite-database-integration/wp-includes/database
  rm -rf /tmp/sqlite-repo
  cp /opt/wp-testbed/wp-content/plugins/sqlite-database-integration/db.copy /opt/wp-testbed/wp-content/db.php
  sed -i "s#'{SQLITE_IMPLEMENTATION_FOLDER_PATH}'#__DIR__.'/plugins/sqlite-database-integration'#g" /opt/wp-testbed/wp-content/db.php
  sed -i "s#{SQLITE_PLUGIN}#sqlite-database-integration/load.php#g" /opt/wp-testbed/wp-content/db.php
  wp config create --path=/opt/wp-testbed --dbname=erfansanat_test --dbuser=root --dbpass=root --skip-check --extra-php="define('WP_DEBUG', true); define('WP_DEBUG_LOG', true); define('WP_DEBUG_DISPLAY', false);"
  wp core install --path=/opt/wp-testbed --url="http://localhost:8080" --title="عرفان صنعت اصفهان" --admin_user=admin --admin_password=admin --admin_email=info@erfansanat.com --skip-email
fi

echo "[6/6] Harness Ready!"
php -v | head -n 1
wp --info | grep "PHP version"
wp core version --path=/opt/wp-testbed
