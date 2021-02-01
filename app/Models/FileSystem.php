<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Psy\Util\Str;

class FileSystem extends Model
{
    public function upload($to,$file){

        try {
            $file_name = time().$file->getClientOriginalName();
            $filename = pathinfo($file_name, PATHINFO_FILENAME);
            $extension = pathinfo($file_name, PATHINFO_EXTENSION);
            $file_name = preg_replace('/\s+/', '', $filename);
            $file_name=  preg_replace('/[^A-Za-z0-9\-]/', '', $file_name);
            $file_name = $file_name.'.'.$extension;
            Storage::disk('public') ->put('files/'.$to.'/'.$file_name, file_get_contents($file -> getRealPath()));
            $store = new FileSystem();
            $store->folder_name = 'files/'.$to;
            $store->file_name = $file_name;
            $store->save();
            return $store;
        }catch (\Exception $exception){
            $str = new \stdClass();
            $str->code = 100;
            return $str;
        }

    }

    public function  getFileUrlAttribute()
    {
        $destinationPath = $this->folder_name.'/'.$this->file_name;
        if(Storage::disk('public')->exists($this->FileDir)){
            return asset('application/storage/app/public/'.$destinationPath);
        }
        return null;
    }

    public function  getFileDirAttribute()
    {
        $destinationPath = $this->folder_name.'/'.$this->file_name;
        return $destinationPath;
    }
}
