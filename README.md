# Magento 2 Webhook Extension for Cyclr

This extension brings Cyclr webhook features to Magento 2.

## Installation Guide

Copy the extension to your Magento instance, e.g.
```
cp -r ./ /var/www/html/app/code/Cyclr/Webhook/
```

Update database schema
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
