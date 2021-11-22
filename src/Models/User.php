<?php

namespace Webkul\ZoomMeeting\Models;

use Webkul\User\Models\User as BaseUser;
use Webkul\ZoomMeeting\Contracts\User as UserContract;

class User extends BaseUser implements UserContract
{
    /**
     * Get the zoom accounts.
     */
    public function accounts()
    {
        return $this->hasMany(AccountProxy::modelClass());
    }
}