<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Message extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $appends = ['has_attachment'];

    protected $fillable = [
        'email_id',
        'content',
        'sender_id',
        'reply_to',
    ];

    protected $casts = [
        'content' => 'array',
        'created_at' => 'datetime:Y-m-d h:i:s A'
    ];

    public function attachments()
    {
        return $this->media()->where('collection_name', 'attachments');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function replyMessage()
    {
        return $this->belongsTo(Message::class, 'reply_to');
    }

    protected function hasAttachment(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->media()->where('collection_name', 'attachments')->count() > 0
        );
    }
}
