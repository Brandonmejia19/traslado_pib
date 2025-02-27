<?php

namespace Cmsmaxinc\FilamentErrorPages;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\App;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class FilamentErrorPagesServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-error-pages';

    public static string $viewNamespace = 'filament-error-pages';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->askToStarRepoOnGitHub('cmsmaxinc/filament-error-pages');
            });

        $configFileName = $package->shortName();

        if (file_exists($package->basePath("/../config/{$configFileName}.php"))) {
            $package->hasConfigFile();
        }

        if (file_exists($package->basePath('/../resources/lang'))) {
            $package->hasTranslations();
        }

        if (file_exists($package->basePath('/../resources/views'))) {
            $package->hasViews(static::$viewNamespace);
        }
    }

    public function packageRegistered(): void {}

    public function packageBooted(): void
    {
        $this->registerCustomErrorHandler();
    }

    protected function registerCustomErrorHandler(): void
    {
        app(ExceptionHandler::class)
            ->renderable(function (Throwable $exception, $request) {
                if (! method_exists($exception, 'getStatusCode')) {
                    return null;
                }

                // Get the status code of the exception
                $statusCode = $exception->getStatusCode();

                // Currently, we're only handling 403 and 404 status codes
                if (! in_array($statusCode, [403, 404])) {
                    return null;
                }

                /**
                 * https://github.com/filamentphp/filament/pull/15137
                 * The current panel is null "filament()->getCurrentPanel()", so we're deriving the panel name from the request path.
                 * A more robust solution is needed in the future.
                 */
                $path = str($request->path());
                $panelName = $path->before('/')->value();
                $tenantId = $path->match('/\d+/')->value();

                // Set the current panel if it exists in the available panels
                if ($panel = filament()->getPanels()[$panelName] ?? false) {
                    filament()->setCurrentPanel($panel);

                    // Get the plugins of the current panel
                    $plugins = filament()->getCurrentPanel()->getPlugins();

                    // Check if the FilamentErrorPagesPlugin is used by the current panel
                    $usedByPanel = collect($plugins)->first(fn ($plugin) => $plugin instanceof FilamentErrorPagesPlugin);

                    if ($usedByPanel) {
                        $route = 'filament.' . $panel->getId() . '.pages.' . $statusCode;

                        // Check if the previous request was redirected to the error page
                        $isRedirected = $request->url() === route(
                            $route,
                            filament()->getCurrentPanel()->getTenantModel() ? $tenantId : null
                        );

                        // Handle NotFoundHttpException for panels
                        if (! $isRedirected) {
                            $isDefaultPanel = filament()->getCurrentPanel()->getId() === filament()->getDefaultPanel()->getId();

                            if (filament()->getPanels()[$panelName] ?? $isDefaultPanel) {
                                // https://github.com/livewire/livewire/discussions/4905#discussioncomment-7115155
                                return (new Redirector(App::get('url')))->route(
                                    $route,
                                    filament()->getCurrentPanel()->getTenantModel() ? $tenantId : null
                                );
                            }
                        }
                    }
                }

                return null;
            });
    }

    protected function getAssetPackageName(): ?string
    {
        return 'cmsmaxinc/filament-error-pages';
    }
}
