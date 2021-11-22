<?php

namespace Webkul\ZoomMeeting\Repositories;

use Webkul\Core\Eloquent\Repository;

class UserRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Webkul\ZoomMeeting\Contracts\User';
    }
}