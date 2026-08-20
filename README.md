# What Is Gleez CMS?

[Gleez CMS](https://gleez.muffetlab.com) is a user-friendly website [Content Management System](http://en.wikipedia.org/wiki/CMS).

With [Gleez CMS](https://gleez.muffetlab.com) you can easily build dynamic websites within a matter of minutes with just the click of your mouse! Maintain your web content, navigation and even limit what groups or specific users can access, from anywhere in the world with just a web browser!

With an emphasis on security and functionality, [Gleez CMS](https://gleez.muffetlab.com) is a professional and robust system suitable for any business or organization website. Built on the [PHP](http://php.net) programming language and the [MySQL](http://www.mysql.com) database, [Gleez CMS](https://gleez.muffetlab.com) delivers superb performance on any size website.

# Supported Versions

| Release | Supported PHP versions | Released    | Active Support | Security Support | Latest                                                              |
|---------|------------------------|-------------|----------------|------------------|---------------------------------------------------------------------|
| 1.3     | 7.1 – 7.3              | 20 Aug 2026 | Yes            | Yes              | [1.3.0](https://github.com/muffetlab/gleez-cms/releases/tag/v1.3.0) |
| 1.2     | 5.4 – 5.6              | 14 Jun 2015 | End of life    | End of life      | [1.2.0](https://github.com/muffetlab/gleez-cms/releases/tag/v1.2.0) |
| 1.1     | 5.3 – 5.5              | 04 Aug 2014 | End of life    | End of life      | [1.1.5](https://github.com/gleez/cms/releases/tag/1.1.5)            |
| 1.0     | 5.3 – 5.5              | 02 Jun 2014 | End of life    | End of life      | [1.0.1](https://github.com/gleez/cms/releases/tag/1.0.1)            |

# Features

* Website Navigation and Web Pages
* Users, User Groups, and Permissions
* Contents or Articles for News or Blogging
* Content Categories
* Content Comments
* Content Tagging
* Content Blocks or Widgets
* Extensions, Modules or Plugins
* Themes and Layouts
* Input formats like Markdown, HTML, etc.
* Shortcodes
* OAuth2 login via Google/Facebook/Windows/GitHub
* Resize images on fly with caching
* ORM, MongoDB, Redis support
* Other Details like SEO, Media, Gravatar, etc.

# Internationalization (i18n Support)

The available locale(s) in Gleez are:

+ English
+ Estonian
+ Chinese (Simplified)
+ Russian
+ Indonesian
+ Italian
+ Romanian

# Installation

## Disclaimer

Please note that Muffet Laboratory cannot be held responsible for anything that results from the following instructions.

## Downloading and Unpacking

There are two ways to get Gleez: via a Git client, or via the GitHub web interface.

### Git Client

Open a terminal, go to the directory where you want the project, and run:

```
git clone git://github.com/muffetlab/gleez-cms.git gleez
```

### Git Web Interface

Click the `Code` -> `Download ZIP` button on GitHub and extract the downloaded archive to a folder named `gleez`.

## Trusted Hosts Setup

Copy the example URL config to the application config folder:

```
cp system/config/url.php application/config/url.php
```

Open `application/config/url.php` and add your hostname(s) to the `trusted_hosts` array.

## File Permissions

Ensure the web server can write to cache, logs and uploads. Example for Linux (adjust user if needed):

```
chmod +w application/cache application/logs public/media
```

## Install PHP Dependencies

From the project root (`gleez`) run:

```
composer install
```

## Setup via Web Installer

Point your browser to the `gleez` folder (e.g. `http://localhost/gleez`). The installer will walk you through a few
steps. On the final step the installer displays a generated username and password — copy these credentials to sign in as
an administrator.

## Editing Content

Log in with the credentials produced by the installer and manage content from the admin interface.

# License

Gleez CMS is released under the [MIT License](LICENSE). Third-party attributions for bundled assets are listed in [`licenses/README.md`](licenses/README.md).
