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
        $about_page = factory(Page::class)->make();
        $about_page->url = '/about';
        $about_page->nav_title = 'About';
        $about_page->page_title = 'About';
        $about_page->header = 'About Madison';
        $about_page->header_nav_link = true;
        $about_page->footer_nav_link = false;
        $about_page->external = false;
        $about_page->save();

        $about_page_content = factory(PageContent::class)->make();
        $about_page_content->content = file_get_contents(app_path() . '/../database/seeds/pages/about.md');
        $about_page_content->page_id = $about_page->id;
        $about_page_content->save();

        /**
         * FAQ Page
         */
        $faq_page = factory(Page::class)->make();
        $faq_page->url = '/faq';
        $faq_page->nav_title = 'FAQ';
        $faq_page->page_title = 'FAQ';
        $faq_page->header = 'Frequently Asked Questions';
        $faq_page->header_nav_link = true;
        $faq_page->footer_nav_link = false;
        $faq_page->external = false;
        $faq_page->save();

        $faq_page_content = factory(PageContent::class)->make();
        $faq_page_content->content = file_get_contents(app_path() . '/../database/seeds/pages/faq.md');
        $faq_page_content->page_id = $faq_page->id;
        $faq_page_content->save();

        /**
         * Copyright Policy Link
         */
        $copyright_policy_link = factory(Page::class)->make();
        $copyright_policy_link->url = 'https://github.com/opengovfoundation/site-policy/blob/master/markdown/copyright.md';
        $copyright_policy_link->nav_title = 'Copyright Policy';
        $copyright_policy_link->header_nav_link = false;
        $copyright_policy_link->footer_nav_link = true;
        $copyright_policy_link->external = true;
        $copyright_policy_link->save();

        /**
         * Privacy Policy Link
         */
        $privacy_policy_link = factory(Page::class)->make();
        $privacy_policy_link->url = 'https://github.com/opengovfoundation/site-policy/blob/master/markdown/privacy-policy.md';
        $privacy_policy_link->nav_title = 'Privacy Policy';
        $privacy_policy_link->header_nav_link = false;
        $privacy_policy_link->footer_nav_link = true;
        $privacy_policy_link->external = true;
        $privacy_policy_link->save();

        /**
         * Terms of Service Link
         */
        $terms_of_service_link = factory(Page::class)->make();
        $terms_of_service_link->url = 'https://github.com/opengovfoundation/site-policy/blob/master/markdown/terms.md';
        $terms_of_service_link->nav_title = 'Terms of Service';
        $terms_of_service_link->header_nav_link = false;
        $terms_of_service_link->footer_nav_link = true;
        $terms_of_service_link->external = true;
        $terms_of_service_link->save();
    }
}
