<?php

class SessionController extends \BaseController {

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
        $username = Request::get('username');
        $password = Request::get('password');

        if (Auth::attempt(['username' => $username, 'password' => $password]))
        {

            return Response::json(array(
                'message'   => 'Login was successful',
                'user'      => Auth::user()
            ), 200);
        }

        return Response::json(array(
            'message'   => 'Login was unsuccessful',
        ), 400);
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy()
	{
        Auth::logout();

        return Response::json(array(
            'message'   => 'The session was destroyed',
        ), 400);
	}


}
