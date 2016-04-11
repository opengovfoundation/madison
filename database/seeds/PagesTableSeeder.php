<?php

use Illuminate\Database\Seeder;
use App\Models\Page;
use App\Models\PageContent;

class PagesTableSeeder extends Seeder
{
    public function run()
    {
        /**
         * About Page
         */
        $about_page = factory(Page::class, [
            'url' => 'about',
            'nav_title' => 'About',
            'page_title' => 'About',
            'header' => 'About Madison',
            'header_nav_link' => true,
            'footer_nav_link' => false,
            'external' => false
        ])->create();

        factory(PageContent::class, [
            'content' => file_get_contents(app_path() . '/../database/seeds/pages/about.html'),
            'page_id' => $about_page->id
        ])->create();

        /**
         * FAQ Page
         */
        $faq_page = factory(Page::class, [
            'url' => 'about',
            'nav_title' => 'FAQ',
            'page_title' => 'FAQ',
            'header' => 'Frequently Asked Questions',
            'header_nav_link' => true,
            'footer_nav_link' => false,
            'external' => false
        ])->create();

        factory(PageContent::class, [
            'content' => file_get_contents(app_path() . '/../database/seeds/pages/faq.html'),
            'page_id' => $faq_page->id
        ])->create();

        /**
         * Copyright Policy Link
         */
        factory(Page::class, [
            'url' => 'https://github.com/opengovfoundation/site-policy/blob/master/markdown/copyright.md',
            'nav_title' => 'Copyright Policy',
            'header_nav_link' => false,
            'footer_nav_link' => true,
            'external' => true
        ])->create();

        /**
         * Privacy Policy Link
         */
        factory(Page::class, [
            'url' => 'https://github.com/opengovfoundation/site-policy/blob/master/markdown/privacy-policy.md',
            'nav_title' => 'Privacy Policy',
            'header_nav_link' => false,
            'footer_nav_link' => true,
            'external' => true
        ])->create();

        /**
         * Terms of Service Link
         */
        factory(Page::class, [
            'url' => 'https://github.com/opengovfoundation/site-policy/blob/master/markdown/terms.md',
            'nav_title' => 'Terms of Service',
            'header_nav_link' => false,
            'footer_nav_link' => true,
            'external' => true
        ])->create();
    }
}
