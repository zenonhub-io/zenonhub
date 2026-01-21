#!/bin/sh
set -e

# Copy default config if it doesn't exist
if [ ! -f /root/.znn/config.json ]; then
    echo "Copying default config to /root/.znn/config.json"
    cp /root/config.json.default /root/.znn/config.json
fi

# Start znnd
exec znnd "$@"
