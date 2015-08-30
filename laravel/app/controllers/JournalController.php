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

        return Response::json(['journals' => $journals], 200);
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$rules = array(
            'publish_date'    => 'required|date|unique_with:journals,user_id,publish_date',
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
        $journal->user_id = Authorizer::getResourceOwnerId();
        $journal->publish_date = Carbon::parse($request['publish_date']);
        $journal->volume = $journal->publish_date->diffInMonths(Config::get('constants.ANNIVERSARY')) + (Config::get('constants.ANNIVERSARY')->day != $journal->publish_date->day ? 2 : 3);
        $journal->day = $journal->publish_date->diffInDays(Config::get('constants.ANNIVERSARY')) + 1;
        $journal->contents = $request['contents'];
        $journal->special_events = array_key_exists('special_events', $request) ? $request['special_events'] : '';

        try
        {
            $journal->save();
        }
        catch (Illuminate\Database\QueryException $e)
        {
            $error = $e->errorInfo;

            if($error[1] = 1062)
            {
                //Duplicate Entry
                return Response::json([
                    'error' => '1062',
                    'error_description' => 'Your journal entry for this date already exists',
                ], 409);
            }
        }

        return Response::json([
            'message'   => 'The resource has been successfuly created',
            'journals'  => $journal,
        ], 201);
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
        $owner = false;

        if(Authorizer::getResourceOwnerId() == $journal->user_id)
        {
            $owner = true;
        }

        return Response::json(['journals' => $journal, 'isOwner' => $owner], 200);
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

        if(User::find(Authorizer::getResourceOwnerId()) != $journal->user)
        {
            return Response::json(['message' => 'Unauthorized operation'], 401);
        }

        $journal->contents = $request['contents'];
        $journal->special_events = array_key_exists('special_events', $request) ? $request['special_events'] : '';

        $journal->save();

        return Response::json(array(
            'message'   => 'The resource has been successfuly updated',
            'journals'  => $journal,
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
        if(User::find(Authorizer::getResourceOwnerId()) != Journal::findOrFail($id)->user)
        {
            return Response::json(['message' => 'Unauthorized operation'], 401);
        }

		Journal::destroy($id);

        return Response::json([
            'message'   => 'The resource has been deleted'
        ], 200);
	}

    public function search()
    {
        $text = "%" . strtolower(Request::get('text')) . "%";
        $volume = Request::get('volume');

        $journals = Journal::ofVolume($volume)
                  ->whereRaw('lower(contents) like ?', array($text))
                  ->orderBy('publish_date')
                  ->get();

        return Response::json(['journals' => $journals], 200);
    }

    public function volume($volume)
    {
        $journals = Journal::ofVolume($volume)->orderBy('publish_date')->get();

        return Response::json(['journals' => $journals], 200);
    }

    public function random()
    {
        $journal = Journal::orderByRaw('RAND()')->first();

        return Response::json(['journals' => $journal], 200);
    }

    public function getDatesWithoutEntry()
    {
        $user = User::find(Authorizer::getResourceOwnerId());
        $journals = $user->journals->lists('publish_date');
        $dates_without_entry = [];
        $dates_with_entry = [];

        foreach ($journals as $journal)
        {
            array_push($dates_with_entry, $journal);
        }

        $period = new DatePeriod(
             new DateTime(Config::get('constants.ANNIVERSARY')),
             new DateInterval('P1D'),
             new DateTime(Carbon::now())
        );

        foreach ($period as $date)
        {
            $formatted_date = $date->format('Y-m-d');

            if (! in_array($formatted_date, $dates_with_entry))
            {
                $dates_without_entry[$formatted_date] = $formatted_date;
            }
        }

        return Response::json($dates_without_entry, 200);
    }
}