# Magento 2 Webhook Extension for Cyclr

This extension brings Cyclr webhook features to Magento 2.

## Installation Guide

Copy the extension to your Magento instance, e.g.
```
cp -r ./ /var/www/html/app/code/Cyclr/Webhook/
```

### Fix file permissions (if needed)

Before running the upgrade command, ensure Magento has proper write permissions. This is especially important on Bitnami instances or managed hosting environments:

```bash
# Navigate to your Magento root directory
cd /path/to/magento

# Fix permissions for var/ directories and config files
sudo chown -R bitnami:bitnami var/ generated/ pub/static/ pub/media/ app/etc/
sudo chmod -R 755 var/ generated/ pub/static/ pub/media/
sudo chmod -R 644 app/etc/*.php
sudo chmod 755 app/etc/
```

**Note:** Replace `bitnami:bitnami` with the appropriate user:group for your server setup:
- Bitnami instances: `bitnami:bitnami`
- Standard Apache: `www-data:www-data` or `apache:apache`  
- Standard Nginx: `nginx:nginx` or `www-data:www-data`

### Update database schema
```
php bin/magento setup:upgrade
```

Clean cache (Optional)
```
php bin/magento cache:clean
```

## User Guide

All operations are carried out by the Magento 2 Webhooks Connector in your [Cyclr](https://cyclr.com/) account.

The webhooks will be processed once per minute. If you wish to change this setting, you can visit 
_System->System Settings->Cron (Scheduled Tasks)->webhooks_