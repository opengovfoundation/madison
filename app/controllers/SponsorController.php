<?php

class SponsorController extends Controller
{
	public function getRequest(){
		return View::make('documents.sponsor.request.index');
	}
	
}