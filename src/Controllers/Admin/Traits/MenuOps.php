<?php

namespace ctf0\SimpleMenu\Controllers\Admin\Traits;

use Illuminate\Http\Request;
use ctf0\SimpleMenu\Models\Menu;
use ctf0\SimpleMenu\Models\Page;
use Illuminate\Support\Facades\Cache;

trait MenuOps
{
    /**
     * get all menu pages for ajax.
     *
     * @param Menu $id [description]
     *
     * @return [type] [description]
     */
    public function getMenuPages($id)
    {
        $pages = collect(Menu::with('pages')->find($id)->pages)->sortBy('pivot_order')->each(function ($item) {
            $item['from'] = 'pages';
        });

        $allPages = cache('sm-pages')->diff($pages)->each(function ($item) {
            $item['from'] = 'allPages';
        });

        return response()->json(compact('pages', 'allPages'));
    }

    /**
     * remove page from menu with ajax.
     *
     * @param [type]  $id      [description]
     * @param Request $request [description]
     *
     * @return [type] [description]
     */
    public function removePage($id, Request $request)
    {
        // remove page from menu
        $menu = Menu::find($id);
        $menu->pages()->detach($request->page_id);
        $menu->touch();

        // clear page nesting
        if (config('simpleMenu.clearRootDescendants')) {
            $this->clearSelfAndNests($request->page_id);
        }

        return response()->json(['done'=>true]);
    }

    /**
     * remove nested child with ajax.
     *
     * @param Request $request [description]
     *
     * @return [type] [description]
     */
    public function removeChild(Request $request)
    {
        $this->clearSelfAndNests($request->child_id);

        return response()->json(['done'=>true]);
    }

    /**
     * save page hierarchy to db.
     *
     * @param [type] $list [description]
     *
     * @return [type] [description]
     */
    protected function saveListToDb($list)
    {
        foreach ($list as $one) {
            $parent = $this->findPage($one->parent_id);

            $this->findPage($one->id)->makeChildOf($parent);
            $parent->touch();

            if ($one->children) {
                $this->saveListToDb($one->children);
            }
        }
    }

    /**
     * helpers.
     *
     * @param [type] $id [description]
     *
     * @return [type] [description]
     */
    protected function clearSelfAndNests($id)
    {
        $page = $this->findPage($id);
        $page->clearSelfAndDescendants();
    }

    protected function findPage($id)
    {
        return Page::find($id);
    }

    protected function clearCache()
    {
        return Cache::forget('sm-menus');
    }
}
