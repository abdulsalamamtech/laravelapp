#!/usr/bin/env bash

# Custom cron script to run Laravel scheduled commands
# cron.log 2>&1
# Command to run
# /bin/sh /home/u200673890/domains/veriscore.app/public_html/staging/cron.sh

# Run Laravel scheduled commands defined in routes/console.php
# Use a cron entry like: * * * * * /path/to/cron.sh

set -e

PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$PROJECT_ROOT"

if [ ! -f artisan ]; then
  echo "artisan not found in project root: $PROJECT_ROOT" >&2
  exit 1
fi

echo "Running Laravel scheduled commands at $(date)" >> storage/logs/cron.log
php artisan schedule:run >> storage/logs/cron.log 2>&1
