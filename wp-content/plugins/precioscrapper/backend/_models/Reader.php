<?php

require __DIR__.'./../vendor/autoload.php';
use Goutte\Client;

class Reader {
  public $crawler;
  public $url;
  public $client;
  public $status;
  public $source; // poolred...

  public function __construct($url) {
    $this->client = new Client();
    $this->crawler = $this->client->request('GET', $url);
    $this->status = $this->client->getResponse()->getStatusCode();
  }

  // returns an array with coincidences
  public function filter($selector){
    return $this->crawler->filter($selector)->extract(['_text']);
  }

  public function html(){
    return $this->crawler->html();
  }
}
