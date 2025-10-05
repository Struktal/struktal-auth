<?php

namespace struktal\Auth;

use \struktal\ORM\GenericUser;

class Auth {
    private static string $userObjectName = "\struktal\ORM\GenericUser";

    /**
     * Sets the user object name to be used for authentication
     * @param string $userObjectName
     * throws \InvalidArgumentException if the class does not exist or is not a subclass of GenericUser
     */
    public static function setUserObjectName(string $userObjectName): void {
        if(!class_exists($userObjectName)) {
            throw new \InvalidArgumentException("The user object name must be a valid class name.");
        }

        if(!is_subclass_of($userObjectName, GenericUser::class)) {
            throw new \InvalidArgumentException("The user object name must be a subclass of " . GenericUser::class);
        }

        self::$userObjectName = $userObjectName;
    }

    /**
     * Checks whether the user is logged in with the session variable
     * @return bool
     */
    public function isLoggedIn(): bool {
        return !empty($_SESSION["userId"]);
    }

    /**
     * Gets the logged-in user, or null if no user is logged in
     * @return GenericUser|null
     */
    public function getLoggedInUser(): ?GenericUser {
        if(!$this->isLoggedIn()) {
            return null;
        }

        $user = (self::$userObjectName)::dao()->getObject([
            "id" => $_SESSION["userId"],
            "emailVerified" => true
        ]);
        if($user instanceof GenericUser) {
            return $user;
        }

        $this->sessionLogout();
        return null;
    }

    /**
     * Enforces the user to be logged in with a minimum permission level, and redirects the user if they do not meet the requirements
     * @param PermissionLevel $requiredPermissionLevel
     * @param string          $redirect
     * @return GenericUser|null
     * @deprecated Use requireLogin() instead (enhanced naming consistency)
     */
    public function enforceLogin(PermissionLevel $requiredPermissionLevel, string $redirect): ?GenericUser {
        return $this->requireLogin($requiredPermissionLevel, $redirect);
    }

    /**
     * Enforces the user to be logged in with a minimum permission level, and redirects the user if they do not meet the requirements
     * @param PermissionLevel $requiredPermissionLevel
     * @param string          $redirect
     * @return GenericUser|null
     */
    public function requireLogin(PermissionLevel $requiredPermissionLevel, string $redirect): ?GenericUser {
        $user = $this->getLoggedInUser();
        if(!$user instanceof GenericUser) {
            header("Location: " . $redirect);
            exit;
        }

        if($user->getPermissionLevel()->value() < $requiredPermissionLevel->value()) {
            header("Location: " . $redirect);
            exit;
        }

        return $user;
    }

    /**
     * Checks the provided login credentials and returns the user if they are valid
     * @param string $login          Username or E-Mail
     * @param bool   $loginWithEmail Login performed with E-Mail instead of username
     * @param string $password       Provided password
     * @return GenericUser|LoginError User or error code if login failed
     */
    public function checkLoginCredentials(string $login, bool $loginWithEmail, string $password): GenericUser|LoginError {
        $filter = [];
        if($loginWithEmail) {
            $login = strtolower($login);
            $filter = [ "email" => $login ];
        } else {
            $filter = [ "username" => $login ];
        }

        $user = (self::$userObjectName)::dao()->getObject($filter);

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
     * Sets the session entry for the user to be logged in
     * @param GenericUser $user
     * @return void
     */
    public function sessionLogin(GenericUser $user): void {
        $_SESSION["userId"] = $user->getId();
    }

    /**
     * Deletes the session entry for the user to be logged out
     * @return void
     */
    public function sessionLogout(): void {
        unset($_SESSION["userId"]);
    }
}
