<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

require_once __DIR__ . '/Support/FakeNuSOAP.php';

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
}
