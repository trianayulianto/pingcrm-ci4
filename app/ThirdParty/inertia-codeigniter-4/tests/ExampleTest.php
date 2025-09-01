<?php

namespace Inertia\Tests;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\ControllerTestTrait;
use Inertia\Support\Header;

class ExampleTest extends CIUnitTestCase
{
    use ControllerTestTrait;

    public function testInertiaResponse()
    {
        helper('inertia');

        $result = $this->withUri('http://example.com')
            ->withRequest($this->request->setHeader(Header::INERTIA, 'true'))
            ->withResponse($this->response->setHeader('Content-Type', 'application/json'))
            ->controller(\Inertia\Controllers\TestController::class)
            ->execute('index');

        $this->assertTrue($result->isOK());
        $this->assertEquals('application/json', $result->response()->getHeaderLine('Content-Type'));
        $this->assertEquals(json_decode($result->response()->getBody())->component, 'Test');
    }
}
