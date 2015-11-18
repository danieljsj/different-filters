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

	$fbImageContents = file_get_contents('http://graph.facebook.com/v2.5/'.$fbUserId.'/picture?width=300&height=300');

	return $fbImageContents;

	$app->sendFile(__DIR__.'/images/fbImages/' . $fbUserId . '.jpg' );

	$response = new Response($fbImageContents, 200);
    $response->headers->set('Content-Type', 'image/jpg');
    return $response;

	// return $app->sendFile(__DIR__.'/images/fbImages/' . $fbUserId . '.jpg' );
	// 1146150058
});

$app->run();
