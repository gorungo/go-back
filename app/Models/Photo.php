<?php

namespace App\Models;

use App\Http\Requests\Photo\setMainPhoto;
use App\Http\Requests\Photo\UploadPhoto;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Image;

class Photo extends Model
{

    public $modelName;
    protected $table = 'photos';
    protected $thmbMaxWidth = 400;
    protected $maxImageWidth = 2000;
    protected $maxFileSize = 15;
    protected $fillable = [
        'img_name',
        'item_id',
        'item_type',
    ];
    protected $appends = ['imageUrl'];

    public function __construct(array $attributes = [])
    {
        if (isset($this->item_type)) {
            $this->modelName = $this->item_type;
        }
        parent::__construct($attributes);
    }

    public function item()
    {
        return $this->morphTo();
    }

    public function getAbsoluteURLAttribute()
    {
        return asset($this->storagePath);
    }

    public function getRelativeURLAttribute()
    {
        return 'storage/images/'.mb_strtolower($this->item_type).'/'.$this->item_id.'/'.$this->img_name;
    }

    public function getImageUrlAttribute()
    {
        return asset($this->storagePath);
    }

    public function getImage1xUrlAttribute()
    {
        return asset($this->storage1xPath);
    }

    public function getImage2xUrlAttribute()
    {
        return asset($this->storage2xPath);
    }

    /**
     * Путь к изобаржению в storage
     */

    public function getStoragePathAttribute()
    {
        return Storage::disk('images')->url(mb_strtolower($this->item_type).'/'.$this->item_id.'/'.$this->img_name);
    }

    public function getStorage2xPathAttribute()
    {
        if( $this->img_name && strpos($this->img_name, '.') > -1){
            list($name, $ext) = explode('.', $this->img_name);
            $fileName2x = $name . 'x2.' . $ext;
            if (Storage::disk('images')->exists( mb_strtolower($this->item_type).'/'.$this->item_id.'/' . htmlspecialchars(strip_tags($fileName2x)))) {
                return Storage::disk('images')->url(mb_strtolower($this->item_type).'/'.$this->item_id.'/'.$fileName2x);
            }
        }
        return null;
    }
    public function getStorage1xPathAttribute()
    {
        if( $this->img_name && strpos($this->img_name, '.') > -1){
            list($name, $ext) = explode('.', $this->img_name);
            $fileName1x = $name . 'x1.' . $ext;
            if (Storage::disk('images')->exists( mb_strtolower($this->item_type).'/'.$this->item_id.'/' . htmlspecialchars(strip_tags($fileName1x)))) {
                return Storage::disk('images')->url(mb_strtolower($this->item_type).'/'.$this->item_id.'/'.$fileName1x);
            }
        }
        return null;
    }

    public function createAndStore(UploadPhoto $request, Model $model, $setMain = false)
    {

        $newPhoto = null;

        if ($model->id !== null) {

            $this->modelName = ucfirst(explode('\\', get_class($model))[2]);

            $image = $request->file('image');
            $newFileName = mb_strtolower('img'.Str::random(5).'.'.$image->getClientOriginalExtension());
            $uploadPath = $this->getStoreDirectoryUrl($model->id).'/';

            // сохраняем изображение на диске в нужной папке, если нужно ресайзим

            if ($this->uploadImage($request, $uploadPath, $newFileName)) {

                $photoStoreData = [
                    'item_id' => $model->id,
                    'item_type' => $this->modelName,
                    'img_name' => $newFileName,
                ];

                $newPhoto = self::create($photoStoreData);
            }
        }

        return $newPhoto;
    }

    /**
     * Путь к директории с изображениями
     * @return string
     */

    public function getStoreDirectoryUrl($itemId = null)
    {
        if ($this->item_id) {
            $itemId = $this->item_id;
            return mb_strtolower($this->item_type).'/'.$itemId;
        } else {
            return mb_strtolower($this->modelName).'/'.$itemId;
        }

    }

    /**
     * Resizing and saving uploaded img
     *
     * @param  Request  $request
     * @param  string  $uploadPath
     * @return bool
     */

