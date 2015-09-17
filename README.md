# GitLab Auto Merger

## Installation

Simply clone repo to your server
```
mkdir /var/www/merger
cd /var/www/merger
git clone git@github.com:ricco24/gitlab-auto-merger.git .

// Rename tmp config
mv app/config/config.local.neon.tmp app/config/config.local.neon

// Create temp and log folders
mkdir temp
mkdir log

// And install composer dependencies
composer update
```

After that make this folder accessible from internet.
Document root is ```MERGER_FOLDER/www```

Now configure comment webhook in GitLab for your project. As URL use base merger url (e.g. http://merger.mywebsite.com)

## Config

Now after installation, you can setup configuration in ```app/config/config.local.neon``` file.