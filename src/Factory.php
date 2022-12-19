<?php

namespace Inertia;

use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\Request;
use Inertia\Config\Services;

class Factory
{
    /**
     * @var array
     */
    protected $sharedProps = [];

    /**
     * @var string
     */
    protected $rootView = 'app';

    /**
     * @var mixed
     */
    protected $version;

    /**
     * @param string $name
     *
     * @return void
     */
    public function setRootView(string $name): void
    {
        $this->rootView = $name;
    }

    /**
     * @param $key
     * @param null $value
     *
     * @return void
     */
    public function share($key, $value = null): void
    {
        if (is_array($key)) {
            $this->sharedProps = array_merge($this->sharedProps, $key);
        } else {
            array_set($this->sharedProps, $key, $value);
        }
    }

    /**
     * @param null $key
     * @return array
     */
    public function getShared($key = null): array
    {
        $sharedProps = $this->sharedProps;

        array_walk_recursive($sharedProps, static function (&$sharedProp) {
            $sharedProp = closure_call($sharedProp);
        });

        if ($key) {
            return array_get($sharedProps, $key);
        }

        return $sharedProps;
    }

    /**
     * @param $version
     *
     * @return void
     */
    public function version($version): void
    {
        $this->version = $version;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return (string) closure_call($this->version);
    }

    /**
     * @param $component
     * @param array $props
     *
     * @return string
     */
    public function render($component, $props = []): string
    {
        return new Response(
            $component,
            array_merge($this->sharedProps, $props),
            $this->rootView,
            $this->getVersion()
        );
    }

    /**
     * @param $page
     *
     * @return string
     */
    public function app($page): string
    {
        return Directive::inertia($page);
    }

    /**
     * @param $uri
     * @return RedirectResponse
     */
    public function redirect($uri): RedirectResponse
    {
        return $this->redirectResponse()->to($uri, 303);
    }

    /**
     * @return RedirectResponse
     */
    public function redirectResponse(): RedirectResponse
    {
        return Services::redirectResponse(null, true);
    }

    public function location($url)
    {
        if ($url instanceof Request) {
            $url = $url->getUri();
        }

        if (Services::request()->hasHeader('X-Inertia')) {
            Services::session()->set('_ci_previous_url', $url);

            return $this->redirectResponse()->setHeader('X-Inertia-Location', $url)
                ->setStatusCode(409);
        }

        return $this->redirect($url);
    }
}
