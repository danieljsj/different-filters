<?php

require('../vendor/autoload.php');

$app = new Silex\Application();
$app['debug'] = true;

// Register the monolog logging service
$app->register(new Silex\Provider\MonologServiceProvider(), array(
  'monolog.logfile' => 'php://stderr',
));

// Register view rendering
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));

// Our web handlers

$app->get('/', function() use($app) {
  $app['monolog']->addDebug('logging output.');
  return $app['twig']->render('index.twig');
});

$app->get('/silexImages/fbImages/{fbUserId}.jpg', function($fbUserId) use ($app) {
	$app['monolog']->addDebug('logging output.');

	$fbImageContents = file_get_contents(
		'http://graph.facebook.com/v2.5/'
		.$fbUserId
		.'/picture?width=600&height=600'
	);

	return $fbImageContents;
});

$app->run();
