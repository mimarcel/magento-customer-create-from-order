# magento-customer-create-from-order

This is a module which allows an administrator to create a customer account from a guest order and connect this order with the account.

## Composer Install
1. Add this repository in composer.json
```
composer config repositories.magento-customer-create-from-order vcs https://github.com/mimarcel/magento-customer-create-from-order
```
2. Install this module in your Composer project
```
composer require mimarcel/magento-customer-create-from-order:dev-default
```

## Fresh Composer Install
1. Install a fresh Magento application using instructions from [Magento Vanilla repository](https://github.com/mimarcel/magento-vanilla)
2. Follow instructions from [Composer Install](#composer-install) section

## Manual Install
1. Copy all files from this module to your Magento project, except:
    * .gitignore
    * README.md
    * composer.json
    * modman
