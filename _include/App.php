<?php
/*
 * ghz.me url shortener
 * when a long url hz.
 *
 * (c) 2014 Sam Thompson <contact@samt.us>
 * License: MIT
 */

namespace Ghz;

class App
{
    /*
     * Results... for noscripters.
     */
    private $results = null;

    /*
     * Get the app's base URL with a trailing or appended path
     *
     * @param string append - Append this to the end of the URL
     * @return string - valid URL with scheme point to either the app's home or
     *    the path specified in append
     */
    public static function getUrl($append = '')
    {
      return ( empty($_SERVER['HTTPS']) ? 'http://' : 'https://' ) .
          $_SERVER['HTTP_HOST'] . \BASENAME . ltrim($append, '/');
    }

    /*
     * Setup some basic things
     */
    public function __construct()
    {
        $this->results = new \stdClass;
        $this->results->success = null;
        $this->results->url = '';
    }

    /*
     * Checks if a redirect link is being requested and send them off.
     *
     * @param void
     * @return void
     */
    public function doRedirect()
    {
        if ($_SERVER['REQUEST_URI'] !== \BASENAME) {
            ob_start();

            $b62id = basename($_SERVER['REQUEST_URI']);
            $url = Store::getInstance()->getUrl($b62id);

            if (!isset($url->id)) {
              $url = new \stdClass;
              $url->destination = static::getUrl();
            }

            // 302 found to ensure we can continue tracking clicks
            header('HTTP/1.1 302 Found');
            header('Location: ' . $url->destination);

            // just in case
            echo '<!DOCTYPE html><html><head>';
            echo '<meta http-equiv="refresh" content="0; url=';
            echo $url->destination . '"></head></html>';

            // user is sent headers now, they're long gone by now.
            ob_flush();

            // only set when we had a good url returned
            if (isset($url->id)) {
                Store::getInstance()->clickUrl($url->id);
            }

            exit;
        }
    }

    /*
     * Save the posted URL to the database
     * This will save the URL and either output JSON for an xhr or return
     * silently but populating the $results property for the getters to read
     * from and display proper error messages.
     *
     * @return void
     */
    public function savePostedUrl()
    {
        if (isset($_POST['url'])) {

            $url = $_POST['url'];
            $this->results->success = false;
            $this->results->url = '';

            // try to fix a URL without a scheme
            $urlParts = parse_url($url);
            if (empty($urlParts['scheme'])) {
                $url = 'http://' . ltrim($url, '/');
            }

            $urlParts = parse_url($url);

            // must be valid AND must not link to itself.
            if (filter_var($url, \FILTER_VALIDATE_URL) &&
                (empty($urlParts['host']) || $urlParts['host'] != $_SERVER['HTTP_HOST'])
            ) {
                $this->results->url = static::getUrl(
                  Store::getInstance()->saveUrl($url)
                );

                $this->results->success = true;
            }

            // Anyone can easily spoof this header, but it's no threat to the
            // integrity of this system. I am giving them the same information
            // whether it be in JSON or HTML.
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
              header('Content-Type: application/json');
              echo json_encode($this->results);
              exit;
            }
        }
    }

    /*
     * Get the generated URL from the insert operation
     *
     * @return string - new URL. Empty if error or no postdata
     */
    public function getGeneratedUrl() {
      return $this->results->url;
    }

    /*
     * NOTE: The below functions are NOT inverses of each other.
     * the Success flag has three (3) states:
     * - True   = POST request AND URL created
     * - False  = POST request AND URL creation failure
     * - Null   = No POST Request
     */

    /*
     * Has the URL creation been a success?
     * For no-script users
     *
     * @return bool - Success flag set to true?
     */
    public function isSuccess() {
      return $this->results->success === true;
    }


    /*
     * Has the URL creation been a failure?
     * For no-script users
     *
     * @return bool - Success flag set to false?
     */
    public function isFailure() {
      return $this->results->success === false;
    }
}
