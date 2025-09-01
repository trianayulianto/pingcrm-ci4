<?php

namespace Inertia;

use Closure;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Inertia\Config\Services;
use Inertia\Support\Header;

class Response
{
    protected $viewData = [];

    protected $component;

    protected $props;

    protected $rootView;

    protected $version;

    protected $clearHistory;

    protected $encryptHistory;

    protected $cacheFor = [];

    protected $urlResolver;

    /**
     * Create a new Inertia response instance.
     *
     * @param  array<array-key, mixed>  $props
     */
    public function __construct(
        string $component,
        array $props,
        string $rootView = 'app',
        string $version = '',
        bool $encryptHistory = false,
        ?Closure $urlResolver = null
    ) {
        $this->component = $component;
        $this->props = $props;
        $this->rootView = $rootView;
        $this->version = $version;
        $this->clearHistory = session()->getFlashdata('inertia.clear_history') ?? false;
        $this->encryptHistory = $encryptHistory;
        $this->urlResolver = $urlResolver;
    }

    public function with($key, $value = null): self
    {
        if (is_array($key)) {
            $this->props = array_merge($this->props, $key);
        } else {
            $this->props[$key] = $value;
        }

        return $this;
    }

    public function withViewData($key, $value = null): self
    {
        if (is_array($key)) {
            $this->viewData = array_merge($this->viewData, $key);
        } else {
            $this->viewData[$key] = $value;
        }

        return $this;
    }

    public function cache(string|array $cacheFor): self
    {
        $this->cacheFor = is_array($cacheFor) ? $cacheFor : [$cacheFor];

        return $this;
    }

    public function isPartial(): bool
    {
        $partialComponenthearder = $this->request()->header(Header::PARTIAL_COMPONENT);

        if ($partialComponenthearder) {
            return $partialComponenthearder->getValue() === $this->component;
        }

        return false;
    }

    public function __toString()
    {
        $request = $this->request();
        $props = $this->resolveProperties($request, $this->props);

        $page = array_merge(
            [
                'component' => $this->component,
                'props' => $props,
                'url' => $this->getUrl(),
                'version' => $this->version,
                'clearHistory' => $this->clearHistory,
                'encryptHistory' => $this->encryptHistory,
            ],
            $this->resolveMergeProps($request),
            $this->resolveDeferredProps($request),
            $this->resolveCacheDirections($request),
        );

        return $this->make($page);
    }

    public function resolveProperties(RequestInterface $request, array $props): array
    {
        $props = $this->resolvePartialProperties($props, $request);
        $props = $this->resolveArrayableProperties($props, $request);
        $props = $this->resolveAlways($props);
        $props = $this->resolvePropertyInstances($props, $request);

        return $props;
    }

    public function resolvePartialProperties(array $props, RequestInterface $request): array
    {
        if (! $this->isPartial($request)) {
            return array_filter($props, static function ($prop) {
                return ! ($prop instanceof IgnoreFirstLoad);
            });
        }

        $only = array_filter(explode(',', $request->header(Header::PARTIAL_ONLY, '')));
        $except = array_filter(explode(',', $request->header(Header::PARTIAL_EXCEPT, '')));

        if (count($only)) {
            $newProps = [];

            foreach ($only as $key) {
                array_set($newProps, $key, array_get($props, $key));
            }

            $props = $newProps;
        }

        if ($except) {
            array_forget($props, $except);
        }

        return $props;
    }

    public function resolveArrayableProperties(array $props, RequestInterface $request, bool $unpackDotProps = true): array
    {
        foreach ($props as $key => $value) {
            if ($value instanceof Closure) {
                $value = closure_call($value);
            }

            if (is_array($value)) {
                $value = $this->resolveArrayableProperties($value, $request, false);
            }

            if ($unpackDotProps && str_contains($key, '.')) {
                array_set($props, $key, $value);
                unset($props[$key]);
            } else {
                $props[$key] = $value;
            }
        }

        return $props;
    }

    public function resolveOnly(RequestInterface $request, array $props): array
    {
        $only = array_filter(explode(',', $request->header(Header::PARTIAL_ONLY, '')));

        $value = [];

        foreach ($only as $key) {
            array_set($value, $key, array_get($props, $key));
        }

        return $value;
    }

    public function resolveExcept(RequestInterface $request, array $props): array
    {
        $except = array_filter(explode(',', $request->header(Header::PARTIAL_EXCEPT, '')));

        array_forget($props, $except);

        return $props;
    }

    public function resolveAlways(array $props): array
    {
        $always = array_filter($this->props, static function ($prop) {
            return $prop instanceof AlwaysProp;
        });

        return array_merge(
            $always,
            $props
        );
    }

    public function resolvePropertyInstances(array $props, RequestInterface $request, ?string $parentKey = null): array
    {
        foreach ($props as $key => $value) {
            $resolveViaApp = [
                Closure::class,
                OptionalProp::class,
                DeferProp::class,
                AlwaysProp::class,
                MergeProp::class,
            ];

            if ($resolveViaApp) {
                $value = closure_call($value);
            }

            $currentKey = $parentKey ? $parentKey.'.'.$key : $key;

            if (is_array($value)) {
                $value = $this->resolvePropertyInstances($value, $request, $currentKey);
            }

            $props[$key] = $value;
        }

        return $props;
    }