    public function uploadImage(Request $request, string $uploadPath, string $fileName): bool
    {
        $minWidth = 300;

        if ($request->hasFile('image')) {

            try {

                $image = $request->file('image');
                $img = Image::make($image->getRealPath());

                if ($img->width() > $this->maxImageWidth) {
                    $img->resize($this->maxImageWidth, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                }
                $img->stream();
                Storage::disk('images')->put($uploadPath.$fileName, $img, 'public');

                [$name,$ext] = explode('.', $fileName);

                $fileName1x = $name . 'x1' . '.' . $ext;
                $fileName2x = $name . 'x2' . '.' . $ext;

                $img->resize( $minWidth*2, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $img->stream();
                Storage::disk('images')->put($uploadPath.$fileName2x, $img, 'public');

                $img->resize( $minWidth, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $img->stream();
                Storage::disk('images')->put($uploadPath.$fileName1x, $img, 'public');

                return Storage::disk('images')->exists($uploadPath);

            } catch (\Exception $e) {
                Log::error($e);
            }

        }

        return false;


    }

    public function uploadProfileImages(Request $request, $uploadPathBig, $uploadPathSmall)
    {

        if ($request->hasFile('image')) {

            try {

                array_map('unlink', glob(public_path('storage/'.$this->getStoreDirectoryUrl())."/*.*"));

                $image = $request->file('image');
                $img = Image::make($image->getRealPath());

                // сохраняем аватарку 200 на 200

                $img->fit(200, 200);
                $img->stream();
                Storage::disk('public')->put($uploadPathBig, $img, 'public');

                // сохраняем аватарку 50 на 50

                $img->fit(50, 50);
                $img->stream();
                Storage::disk('public')->put($uploadPathSmall, $img, 'public');

                if (file_exists('storage/'.$uploadPathBig) && file_exists('storage/'.$uploadPathSmall)) {
                    return true;
                }

            } catch (\Exception $e) {
                Log::error($e);
            }

        }

        return false;


    }


    /**
     * Making photo main
     *
     * @param  SetMainPhoto  $request
     * @return array
     */

    public function setMain($minWidth = 300)
    {
        try {
            $img = Image::make(public_path($this->relativeURL));
            array_map('unlink', glob(public_path('storage/'.$this->getStoreDirectoryUrl())."/tmb*.*"));
            list($txt, $ext) = explode(".", $this->img_name);

            $rnd = Str::random(5);

            $newMainPhotoFileName = 'tmb' . $rnd . '.' . $ext;
            $newMainPhotoFileName2x = 'tmb' . $rnd . 'x2' . '.' . $ext;


            // firstly save x2 image for retina
            $img->resize( $minWidth * 2, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $img->stream();
            Storage::disk('images')->put($this->getStoreDirectoryUrl() . '/' . $newMainPhotoFileName2x, $img, 'public');
            $img->resize( $minWidth, null, function ($constraint) {
                $constraint->aspectRatio();
            });


            // save normal image
            $img->stream();
            Storage::disk('images')->put($this->getStoreDirectoryUrl().'/'.$newMainPhotoFileName, $img, 'public');

            $this->item->thmb_file_name = $newMainPhotoFileName;
            $this->item->save();

            return ['type' => 'ok', 'file_name' => $newMainPhotoFileName];

        } catch (\Exception $e) {

            Log::error($e);

        }

        return [
            'type' => 'error',
        ];

    }

    public function uploadMainPhoto($request)
    {
        $newPhoto = null;

        $image = $request->file('image');
        $rnd = Str::random(5);

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

    /**
     * Remove photo
     * @return boolean
     */

    public function deletePhoto()
    {
        if(Storage::disk('images')->exists($this->getStoreDirectoryUrl() . '/' . $this->img_name)){
            Storage::disk('images')->delete($this->getStoreDirectoryUrl() . '/' . $this->img_name);
        }
        return !Storage::disk('images')->exists($this->getStoreDirectoryUrl() . '/' . $this->img_name);
    }

    public function scopeIsActive($query)
    {
        return $query->where('active', '=', '1');
    }

}
