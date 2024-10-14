#!/bin/sh
# Get the filename with the full path
FILE="$6"
CURRENT_DIR=$(pwd)

# Define the base dir
BASE_DIR="/home/emnik/docker/lamp/html/tuitionweb"

# Remove the base dir from the filename making the filename relative to the base dir
RELATIVE_DIR=$(realpath --relative-to="$BASE_DIR" "$CURRENT_DIR")

# Setup the working directory for the container
DIRECTORY="/var/www/html/tuitionweb/$RELATIVE_DIR"

# Get the filename
FILENAME=$(basename "$FILE")

docker-compose exec -T web-server php-cs-fixer fix "$DIRECTORY/$FILENAME" --config="/var/www/html/tuitionweb/vscode-scripts/.php-cs-fixer.dist.php"