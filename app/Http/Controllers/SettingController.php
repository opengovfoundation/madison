<?php

namespace App\Http\Controllers;

use App\Config\Models\Config as ConfigModel;
use App\Models\Doc as Document;
use App\Models\Setting;
use App\Http\Requests\Setting as Requests;
use SiteConfigSaver;

class SettingController extends Controller
{
    /**
     * Admin page for configuring site settings.
     *
     * @return \Illuminate\Http\Response
     */
    public function siteSettingsIndex(Requests\SiteSettings\Index $request)
    {
        $dbSettings = collect(SiteConfigSaver::get());

        $currentSettings = new \stdClass();
        foreach (static::getSiteSettingKeys() as $key) {
            $k = str_replace('.', '_', $key);
            $currentSettings->{$k} = $dbSettings->get($key, 'default');
        }

        // reset config() to just the configuration file contents so we can
        // show correct default values
        (new \Illuminate\Foundation\Bootstrap\LoadConfiguration())
            ->bootstrap(app());

        $dateFormats = static::addDefaultOption(
            static::validDateFormats(),
            config('madison.date_format')
        );
        $timeFormats = static::addDefaultOption(
            static::validTimeFormats(),
            config('madison.time_format')
        );

        return view('settings.site-settings', compact([
            'currentSettings',
            'dateFormats',
            'timeFormats',
        ]));
    }

    public function siteSettingsUpdate(Requests\SiteSettings\Update $request)
    {
        foreach (static::getSiteSettingKeys() as $key) {
            $input = $request->input(str_replace('.', '_', $key));

            list($group, $item) = ConfigModel::explodeGroupAndKey($key);
            $existingModel = ConfigModel
                ::where('group', $group)
                ->where('key', $item);

            if ((!$input || $input === 'default') && $existingModel) {
                $existingModel->delete();
                SiteConfigSaver::refresh();
            } else {
                SiteConfigSaver::set($key, $input);
            }
        }

        flash(trans('messages.updated'));
        return redirect()->route('settings.site.index');
    }

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

        flash(trans('messages.setting.updated_featured_documents'));
        return redirect()->route('setings.featured-documents.index');
    }

    public static function addDefaultOption($choices, $current)
    {
        $value = '';
        if (!isset($choices[$current])) {
            $value = 'Unknown';
        } else {
            $value = 'Default ('.$choices[$current].')';
        }
        return ['default' => $value]+$choices;
    }

    public static function validDateFormats()
    {
        return [
            'Y-m-d' => 'ISO 8601: 2009-06-27',
            'n/j/Y' => 'US: 06/27/2009',
            'd-m-Y' => 'Europe: 27-06-2009',
        ];
    }

    public static function validTimeFormats()
    {
        return [
            'g:i A' => '12 Hour, 1:15 PM',
            'H:i' => '24 Hour, 13:15',
        ];
    }

    public static function getSiteSettingKeys()
    {
        return [
            'madison.date_format',
            'madison.time_format',
        ];
    }
}
