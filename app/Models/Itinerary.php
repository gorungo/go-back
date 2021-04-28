<?php

namespace App\Models;

use Image;
use App\Http\Middleware\LocaleMiddleware;
use App\Http\Requests\Photo\UploadPhoto;
use App\Http\Requests\Place\StorePlace;
use App\Models\Traits\Imageble;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Support\Str;

class Itinerary extends Model
{
    use SoftDeletes, Imageble, Sortable;

    protected $table = 'itineraries';

    protected $perPage = 60;

    protected $guarded = [];

    protected $with = ['localisedItineraryDescription'];

    public $timestamps = false;

    public $defaultTmb = 'images/interface/placeholders/idea.png';

    public function itineraryIdea()
    {
        return $this->belongsTo('App\Models\Idea');
    }

    public function itineraryDescriptions()
    {
        return $this->hasMany('App\Models\ItineraryDescription');
    }

    public function localisedItineraryDescription()
    {
        return $this
            ->hasOne('App\Models\ItineraryDescription')
            ->where('locale_id', LocaleMiddleware::getLocaleId());
    }


    Public function getTitleAttribute()
    {
        if ($this->localisedItineraryDescription != null) {
            return $this->localisedItineraryDescription->title;
        } else {
            $itineraryDescription = $this->itineraryDescriptions()->first();
            if ($itineraryDescription) {
                return $itineraryDescription->title;
            }
        }

    }

    Public function getDescriptionAttribute()
    {
        if ($this->localisedItineraryDescription != null) {
            return $this->localisedItineraryDescription->description;
        } else {
            $ideaDescription = $this->itineraryDescriptions()->first();
            if ($ideaDescription) {
                return $ideaDescription->description;
            }
        }

    }

    Public function getWhatIncludedAttribute()
    {
        if ($this->localisedItineraryDescription != null) {
            return $this->localisedItineraryDescription->what_included;
        } else {
            $ideaDescription = $this->itineraryDescriptions()->first();
            if ($ideaDescription) {
                return $ideaDescription->what_included;
            }
        }

    }

    Public function getWillVisitAttribute()
    {
        if ($this->localisedItineraryDescription != null) {
            return $this->localisedItineraryDescription->will_visit;
        } else {
            $ideaDescription = $this->itineraryDescriptions()->first();
            if ($ideaDescription) {
                return $ideaDescription->will_visit;
            }
        }

    }

    public function getLocaleAttribute()
    {
        return $this->localisedItineraryDescription ? $this->localisedItineraryDescription->locale : null;
    }

    public function uploadPhoto(UploadPhoto $request)
    {
        $newPhoto = null;

        $image = $request->file('image');
        $rnd = Str::random(5);

        $newFileNameBig = mb_strtolower('img' . $rnd . '.' . $image->getClientOriginalExtension());
        $newFileNameSmall = mb_strtolower('img' . $rnd . '_sml.' . $image->getClientOriginalExtension());

        $uploadPathBig = 'images/itinerary/' . $this->id . '/' . $newFileNameBig;
        $uploadPathSmall = 'images/itinerary/' . $this->id . '/' . $newFileNameSmall;

        // сохраняем изображение на диске в нужной папке, если нужно ресайзим

        if ($request->hasFile('image')) {

            try {

                array_map( 'unlink', glob( public_path( 'storage/images/itinerary/' . $this->id ) . "/img*.*" ) );


                $image = $request->file('image');
                $img = Image::make($image->getRealPath())->orientate();

                // сохраняем аватарку 200 на 200

                $img->fit( 800, 600 );
                $img->stream();
                Storage::disk( 'public' )->put( $uploadPathBig, $img, 'public' );

                // сохраняем аватарку 50 на 50

                $img->fit( 400, 300 );
                $img->stream();
                Storage::disk( 'public' )->put( $uploadPathSmall, $img, 'public' );

                if(file_exists('storage/'. $uploadPathBig) && file_exists('storage/'. $uploadPathSmall)){
                    $this->thmb_file_name = $newFileNameBig;
                    $this->save();
                }

            } catch (\Exception $e){
                Log::error($e);
            }

        }


        return $this->FullTmbImgPath;

    }


}