    public function resolveCacheDirections(RequestInterface $request): array
    {
        if (count($this->cacheFor) === 0) {
            return [];
        }

        return [
            'cache' => array_map(fn ($value) => intval($value), $this->cacheFor),
        ];
    }

    public function resolveMergeProps(RequestInterface $request): array
    {
        // Parse and sanitize header values with error handling
        $headerFilters = $this->parseHeaderFilters($request);

        if (empty($this->props)) {
            return [];
        }

        // Single iteration to collect and categorize mergeable props
        $mergeableProps = $this->collectMergeableProps($headerFilters);

        if (empty($mergeableProps)) {
            return [];
        }

        // Build result arrays efficiently
        return $this->buildMergePropsResult($mergeableProps);
    }

    public function resolveDeferredProps(RequestInterface $request): array
    {
        if ($this->isPartial($request) || empty($this->props)) {
            return [];
        }

        $deferredGroups = $this->collectDeferredProps();

        return ! empty($deferredGroups) ? ['deferredProps' => $deferredGroups] : [];
    }

    private function parseHeaderFilters(RequestInterface $request): array
    {
        $resetHeader = $request->getHeaderLine(Header::RESET);
        $onlyHeader = $request->getHeaderLine(Header::PARTIAL_ONLY);
        $exceptHeader = $request->getHeaderLine(Header::PARTIAL_EXCEPT);

        return [
            'reset' => $this->parseCommaSeparatedHeader($resetHeader),
            'only' => $this->parseCommaSeparatedHeader($onlyHeader),
            'except' => $this->parseCommaSeparatedHeader($exceptHeader),
        ];
    }

    private function parseCommaSeparatedHeader(string $header): array
    {
        if (empty($header)) {
            return [];
        }

        return array_filter(
            array_map('trim', explode(',', $header)),
            fn ($value) => $value !== ''
        );
    }

    private function collectMergeableProps(array $headerFilters): array
    {
        $mergeableProps = [];
        $resetProps = array_flip($headerFilters['reset']);
        $onlyProps = $headerFilters['only'];
        $exceptProps = array_flip($headerFilters['except']);
        $hasOnlyFilter = ! empty($onlyProps);
        $onlyPropsFlipped = $hasOnlyFilter ? array_flip($onlyProps) : [];

        foreach ($this->props as $key => $prop) {
            // Skip non-mergeable props
            if (! $prop instanceof Mergeable || ! $prop->shouldMerge()) {
                continue;
            }

            // Apply header filters
            if (isset($resetProps[$key]) || isset($exceptProps[$key])) {
                continue;
            }

            if ($hasOnlyFilter && ! isset($onlyPropsFlipped[$key])) {
                continue;
            }

            $mergeableProps[$key] = $prop;
        }

        return $mergeableProps;
    }

    private function buildMergePropsResult(array $mergeableProps): array
    {
        $result = [
            'mergeProps' => [],
            'deepMergeProps' => [],
            'matchPropsOn' => [],
        ];

        foreach ($mergeableProps as $key => $prop) {
            if ($prop->shouldDeepMerge()) {
                $result['deepMergeProps'][] = $key;
            } else {
                $result['mergeProps'][] = $key;
            }

            // Collect match strategies
            $matchStrategies = $prop->matchesOn();
            if (! empty($matchStrategies)) {
                foreach ($matchStrategies as $strategy) {
                    $result['matchPropsOn'][] = $key.'.'.$strategy;
                }
            }
        }

        // Filter out empty arrays to maintain backward compatibility
        return array_filter($result, fn ($array) => ! empty($array));
    }

    private function collectDeferredProps(): array
    {
        $groups = [];

        foreach ($this->props as $key => $prop) {
            if ($prop instanceof DeferProp) {
                $group = $prop->group() ?? 'default';
                $groups[$group][] = $key;
            }
        }

        return $groups;
    }

    private function make($page): string
    {
        $inertia = $this->request()->header(Header::INERTIA);

        if ($inertia && $inertia->getValue()) {
            $this->response()->setJSON($page);
            $this->response()->setHeader(Header::INERTIA, 'true');

            return $this->response()->getJSON();
        }

        return $this->view($page);
    }

    private function view($page): string
    {
        return Services::renderer()
            ->setData($this->viewData + ['page' => $page], 'raw')
            ->render($this->rootView);
    }

    private function getUrl(): string
    {
        $urlResolver = $this->urlResolver ?? function (RequestInterface $request) {
            $uri = $request->getUri();
            $url = str_start(rtrim(sprintf('%s?%s', $uri->getPath(), $uri->getQuery()), '?'), '/');
            $rawUri = str_before((string) $uri, '?');

            return str_ends_with($rawUri, '/') ? $this->finishUrlWithTrailingSlash($url) : $url;
        };

        return closure_call($urlResolver, ['request' => $this->request()]);
    }

    protected function finishUrlWithTrailingSlash(string $url): string
    {
        // Make sure the relative URL ends with a trailing slash and re-append the query string if it exists.
        $urlWithoutQueryWithTrailingSlash = str_finish(str_before($url, '?'), '/');

        return str_contains($url, '?')
            ? $urlWithoutQueryWithTrailingSlash.'?'.str_after($url, '?')
            : $urlWithoutQueryWithTrailingSlash;
    }

    private function request(): RequestInterface
    {
        return Services::request();
    }

    private function response(): ResponseInterface
    {
        return Services::response();
    }
}
