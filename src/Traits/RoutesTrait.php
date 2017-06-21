<?php

namespace ctf0\SimpleMenu\Traits;

use ctf0\SimpleMenu\Models\Page;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;

trait RoutesTrait
{
    protected $allRoutes = [];
    protected $localeCodes;
    protected $listFileFound = true;

    public function createRoutes()
    {
        Route::group([
            'prefix'     => LaravelLocalization::setLocale(),
            'middleware' => [
                'web',
                LocaleSessionRedirect::class,
                LaravelLocalizationRedirectFilter::class,
            ],
            'namespace'  => config('simpleMenu.pagesControllerNS'),
            ], function () {
                $this->utilCheck();
            }
        );
    }

    protected function utilCheck()
    {
        if (!File::exists($this->listFileDir)) {
            $this->localeCodes = array_keys(LaravelLocalization::getSupportedLocales());
            $this->listFileFound = false;

            $this->utilLoop();
            $this->saveRoutesListToFile($this->allRoutes);
        } else {
            $this->utilLoop();
        }
    }

    protected function utilLoop()
    {
        foreach (Page::all() as $page) {
            $this->pageComp($page);
        }
    }

    /**
     * runs everytime you change the current local so routes links gets
     * dynamically changed without causing issues.
     *
     * @param [type] $page [description]
     *
     * @return [type] [description]
     */
    protected function pageComp($page)
    {
        // page data
        $title = $page->title;
        $body = $page->body;
        $desc = $body;
        $template = $page->template;
        $breadCrump = $page->getAncestors();

        // route data
        $url = $page->url;
        $action = $page->action;
        $prefix = $page->prefix;
        $route = $this->clearExtraSlash("$prefix/$url");
        $routeName = $page->route_name;

        // middlewares
        $roles = 'role:'.implode(',', $page->roles()->pluck('name')->toArray());
        $permissions = 'perm:'.implode(',', $page->permissions()->pluck('name')->toArray());

        // make route
        $this->routeGen($routeName, $route, $action, $roles, $permissions, $template, $title, $body, $desc, $breadCrump);

        // create route list
        if (!$this->listFileFound) {
            $this->createRoutesList($action, $page, $routeName);
        }
    }

    protected function routeGen($routeName, $route, $action, $roles, $permissions, $template, $title, $body, $desc, $breadCrump)
    {
        // escape empty route
        if ($routeName != config('simpleMenu.mainRouteName') && $route == '/') {
            return;
        }

        // dynamic
        if ($action) {
            Route::get($route, $action)->name($routeName)->middleware([$roles, $permissions]);
        }
        // static
        else {
            Route::get($route, function () use ($template, $title, $body, $desc, $breadCrump) {
                return view("pages.{$template}")->with([
                    'title'      => $title,
                    'body'       => $body,
                    'desc'       => $desc,
                    'breadCrump' => $breadCrump,
                ]);
            })
            ->name($routeName)
            ->middleware([$roles, $permissions]);
        }
    }

    protected function createRoutesList($action, $page, $routeName)
    {
        foreach ($this->localeCodes as $code) {
            $url = $page->getTranslationWithoutFallback('url', $code);
            $prefix = $page->getTranslationWithoutFallback('prefix', $code);

            if (empty($url)) {
                continue;
            }

            $route = $this->clearExtraSlash("$prefix/$url");
            $this->allRoutes[$routeName][$code] = $route;
        }
    }

    protected function saveRoutesListToFile($routes)
    {
        $data = "<?php\n\nreturn ".var_export($routes, true).';';
        $data = $this->clearExtraSlash($data);

        // array(...) to [...]
        $data = str_replace('array (', '[', $data);
        $data = str_replace(')', ']', $data);
        $data = preg_replace('/=>\s+\[/', '=> [', $data);

        return File::put($this->listFileDir, $data);
    }

    protected function clearExtraSlash($url)
    {
        return preg_replace('/\/+/', '/', $url);
    }
}
