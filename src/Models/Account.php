<?php

namespace Webkul\ZoomMeeting\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\ZoomMeeting\Contracts\Account as AccountContract;

class Account extends Model implements AccountContract
{
    protected $table = 'zoom_accounts';

    protected $fillable = [
        'zoom_id',
        'name',
        'token',
    ];

    protected $casts = [
        'token' => 'json',
    ];

    /**
     * Get the user that owns the zoom account.
     */
    public function user()
    {
        return $this->belongsTo(AccountProxy::modelClass());
    }
}