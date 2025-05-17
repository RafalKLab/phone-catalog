#!/bin/sh

if [ "$1" = "scrape" ]; then
  echo "Running scraper script..."
  python main.py
else
  exec "$@"
fi
