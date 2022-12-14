<?php

namespace App\Http\Controllers\App;

use App\Http\Requests\App\StoreDevRequest;
use App\Http\Resources\Response;
use App\Traits\Generator;
use Illuminate\Support\Facades\Route;

/**
 * @group #Dev Management
 * @authenticated
 */
class DevController extends Controller
{
    /**
     * List Routes
     *
     * List all route
     */
    public function index()
    {
        $permissions = collect(auth()->user()->getPermissions());
        $routes = collect(Route::getRoutes()->get())
            ->filter(function ($route) {
                $middleware = collect($route->action['middleware']);

                return $middleware->contains('api');
            })->map(function ($route) {
                $filtered = [
                    'uri' => $route->uri,
                    'methods' => $route->methods,
                    'permission' => $route->action['as'] ?? '',
                ];

                return $filtered;
            })->paginate();

        return new Response($routes);
    }

    /**
     * Generator
     */
    public function store(StoreDevRequest $request)
    {
        $request->validated();
        $name = $request->post('uri');
        $methods = $request->post('methods');
        $schema = $request->post('schema');
        $force = $request->post('force');
        $migrate = true;
        [$result, $messages] = (new Generator)->generate($name, $methods, $schema, $migrate, $force);

        return new Response($result, 200, $messages);
    }
}
