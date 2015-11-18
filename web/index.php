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

$app->get('/getFbIdFromUrl/{fbUrl}', function($fbUrl) use ($app) {

	$app['monolog']->addDebug('logging output.');

	$postdata = http_build_query(
	    array(
	        'url' => htmlentities($fbUrl),
	    )
	);
	$opts = array('http' =>
	    array(
	        'method'  => 'POST',
	        'header'  => 'Content-type: application/x-www-form-urlencoded',
	        'content' => $postdata
	    )
	);
	$context  = stream_context_create($opts);

	$xml = file_get_contents('http://findmyfbid.com/', false, $context);

	$dom = new DOMDocument;
	$dom->loadXML($xml);
	$books = $dom->getElementsByTagName('code');
	foreach ($els as $el) {
	    return $el->nodeValue;
	}

	// ridiculous; I get a 500 when I run the script for a simple url, and 404 when I run it for a big one.
	// 0776677 500 (Internal Server Error)k.cors.a.crossDomain.send @ jquery.min.js:4n.extend.ajax @ jquery.min.js:4handleSubmit @ embedded:38ReactErrorUtils.invokeGuardedCallback @ react.js:9696executeDispatch @ react.js:2806executeDispatchesInOrder @ react.js:2829executeDispatchesAndRelease @ react.js:2269executeDispatchesAndReleaseTopLevel @ react.js:2280forEachAccumulated @ react.js:16138EventPluginHub.processEventQueue @ react.js:2485runEventQueueInBatch @ react.js:9721ReactEventEmitterMixin.handleTopLevel @ react.js:9737handleTopLevelWithoutPath @ react.js:9835handleTopLevelImpl @ react.js:9815Mixin.perform @ react.js:15620ReactDefaultBatchingStrategy.batchedUpdates @ react.js:8452batchedUpdates @ react.js:13696ReactEventListener.dispatchEvent @ react.js:9946
	// I'm officially donezors with this hacky scrapy path. I need to actually use OAuth to get the image. End of story.
});

$app->run();