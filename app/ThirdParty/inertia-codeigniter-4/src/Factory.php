<?php

namespace Inertia;

use Closure;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\Request;
use Inertia\Config\Services;
use Inertia\Support\Header;

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
     * @var Closure|null
     */
    protected $urlResolver;

    /**
     * @var bool
     */
    protected $clearHistory = false;

    /**
     * @var bool
     */
    protected $encryptHistory;

    public function setRootView(string $name): void
    {
        $this->rootView = $name;
    }

    /**
     * @param  string|array<array-key, mixed>  $key
     * @param  mixed  $value
     */
    public function share($key, $value = null): void
    {
        if (is_array($key)) {
            $this->sharedProps = array_merge($this->sharedProps, $key);
        } else {
            array_set($this->sharedProps, $key, $value);
        }
    }

    public function flushShared()
    {
        $this->sharedProps = [];
    }

    public function getShared(?string $key = null, $default = null): array
    {
        $sharedProps = $this->sharedProps;

        if ($key) {
            return array_get($sharedProps, $key, $default);
        }

        return $sharedProps;
    }

    public function version($version): void
    {
        $this->version = $version;
    }

    public function getVersion(): string
    {
        return (string) closure_call($this->version);
    }

    public function resolveUrlUsing(?Closure $urlResolver = null): void
    {
        $this->urlResolver = $urlResolver;
    }

    public function clearHistory(): void
    {
        session()->setFlashdata('inertia.clear_history', true);
    }

    public function encryptHistory($encrypt = true): void
    {
        $this->encryptHistory = $encrypt;
    }

    public function optional(callable $callback): OptionalProp
    {
        return new OptionalProp($callback);
    }

    public function defer(callable $callback, string $group = 'default'): DeferProp
    {
        return new DeferProp($callback, $group);
    }

    public function merge($value): MergeProp
    {
        return new MergeProp($value);
    }

    public function deepMerge($value): MergeProp
    {
        return (new MergeProp($value))->deepMerge();
    }

    public function always($value): AlwaysProp
    {
        return new AlwaysProp($value);
    }

    /**
     * Create an Inertia response.
     *
     * @param  array<array-key, mixed>  $props
     */
    public function render(string $component, $props = []): string
    {
        return new Response(
            $component,
            array_merge($this->sharedProps, $props),
            $this->rootView,
            $this->getVersion(),
            $this->encryptHistory ?? config('Inertia')->history['encrypt'] ?? false,
            $this->urlResolver,
        );
    }

    public function app($page): string
    {
        return Directive::inertia($page);
    }

    public function redirectResponse(): RedirectResponse
    {
        return Services::redirectResponse();
    }

    public function redirect($uri): RedirectResponse
    {
        return $this->redirectResponse()->to($uri, 303);
    }

    public function location($url): RedirectResponse
    {
        if ($url instanceof Request) {
            $url = $url->getUri();
        }

        if (Services::request()->hasHeader(Header::INERTIA)) {
            Services::session()->set('_ci_previous_url', $url);

            return $this->redirectResponse()->setStatusCode(409)
                ->setHeader(Header::LOCATION, $url);
        }

        return $this->redirect($url);
    }
}
