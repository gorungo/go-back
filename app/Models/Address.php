<?php

namespace App\Models;

use App\Http\Middleware\LocaleMiddleware;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $table = 'addresses';
    protected $perPage = 60;
    protected $guarded = [];

    public $timestamps = false;

    Public function getFullAddressAttribute() {
        return $this->locality;
    }

    Public function getAddressAttribute() {
        if ($this->localisedAddressDescription != null) {
            return $this->localisedAddressDescription->address;
        } else {
            $addressDescriptions = $this->addressDescriptions()->first();
            if ($addressDescriptions) {
                return $addressDescriptions->address;
            }
        }
        return '';
    }

    Public function getCountryAttribute() {
        if ($this->localisedAddressDescription != null) {
            return $this->localisedAddressDescription->country;
        } else {
            $addressDescriptions = $this->addressDescriptions()->first();
            if ($addressDescriptions) {
                return $addressDescriptions->country;
            }
        }
        return '';
    }

    Public function getRegionAttribute() {
        if ($this->localisedAddressDescription != null) {
            return $this->localisedAddressDescription->region;
        } else {
            $addressDescriptions = $this->addressDescriptions()->first();
            if ($addressDescriptions) {
                return $addressDescriptions->region;
            }
        }
        return '';
    }

    Public function getCityAttribute() {
        if ($this->localisedAddressDescription != null) {
            return $this->localisedAddressDescription->city;
        } else {
            $addressDescriptions = $this->addressDescriptions()->first();
            if ($addressDescriptions) {
                return $addressDescriptions->city;
            }
        }
        return '';
    }

    public function addressDescriptions() {
        return $this->hasMany( 'App\Models\AddressDescription', 'address_id', 'id' );
    }

    public function localisedAddressDescription() {
        return $this
            ->hasOne( 'App\Models\AddressDescription', 'address_id', 'id' )
            ->where( 'locale_id', LocaleMiddleware::getLocaleId() );
    }

    public function hasLocaleName( $localeName ) {
        return $this
            ->hasOne( 'App\Models\AddressDescription', 'address_id', 'id' )
            ->where( 'locale_id', LocaleMiddleware::getLocaleId( $localeName ) )
            ->count();
    }

    public function hasLocaleId( $localeId ) {
        return $this
            ->hasOne( 'App\Models\AddressDescription', 'address_id', 'id' )
            ->where( 'locale_id', $localeId )->count();

    }

    public function defaultAddressAttributes()
    {
        $lastPlace = Place::lastCreatedPlaceOfAuthUser();

        return [
            // Address
            'postal_code' => $lastPlace && $lastPlace->placeAddress ? $lastPlace->placeAddress->postal_code : '000000',
            'country_code' => $lastPlace && $lastPlace->placeAddress ? $lastPlace->placeAddress->country_code : 'RU',

            // AddressDescription
            'address' => $lastPlace && $lastPlace->placeAddress ? $lastPlace->placeAddress->address : '',
            'country' => $lastPlace && $lastPlace->placeAddress ? $lastPlace->placeAddress->country : '',
            'region' => $lastPlace && $lastPlace->placeAddress ? $lastPlace->placeAddress->region : '',
            'city' => $lastPlace && $lastPlace->placeAddress ? $lastPlace->placeAddress->city : '',
        ];
    }

}
