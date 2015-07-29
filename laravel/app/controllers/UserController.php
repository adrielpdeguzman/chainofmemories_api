<?php

use Carbon\Carbon;

class UserController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$users = User::all();

        return Response::json($users, 200);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$rules = array(
            'first_name'    => 'required',
            'last_name'     => 'required',
            'username'      => 'required|unique:users',
            'password'      => 'required',
        );

        $request = Request::all();

        $errors = $this->validateAndReturnErrors($request, $rules);

        if ($errors)
        {
            return Response::json(array(
                'message'   => 'The following errors were encountered ',
                'errors'    => $errors,
            ), 400);
        }

        $user = new User();
        $user->first_name = $request['first_name'];
        $user->last_name = $request['last_name'];
        $user->username = $request['username'];
        $user->password = Hash::make($request['password']);
        $user->last_login = Carbon::now()->format('Y-m-d H:i:s');

        $user->save();

        return Response::json(array(
            'message'   => 'The resource has been successfuly created',
            'data'      => $user,
        ), 201);
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$user = User::findOrFail($id);

        return Response::json($user, 200);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$rules = array(
            'first_name'    => 'required',
            'last_name'     => 'required',
        );

        $request = Request::all();

        $errors = $this->validateAndReturnErrors($request, $rules);

        if ($errors)
        {
            return Response::json(array(
                'message'   => 'The following errors were encountered ',
                'errors'    => $errors,
            ), 400);
        }

        $user = User::findOrFail($id);
        $user->first_name = $request['first_name'];
        $user->last_name = $request['last_name'];

        $user->save();

        return Response::json(array(
            'message'   => 'The resource has been successfuly updated',
            'data'      => $user,
        ), 200);
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
        User::findOrFail($id)->delete();

        return Response::json(array(
            'message'   => 'The resource has been deleted'
        ), 204);
	}


}
