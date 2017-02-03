<?php

namespace App\Http\Controllers;

use App\Models\Doc as Document;
use App\Models\Setting;
use App\Http\Requests\Setting as Requests;

class SettingController extends Controller
{

    /**
     * Show a list of featured documents so an admin can manage them.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexFeaturedDocuments(Requests\FeaturedDocuments\Index $request)
    {
        $documents = Document::getFeatured(false);
        return view('settings.featured-documents', compact([
            'documents'
        ]));
    }

    /**
     * Updates the position of a featured document.
     *
     * @return \Illuminate\Http\Response
     */
    public function updateFeaturedDocuments(Requests\FeaturedDocuments\Update $request, Document $document)
    {
        $action = $request->input('action');

        $featuredSetting = Setting::where('meta_key', '=', 'featured-doc')->first();
        $featuredIds = explode(',', $featuredSetting->meta_value);

        $currentPos = array_search((string) $document->id, $featuredIds);

        if ($currentPos === false) {
            throw new \Exception('Invalid Document ID');
        }

        if ($currentPos === 0 && $action === 'up' || $currentPos === count($featuredIds) - 1 && $action === 'down') {
            throw new \Exception('Invalid move');
        }

        # Create a copy of the original array, to avoid errors from reference
        # assignments as opposed to value assignments.
        $idReferences = array_flip(array_flip($featuredIds));

        if ($action === "up") {
            $featuredIds[$currentPos] = $idReferences[$currentPos - 1];
            $featuredIds[$currentPos - 1] = (string) $document->id;
        } else if ($action === "down") {
            $featuredIds[$currentPos] = $idReferences[$currentPos + 1];
            $featuredIds[$currentPos + 1] = (string) $document->id;
        } else if ($action === "remove") {
            unset($featuredIds[$currentPos]);
        }

        $featuredSetting->meta_value = join(',', $featuredIds);
        $featuredSetting->save();

        flash(trans('messages.settings.updated_featured_documents'));
        return redirect()->route('settings.featured-documents.list');
    }

}
