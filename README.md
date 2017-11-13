# ezplatform-link-manager

This package provides prototype of Public API and UI for links management in eZ Platform.

## Features

* UI for an overview of all URLs with pagination and filtering
* UI for viewing details of a URL including basic information (address, status, last checked, created,  modified date) and usages listing.
* UI for a URL editing
* Public API for links management 

## Installation

1. Add the following repository to `composer.json`:
```json
"repositories": [
    {
        "type":"vcs",
        "url":"https://github.com/ezsystems/ezplatform-link-manager.git"
    }
]
```

2. Enable the bundle in the kernel:

```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new EzSystems\EzPlatformLinkManagerBundle\EzPlatformLinkManagerBundle(),  
        // ...
    );
    
    // ...
}
```

3. Import routing files 

```yaml
# app/config/routing.yml

_linkManager:
    resource: '@EzPlatformLinkManagerBundle/Resources/config/routing.yml'
```

4. Require the bundle with composer 
```shell
composer require "ezsystems/ezplatform-link-manager" "dev-master"
```

4. Done. You should be able to see "Link management" menu item under the `Admin` tab your administration panel.
