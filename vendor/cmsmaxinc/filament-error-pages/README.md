# Filament Error Pages

This plugin provides a more user-friendly error page for Filament panels when an error occurs. Outside the Filament panel, the default Laravel error page will be displayed.

![thumbnail.png](art/thumbnail.png)

## Installation

You can install the package via composer:

```bash
composer require cmsmaxinc/filament-error-pages
```

### Custom Theme

You will need to [create a custom theme](https://filamentphp.com/docs/3.x/panels/themes#creating-a-custom-theme) for the styles to be applied correctly.


Make sure you add the following to your `tailwind.config.js file.

```bash
'./vendor/cmsmaxinc/filament-error-pages/resources/**/*.blade.php',
```

## Translations
If you want to customize the translations, you can publish the translations file.

```bash
php artisan vendor:publish --tag="filament-error-pages-translations"
```

## How does it work?
When an error occurs, the plugin will check if the request is coming from a Filament panel. If it is, the custom error page will be displayed. If it is not, the default Laravel error page will be displayed.

#### Are pages outside the panel covered?
The error pages are part of the Filament panel, and the plugin is designed to work within the panel. The plugin will not cover pages outside the panel. For example if your panel base URL is `/admin`, the plugin will cover `/admin/*` but not anything outside of `/admin`.

## What pages are covered?
The plugin will cover the following error pages:
- 404 (Page not found)
- 403 (Forbidden)

## Usage

Add the plugin to the panel where you want to use it. If you have multiple panels, ensure you add it to each one. If any panel is not set up correctly, a default Laravel error page will be displayed.

```php
->plugins([
    FilamentErrorPagesPlugin::make(),
])
```