#!/bin/bash

# Get the folder name (default to current directory name if not provided)
FOLDER_NAME=${1:-$(basename "$PWD")}

# Move to parent directory and create zip
cd .. && zip -r "${FOLDER_NAME}.zip" "${FOLDER_NAME}/" \
    -x "*/node_modules/*" \
    -x "*/.git/*" \
    -x "*/.*" \
    -x "*/deprecated/*" \
    -x "*/ressources/*" \
    -x "*/tests/*" \
    -x "*/test/*" \
    -x "*/phpunit.xml" \
    -x "*/phpcs.xml.dist" \
    -x "*/composer.lock" \
    -x "*/package.json" \
    -x "*/webpack.config.js" \
    -x "*/package-lock.json" \
    -x "*/zip.sh" \
    -x "*.json"

echo "Created ${FOLDER_NAME}.zip"