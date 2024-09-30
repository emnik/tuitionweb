# Python script to update icons from font-awesome version 3 to version 6 automatically!
# I have to setup the icon mappings below!

import os

# Define the mapping of old to new class names
icon_mapping = {
    "icon-camera": "fa-solid fa-camera",
    # Add more mappings here
}

# Path to your views directory
views_dir = "path/to/your/views"

# Function to update icon classes in a file
def update_icons_in_file(file_path):
    with open(file_path, 'r') as file:
        content = file.read()
    for old_icon, new_icon in icon_mapping.items():
        content = content.replace(old_icon, new_icon)
    with open(file_path, 'w') as file:
        file.write(content)

# Iterate over all files in the views directory
for root, dirs, files in os.walk(views_dir):
    for file in files:
        if file.endswith(".php"):  # Assuming your views are PHP files
            update_icons_in_file(os.path.join(root, file))

print("Icon classes updated successfully!")

