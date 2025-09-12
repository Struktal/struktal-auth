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
    public static function isLoggedIn(): bool {
        return !empty($_SESSION["userId"]);
    }

    /**
     * Gets the logged-in user, or null if no user is logged in
     * @return GenericUser|null
     */
    public static function getLoggedInUser(): ?GenericUser {
        if(!self::isLoggedIn()) {
            return null;
        }

        $user = (self::$userObjectName)::dao()->getObject([
            "id" => $_SESSION["userId"],
            "emailVerified" => true
        ]);
        if($user instanceof GenericUser) {
            return $user;
        }

        self::logout();
        return null;
    }

    /**
     * Enforces the user to be logged in with a minimum permission level, and redirects the user if they do not meet the requirements
     * @param PermissionLevel $requiredPermissionLevel
     * @param string          $redirect
     * @return GenericUser|null
     */
    public static function enforceLogin(PermissionLevel $requiredPermissionLevel, string $redirect): ?GenericUser {
        $user = self::getLoggedInUser();
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
     * Sets the session entry for the user to be logged in
     * @param GenericUser $user
     * @return void
     */
    public static function login(GenericUser $user): void {
        $_SESSION["userId"] = $user->getId();
    }

    /**
     * Deletes the session entry for the user to be logged out
     * @return void
     */
    public static function logout(): void {
        unset($_SESSION["userId"]);
    }
}
