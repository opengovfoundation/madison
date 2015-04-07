<?php
/**
 * Partial Routing File.
 *
 * This file includes all page routes that have already been converted to the single-page-app-ness
 */

//Match any slug that does not begin with api/ and serve up index.html
  //Angular takes it from there and communicates via api
Route::any('{slug}', function ($slug) {
  if (!Config::get('app.debug')) {
      return File::get(public_path().'/index.html');
  } else {
      return File::get(public_path().'/pre-build.html');
  }

})->where('slug', '^(?!api/)(.*)$');
