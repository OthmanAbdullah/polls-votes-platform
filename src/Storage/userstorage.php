<?php
include_once("storage.php");
class UserStorage extends Storage {
  public function __construct($filePath) {
    parent::__construct(new JsonIO($filePath)  );
  }
}