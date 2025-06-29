<?php

namespace struktal\Auth;

enum LoginError {
    case USER_NOT_FOUND;
    case INVALID_PASSWORD;
    case EMAIL_NOT_VERIFIED;
}
