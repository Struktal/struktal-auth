<?php

namespace struktal\ORM;

use \DateTimeImmutable;
use \struktal\Auth\PermissionLevel;

class GenericUser extends GenericEntity {
    public string $username = "";
    public string $password = "";
    public string $email = "";
    public bool $emailVerified = false;
    public ?PermissionLevel $permissionLevel = null;
    public ?string $oneTimePassword = null;
    public ?DateTimeImmutable $oneTimePasswordExpiration = null;

    /**
     * Returns the user's username
     * @return string
     */
    public function getUsername(): string {
        return $this->username;
    }

    /**
     * Sets the user's username
     * @param string $username
     */
    public function setUsername(string $username): void {
        $this->username = $username;
    }

    /**
     * Returns the user's password hash
     * @return string
     */
    public function getPassword(): string {
        return $this->password;
    }

    /**
     * Sets the user's password
     * The passed password will be hashed with the default PHP hashing algorithm
     * @param string $password
     */
    public function setPassword(string $password): void {
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Returns the user's E-Mail
     * @return string
     */
    public function getEmail(): string {
        return $this->email;
    }

    /**
     * Sets the user's E-Mail
     * The E-Mail will be converted to lowercase letters
     * @param string $email
     */
    public function setEmail(string $email): void {
        $this->email = strtolower($email);
    }

    /**
     * Returns the user's E-Mail verification status
     * @return bool
     */
    public function getEmailVerified(): bool {
        return $this->emailVerified;
    }

    /**
     * Sets the user's E-Mail verification status
     * @param bool $emailVerified
     */
    public function setEmailVerified(bool $emailVerified): void {
        $this->emailVerified = $emailVerified;
    }

    /**
     * Returns the user's permission level
     * @return ?PermissionLevel
     */
    public function getPermissionLevel(): ?PermissionLevel {
        return $this->permissionLevel;
    }

    /**
     * Sets the user's permission level
     * @param ?PermissionLevel $permissionLevel
     */
    public function setPermissionLevel(?PermissionLevel $permissionLevel): void {
        $this->permissionLevel = $permissionLevel;
    }

    /**
     * Returns the user's one-time-password
     * @return string|null
     */
    public function getOneTimePassword(): ?string {
        return $this->oneTimePassword;
    }

    /**
     * Sets the user's one-time-password
     * The one-time-password will be hashed with the default PHP hashing algorithm
     * @param string|null $oneTimePassword
     */
    public function setOneTimePassword(?string $oneTimePassword): void {
        if($oneTimePassword !== null) {
            $this->oneTimePassword = password_hash($oneTimePassword, PASSWORD_DEFAULT);
        } else {
            $this->oneTimePassword = null;
        }
    }

    /**
     * Returns the user's one-time-password expiration date
     * @return DateTimeImmutable|null
     */
    public function getOneTimePasswordExpiration(): ?DateTimeImmutable {
        return $this->oneTimePasswordExpiration;
    }

    /**
     * Sets the user's one-time-password expiration date
     * @param DateTimeImmutable|null $oneTimePasswordExpiration
     */
    public function setOneTimePasswordExpiration(?DateTimeImmutable $oneTimePasswordExpiration): void {
        $this->oneTimePasswordExpiration = $oneTimePasswordExpiration;
    }
}
