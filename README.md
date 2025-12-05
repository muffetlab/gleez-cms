# What Is Gleez CMS?

[Gleez CMS](https://gleezcms.org) is a user-friendly website [Content Management System](http://en.wikipedia.org/wiki/CMS).

With [Gleez CMS](https://gleezcms.org) you can easily build dynamic websites within a matter of minutes with just the click of your mouse! Maintain your web content, navigation and even limit what groups or specific users can access, from anywhere in the world with just a web browser!

With an emphasis on security and functionality, [Gleez CMS](https://gleezcms.org) is a professional and robust system suitable for any business or organization website. Built on the [PHP](http://php.net) programming language and the [MySQL](http://www.mysql.com) database, [Gleez CMS](https://gleezcms.org) delivers superb performance on any size website.

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
* Input formats like Markdown, HTML etc
* Shortcodes
* oAuth2 login via Google/Facebook/Windows/Github
* Resize images on fly with caching
* ORM, MongoDB, Redis support
* Other Details like SEO, Media, Gravatar, etc.

# Demo

You can visit our demo site to become familiar with the basic features of Gleez CMS

[https://demo.gleezcms.org](https://demo.gleezcms.org)

| Login details | Typical user | Administrator |
| ------------- |:------------:| -------------:|
| *Username*    | demo         | demoadmin     |
| *Password*    | demo         | demoadmin     |

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

***

[Official Website](https://gleezcms.org) | [Facebook Page](https://www.facebook.com/gleezcms) | [License](https://github.com/gleez/cms/wiki/License) | [Contributors](https://github.com/gleez/cms/wiki/Contributors)
