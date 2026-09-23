# LarabergCMS

Gutenberg-style content management for Laravel, built on top of [van-ons/laraberg](https://github.com/VanOns/laraberg).

Install it in a fresh Laravel application and you get a working post CRUD with the WordPress Gutenberg block editor, category support and media upload — no extra wiring needed.

This package is a simplified and modified version of Laraberg for Laravel. I created it out of frustration with how much setup and configuration was needed to get Laraberg working the way I wanted. Instead of dealing with unnecessary complexity just to have a Gutenberg-style editor in a Laravel application, I wanted something that was easier to install, configure, and use. This package is my attempt to make that experience as simple and straightforward as possible.


## Features

- Full post management (`index / create / store / show / edit / update / destroy`)
- Gutenberg block editor UI (van-ons/laraberg)
- Media upload endpoint for the editor's media library (extension and SVG/HTML sanitization built in)
- Categories relation
- Slug auto-generation with `-1`, `-2`, ... deduplication
- Publish date + status (`draft`, `published`, `archived`)
- Blade views published into your app so you can restyle them
- Ships a `CategorySeeder` with dummy categories for local development (see [Demo data](#demo-data))

## Requirements

- PHP `^8.2`
- Laravel `^11.0 | ^12.0 | ^13.0`

## Installation

```bash
composer require adeguntoro/larabergcms
```

Publish the van-ons/laraberg editor assets (the compiled `laraberg.js` / CSS used by every view):

```bash
php artisan vendor:publish --provider="VanOns\Laraberg\LarabergServiceProvider" --tag=public
```

Run the migrations:

```bash
php artisan migrate
```

Optional dummy categories for local testing (see [Demo data](#demo-data)):

```bash
php artisan db:seed --class="LarabergCms\LarabergCms\Database\Seeders\CategorySeeder"
```

Link storage for media uploads (when using the default `public` disk):

```bash
php artisan storage:link
```

Publish the local Bootstrap/React/font assets and register them with Vite:

```bash
php artisan vendor:publish --tag=larabergcms-assets
```

Then add the two files to the `input` array of your `vite.config.js`:

```js
laravel({
    input: [
        'resources/css/app.css',
        'resources/js/app.js',
        'resources/vendor/larabergcms/larabergcms.css',
        'resources/vendor/larabergcms/larabergcms.js',
    ],
    refresh: true,
}),
```

Finally build (or run the dev server) so the assets are compiled:

```bash
npm install
npm run build
```

The package ships everything locally — Bootstrap 5.3.3, React 17 and the Playfair Display / Source Sans 3 fonts — so the CMS works with no CDN dependency.

## Authentication

The CMS routes require login out of the box (`'middleware' => ['web', 'auth']`). You need a working login in your host app. Pick one of the two options below — **Option A (Breeze)** is the simplest; **Option B (Jetstream)** adds teams, 2FA and API tokens.

### Option A — Laravel Breeze (simplest)

```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
php artisan migrate
npm install
npm run build
```

You now have `/login` and `/register`.

### Option B — Laravel Jetstream

```bash
composer require laravel/jetstream
php artisan jetstream:install livewire        # or: --teams for team support
php artisan migrate
npm install
npm run build
```

Jetstream supports: `livewire`, `livewire --teams`, `inertia`, `inertia --teams` — choose whichever fits.

### Create a user (both options)

Neither Breeze nor Jetstream ships a user out of the box — you must register manually or seed one. Create a seeder:

```bash
php artisan make:seeder UserSeeder
```

`database/seeders/UserSeeder.php`:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
            ]
        );
    }
}
```

Call it from `DatabaseSeeder`:

```php
public function run(): void
{
    $this->call(UserSeeder::class);
}
```

Then seed:

```bash
php artisan db:seed --class=UserSeeder
```

Log in at `/login` with `admin@example.com` / `password`, then visit `/laraberg`.

> For admin-only access, add an admin middleware/role and set `'middleware' => ['web', 'auth']` + your admin check in `config/larabergcms.php` (see Configuration below).

## Usage

The routes are registered automatically. Visit `/laraberg` in your browser:

```
GET    /laraberg                  → post list      (larabergcms.index)
GET    /laraberg/create           → create form    (larabergcms.create)
POST   /laraberg                  → store post     (larabergcms.store)
GET    /laraberg/{id}             → show post      (larabergcms.show)
GET    /laraberg/{id}/edit        → edit form      (larabergcms.edit)
PUT    /laraberg/{id}             → update post    (larabergcms.update)
DELETE /laraberg/{id}             → delete post    (larabergcms.destroy)
POST   /laraberg/media            → media upload   (larabergcms.media)
```

Render a post's block content in your own Blade file with the model's `render()` method:

```blade
@vite(['resources/vendor/larabergcms/larabergcms.css', 'resources/vendor/larabergcms/larabergcms.js'])
<link rel="stylesheet" href="{{ asset('vendor/laraberg/css/laraberg.css') }}">

<div class="laraberg-content">
    {!! $post->render() !!}
</div>
```

> **Note:** the `laraberg.css` stylesheet is what styles rendered blocks on the front-end — include it on every page that displays block content. No editor JavaScript is required for viewing; blocks are rendered server-side as plain HTML.

The model is accessible as `LarabergCms\LarabergCms\Models\Laraberg`:

```php
use LarabergCms\LarabergCms\Models\Laraberg;

$posts = Laraberg::where('status', 'published')->latest()->paginate(10);
```

Each post belongs to a category (`larabergs.category_id`, nullable). The relations and the category model:

```php
use LarabergCms\LarabergCms\Models\Category;

$post->category;       // BelongsTo
$category->larabergs;  // HasMany
```

## Demo data

The package ships a `CategorySeeder` with dummy categories — handy while wiring the editor up locally, because it fills the category dropdown on the create/edit screens:

```bash
php artisan migrate
php artisan db:seed --class="LarabergCms\LarabergCms\Database\Seeders\CategorySeeder"
```

Seeding is idempotent: categories are matched on `slug`, so running it again updates the existing rows instead of creating duplicates. You can also call it from your own `DatabaseSeeder`:

```php
public function run(): void
{
    $this->call(\LarabergCms\LarabergCms\Database\Seeders\CategorySeeder::class);
}
```

## Configuration

Copy the config file to customize route prefix, middleware and upload disk:

```bash
php artisan vendor:publish --tag=larabergcms-config
```

Available settings in `config/larabergcms.php`:

| Key                         | Default                 | Description                                   |
|-----------------------------|-------------------------|-----------------------------------------------|
| `prefix`                    | `laraberg`              | URL prefix for all CMS routes                 |
| `middleware`                | `['web', 'auth']`       | Middleware applied to CMS routes              |
| `disk`                      | `public`                | Storage disk for media uploads                |
| `max_upload_kb`             | `51200`                 | Max upload size in kilobytes (applied to both multipart and raw uploads) |
| `enforce_allowed_extensions`| `true`                  | When `true`, only `allowed_extensions` are accepted; when `false`, any extension is accepted |
| `allowed_extensions`        | see config              | Whitelist of acceptable file extensions       |

By default all routes require login (`auth` middleware). For admin-only access, replace `auth` with your own middleware:

```php
'middleware' => ['web', 'auth', 'ensure.admin'],
```

To expose the CMS publicly (not recommended), remove `auth`:

```php
'middleware' => ['web'],
```

> **Requirement:** `auth` middleware assumes the host app has a login route. Without it, users hitting `/laraberg` are redirected to `/login` and get a 404. See the [Authentication](#authentication) section to set up Breeze or Jetstream.

## Security

- **Auth by default** — all routes require login out of the box.
- **Upload size cap** — enforced on both multipart and raw uploads against `max_upload_kb`.
- **Extension whitelist** — media uploads only allow the configured extensions (`enforce_allowed_extensions`). Executable types (`.php`, `.svg`, `.htaccess`) are excluded by default.
- **HTML/SVG content sniffing** — raw uploads are scanned for HTML/SVG markup and rejected (defense-in-depth; the extension whitelist is the primary control).
- **Trusted-author model** — post content is rendered as raw HTML server-side (`render()`). A `core/html` block or malformed content can inject script. Only trusted users who can log in should be allowed to author posts — keep `auth` on.
- Always keep the `web` middleware so CSRF protection applies.

## Customizing the views

Publish the Blade views so you can change the markup or swap Bootstrap styling:

```bash
php artisan vendor:publish --tag=larabergcms-views
```

The views will be copied to `resources/views/vendor/larabergcms/` and take precedence over the package's built-in views.

> **Careful when editing the script tags:** keep `laraberg.js` as `<script type="module">` and keep it *after* the `@vite([...])` entry. The Vite bundle is what publishes `window.React` / `window.ReactDOM`, and module scripts run in document order — removing `type="module"` or moving the tag above the `@vite` line brings back `Laraberg is not defined` (see [Troubleshooting](#troubleshooting)).

## Troubleshooting

### `Laraberg is not defined` (browser console)

The editor itself (`van-ons/laraberg`) is served as a static file from
`public/vendor/laraberg/`. If those assets were never published the `<script>`
in the create/edit views 404s and the global `Laraberg` does not exist, so
`Laraberg.init(...)` throws:

```bash
php artisan vendor:publish --provider="VanOns\Laraberg\LarabergServiceProvider" --tag=public
```

Check that `https://your-app.test/vendor/laraberg/js/laraberg.js` returns **200**
and not 404.

### `Minified React error #227` / `window.React is undefined`

`larabergcms.js` bundles React 17 + ReactDOM 17 and publishes them as
`window.React` / `window.ReactDOM` for van-ons/laraberg. If the published copy of
that file (or `public/build`) is older than this package, the stale bundle keeps
running the previous version. Re-publish the assets **with `--force`** and
rebuild:

```bash
php artisan vendor:publish --tag=larabergcms-assets --force
npm run build
```

Remember to re-run `npm run build` (or `npm run dev`) whenever the published
`resources/vendor/larabergcms/*` files change — Vite only serves what it has
compiled.

## Publishing to Packagist

1. Create a repository on GitHub (e.g. `adeguntoro/larabergcms`) and push this package.
2. Create a tag and push it: `git tag v1.0.2 && git push origin v1.0.2` — bump the version for every release; Packagist picks new tags up automatically.
3. On [packagist.org](https://packagist.org), click **Submit package**, enter the GitHub URL and submit.
4. Authenticate Packagist with the GitHub repo (Settings → Service Hooks / Webhooks) so new tags publish automatically.

## Credits

Big thanks to the **Van Ons** team for creating and maintaining [van-ons/laraberg](https://github.com/VanOns/laraberg) — the WordPress Gutenberg editor running inside Laravel. This package is a thin CMS layer on top of their excellent work; all the block-editing magic belongs to them.

Also bundled locally (all MIT-licensed):

- [Bootstrap](https://getbootstrap.com) 5.3.3
- [React](https://react.dev) 17
- [Playfair Display](https://fonts.google.com/specimen/Playfair+Display) and [Source Sans 3](https://fonts.google.com/specimen/Source+Sans+3) by Google Fonts

## License

MIT
