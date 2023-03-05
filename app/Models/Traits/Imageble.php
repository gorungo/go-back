<?php

namespace App\Models\Traits;

use App\Models\Photo;
use App\Http\Requests\Photo\UploadPhoto;
use Illuminate\Support\Facades\Storage;


trait Imageble
{

    public function photos()
    {
        return $this->morphMany(Photo::class, 'item');
    }

    public function uploadPhoto(UploadPhoto $request)
    {
        $photo = New Photo();
        return $photo->createAndStore($request, $this);
    }

    public function uploadMainPhoto(UploadPhoto $request)
    {
        $photo = New Photo();
        return $photo->createAndStore($request, $this, true);
    }

    /**
     * Get path to tmb img of item
     * @return string
     */
    public function getTmbImgPathAttribute()
    {
        $src = null;

        if ($this->thmb_file_name && Storage::disk('images')->exists( mb_strtolower(class_basename(get_class($this))) . '/' . $this->id . '/' . htmlspecialchars(strip_tags($this->thmb_file_name)))) {
            $src = Storage::disk('images')->url(class_basename(mb_strtolower(get_class($this))) . '/' . $this->id . '/' . htmlspecialchars(strip_tags($this->thmb_file_name)));
        };

        return $src;
    }

    public function getTmbImg2xPathAttribute()
    {
        $src = $this->tmbImgPath;

        if ($this->id && $this->thmb_file_name && strpos($this->thmb_file_name, '.') > -1) {
            list($name, $ext) = explode('.', $this->thmb_file_name);
            $fileName2x = $name . 'x2.' . $ext;

            if (Storage::disk('images')->exists( mb_strtolower(class_basename(get_class($this))) . '/' . $this->id . '/' . htmlspecialchars(strip_tags($fileName2x)))) {
                $src = Storage::disk('images')->url(class_basename(mb_strtolower(get_class($this))) . '/' . $this->id . '/' . htmlspecialchars(strip_tags($fileName2x)));
            };
        }

        return $src;
    }

    public function getFullTmbImgPathAttribute()
    {
        return $this->tmbImgPath ? asset($this->tmbImgPath) : null;
    }

    public function getImageUrlAttribute()
    {
        return $this->tmbImgPath ?: null;
    }

    public function getImage2xUrlAttribute()
    {
        return $this->tmbImg2xPath ?: null;
    }

}
