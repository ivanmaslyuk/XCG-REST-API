<?php


class ApiResponse
{
    public $response;

    public function __construct($response)
    {
        $this->response = $response;
    }

    public function send() {
        exit(json_encode(array("response" => $this->response)));
    }
}