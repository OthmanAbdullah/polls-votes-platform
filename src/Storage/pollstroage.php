<?php
include_once("DB/users.json");
class PollStorage extends Storage {
  public function __construct() {
    parent::__construct(new JsonIO('polls.json'));
  }
}