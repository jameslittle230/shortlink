# Shortlink :link:

A lightweight, file-based URL shortening service written in PHP. Meant for web servers with no available database service.

Written for <http://jil.im>.

Allows for custom shortlinks and password-protected namespaces (for example, one password can be used for urls like `jil.im/foo` and another password can be used for only urls like `jil.im/bar/baz`).

To get started, rename `secret_template.php` to `secret.php` and edit accordingly.
