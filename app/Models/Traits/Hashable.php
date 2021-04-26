<?php

namespace App\Models\Traits;

use Illuminate\Support\Facades\Log;
use Hashids\Hashids;

trait Hashable
{
    public function getHidAttribute()
    {
        return $this->getRouteKey() ?? null;
    }

    public function getRouteKey()
    {
        $hashids = new \Hashids\Hashids(config('app.name'), self::hidLength);

        return $hashids->encode($this->getKey());
    }

    public static function hidToId($hid)
    {
        if($hid && strlen($hid) === self::hidLength){
            try{
                $hashids = new Hashids(config('app.name'), self::hidLength);
                return $hashids->decode($hid)[0];
            }catch(\Exception $exception){
                Log::error('Cant find id from hid ', $hid);
            }
        }
        return null;
    }

    public function scopeWhereHid($query, $hid)
    {
        $id = self::hidToId($hid);
        if($id){
            return $query->whereId($id);
        }
        return $query;
    }

    public static function findByHid($hid)
    {
        return self::find(self::hidToId($hid));
    }
}
