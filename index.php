<?php
/*
 * ghz.me url shortener
 * when a long url hz.
 *
 * (c) 2014 Sam Thompson <contact@samt.us>
 * License: MIT
 */

define('ROOT_PATH', __DIR__ . '/');
require ROOT_PATH . 'loader.php';

$app = new Ghz\App();

// Handle our two cases:
// - Redirect (when requestiong anything except BASEPATH)
// - Save URL when posted here.
$app->doRedirect();
$app->savePostedUrl();

?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>GHz url shortener</title>
    <link rel="stylesheet" href="_assets/style.css">

    <?php if (defined('GA_TRACKING')) : ?>
      <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

        ga('create', '<?php echo GA_TRACKING; ?>', 'auto');
        ga('send', 'pageview');

      </script>
    <?php endif; ?>

  </head>
  <body>
    <div class="main">
      <h1>
        <a href="/">Ghz</a>
      </h1>

      <p class="subhead">url shortener</p>

      <form action="" method="post">

        <div class="error<?php if ($app->isFailure()) : ?> visible<?php endif; ?>">
          please enter a valid url
        </div>
        <div class="success<?php if ($app->isSuccess()) : ?> visible<?php endif; ?>">
          created!
        </div>

        <div>
          <input placeholder="paste or enter a url and press enter" name="url"
            type="text" value="<?php echo $app->getGeneratedUrl(); ?>">
        </div>
      </form>

    </div>

    <div class="bottom-left">
      &copy; <?php echo date('Y'); ?> Ghz.me
    </div>

    <div class="bottom-right">
      &nbsp;
    </div>

    <!-- fork me banner -->
    <a href="https://github.com/samt/ghz">
      <img style="position: absolute; top: 0; right: 0; border: 0;" src="https://camo.githubusercontent.com/38ef81f8aca64bb9a64448d0d70f1308ef5341ab/68747470733a2f2f73332e616d617a6f6e6177732e636f6d2f6769746875622f726962626f6e732f666f726b6d655f72696768745f6461726b626c75655f3132313632312e706e67" alt="Fork me on GitHub" data-canonical-src="https://s3.amazonaws.com/github/ribbons/forkme_right_darkblue_121621.png">
    </a>

    <script type="text/javascript" src="_assets/jquery-2.1.1.min.js"></script>
    <script type="text/javascript" src="_assets/main.js"></script>
  </body>
</html>
