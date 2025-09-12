<?php

namespace struktal\Auth;

interface PermissionLevel extends \struktal\ORM\ORMEnum {
    public function value(): int;
}
