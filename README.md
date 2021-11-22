### 1. Introduction:

Krayin Zoom Integration.

It packs in lots of demanding features that allows your business to scale in no time:

* Admin user can connect to their zoom account.
* User can create zoom meeting link directly from activity form


### 2. Requirements:

* **Krayin**: v1.2.2 or higher.


### 3. Installation:

* Go to the root folder of **Krayin** and run the following command

~~~php
composer require krayin/krayin-zoom-integration
~~~

* Run these commands below to complete the setup

~~~
php artisan migrate
~~~

~~~
php artisan route:cache
~~~

~~~
php artisan vendor:publish

-> Press 0 and then press enter to publish all assets and configurations.
~~~


### 4. Configuration:

* Goto .env file and add following lines

```.env
ZOOM_CLIENT_ID=
ZOOM_CLIENT_SECRET=
ZOOM_REDIRECT_URI="${APP_URL}/admin/zoom/oauth"
```

* Goto config/services.php file and add following lines

```php
return [
    // ...
    
    'zoom' => [
        // Our Zoom API credentials.
        'client_id' => env('ZOOM_CLIENT_ID'),
        'client_secret' => env('ZOOM_CLIENT_SECRET'),
        
        // The URL to redirect to after the OAuth process.
        'redirect_uri' => env('ZOOM_REDIRECT_URI'),
    ],
];
```

### 5. Clear Cache:
~~~
php artisan cache:clear

php artisan config:cache
~~~


> That's it, now just execute the project on your specified domain.
