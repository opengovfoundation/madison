<?php

namespace App\Providers;

use App\Models\Annotation;
use App\Models\AnnotationTypes;
use App\Models\Doc as Document;
use Form;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Laravel\Dusk\DuskServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->environment('production', 'staging') && !empty(config('services.rollbar.access_token'))) {
            $this->app->register(\Jenssegers\Rollbar\RollbarServiceProvider::class);
        }

        if ($this->app->environment('local', 'testing')) {
            $this->app->register(DuskServiceProvider::class);
        }

        Relation::morphMap([
            Annotation::ANNOTATABLE_TYPE => Annotation::class,
            Document::ANNOTATABLE_TYPE => Document::class,

            Annotation::TYPE_COMMENT => AnnotationTypes\Comment::class,
            Annotation::TYPE_FLAG => AnnotationTypes\Flag::class,
            Annotation::TYPE_LIKE => AnnotationTypes\Like::class,
            Annotation::TYPE_RANGE => AnnotationTypes\Range::class,
            Annotation::TYPE_SEEN => AnnotationTypes\Seen::class,
            Annotation::TYPE_TAG => AnnotationTypes\Tag::class,
            Annotation::TYPE_HIDDEN => AnnotationTypes\Hidden::class,
            Annotation::TYPE_RESOLVED => AnnotationTypes\Resolved::class,
        ]);

        Form::component('mInput', 'components.form.input', [
            'type',
            'name',
            'displayName',
            'value' => null,
            'attributes' => [],
            'helpText' => null,
        ]);
        Form::component('mSelect', 'components.form.select', [
            'name',
            'displayName',
            'list' => [],
            'selected' => null,
            'attributes' => [],
            'helpText' => null,
        ]);
        Form::component('mSubmit', 'components.form.submit', [
            'text',
            'attributes' => [],
        ]);

        Blade::directive('includeLocale', function ($expression) {
            $localeTemplate = str_replace('$locale', \Lang::getLocale(), $expression);
            $fallbackTemplate = str_replace('$locale', 'en', $expression);

            if (\View::exists($localeTemplate)) {
                return Blade::compileString("@include($localeTemplate)");
            } else {
                return Blade::compileString("@include($fallbackTemplate)");
            }
        });

        // https://github.com/laravel/framework/issues/15409#issuecomment-247083776
        Collection::macro('mapWithKeys_v2', function ($callback) {
            $result = [];

            foreach ($this->items as $key => $value) {
                $assoc = $callback($value, $key);

                foreach ($assoc as $mapKey => $mapValue) {
                    $result[$mapKey] = $mapValue;
                }
            }

            return new static($result);
        });
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
