<?php

namespace Qween\Geolocation\Services;

use Illuminate\Support\Facades\Cache;

class OpenStreetMapService implements GeolocationService
{
    const SEARCH_PATH  = '/search';
    const REVERSE_PATH = '/reverse';

    protected array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function geocode(string $address): ?array
    {
        $cacheKey = 'geocode_' . md5($address);

        $geocodeFunction = function () use ($address) {
            if ($this->config['rate_limit']['enabled']) {
                sleep($this->config['rate_limit']['seconds']);
            }

            $query = [
                'q'      => $address,
                'format' => 'json',
                'limit'  => 1,
            ];

            $data = $this->getFormattedResponse($query, self::SEARCH_PATH);

            if (empty($data)) {
                return null;
            }

            return [
                'lat' => $data[0]['lat'],
                'lon' => $data[0]['lon'],
            ];
        };

        if ($this->config['cache']['enabled']) {
            return Cache::remember($cacheKey, $this->config['cache']['minutes'], $geocodeFunction);
        }

        return $geocodeFunction();
    }

    public function reverseGeocode($lat, $lon): ?string
    {
        $cacheKey = 'reverse_geocode_' . md5($lat . $lon);

        $reverseGeocodeFunction = function () use ($lat, $lon) {
            if ($this->config['rate_limit']['enabled']) {
                sleep($this->config['rate_limit']['seconds']);
            }

            $query = [
                'lat'    => $lat,
                'lon'    => $lon,
                'format' => 'json',
            ];

            $data = $this->getFormattedResponse($query, self::REVERSE_PATH);

            if (empty($data)) {
                return null;
            }

            return $data['display_name'];
        };

        if ($this->config['cache']['enabled']) {
            return Cache::remember($cacheKey, $this->config['cache']['minutes'], $reverseGeocodeFunction);
        }

        return $reverseGeocodeFunction();
    }

    /**
     * @param array  $query
     * @param string $url
     * @return mixed
     */
    private function getFormattedResponse(array $query, string $url = ''): mixed
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->config['osm']['base_url'] . $url . '?' . http_build_query($query));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->config['osm']['timeout']);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->config['osm']['user_agent']);

        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);
        return $data;
    }
}