#!/bin/bash

# This is a custom bash script that is called from the extention Code Runner to use php from Docker when executing php scripts
# This script is named run_php.sh and it lays in the base directory of my app (see BASE_DIR below)
# in the settings.json use:
#     "code-runner.executorMap": {
#         "php": "/home/emnik/docker/lamp/html/tuitionweb/vscode-scripts/run_php.sh ${file}",
#     }"      


# Get the filename with the full path
FILE="$1"

# Define the base dir
BASE_DIR="/home/emnik/docker/lamp/html/tuitionweb"

# Remove the base dir from the filename making the filename relative to the base dir
RELATIVE_FILE=$(realpath --relative-to="$BASE_DIR" "$FILE")

# Setup the working directory for the container
DIRECTORY=$(dirname "/var/www/html/tuitionweb/$RELATIVE_FILE")

# Get the filename
FILENAME=$(basename "/var/www/html/tuitionweb/$RELATIVE_FILE")

# Change to the working directory inside the container and run php in the file

# -T in the next cmd is needed when in the settings.json we set:
# "code-runner.runInTerminal": false
# in this case the output is shown in the OUTPUT tab below
# If it is set to true then -T is not needed (but it works even if you don't remove it).
# In this case the output is shown in  the TERMINAL tab.
docker-compose exec -T -w "$DIRECTORY" web-server php "$FILENAME"
