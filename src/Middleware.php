<?php

namespace Inertia;

use CodeIgniter\HTTP\Request;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     *
     * @param  Request  $request
     * @return string|null
     */
    public function version(Request $request)
    {
        if (file_exists($manifest = './mix-manifest.json')) {
            return md5_file($manifest);
        }

        if (file_exists($manifest = './build/manifest.json')) {
            return md5_file($manifest);
        }

        return null;
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @param  Request  $request
     * @return array
     */
    public function share(Request $request)
    {
        return [
            'errors' => function () use ($request) {
                return $this->resolveValidationErrors($request);
            },
        ];
    }

    /**
     * Sets the root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @param  Request  $request
     * @return string
     */
    public function rootView(Request $request)
    {
        return $this->rootView;
    }

    /**
     * Handle the incoming request.
     *
     * @param  RequestInterface  $request
     * @param null $arguments
     *
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $request = Services::request();

        Inertia::version(function () use ($request) {
            return $this->version($request);
        });

        Inertia::share($this->share($request));

        Inertia::setRootView($this->rootView($request));
    }

    /**
     * Handle the incoming request.
     *
     * @param  RequestInterface  $request
     * @param  ResponseInterface  $response
     * @param null $arguments
     *
     * @return mixed
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        $request = Services::request();
        Services::response()->setHeader('Vary', 'X-Inertia');

        if (! $request->header('X-Inertia')) {
            return;
        }

        if ($request->getMethod(true) === 'GET' &&
            $request->header('X-Inertia-Version', '') !== Inertia::getVersion()
        ) {
            $response = $this->onVersionChange($request, $response);
        }

        if ($response->getStatusCode() === 200 &&
            empty($response->sendBody())
        ) {
            $response = $this->onEmptyResponse($request, $response);
        }

        if ($response->getStatusCode() === 302 &&
            in_array($request->getMethod(true), ['PUT', 'PATCH', 'DELETE'])
        ) {
            $response->setStatusCode(303);
        }
    }

    /**
     * Determines what to do when an Inertia action returned with no response.
     * By default, we'll redirect the user back to where they came from.
     *
     * @param  Request  $request
     * @param  ResponseInterface  $response
     * @return ResponseInterface
     */
    public function onEmptyResponse(Request $request, ResponseInterface $response): ResponseInterface
    {
        return Inertia::redirectResponse()->back();
    }

    /**
     * In the event that the assets change, initiate a
     * client-side location visit to force an update.
     *
     * @param  Request  $request
     * @param  ResponseInterface  $response
     * @return ResponseInterface
     */
    public function onVersionChange(Request $request, ResponseInterface $response)
    {
        return Inertia::location((string) $request->getUri());
    }

    /**
     * Resolves and prepares validation errors in such
     * a way that they are easier to use client-side.
     *
     * @param  Request  $request
     * @return object
     */
    public function resolveValidationErrors(Request $request)
    {
        Services::session();

        $errors = Services::validation()->getErrors();

        if (! $errors) {
            return (object) [];
        }

        if ($request->header('x-inertia-error-bag')) {
            return (object) [$request->header('x-inertia-error-bag') => $errors];
        }

        return (object) $errors;
    }
}
