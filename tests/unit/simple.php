<?php
require __DIR__.'/../../vendor/autoload.php';
use PHPUnit\Framework\TestCase;
require __DIR__.'/../../Vkim.class.php';

class SimpleTest extends TestCase
{
    private $vkim;

    protected function setUp()
    {
        $this->vkim = new Vkim();
    }

    protected function tearDown()
    {
        $this->vkim = NULL;
    }

    public function testIsSuccessResponse()
    {
        $response1 = new stdClass();
        $this->assertEquals(false, $this->vkim->isSuccessResponse($response1));

        $response2 = new stdClass();
        $response2->error = 'Hello';
        $this->assertEquals(false, $this->vkim->isSuccessResponse($response2));
    }
}
