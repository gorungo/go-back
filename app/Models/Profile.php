<?php

namespace App\Models;

use App\Http\Requests\Photo\UploadProfilePhoto;
use App\Http\Requests\Profile\Store;
use App\Models\Traits\Hashable;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Image;

class Profile extends Model
{

    use Hashable;

    const hidLength = 20;
    protected $table = 'profiles';
    protected $fillable = ['name', 'site', 'sex', 'description', 'phone'];

    /**
     * Create default profile for user
     * @param  User  $user
     * @return $profile
     */
    public static function createFor(User $user)
    {
        $profile = $user->profile()->create([
            'name' => $user->name,
        ]);

        $profile->save();
        $user->save();

        return $profile;
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }


    public function getTmbImgPathAttribute()
    {
        $src = null;

        if ($this->thmb_file_name && Storage::disk('images')->exists( mb_strtolower(class_basename(get_class($this))) . '/' . $this->id . '/' . htmlspecialchars(strip_tags($this->thmb_file_name)))) {
            $src = Storage::disk('images')->url(class_basename(mb_strtolower(get_class($this))) . '/' . $this->id . '/' . htmlspecialchars(strip_tags($this->thmb_file_name)));
        };

        return $src;
    }

    public function getFullTmbImgPathAttribute()
    {
        return $this->tmbImgPath ? asset($this->tmbImgPath) : null;
    }

    public function getImageUrlAttribute()
    {
        return $this->tmbImgPath ? asset($this->tmbImgPath) : null;
    }

    public function updateAndSync(Store $request)
    {
        $this->update($request->input('data.attributes'));
        return $this;
    }

    public function uploadPhoto(UploadProfilePhoto $request)
    {
        $newPhoto = null;

        $image = $request->file('image');
        $rnd = str_random(5);

        $newFileNameBig = mb_strtolower('img'.$rnd.'.'.$image->getClientOriginalExtension());
        $newFileNameSmall = mb_strtolower('img'.$rnd.'_sml.'.$image->getClientOriginalExtension());

        $uploadPathBig = 'images/profile/'.$this->id.'/'.$newFileNameBig;
        $uploadPathSmall = 'images/profile/'.$this->id.'/'.$newFileNameSmall;

        // сохраняем изображение на диске в нужной папке, если нужно ресайзим

        if ($request->hasFile('image')) {

            try {

                array_map('unlink', glob(public_path('storage/images/profile/'.$this->id)."/img*.*"));


                $image = $request->file('image');
                $img = Image::make($image->getRealPath())->orientate();

                // сохраняем аватарку 200 на 200

                $img->fit(200, 200);
                $img->stream();
                Storage::disk('public')->put($uploadPathBig, $img, 'public');

                // сохраняем аватарку 50 на 50

                $img->fit(50, 50);
                $img->stream();
                Storage::disk('public')->put($uploadPathSmall, $img, 'public');

                if (file_exists('storage/'.$uploadPathBig) && file_exists('storage/'.$uploadPathSmall)) {
                    $this->thmb_file_name = $newFileNameBig;
                    $this->save();
                }

            } catch (\Exception $e) {
                Log::error($e);
            }

        }


        return $this->imageUrl;

    }

}
