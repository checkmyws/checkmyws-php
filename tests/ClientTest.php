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

        $body = $client->request('/dummy/5.0', "GET", NULL, 200, 1);
        $this->assertNull($body);
    }


    public function testStatus() {
        global $check_id;

        $client = new CheckmywsClient();

        $body = $client->status('123456789');
        $this->assertNull($body);

        $body = $client->status($check_id);
        $this->assertObjectHasAttribute('_id', $body);
    }

    public function testStatus_Logs() {
        global $check_id;

        $client = new CheckmywsClient();

        $body = $client->status_logs('123456789');
        $this->assertNull($body);

        $body = $client->status_logs($check_id);
        $this->assertInternalType('array', $body);
    }

    public function testStatus_Metrics() {
        global $check_id;

        $client = new CheckmywsClient();

        $body = $client->status_metrics('123456789');
        $this->assertNull($body);

        $body = $client->status_metrics($check_id);
        $this->assertObjectHasAttribute('locations', $body);

        $body = $client->status_metrics($check_id, $timewindow="day");
        $this->assertObjectHasAttribute('locations', $body);
    }

    public function testSignin() {
        $client = new CheckmywsClient();

        $account = $client->signin();

        $this->assertNull($account);
        $this->assertNull($client->account);

        $client->logout();

        // Check SSL
        $client = new CheckmywsClient("unittest", "unittest");
        $client->base_url = "https://api.dev.checkmy.ws/api";

        $account = $client->signin();
        $this->assertNull($account);

        $client = new CheckmywsClient("unittest", "unittest");
        $account = $client->signin();
        $this->assertNull($account);

        // Plain password
        $client = new CheckmywsClient("unittest", "unittest");
        $client->base_url = "https://api.dev.checkmy.ws/api";
        $client->strict_ssl = false;

        $account = $client->signin();

        $this->assertNotNull($account);
        $this->assertNotNull($client->account);
        $this->assertNotNull($client->cookie);

        $client->logout();
        $this->assertNull($client->account);
        $this->assertNull($client->cookie);

        // SHA1 password
        $client = new CheckmywsClient("unittest", "94e060874450b5ea724bb6ce5ca7be4f6a73416b");
        $client->base_url = "https://api.dev.checkmy.ws/api";
        $client->strict_ssl = false;

        $account = $client->signin();
        $this->assertNotNull($account);
        $client->logout();
    }

    public function testChecks() {
        $client = new CheckmywsClient("unittest", "unittest");
        $client->base_url = "https://api.dev.checkmy.ws/api";
        $client->strict_ssl = false;

        // Create
        $check = $client->check_create(array(
            "url" => "http://www.checkmy.ws",
            "locations" => ["FR:RBX:OVH:DC"]
        ));

        $this->assertNotNull($check->_id);

        $check_id = $check->_id;

        // Get
        $check = $client->check($check_id);
        $this->assertNotNull($check);
        $this->assertEquals($check->_id, $check_id);

        // Overview
        $overview = $client->check_overview($check_id);
        $this->assertNotNull($overview);

        // Update
        $check = $client->check_update($check->_id, array(
            "pattern" => "test"
        ));
        $this->assertEquals($check->pattern, "test");

        $check = $client->check($check_id);
        $this->assertEquals($check->pattern, "test");

        // List
        $checks = $client->checks();
        $this->assertNotNull($checks);
        $this->assertEquals(count($checks), 2);

        // Delete
        $client->check_delete($check_id);

        $check = $client->check($check_id);
        $this->assertNull($check);

        $client->logout();
    }

    public function testChecks_Metrics() {
        $check_id = "e552e72d-3953-4ea8-b68f-4516681df91a";

        $client = new CheckmywsClient("unittest", "unittest");
        $client->base_url = "https://api.dev.checkmy.ws/api";
        $client->strict_ssl = false;

        $data = $client->check_metrics($check_id, ["httptime"]);

        $this->assertEquals($data->length, 1);
        $this->assertNotNull($data->series);

        $data = $client->check_metrics($check_id, "httptime");

        $this->assertEquals($data->length, 1);
        $this->assertNotNull($data->series);
    }
}

?>