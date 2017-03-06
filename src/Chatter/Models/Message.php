<?php

namespace Chatter\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    public function output()
    {
        return [
            'body'        => $this->body,
            'user_id'     => $this->user_id,
            'user_uri'    => '/users/' . $this->user_id,
            'image_url'   => $this->image_url,
            'message_id'  => $this->id,
            'message_uri' => '/messages/' . $this->id,
            'created_at'  => $this->created_at
        ];
    }
}
