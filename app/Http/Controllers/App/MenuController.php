<?php

namespace App\Http\Controllers\App;

use App\Http\Requests\App\StoreMenuRequest;
use App\Http\Requests\App\UpdateMenuRequest;
use App\Http\Resources\Response;
use App\Models\Menu;

/**
 * @group #Menu Management
 * @authenticated
 */
class MenuController extends Controller
{
    /**
     * List Menu
     *
     * List all menu
     */
    public function index(Menu $menu)
    {
        return new Response($menu->route()->filter()->table());
    }

    /**
     * Show Menu
     *
     * Show Menu Detail
     */
    public function show(Menu $menu)
    {
        return new Response($menu->route());
    }

    /**
     * Save Menu
     */
    public function store(Menu $menu, StoreMenuRequest $request)
    {
        $data = $request->validated();
        $menu = $menu->create($data);

        return new Response($menu, 200, 'Updated');
    }

    /**
     * Update Menu
     */
    public function update(Menu $menu, UpdateMenuRequest $request)
    {
        $data = $request->validated();
        $menu = $menu->update($data);

        return new Response($menu);
    }

    /**
     * Delete Menu
     */
    public function destroy(Menu $menu)
    {
        return new Response($menu);
    }
}
