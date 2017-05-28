<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Models\Sponsor;
use App\Models\Doc as Document;
use App\Models\DocContent;
use App\Models\Setting;

class DocumentsTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker\Factory::create();
        $sponsor = Sponsor::where([ 'status' => Sponsor::STATUS_ACTIVE ])->first();

        $documents = factory(Document::class, (int)config('madison.seeder.num_docs'))->create([
            'publish_state' => Document::PUBLISH_STATE_PUBLISHED,
        ])->each(function ($document) use ($sponsor, $faker) {
            $document->sponsors()->attach($sponsor);
            $document->setIntroText($faker->paragraphs($faker->numberBetween(1, 10), true));
            $document->content()->save(factory(DocContent::class)->make());
        });

        $documents->first()->setAsFeatured();
    }
}
