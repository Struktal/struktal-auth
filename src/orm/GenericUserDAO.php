<?php

namespace struktal\ORM;

use \struktal\Auth\LoginError;

class GenericUserDAO extends GenericEntityDAO {
    /**
     * Authentication of a login
     * @param string $login Username or E-Mail
     * @param bool $loginWithEmail Login performed with E-Mail instead of username
     * @param string $password Provided password
     * @return GenericUser|LoginError User or error code if login failed
     */
    public function login(string $login, bool $loginWithEmail, string $password): GenericUser|LoginError {
        if($loginWithEmail) {
            $login = strtolower($login);
            $user = $this->getObject([
                "email" => $login
            ]);
        } else {
            $user = $this->getObject([
                "username" => $login
            ]);
        }

        if($user instanceof GenericUser) {
            if(password_verify($password, $user->getPassword())) {
                if(!$user->getEmailVerified()) {
                    return LoginError::EMAIL_NOT_VERIFIED;
                }

                return $user;
            }
        } else {
            return LoginError::USER_NOT_FOUND;
        }

        return LoginError::INVALID_PASSWORD;
    }

    /**
     * Registers a new user
     * @param string $username Username
     * @param string $password Password
     * @param string $email E-Mail
     * @param int $permissionLevel Permission level
     * @param string $oneTimePassword One-time-password for E-Mail verification
     * @return GenericUser Newly created user
     */
    public function register(string $username, string $password, string $email, int $permissionLevel, string $oneTimePassword): GenericUser {
        $user = new ($this->getClassInstance())();
        $user->setUsername($username);
        $user->setPassword($password);
        $user->setEmail($email);
        $user->setEmailVerified(false);
        $user->setPermissionLevel($permissionLevel);
        $user->setOneTimePassword($oneTimePassword);
        $user->setOneTimePasswordExpiration(null);
        $this->save($user);

        return $user;
    }

    /**
     * Returns a unique one-time-password
     * @return string
     */
    public function generateOneTimePassword(): string {
        $chars = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $oneTimePassword = "";
        for($i = 0; $i < 127; $i++) {
            $oneTimePassword .= $chars[rand(0, strlen($chars) - 1)];
        }

        // Check whether the generated one-time-password already exists
        if(sizeof($this->getObjects(["oneTimePassword" => $oneTimePassword])) > 0) {
            $oneTimePassword = $this->generateOneTimePassword();
        }

        return $oneTimePassword;
    }
}
