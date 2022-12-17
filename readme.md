# Installation: 

1- Composer require colbeh/logs

2- add this to config/app.php

        Rap2hpoutre\LaravelLogViewer\LaravelLogViewerServiceProvider::class,
        
3- define gate in AppServiceProvider :

        Gate::define('viewLogs', function ($user) {
            return in_array($user->username, [
                'admin'
                // any admin username 
            ]);
        });

4- Go to 

view daily logs : 

        domain.com/log-viewer
                
view laravel log:
 
        domain.com/log-viewer/laravel
        

# Upgrade:
 
        composer require colbeh/logs:x.x.x


