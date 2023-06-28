#!/usr/bin/env bash

##################################################
# Automatically downloads and installs WordPress
# inside the Lando PHP container.
##################################################

shopt -s expand_aliases

# LANDO_WEBROOT=/app/dev/public
WP_PATH=${LANDO_WEBROOT}/wp
alias wp="/usr/local/bin/wp --path=${WP_PATH}"

function download_wp() {
  echo "* Downloading WordPress..."

  wp core download \
    --version=${WP_VERSION:-latest} \
    --force
}

function install_wp() {
  echo "* Installing WordPress..."

  # Wait for the database service to actually be ready...
  sleep 5

  wp core install \
    --url=${WP_HOME:-https://telemetry-library.lndo.site}/ \
    --title="Telemetry Library dev site" \
    --admin_user=admin \
    --admin_password=password \
    --admin_email=admin@telemetry-library.lndo.site \
    --skip-email
}

if [ ! -d "${WP_PATH}" ]; then
  download_wp
  install_wp

  mkdir -p ${LANDO_WEBROOT}/wp-content/{themes,plugins}
  cp -af ${WP_PATH}/wp-content/themes/. ${LANDO_WEBROOT}/wp-content/themes/
else
  echo "* WordPress directory found at ${WP_PATH}. Skipping installation..."
fi
