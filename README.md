Mailcow PHP API Client
=======================

A PHP 7.2+ library for interacting with the Mailcow API.

[![Latest Stable Version](http://poser.pugx.org/hostmyservers/mailcow-api/v)](https://packagist.org/packages/hostmyservers/mailcow-api) 
[![Total Downloads](http://poser.pugx.org/hostmyservers/mailcow-api/downloads)](https://packagist.org/packages/hostmyservers/mailcow-api) 
[![License](http://poser.pugx.org/hostmyservers/mailcow-api/license)](https://packagist.org/packages/hostmyservers/mailcow-api)
[![PHP Version Require](http://poser.pugx.org/hostmyservers/mailcow-api/require/php)](https://packagist.org/packages/hostmyservers/mailcow-api)

## Installation

Install via Composer:

```bash
composer require hostmyservers/mailcow-api
```

## Basic Usage

```php
<?php
require_once 'vendor/autoload.php';

use HostMyServers\MailCowAPI;

// Initialize the client
$client = new MailCowAPI('https://mail.example.com', 'your-api-token');

// Get all domains
$domains = $client->domains()->getAll();

// Create a new domain
$client->domains()->create(
    'example.com',
    'My Domain',
    10,  // aliases
    5,   // mailboxes
    1000, // defquota
    2000, // maxquota
    5000  // quota
);

// Create a mailbox
$client->mailBoxes()->create(
    'user',
    'example.com',
    'John Doe',
    'password123'
);

// Create an alias
$client->aliases()->create(
    'alias@example.com',
    'destination@example.com'
);
```

## Available Features

### Domains
```php
// Get all domains
$client->domains()->getAll();

// Get specific domain
$client->domains()->get('example.com');

// Create domain
$client->domains()->create('example.com', 'Description');

// Update domain
$client->domains()->update('example.com', ['description' => 'New description']);

// Delete domain
$client->domains()->delete('example.com');

// Enable/disable domain
$client->domains()->setActive('example.com', true);
```

### Mailboxes
```php
// Get all mailboxes
$client->mailBoxes()->getAll();

// Create mailbox
$client->mailBoxes()->create('user', 'domain.com', 'User Name', 'password');

// Update mailbox
$client->mailBoxes()->update('user@domain.com', ['name' => 'New Name']);

// Delete mailbox
$client->mailBoxes()->delete('user@domain.com');

// Update spam score
$client->mailBoxes()->updateSpamScore('user@domain.com', '5.0');
```

### Aliases
```php
// Get all aliases
$client->aliases()->getAll();

// Create alias
$client->aliases()->create('alias@domain.com', 'destination@domain.com');

// Update alias
$client->aliases()->update('alias_id', ['goto' => 'new@domain.com']);

// Delete alias
$client->aliases()->delete('alias_id');
```

### DKIM
```php
// Get DKIM info
$client->dkim()->getDkim('domain.com');

// Generate DKIM
$client->dkim()->generate('domain.com', 'selector', 2048);

// Delete DKIM
$client->dkim()->delete('domain.com');
```

### Anti-Spam
```php
// Get whitelist policy
$client->antiSpam()->getWhitelistPolicy('domain.com');

// Get blacklist policy
$client->antiSpam()->getBlacklistPolicy('domain.com');

// Add policy
$client->antiSpam()->addPolicy('domain.com', 'whitelist', 'sender@domain.com');

// Delete policy
$client->antiSpam()->deletePolicy(['policy_id']);
```

## Requirements

- PHP 7.2 or higher
- Guzzle HTTP Client
- PSR-7 implementation

## License

This project is licensed under the MIT License - see the LICENSE file for details.