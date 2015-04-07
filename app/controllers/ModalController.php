<?php

class ModalController extends Controller
{
    public function __construct()
    {
        //$this->beforeFilter('auth', array('on' => array('post','put', 'delete')));
    }

    public function seenAnnotationThanksModal()
    {
        if (!Auth::check()) {
            throw new Exception("Unauthorized");
        }

        $userId = Auth::user()->id;

        $userMeta = UserMeta::firstOrNew(array(
            'user_id' => $userId,
            'meta_key' => UserMeta::TYPE_SEEN_ANNOTATION_THANKS, ));

        $userMeta->meta_value = true;

        $userMeta->save();
    }

    public function getAnnotationThanksModal()
    {
        return View::make('modal.annotations.thanks');
    }
}
