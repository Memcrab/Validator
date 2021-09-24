PHP Validator as Composer Library
==========================

Install
--------
```composer require memcrab/validator```

Dependencies
--------
```
- psr/log: "^1.0"
- respect/validation: "^2.1"
```

Usage
--------
```php
use Memcrab\Validator\Validator;
use Respect\Validation\Validator as v;

class Auth extends Validator
{

    public function authorization()
    {
        $this
            ->addBodyRule('email', v::email(), 'Email not valid', 400101)
            ->addBodyRule('password', v::length(8, 100), 'Password should be more than 8 symbols', 400102);
    }
}
```
```php
<?php declare (strict_types = 1);
require_once __DIR__ . "/vendor/autoload.php";

use Memcrab\Validator\Validator;
use Memcrab\Validator\ValidatorException;

try {
  
  // ... getting request GET($getParramethers) and POST($postParramethers)  ...
  
  $Validator = new Auth($getParramethers, $postParramethers);
  $Validator->authorization();
  $Validator->validate();
  
} catch (ValidatorException $error) {
  // handle validation error
}

```

---
**MIT Licensed**
