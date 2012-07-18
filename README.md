Robots.txt parser [![Build Status](https://secure.travis-ci.org/webignition/robots-txt-parser.png?branch=master)](http://travis-ci.org/webignition/robots-txt-parser)
====================

Overview
---------

A parser for robots.txt files. Give it the content of a robots.txt file, it gives
you back a [robots-txt-file](https://github.com/webignition/robots-txt-file) object
for you to play with.

Usage
-----

### The "Hello World" example for finding a sitemap URL

```php
<?php
$parser = new \webignition\RobotsTxt\File\Parser();
$parser->setSource(file_get_contents('http://webignition.net/robots.txt'));

$file = $parser->getFile();

$sitemapDirectives = $file->directiveList()->filter(array('field' => 'sitemap'))->get();
$firstSitemapDirective = $sitemapDirectives[0];

$this->assertEquals('http://webignition.net/sitemap.xml', (string)$firstSitemapDirective->getValue());
```

Building
--------

#### Using as a library in a project

If used as a dependency by another project, update that project's composer.json
and update your dependencies.

    "require": {
        "webignition/robots-txt-parser": "*"      
    }

#### Developing

This project has external dependencies managed with [composer][3]. Get and install this first.

    # Make a suitable project directory
    mkdir ~/robots-txt-parser && cd ~/robots-txt-parser

    # Clone repository
    git clone git@github.com:webignition/robots-txt-parser.git.

    # Retrieve/update dependencies
    composer.phar install

Testing
-------

Have look at the [project on travis][4] for the latest build status, or give the tests
a go yourself.

    cd ~/robots-txt-parser
    phpunit tests


[3]: http://getcomposer.org
[4]: http://travis-ci.org/webignition/robots-txt-parser/builds