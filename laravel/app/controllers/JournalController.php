<?php

use Carbon\Carbon;

class JournalController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$journals = Journal::orderBy('publish_date')->get();

        return Response::json($journals, 200);
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$rules = array(
            'user_id'    => 'required|exists:users,id',
            'publish_date'    => 'required|date',
            'contents'      => 'required',
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

        $journal = new Journal();
        $journal->user_id = $request['user_id'];
        $journal->publish_date = Carbon::parse($request['publish_date']);
        $journal->volume = $journal->publish_date->diffInMonths(Config::get('constants.ANNIVERSARY')) + (Config::get('constants.ANNIVERSARY')->day != $journal->publish_date->day ? 2 : 3);
        $journal->day = $journal->publish_date->diffInDays(Config::get('constants.ANNIVERSARY')) + 1;
        $journal->contents = $request['contents'];
        $journal->special_events = array_key_exists('special_events', $request) ? $request['special_events'] : '';

        $journal->save();

        return Response::json(array(
            'message'   => 'The resource has been successfuly created',
            'data'      => $journal,
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
		$journal = Journal::findOrFail($id);

        return Response::json($journal, 200);
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
            'contents'      => 'required',
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

        $journal = Journal::findOrFail($id);
        $journal->contents = $request['contents'];
        $journal->special_events = array_key_exists('special_events', $request) ? $request['special_events'] : '';

        $journal->save();

        return Response::json(array(
            'message'   => 'The resource has been successfuly updated',
            'data'      => $journal,
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
		Journal::destroy($id);

        return Response::json(array(
            'message'   => 'The resource has been deleted'
        ), 204);
	}

    public function search()
    {
        $text = "%" . strtolower(Request::get('text')) . "%";
        $volume = Request::get('volume');

        $journals = Journal::ofVolume($volume)
                  ->whereRaw('lower(contents) like ?', array($text))
                  ->orderBy('publish_date')
                  ->get();

        return Response::json($journals, 200);
    }

    public function volume($volume)
    {
        $journals = Journal::ofVolume($volume)->orderBy('publish_date')->get();

        return Response::json($journals, 200);
    }

    public function random()
    {
        $journal = Journal::orderByRaw('RAND()')->first();

        return Response::json($journal, 200);
    }
}
