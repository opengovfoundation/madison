<?php

namespace App\Providers;

use App\Models\Annotation;
use App\Models\AnnotationTypes;
use App\Models\Doc;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Relation::morphMap([
            Annotation::ANNOTATABLE_TYPE => Annotation::class,
            Doc::ANNOTATABLE_TYPE => Doc::class,

            Annotation::TYPE_COMMENT => AnnotationTypes\Comment::class,
            Annotation::TYPE_FLAG => AnnotationTypes\Flag::class,
            Annotation::TYPE_LIKE => AnnotationTypes\Like::class,
            Annotation::TYPE_RANGE => AnnotationTypes\Range::class,
            Annotation::TYPE_SEEN => AnnotationTypes\Seen::class,
            Annotation::TYPE_TAG => AnnotationTypes\Tag::class,
        ]);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
