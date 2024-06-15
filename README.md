# chunt2.org

> [!WARNING]
> This is not a functional backend yet!

This is the backend for chunt.org based on [Kirby 4](https://getkirby.com/) and `kirby-headless-starter`. For information on how to retrieve data via (one of) the API(s), please see the [kirby-headless-starter repository by johannschopplich](https://github.com/johannschopplich/kirby-headless-starter).

## Prerequisites

- PHP 8.1+

## Setup

### Composer

Kirby-related dependencies are managed via [Composer](https://getcomposer.org) and located in the `vendor` directory. To install them, run:

```bash
composer install
```

### Environment Variables

Duplicate the [`.env.development.example`](.env.development.example) as `.env`:

```bash
cp .env.development.example .env
```

Optionally, adapt its values.

> [!NOTE]
> Make sure to set the correct requesting origin instead of the wildcard `KIRBY_HEADLESS_ALLOW_ORIGIN=*` for your deployment.

## Usage

### KirbyQL

> 📖 [See documentation in `kirby-headless` plugin](https://github.com/johannschopplich/kirby-headless#kirbyql)

### Private vs. Public API

It's recommended to secure your API with a token. To do so, set the environment variable `KIRBY_HEADLESS_API_TOKEN` to a token string of your choice.

You will then have to provide the HTTP header `Authentication: Bearer ${token}` with each request.

> [!WARNING]
> Without a token your page content will be publicly accessible to everyone.

> [!NOTE]
> The internal `/api/kql` route will always enforce bearer authentication, unless you explicitly disable it in your config (see below).


### Panel Settings

#### Preview URL to the Frontend

With the headless approach, the default preview link from the Kirby Panel won't make much sense, since it will point to the backend API itself. Thus, we have to overwrite it utilizing a custom page method in your site/page blueprints:

```yaml
options:
  # Or `site.frontendUrl` for the `site.yml`
  preview: "{{ page.frontendUrl }}"
```

Set your frontend URL in your `.env`:

```ini
KIRBY_HEADLESS_FRONTEND_URL=https://example.com
```

If left empty, the preview button will be disabled.

#### Redirect to the Panel

Editors visiting the headless Kirby site may not want to see any API response, but use the Panel solely. To let them automatically be redirected to the Panel, set the following option in your Kirby configuration:

```php
# /site/config/config.php
return [
    'headless' => [
        'panel' => [
            'redirect' => false
        ]
    ]
];
```

### Deployment

> [!NOTE]
> See [ploi-deploy.sh](./scripts/ploi-deploy.sh) for exemplary deployment instructions.
>
> Some hosting environments require uncommenting `RewriteBase /` in [`.htaccess`](./public/.htaccess) to make site links work.
