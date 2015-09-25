<?php


class SponsorApiController extends ApiController
{
    public function __construct()
    {
        parent::__construct();

        $this->beforeFilter('auth', array(
            'on' => array('post', 'put', 'delete'),
        ));
    }

    public function getAllSponsors()
    {
        $results = Doc::getAllValidSponsors();

        return Response::json($results);
    }

    /**
     * Saves user submissions for Independent Sponsor requests.
     */
    public function putRequest()
    {
        //Validate input
        $rules = array(
            'address1'    => 'required',
            'city'        => 'required',
            'state'       => 'required',
            'postal_code' => 'required',
            'phone'       => 'required',
        );

        $validation = Validator::make(Input::all(), $rules);
        if ($validation->fails()) {
            return Response::json($this->growlMessage($validation->messages()->all(), 'error'), 400);
        }

        //Add new user information to their record
        $user = Auth::user();
        $user->address1 = Input::get('address1');
        $user->address2 = Input::get('address2');
        $user->city = Input::get('city');
        $user->state = Input::get('state');
        $user->postal_code = Input::get('postal_code');
        $user->phone = Input::get('phone');
        $user->save();

        if (!$user->getSponsorStatus()) {
            //Add UserMeta request
            $request = new UserMeta();
            $request->meta_key = UserMeta::TYPE_INDEPENDENT_SPONSOR;
            $request->meta_value = 0;
            $request->user_id = $user->id;
            $request->save();
        }

        return Response::json();
    }
}
