<?php

namespace App\Http\Controllers\Post;

use App\Models\BarTest;
use App\Http\Controllers\Controller;
use App\Http\Resources\Response;
use App\Http\Requests\StoreBarTestRequest;
use App\Http\Requests\UpdateBarTestRequest;

/**
 * @group BarTest
 * @authenticated
 */
class BarTestController extends Controller
{
	/**
     * Index bar_test
     *
	 * Display a listing of bar_test.
	 */
	public function index(BarTest $bar_test)
	{
        return new Response($bar_test->filter()->table());
	}

	/**
     * Store bar_test
     *
	 * Store a newly created bar_test in storage.
	 */
	public function store(BarTest $bar_test, StoreBarTestRequest $request)
	{
        $data = $request->validated();
        $response = $bar_test->create($data);

        return new Response($response, 200, 'Added');
	}

	/**
     * Show bar_test
     *
	 * Display the specified bar_test.
	 */
	public function show(BarTest $bar_test)
	{
        return new Response($bar_test);
	}

	/**
	 * Update the specified bar_test in storage.
	 *
	 * @param BarTest  $bar_test
     */
    public function update(BarTest $bar_test, UpdateBarTestRequest $request)
	{
        $data = $request->validated();
        $response = $bar_test->update($data);

        return new Response($response, 200, 'Updated');
	}

	/**
	 * Remove the specified bar_test from storage.
	 *
	 * @param BarTest  $bar_test
	 */
	public function destroy(BarTest $bar_test)
	{
        $response = $bar_test->delete();
        
        return new Response($response, 200, 'Deleted');
	}
}
