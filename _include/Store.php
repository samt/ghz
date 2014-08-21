<?php
/*
 * ghz.me url shortener
 * when a long url hz.
 *
 * (c) 2014 Sam Thompson <contact@samt.us>
 * License: MIT
 */

namespace Ghz;

class Store
{
    /*
     * Table names
     */
    const URLS_TABLE = 'urls';
    const CLICKS_TABLE = 'clicks';

    /*
     * Singleton instnace
     */
    public static $storeInstance = null;

    /*
     * @param object db - Instnace of PDO (set by __construct)
     */
    private $db = null;

    /*
     * Get the store object
     * @param object db (optional) - PDO object
     */
    public static function getInstance()
    {
        return static::$storeInstance;
    }

    /*
     * Create a new instnace of the store singleton
     * @param object db (optional) - PDO object
     */
    public static function newInstance($db)
    {
        static::$storeInstance = new static($db);

        return static::$storeInstance;
    }

    /*
     * Constructor
     * @param object db - Instance of PDO
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    /*
     * Save a URL to the database
     *
     * @param string longurl - The long url to save
     * @return string - Short URL slug (i.e. ghz.me/$slug)
     */
    public function saveUrl($longurl)
    {
        $sql = 'INSERT INTO ' . static::URLS_TABLE . '
            (destination, clicks, ipaddr, useragent, referrer, language)
            VALUES (:dest, 0, :ip, :ua, :ref, :lang)';
        $stmt = $this->db->prepare($sql);

        $stmt->bindValue(':dest', htmlspecialchars($longurl));
        $stmt->bindValue(':ip', htmlspecialchars($_SERVER['REMOTE_ADDR']));
        $stmt->bindValue(':ua', htmlspecialchars($_SERVER['HTTP_USER_AGENT']));
        $stmt->bindValue(':ref', htmlspecialchars($_SERVER['HTTP_REFERER']));
        $stmt->bindValue(':lang', htmlspecialchars($_SERVER['HTTP_ACCEPT_LANGUAGE']));

        $stmt->execute();

        $id = $this->db->lastInsertId();

        return $id != 0 ? Base62::fromBase10($id) : null;
    }

    /*
     * Get the long URL from the database
     *
     * @param string short - The shorturl to lookup
     * @return array - Record form the database matching the short URL
     */
    public function getUrl($shorturl)
    {
        $id = Base62::toBase10($shorturl);

        $sql = 'SELECT *
            FROM ' . static::URLS_TABLE . '
            WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();

        return $stmt->fetch();
    }

    /*
     * Register a click on the URL
     *
     * @param int id - The ID of the Url
     * @return bool - everything okay?
     */
    public function clickUrl($id)
    {
        $success = false;

        $sql = 'INSERT INTO ' . static::CLICKS_TABLE . '
            (uid,ipaddr,useragent,referrer,language)
            VALUES (:uid,:ip,:ua,:ref,:lang)';
        $stmt = $this->db->prepare($sql);

        $stmt->bindValue(':uid', $id);
        $stmt->bindValue(':ip', htmlspecialchars($_SERVER['REMOTE_ADDR']));
        $stmt->bindValue(':ua', htmlspecialchars($_SERVER['HTTP_USER_AGENT']));
        $stmt->bindValue(':ref', htmlspecialchars($_SERVER['HTTP_REFERER']));
        $stmt->bindValue(':lang', htmlspecialchars($_SERVER['HTTP_ACCEPT_LANGUAGE']));

        $stmt->execute();

        if ($stmt->rowCount()) {
          $sql = 'UPDATE ' . static::URLS_TABLE . '
              SET clicks = clicks + 1
              WHERE id = :id';
          $stmt = $this->db->prepare($sql);
          $stmt->bindValue(':id', $id);
          $stmt->execute();
          $success = $stmt->rowCount() ? true : false;
        }

        return $success;
    }
}
