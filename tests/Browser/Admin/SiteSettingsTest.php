<?php

namespace Tests\Browser;

use App\Models\User;
use App\Http\Controllers\AdminController;
use SiteConfigSaver;
use Tests\Browser\AdminTestCase;
use Tests\Browser\Pages\Admin;
use Laravel\Dusk\Browser;

class SiteSettingsTest extends AdminTestCase
{
    public function testSiteSettings()
    {
        $this->browse(function ($browser) {
            $page = new Admin\SiteSettingsPage;

            $this->assertNonAdminsDenied($browser, $page);

            $browser
                ->loginAs($this->admin)
                ->visit($page)
                ;

            $dateFormatKey = array_keys(AdminController::validDateFormats())[0];
            $browser
                ->assertSelected('madison.date_format', 'default')
                ->select('madison.date_format', $dateFormatKey)
                ->click('@submitBtn')
                ->assertPathIs($page->url())
                ->assertVisible('.alert.alert-info') // some success
                ->assertSelected('madison.date_format', $dateFormatKey)
                ;

            SiteConfigSaver::refresh();
            $this->assertEquals($dateFormatKey, config('madison.date_format'));

            $timeFormatKey = array_keys(AdminController::validTimeFormats())[0];
            $browser
                ->assertSelected('madison.time_format', 'default')
                ->select('madison.time_format', $timeFormatKey)
                ->click('@submitBtn')
                ->assertPathIs($page->url())
                ->assertVisible('.alert.alert-info') // some success
                ->assertSelected('madison.time_format', $timeFormatKey)
                ;

            SiteConfigSaver::refresh();
            $this->assertEquals($timeFormatKey, config('madison.time_format'));

            // Google Analytics
            $browser
                ->assertInputValue('madison.google_analytics_property_id', '')
                ->assertSourceMissing('window.ga')
                ->type('madison.google_analytics_property_id', 'UA-123-456')
                ->click('@submitBtn')
                ->assertPathIs($page->url())
                ->assertSourceHas('window.ga')
                ->assertSourceHas("ga('create', 'UA-123-456', 'auto');")
                ->clear('madison.google_analytics_property_id')
                ->click('@submitBtn')
                ->assertPathIs($page->url())
                ->assertSourceMissing('window.ga')
                ;
        });
    }
}
