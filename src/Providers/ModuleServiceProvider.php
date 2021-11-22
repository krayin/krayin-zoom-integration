<?php

namespace Webkul\ZoomMeeting\Providers;

use Webkul\Core\Providers\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \Webkul\ZoomMeeting\Models\Account::class,
        \Webkul\ZoomMeeting\Models\User::class,
    ];
}