Client Error Trace
==================

A Drupal module to help track down
[HTTP 4XX client errors](https://en.wikipedia.org/wiki/List_of_HTTP_status_codes#4xx_Client_Error).

Requirements
------------

* [Plug](http://drupal.org/project/plug)

Installation and Use
--------------------

Enable the module, being sure to let Composer Manager update libraries if
required. Browse to Admin / Reports / Client Error Trace, and use the form to
run tests on any problematic URLs.

Included Plugins
----------------

* Access Content: Checks that users have the access content permission on node
  URLs.

Writing Client Error Plugins
----------------------------

This module uses a backport of the Drupal 8 plugin system to let other modules
add traces for debugging client errors. For example, many sites have custom CDN
or caching configurations. By writing a custom module with plugins for each
system to check, it is possible for Client Error Trace to provide detailed
reports.

### Examples and Documentation

* [Drupal 8 Plugins Explained](https://drupalize.me/blog/201407/drupal-8-plugins-explained).
* The "Access Content" plugin included with this module is a good starting point
  to learn how to add your own plugins to your own custom modules.
* [plug_example](https://github.com/Plug-Drupal/plug/tree/7.x-1.x/modules/plug_example)
  is also an excellent guide to the Drupal 8 plugin system.

### Steps for a new plugin

1. In your module, create a `src/Plugin/client_error` directory.
1. Create a new class in that directory that extends `ClientErrorBase` and
   implements `ClientErrorInterface`.
1. Annotate the class with the `@ClientError` annotation containing:
   * An `id` property, with the name of your plugin in underscore format.
   * A plain-text `description` property describing your plugin.
   * An integer `status_code` property describing what HTTP error code your
     plugin assists in debugging. This will generally be 403 or 404.
1. Create a new class that extends `ReportBase` and implements `ReportInterface`
   to use for reporting the results of your client error plugin. Return a new
   instance of this class from your execute() method.
1. Clear all caches.
1. Browse to `admin/reports/client-error`, and your new plugin should be listed.
