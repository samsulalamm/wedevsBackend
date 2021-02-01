<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public function fileAttach()
    {
        return $this->hasOne('App\Models\FileSystem', 'id', 'file_id');
    }

    public function getImageAttribute()
    {
        if ($this->fileAttach) {
            return $this->fileAttach->FileUrl;
        }
        return null;
    }
}
