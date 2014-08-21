<?php
/*
 * ghz.me url shortener
 * when a long url hz.
 *
 * (c) 2014 Sam Thompson <contact@samt.us>
 * License: MIT
 */

use Ghz\Base62;
use Ghz\Store;

if (!defined('ROOT_PATH')) exit;

require ROOT_PATH . 'config.php';
require ROOT_PATH . '_include/Base62.php';
require ROOT_PATH . '_include/Store.php';
require ROOT_PATH . '_include/App.php';

try {
  $db = new PDO(DB_DSN, DB_USER, DB_PASS);
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
}
catch (PDOException $e) {
  // there's not much we can do about a borked database. Log it to the OS-level
  // error log and alert the user.
  error_log('DB Connection failed: ' , $e->getMessage());
  echo 'something went wrong...<br>';
  echo 'we\'ve been alerted of the problem and are in the process of fixing it';
  exit;
}

Store::newInstance($db);
