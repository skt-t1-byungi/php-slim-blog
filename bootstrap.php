<?php
require_once "vendor/autoload.php";

const ROOT_DIR = __DIR__;
const PUBLIC_DIR = ROOT_DIR . '/public';

(new \Dotenv\Dotenv(ROOT_DIR))->load();
