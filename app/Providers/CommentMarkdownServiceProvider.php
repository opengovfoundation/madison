<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use League\CommonMark\Converter;
use League\CommonMark\DocParser;
use League\CommonMark\Environment;
use League\CommonMark\HtmlRenderer;

class CommentMarkdownServiceProvider extends ServiceProvider
{
    protected $defer = true;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerEnvironment();
        $this->registerMarkdown();
    }

    /**
     * Register the environment class.
     *
     * @return void
     */
    protected function registerEnvironment()
    {
        $this->app->singleton('comment_markdown.environment', function ($app) {
            // make our normal markdown environment
            $environment = Environment::createCommonMarkEnvironment();
            $config = $app->config->get('markdown');
            $environment->mergeConfig(array_except($config, ['extensions', 'views']));

            // disable some things
            $environment->mergeConfig([
                'html_input' => 'strip',
                'allow_unsafe_links' => false,
            ]);

            foreach ((array) array_get($config, 'extensions') as $extension) {
                $environment->addExtension($app->make($extension));
            }

            return $environment;
        });
    }

    /**
     * Register the markdown class.
     *
     * @return void
     */
    protected function registerMarkdown()
    {
        $this->app->singleton('comment_markdown', function ($app) {
            $environment = $app['comment_markdown.environment'];
            $docParser = new DocParser($environment);
            $htmlRenderer = new HtmlRenderer($environment);

            return new Converter($docParser, $htmlRenderer);
        });
    }

    public function provides()
    {
        return [
            'comment_markdown.environment',
            'comment_markdown',
        ];
    }
}
