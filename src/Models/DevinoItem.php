<?php
namespace Trin4ik\DevinoApi\Models;

use Illuminate\Database\Eloquent\Model;

class DevinoItem extends Model
{
    protected $table = 'sms_devino';
    protected $guarded = [];

    protected $attributes = [
        'status'    => 'new',
        'log'       => '[]'
    ];

    protected $casts = [
        'log' => 'array',
    ];

    public function getLogAttribute($value)
    {
        if (empty($value)) {
            return [];
        }
        return json_decode($value, TRUE);
    }


    /**
     * Set that jawn on the way out
     */
    public function setLogAttribute($value) {
        $this->attributes['log'] = json_encode($value);
    }
}