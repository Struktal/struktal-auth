# Struktal-Auth

This is a PHP library that provides a basic structure for authentication and authorization in Struktal applications.

# Installation

To install this library, include it in your project using Composer:

```bash
composer require struktal/struktal-auth
```

# GenericUser

This library uses the [struktal/struktal-orm](https://github.com/Struktal/struktal-orm) library to provide a `GenericUser` class that can be used as a base class for your own user model object.

Besides the standard `id`, `created`, and `updated` attributes, it also provides the following fields:
- `username` (string) - The username of the user
- `password` (string) - The hashed password of the user
- `email` (string) - The email address of the user (encrypted)
- `emailVerified` (boolean) - Whether the user's email address has been verified
- `permissionLevel` (integer) - The permission level of the user (0 by default, can be any integer value)
- `oneTimePassword` (string) - A one-time password for the user which can be used for email verification or password reset
- `oneTimePasswordExpiration` (DateTimeImmutable) - The expiration date and time of the one-time password

You can create the database table with the following SQL statement:

```sql
CREATE TABLE IF NOT EXISTS `GenericUser` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(255) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `emailVerified` TINYINT(1) NOT NULL DEFAULT 0,
    `permissionLevel` INT NOT NULL,
    `oneTimePassword` VARCHAR(255) NULL,
    `oneTimePasswordExpiration` DATETIME NULL,
    `created` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
    `updated` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
    PRIMARY KEY (`id`),
    UNIQUE KEY (`username`),
    UNIQUE KEY (`email`),
    UNIQUE KEY (`oneTimePassword`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

# Usage

At first, create a custom `PermissionLevel` enum to define the permission levels of your application:

```php
enum PermissionLevel: int implements \struktal\Auth\PermissionLevel {
    // Examples:
    case USER = 0;
    case ADMIN = 1;
    
    public function value(): int {
        return $this->value;
    }
}
```

Then, create a custom user object that extends the `GenericUser` class

```php
class User extends \struktal\ORM\GenericUser {
    #[\struktal\ORM\InheritedType(PermissionLevel::class)]
    public ?PermissionLevel $permissionLevel = null;

    // You can add custom methods or properties here if needed
}
```

and a custom data access object (DAO) that extends the `GenericUserDAO` class

```php
class UserDAO extends \struktal\ORM\GenericUserDAO {
    // You can add custom methods or properties here if needed
}
```

If your user object contains custom fields that have to be set when registering the user, you should also override the `register()` method in the `UserDAO` class.
Take a look at the `GenericUserDAO` class to see how the method is implemented there.

Finally, you can create the database table.
To do so, orientate yourself on the example above for the `GenericUser` table.
You have to extend the SQL code with your custom fields and change the table name to the name of your custom user object (e.g. `User`).

In your application's startup script, you then have to register the custom user object and DAO:

```php
\struktal\Auth\Auth::setUserObjectName(User::class);
```

## Registering new Users

To register a new user, use the `register()` function, which creates a new user object, sets the required fields with the passed parameters, and saves it to the database.

## Login and Logout

To check the account credentials when a user tries to log in, you can use the `login()` method from the `UserDAO` class.
It returns the user object if the credentials are valid or a `LoginError` to describe the error that occurred.

To set the session variable for a user to be logged in, you can use the `login()` method from the `\struktal\Auth\Auth` class.
You have to pass a corresponding `GenericUser` object to the method.

To log out a user, you can use the `logout()` method from the `\struktal\Auth\Auth` class, which deletes the session variable for the logged-in user.

## Required Login

If you want a user to be logged in when accessing a specific page of your application, use the `enforceLogin()` method from the `\struktal\Auth\Auth` class immediately at the beginning of your script.
It takes parameters for the minimum required permission level (as an enum from your `PermissoinLevel` enum) and a redirect URL to which the user will be redirected if they are not logged in or do not have the required permission level.

```php
$auth = new \struktal\Auth\Auth();
$user = $auth->enforceLogin(PermissionLevel::USER, Router->generate("nologin"));
```

## Optional Login

If you only want to retrieve the currently logged-in user without enforcing a login, you can use the `getLoggedInUser()` method from the `\struktal\Auth\Auth` class.

```php
$auth = new \struktal\Auth\Auth();
$user = $auth->getLoggedInUser();
```

# Dependencies

This library uses the following dependencies:

- **ext-pdo**
- **struktal/struktal-orm** - GitHub: [Struktal/sturktal-orm](https://github.com/Struktal/struktal-orm), licensed under [MIT license](https://github.com/Struktal/struktal-orm/blob/main/LICENSE)

# License

This software is licensed under the MIT license.
See the [LICENSE](LICENSE) file for more information.
