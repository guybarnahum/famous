<?php
/**
 * Resource to allow Famous to handle real-time updates from third-party SNS's
 */

namespace App\Http\Controllers\Api;

use App\Http\Middleware\CallbackManager;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

 use \Log;

/**
 * Class CallbackController
 *
 * NOTE: If you run into problem about not having proper configuration data, you will have
 * to copy /<project root/conf/famous.ini to /var/conf/famous.ini
 *
 * @package App\Http\Controllers\Api
 */
class CallbackController extends Controller {

    /**
     * Some providers postback data onto callbacks using GET
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request)
	{
        return Response::create('ok', 200);
	}

    /**
     * Handles data posted back (callback) from provider
     *
     * @param Request $request
     * @param string $namespace
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create(Request $request, $namespace = '')
	{
        $cb_manager = new CallbackManager();
        $cb_manager->emit($request, $namespace, []);
        return Response::create($namespace, 200);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
     * @param Request $request
	 * @param  string $namespace
	 * @return Response
	 */
	public function show(Request $request, $namespace)
    {
        $cb_manager = new CallbackManager();
        $cb_manager->emit($request, $namespace, []);
        return Response::create($namespace, 200);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}
}
