<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Tests\Browser\Pages\HomePage;
use Laravel\Dusk\Browser;

use App\Models\Page;
use App\Models\PageContent;

class PageTest extends DuskTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->page = factory(Page::class)->create();
        $this->page->content()->save(factory(PageContent::class)->make());
    }

    public function testCanViewCustomPage()
    {
        $this->browse(function ($browser){
            $browser->visit('/pages' . $this->page->url)
                ->assertTitleContains($this->page->page_title)
                ->assertSee($this->page->header)
                ->assertSee($this->page->content->content)
                ;
        });
    }

    public function testPagesPopulateNavsCorrectly()
    {
        $headerPage = factory(Page::class)->create([
            'header_nav_link' => true,
            'footer_nav_link' => false,
        ]);

        $footerPage = factory(Page::class)->create([
            'header_nav_link' => false,
            'footer_nav_link' => true,
        ]);

        $omnipresentPage = factory(Page::class)->create([
            'header_nav_link' => true,
            'footer_nav_link' => true,
        ]);

        $absentPage = factory(Page::class)->create([
            'header_nav_link' => false,
            'footer_nav_link' => false,
        ]);

        $this->browse(function ($browser) use ($headerPage, $footerPage, $omnipresentPage, $absentPage) {
            $browser->visit(new HomePage())
                ->assertSeeIn('@headerNav', $headerPage->nav_title)
                ->assertSeeIn('@headerNav', $omnipresentPage->nav_title)
                ->assertDontSeeIn('@headerNav', $footerPage->nav_title)
                ->assertDontSeeIn('@headerNav', $absentPage->nav_title)
                ->assertSeeIn('@footerNav', $footerPage->nav_title)
                ->assertSeeIn('@headerNav', $omnipresentPage->nav_title)
                ->assertDontSeeIn('@footerNav', $headerPage->nav_title)
                ->assertDontSeeIn('@footerNav', $absentPage->nav_title)
                ;
        });
    }
}
