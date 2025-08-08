<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\CmsController;
use App\Services\Authenticate;

class AuthController extends CmsController
{
    use Authenticate;
}
