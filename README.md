# Cyfer Marketplace
# This is not production ready, use at your own risk.

Guide to setting up Whonix on a dedicated server with no GUI: 

## Features:

- No JavaScript
- No third party requests for tracking, font, or css scripts.
- Multisignature Bitcoin transactions
- Monero/Zcash support
- Vendor/Buyer/Admin accounts
- Listing builder to make creating products easier
- Child products of a listing
- PGP two factor authentication
- Automatically wipes orders and messages older than 30 days
- Dispute and report system
- Admin can easily create and delete categories
- Filter search results
- Adjustable vendor point and level system
- Transaction fee based on vendor level

## Future Support

- SQL Server Always Encrypted
- Monero multisignature
- t-addr Zcash multisignature option
- Improve code base
- Implement full checksums for cryptocurrency addresses and keys instead of regex
- Mobile version
- Better resolution support
- Check if initial order message is encrypted before sending
- 32 bit

## Requirements:

php7.2 php7.2-xml php7.2-mysql php7.2-mbstrings php7.2-json php7.2-dev php7.2-curl php7.2-bcmath mysql5.7 gnugp

How to install gnupg for php: https://secure.php.net/manual/en/gnupg.installation.php

## How to Install:

1. Put content in web directory
2. Point request to public/index.php
3. Navigate to /install/
4. Login as admin and create categories
5. Modify config/rpc.yaml with your rpc info
6. Add a PGP key without a password to the config .key files
