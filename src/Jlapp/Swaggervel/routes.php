<?php

Route::any(Config::get('swaggervel::app.doc-route').'/{page?}', function($page='api-docs.json') {
    $filePath = Config::get('swaggervel::app.doc-dir') . "/{$page}";

    if (File::extension($filePath) === "") {
        $filePath .= ".json";
    }
    if (!File::Exists($filePath)) {
        App::abort(404, "Cannot find {$filePath}");
    }

    $content = File::get($filePath);
    return Response::make($content, 200, array(
        'Content-Type' => 'application/json'
    ));
});

Route::get('api-docs', function() {
    if (Config::get('swaggervel::app.generateAlways')) {
        $appDir = base_path()."/".Config::get('swaggervel::app.app-dir');
        $docDir = Config::get('swaggervel::app.doc-dir');

        if (!File::exists($docDir) || is_writable($docDir)) {
            // delete all existing documentation
            if (File::exists($docDir)) {
                File::deleteDirectory($docDir);
            }

            File::makeDirectory($docDir);

            $defaultBasePath = Config::get('swaggervel::app.default-base-path');
            $defaultApiVersion = Config::get('swaggervel::app.default-api-version');
            $defaultSwaggerVersion = Config::get('swaggervel::app.default-swagger-version');
            $excludeDirs = Config::get('swaggervel::app.excludes');

            $swagger = \Swagger\scan($appDir, [
               'exclude' => $excludeDirs,
            ]);

            $filename = $docDir . '/api-docs.json';
            file_put_contents($filename, $swagger);

        }
    }

    if (Config::get('swaggervel::app.behind-reverse-proxy')) {
        $proxy = Request::server('REMOTE_ADDR');
        Request::setTrustedProxies(array($proxy));
    }

    Blade::setEscapedContentTags('{{{', '}}}');
    Blade::setContentTags('{{', '}}');

    //need the / at the end to avoid CORS errors on Homestead systems.
    $response = Response::make(
        View::make('swaggervel::index', array(
                'secure'         => Request::secure(),
                'urlToDocs'      => url(Config::get('swaggervel::app.doc-route')),
                'requestHeaders' => Config::get('swaggervel::app.requestHeaders'),
                'clientId'       => Input::get("client_id"),
                'clientSecret'   => Input::get("client_secret"),
                'realm'          => Input::get("realm"),
                'appName'        => Input::get("appName"),
                'apiKey'         => Config::get('swaggervel::app.api-key'),
            )
        ),
        200
    );

    if (Config::has('swaggervel::app.viewHeaders')) {
        foreach (Config::get('swaggervel::app.viewHeaders') as $key => $value) {
            $response->header($key, $value);
        }
    }

    return $response;
});
