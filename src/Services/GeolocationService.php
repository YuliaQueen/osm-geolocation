<?php

namespace Qween\Geolocation\Services;

interface GeolocationService
{
    public function geocode(string $address): ?array;
    public function reverseGeocode(string$lat, string $lon): ?string;
}
