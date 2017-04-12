[![Build Status](https://scrutinizer-ci.com/g/gplcart/skeleton/badges/build.png?b=master)](https://scrutinizer-ci.com/g/gplcart/skeleton/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/gplcart/skeleton/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/gplcart/skeleton/?branch=master)

Skeleton is a [GPL Cart](https://github.com/gplcart/gplcart) module that allows developers to generate blank modules with predefined class and folder structure  depending on your needs. It also extracts hooks from the source files within selected scopes and creates the corresponding methods, so you don't need to learn too much about API.

Just add some code and you're ready to go!

**Installation**

1. Download and extract to `system/modules` manually or using composer `composer require gplcart/skeleton`. IMPORTANT: If you downloaded the module manually, be sure that the name of extracted module folder doesn't contain a branch/version suffix, e.g `-master`. Rename if needed.
2. Go to `admin/module/list` end enable the module

**Usage**

Go to `admin/tool/skeleton` and generate your module