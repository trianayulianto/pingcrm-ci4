<?php

namespace Inertia\Ssr;

use Exception;
use CodeIgniter\Config\Factories;
use CodeIgniter\Config\Services;

class HttpGateway implements Gateway
{
    protected $config;

    public function __construct()
    {
        $this->config = (array) Factories::config('Inertia');
    }

    /**
     * Dispatch the Inertia page to the Server Side Rendering engine.
     *
     * @param  array  $page
     * @return Response|null
     */
    public function dispatch(array $page): ?Response
    {
        if (! $this->getConfig('ssr.enabled', false)) {
            return null;
        }

        $url = $this->getConfig('ssr.url', 'http://127.0.0.1:13714/render');

        try {
            $client = Services::curlrequest();
            $response = $client->setJSON($page)->post($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ]
            ]);
            $response = json_decode($response->getBody(), true);
        } catch (Exception $e) {
            return null;
        }

        if (is_null($response)) {
            return null;
        }

        return new Response(
            implode("\n", $response['head']),
            $response['body']
        );
    }

    /**
     * Get the guard configuration.
     *
     * @param  string  $name
     */
    protected function getConfig($name, $default = null)
    {
        return array_get($this->config, $name, $default);
    }
}
