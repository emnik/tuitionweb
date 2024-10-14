in the /home/emnik/.config/Code/User/settings.json:

#for php-cs-fixer:
    "php-cs-fixer.executablePath": "/home/emnik/docker/lamp/html/tuitionweb/vscode-scripts/php-cs-fixer.sh",
    // Set PHP CS Fixer as the default formatter for PHP files
    // It will avoid conflict with other formatters like prettier etc.
    "[php]": {
        "editor.defaultFormatter": "junstyle.php-cs-fixer"
    },

#for php executables: (Used to validate php files)
    "php.validate.executablePath": "/home/emnik/docker/lamp/html/tuitionweb/vscode-scripts/php-docker.sh",

#for php code runner: (To run a php file!)
    "php": "/home/emnik/docker/lamp/html/tuitionweb/vscode-scripts/run_php.sh ${file}",

