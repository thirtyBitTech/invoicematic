<?php

namespace Thirtybittech\Invoicematic\Tests;

use Thirtybittech\Invoicematic\ServiceProvider;
use Statamic\Testing\AddonTestCase;

abstract class TestCase extends AddonTestCase
{
    protected string $addonServiceProvider = ServiceProvider::class;
}
