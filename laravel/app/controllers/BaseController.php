<?php

class BaseController extends Controller {

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}

    protected function validateAndReturnErrors($data, $rules)
    {
        $validator = Validator::make($data, $rules);
        if ($validator->fails())
            return $validator->messages();
        else
            return null;
    }

}
