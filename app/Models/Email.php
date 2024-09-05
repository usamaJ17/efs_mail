<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Email extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'subject',
        'from',
        'is_read',
        'is_sent',
        'is_important',
        'is_starred',
        'owner_id',
        'group_id'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'is_sent' => 'boolean',
        'is_important' => 'boolean',
        'is_starred' => 'boolean',
        'created_at' => 'date:M d',
    ];

    protected $appends = ['has_attachment'];


    public function from()
    {
        return $this->belongsTo(User::class, 'from');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function receivers()
    {
        return $this->hasMany(EmailReceiver::class);
    }

    protected function hasAttachment(): Attribute
    {
        return Attribute::make(
            get: function () {
                foreach ($this->messages()->get() as $message) {
                    if ($message->has_attachment) {
                        return true;
                    }
                }
                return false;
            }
        );
    }
}
