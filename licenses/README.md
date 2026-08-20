# Third-party licenses

Gleez CMS project code is licensed under the MIT License; see [`LICENSE`](../LICENSE)
at the repository root (also declared in `composer.json`).

This directory holds license texts for bundled third-party components that are
not covered by Composer’s `vendor/` tree.

| Component                                      | Version in tree | License file(s)                                              |
|------------------------------------------------|-----------------|--------------------------------------------------------------|
| Kohana Framework                               | 3.5.0           | [Kohana.txt](Kohana.txt)                                     |
| Zend Framework Locale (adapted)                | 1.12            | [Zend.txt](Zend.txt)                                         |
| jQuery                                         | 2.2.4           | [jQuery.txt](jQuery.txt)                                     |
| Bootstrap                                      | 3.4.1           | [Bootstrap.txt](Bootstrap.txt)                               |
| DataTables                                     | 2.3.6           | [DataTables.txt](DataTables.txt)                             |
| Font Awesome Free                              | 7.2.0           | [FontAwesome.txt](FontAwesome.txt), [OFL1.1.txt](OFL1.1.txt) |
| TinyMCE                                        | 8.3.2           | [GPL2.txt](GPL2.txt) (open-source edition)                   |
| DOMPurify (via TinyMCE)                        | 3.2.6           | [Apache2.txt](Apache2.txt)                                   |
| Kohana UTF-8 helpers / jQuery Form (LGPL path) | —               | [LGPL2.1.txt](LGPL2.1.txt)                                   |
| Dual-licensed jQuery plugins (MIT or GPL-2.0)  | —               | [jQuery.txt](jQuery.txt), [GPL2.txt](GPL2.txt)               |

Composer packages (PHPMailer, php-markdown, PHPUnit, etc.) ship their own license
files under `vendor/`.
