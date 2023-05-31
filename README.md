# yeppinails.com

## Features

+ Dependency management with [Composer](http://getcomposer.org)
+ Easy WordPress configuration with environment specific files
+ Environment variables with [Dotenv](https://github.com/vlucas/phpdotenv)
+ Enhanced security (separated web root and secure passwords with [wp-password-bcrypt](https://github.com/roots/wp-password-bcrypt))
+ Custom domain, for example `local.yeppinails.com`
+ [WP-CLI](https://wp-cli.org/) - WP-CLI is the command-line interface for WordPress.
+ CLI scripts
	- Create a self-signed SSL certificate for using https
	- Trust certs in macOS System Keychain
	- Setup the local domain in your in `/etc/hosts`

## Requirements

* PHP >= 8.0
* Composer - [Install](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx)
+ Openssl for creating the SSL cert. If on macOS, install using Homebrew with `brew install openssl`

## Installation

1. Clone this repo

2. Update environment variables in `./.env` file:

	- `DOMAIN` - Local domain (i.e. local.yeppinails.com)

3. Create SSL cert

macOS and Linux

```shell
cd cli
./create-cert.sh
```

> Note: OpenSSL needs to be installed.

### Windows

Use the bat file in in `./cli/windows scripts/create-cert.bat`

4. Trust the cert

#### Add to macOS Keychain

Chrome and Safari will trust the certs using this script.

> In Firefox: Select Advanced, Select the Encryption tab, Click View Certificates. Navigate to where you stored the certificate and click Open, Click Import.

```shell
cd cli
./trust-cert.sh
```

#### Windows

Follow the instructions in  `./cli/windows scripts/trust-cert.txt`

5. Add the local domain in /etc/hosts

To be able to use for example `https://local.yeppinails.com` in our browser, we need to modify the `/etc/hosts` file on our local machine to point the custom domain name. The `/etc/hosts` file contains a mapping of IP addresses to URLs.

#### macOS and Linux

```shell
cd cli
./setup-hosts-file.sh
```

> The helper script can both add or remove a entry from /etc/hosts. First enter the domain name, then press "a" for add, or "r" to remove. Follow the instructions on the screen.

#### Windows

Follow the instructions in  `./cli/windows scripts/setup-hosts-file.txt`

6. Update environment variables in `./dist/.env` file:

  - `DB_NAME` - Database name
  - `DB_USER` - Database user
  - `DB_PASSWORD` - Database password
  - `DB_HOST` - Database host
  - `WP_ENVIRONMENT_TYPE` - Set to environment (`local`, `development`, `staging`, `production`)
  - `WP_HOME` - Full URL to WordPress home (https://local.yeppinails.com)
  - `WP_SITEURL` - Full URL to WordPress including subdirectory (https://local.yeppinails.com/wp)
  - `AUTH_KEY`, `SECURE_AUTH_KEY`, `LOGGED_IN_KEY`, `NONCE_KEY`, `AUTH_SALT`, `SECURE_AUTH_SALT`, `LOGGED_IN_SALT`, `NONCE_SALT`

  If you want to automatically generate the security keys (assuming you have wp-cli installed locally) you can use the very handy [wp-cli-dotenv-command][wp-cli-dotenv]:

        wp package install aaemnnosttv/wp-cli-dotenv-command

        wp dotenv salts regenerate

  Or, you can cut and paste from the [Roots WordPress Salt Generator][https://roots.io/salts.html].

7. Install WP and plugins

```shell
cd dist && composer install
```
> If you have Composer installed on your computer you can also use `cd dist && composer install`

8. Setup Front-End

From the root of the theme (i.e. `./dist/app/themes/THEME_NAME`), install Node packages:

```shell
yarn
```

9. Compile front-end code and start a watch server

From the root of the theme (i.e. `./dist/app/themes/THEME_NAME`), install Node packages:

```shell
yarn serve
```

10. Access Site

+ Front-end with BrowserSync: `https://local.yeppinails.com:3000`
+ WP admin: `https://local.yeppinails.com/wp/wp-admin`

## Plugins

### Composer Plugin management

If plugins are available by Composer, it is preferable to manage them that way, instead of committing plugin files to the repository. E.g.: `composer require wpackagist-plugin/plugin-name`

By default, Composer-managed plugins will be installed into the `plugins` directory. Plugins placed there must be activated manually. A potential downside to this is that all administrative users will also be able to disable such plugins later.

Crucial plugins can be sent to the mu-plugins folder. ["Must Use" Plugins](https://wordpress.org/support/article/must-use-plugins/) are always active and cannot be de-activated in the WordPress GUI. [roots/bedrock-autoloader](https://github.com/roots/bedrock-autoloader) is used to ensure all plugins in this directory are loaded. To have Composer install a plugin to the mu-plugins directory, in composer.json, you must add the plugin name to the `web/app/mu-plugins/{$name}/` array in the `installer-paths` object under `extra`. Include the vendor prefix, e.g. `jameelmoses/acf-icomoon` and not just `acf-icomoon`.

Keep in mind the [caveats specified in the WordPress documentation](https://wordpress.org/support/article/must-use-plugins/#caveats). Most importantly:

> Activation hooks are not executed in plugins added to the must-use plugins folder. These hooks are used by many plugins to run installation code that sets up the plugin initially and/or uninstall code that cleans up when the plugin is deleted. Plugins depending on these hooks may not function in the mu-plugins folder, and as such all plugins should be carefully tested specifically in the mu-plugins directory before being deployed to a live site.

That said, `roots/bedrock-autoloader` does specifically provide support for the `activate_{PLUGIN}` hook.

### Non-composer plugins

If a plugin cannot be managed by Composer - e.g. commercial plugins that are not available for public download - you have a few options:
* Use [junaidbhura/composer-wp-pro-plugins](https://github.com/junaidbhura/composer-wp-pro-plugins) or [ffraenz/private-composer-installer](https://github.com/ffraenz/private-composer-installer) (included in the boilerplate) to install the plugin via Composer.
* Place the plugin folder in either `plugins` or `mu-plugins` as appropriate, and add an exception to .gitignore using the ! prefix.
* Put the plugin in a private IS repository for use via Composer. Some plugins such as Gravity Forms and and FacetWP are already included in the boilerplate this way. The downside is, the private repo must be manually updated for new versions.

## Development tools

### Kint
Enable the Kint Debugger plugin on your local instance to make use of d() and other helpful dumping functions in PHP, output in Kint for better readability. See full documentation at <https://wordpress.org/plugins/kint-debugger>. The boilerplate theme adds the d() function to Twig as well:

```
{{ d(my_var) }}
```

Enable the Debug Bar plugin for a better experience. (Kint Debugger output will be accessible in the Debug Bar panel, instead of outputting on the page wherever the code happens to execute.)

## Deployments

`composer install` must be run as part of the deploy process.


### Stage and Production

#### WordPress behind a Proxy/Load Balancer
A common issue in WordPress Nginx deployments has to do with the web server being set up behind a load balancer that "terminates" HTTPS (with the load balancer sending requests to the web server over plain HTTP).

If WordPress's .env file is set with the HTTPS URL for the site - which it should be, if the site is being served to the public over HTTPS - you may encounter an infinite redirect loop. This is because WordPress relies on the `$_SERVER['HTTPS']` to tell it if the site is being served over HTTPS. By default this will correspond to the protocol used for the immediate connection to the web server. Base Nginx configurations won't check for forwarding headers from the load balancer telling it that HTTPS traffic is being forwarded. As a result, WordPress will see that the `$_SERVER['HTTPS']` variable is empty; redirect the user to a HTTPS URL of the site; from that redirect, get another request, where `$_SERVER['HTTPS']` is still empty; and so on.

To fix this, you need to do a couple things:

1. **Define an Nginx variable that can later be used to set the `$_SERVER['HTTPS']` variable.**

  Check that your main nginx.conf file is including all .conf files in its conf.d subdirectory. You'll see a line like this if so:
  ```
  include /etc/nginx/conf.d/*.conf;
  ```
  *(If not, you'll need to find an alternate way to ensure custom conf files are included. You'll likely want to check with the host before altering nginx.conf itself.)*

  Create a file in the `conf.d` folder that will be sorted at the top alphabetically, e.g. `00-https-forwarded-proto.conf`. The contents of the file should be the following **map** block. (It sets `$fastcgi_https` to be either blank or `on` depending on whether `$http_x_forwarded_proto` is equal to `https`.
  ```
  map $http_x_forwarded_proto $fastcgi_https {
    default '';
    https on;
  }
  ```

2. **Create a custom PHP/fastcgi configuration that sets `$_SERVER['HTTPS']` from the Nginx variable you just mapped.**

  Somewhere in your site's Nginx configuration file there should be a location block like this:
  ```
  location ~ \.php$ {
  ```
  Or else an `include` statement pulling in another configuration file that does have such a block.

  At the bottom of this location block (whatever file it happens to be in), add the following line:
  ```
  fastcgi_param HTTPS $fastcgi_https;
  ```

3. **Reload Nginx configuration**.

  You can check if the configuration has any syntax problems first with `nginx -t`.

  On many systems you can restart Nginx with `service nginx restart`. You can also have Nginx reload its configuration without a full restart with `nginx -s reload`.
