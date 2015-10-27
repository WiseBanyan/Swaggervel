<?php
header('Access-Control-Allow-Origin: *');

header('Access-Control-Allow-Methods: GET, POST');

header("Access-Control-Allow-Headers: X-Requested-With");
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Swagger UI</title>
  {{ HTML::style('packages/jlapp/swaggervel/images/favicon-32x32.png', array('rel' => 'icon', 'type' => 'image/png', 'sizes' => '32x32'), $secure); }}
  {{ HTML::style('packages/jlapp/swaggervel/images/favicon-16x16.png', array('rel' => 'icon', 'type' => 'image/png', 'sizes' => '16x16'), $secure); }}
  {{ HTML::style('packages/jlapp/swaggervel/css/typography.css', array('media' => 'screen'), $secure); }}
  {{ HTML::style('packages/jlapp/swaggervel/css/reset.css', array('media' => 'screen'), $secure); }}
  {{ HTML::style('packages/jlapp/swaggervel/css/reset.css', array('media' => 'print'), $secure); }}
  {{ HTML::style('packages/jlapp/swaggervel/css/screen.css', array('media' => 'screen'), $secure); }}
  {{ HTML::style('packages/jlapp/swaggervel/css/screen.css', array('media' => 'print'), $secure); }}

  {{ HTML::script('packages/jlapp/swaggervel/lib/jquery-1.8.0.min.js', array(), $secure); }}
  {{ HTML::script('packages/jlapp/swaggervel/lib/jquery.slideto.min.js', array(), $secure); }}
  {{ HTML::script('packages/jlapp/swaggervel/lib/jquery.wiggle.min.js', array(), $secure); }}
  {{ HTML::script('packages/jlapp/swaggervel/lib/jquery.ba-bbq.min.js', array(), $secure); }}
  {{ HTML::script('packages/jlapp/swaggervel/lib/handlebars-2.0.0.js', array(), $secure); }}
  {{ HTML::script('packages/jlapp/swaggervel/lib/underscore-min.js', array(), $secure); }}
  {{ HTML::script('packages/jlapp/swaggervel/lib/backbone-min.js', array(), $secure); }}
  {{ HTML::script('packages/jlapp/swaggervel/swagger-ui.js', array(), $secure); }}
  {{ HTML::script('packages/jlapp/swaggervel/lib/highlight.7.3.pack.js', array(), $secure); }}
  {{ HTML::script('packages/jlapp/swaggervel/lib/marked.js', array(), $secure); }}
  {{ HTML::script('packages/jlapp/swaggervel/lib/swagger-oauth.js', array(), $secure); }}

          <!-- Some basic translations -->
    <!-- <script src='lang/translator.js' type='text/javascript'></script> -->
    <!-- <script src='lang/ru.js' type='text/javascript'></script> -->
    <!-- <script src='lang/en.js' type='text/javascript'></script> -->

    <script type="text/javascript">
        $(function () {

            var url = window.location.search.match(/url=([^&]+)/);
            if (url && url.length > 1) {
                url = decodeURIComponent(url[1]);
            } else {
                url = "{{ $urlToDocs }}";
            }

            // Pre load translate...
            if(window.SwaggerTranslator) {
                window.SwaggerTranslator.translate();
            }
            window.swaggerUi = new SwaggerUi({
                url: url,
                dom_id: "swagger-ui-container",
                supportedSubmitMethods: ['get', 'post', 'put', 'delete', 'patch'],
                onComplete: function (swaggerApi, swaggerUi) {
                    log("Loaded SwaggerUI");
            @if (isset($requestHeaders))
                @foreach($requestHeaders as $requestKey => $requestValue)
                    window.authorizations.add("{{$requestKey}}", new ApiKeyAuthorization("{{$requestKey}}", "{{$requestValue}}", "header"));
                @endforeach
            @endif

                    if (typeof initOAuth == "function") {
                        initOAuth({
                            clientId: "{{ $clientId }}"||"my-client-id",
                            clientSecret: "{{ $clientSecret }}"||"_",
                            realm: "{{ $realm }}"||"_",
                            appName: "{{ $appName }}"||"_",
                            scopeSeparator: ","
                        });

                        window.oAuthRedirectUrl = "{{ url('packages/jlapp/swaggervel/o2c.html') }}";
                        $('#clientId').html("{{ $clientId }}"||"my-client-id");
                        $('#redirectUrl').html(window.oAuthRedirectUrl);
                    }

                    if (window.SwaggerTranslator) {
                        window.SwaggerTranslator.translate();
                    }

                    $('pre code').each(function (i, e) {
                        hljs.highlightBlock(e)
                    });

                    addApiKeyAuthorization();
                },
                onFailure: function (data) {
                    log("Unable to Load SwaggerUI");
                },
                docExpansion: "none",
                apisSorter: "alpha",
                showRequestHeaders: false
            });

            function addApiKeyAuthorization() {
                var key = encodeURIComponent($('#input_apiKey')[0].value);
                if (key && key.trim() != "") {
                    var apiKeyAuth = new SwaggerClient.ApiKeyAuthorization("{{$apiKey}}", key, "query");
                    window.swaggerUi.api.clientAuthorizations.add("{{$apiKey}}", apiKeyAuth);
                    log("added key " + key);
                } else {
                    window.swaggerUi.api.clientAuthorizations.remove('{{$apiKey}}');
                }
            }

            $('#input_apiKey').change(addApiKeyAuthorization);

            // if you have an apiKey you would like to pre-populate on the page for demonstration purposes...
            /*
             var apiKey = "myApiKeyXXXX123456789";
             $('#input_apiKey').val(apiKey);
             */

            $('#init-oauth').click(function(){
                if (typeof initOAuth == "function") {
                    initOAuth({
                        clientId: $('#input_clientId').val()||"my-client-id",
                        clientSecret: $('#input_clientSecret').val()||"_",
                        realm: $('#input_realm').val()||"_",
                        appName: $('#input_appName').val()||"_",
                        scopeSeparator: ","
                    });
                }
            });

            window.swaggerUi.load();

            function log() {
                if ('console' in window) {
                    console.log.apply(console, arguments);
                }
            }
        });
    </script>
</head>
<body class="swagger-section">
<div id='header'>
    <div class="swagger-ui-wrap">
        <a id="logo" href="http://swagger.wordnik.com">swagger</a>

        <form id='api_selector'>
            <div class='input icon-btn'>
                {{ HTML::image('packages/jlapp/swaggervel/images/pet_store_api.png', "", array('id' => 'show-pet-store-icon', 'title' => 'Show Swagger Petstore Example Apis'), $secure); }}
            </div>
            <div class='input icon-btn'>
                {{ HTML::image('packages/jlapp/swaggervel/images/wordnik_api.png', "", array('id' => 'show-wordnik-dev-icon', 'title' => 'Show Wordnik Developer Apis'), $secure); }}
            </div>
            <div class='input'><input placeholder="http://example.com/api" id="input_baseUrl" name="baseUrl" type="text"/></div>
            <div class='input'><input placeholder="{{$apiKey}}" id="input_apiKey" name="apiKey" type="text"/></div>
            <div class='input'><a id="explore" href="#" data-sw-translate>Explore</a></div>
        </form>
    </div>
</div>

<div id="message-bar" class="swagger-ui-wrap" data-sw-translate>&nbsp;</div>
<div id="swagger-ui-container" class="swagger-ui-wrap"></div>
</body>
</html>
