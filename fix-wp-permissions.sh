#!/bin/bash

sudo chown -R bob:www-data ./*
sudo find . -type d -exec chmod 755 {} \;
sudo find . -type f -exec chmod 644 {} \;
sudo chown -R www-data:www-data ./wp-content/uploads
