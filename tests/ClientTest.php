<?php

//Memo: https://phpunit.de/manual/current/en/appendixes.assertions.html

$check_id = "3887e18a-28d6-4eac-9eb0-c6d9075e4c7e";

class CheckmywsClientTest extends PHPUnit_Framework_TestCase {

    public function testRequest() {
        global $check_id;

        $client = new CheckmywsClient();

        $body = $client->request('/dummy', "GET");
        $this->assertContains("Dummy", $body);

        $body = $client->request('/dummy/404', "GET");
        $this->assertNull($body);

        $body = $client->request('/dummy/404', "GET", NULL, 404);
        $this->assertNotNull($body);

        $body = $client->request('/status/' . $check_id, "GET");
        $this->assertObjectHasAttribute('_id', $body);

        //$body = $client->request('/dummy/5.0', "GET", NULL, 200, 1);
        //$this->assertNull($body);
    }

    public function testStatus() {
        global $check_id;

        $client = new CheckmywsClient();

        $body = $client->status('123456789');
        $this->assertNull($body);

        $body = $client->status($check_id);
        $this->assertObjectHasAttribute('_id', $body);
    }

    public function testLogs() {
        global $check_id;

        $client = new CheckmywsClient();

        $body = $client->logs('123456789');
        $this->assertNull($body);

        $body = $client->logs($check_id);
        $this->assertInternalType('array', $body);
    }

    public function testMetrics() {
        global $check_id;

        $client = new CheckmywsClient();

        $body = $client->metrics('123456789');
        $this->assertNull($body);

        $body = $client->metrics($check_id);
        $this->assertObjectHasAttribute('locations', $body);

        $body = $client->metrics($check_id, $timewindow="day");
        $this->assertObjectHasAttribute('locations', $body);
    }

}

?>