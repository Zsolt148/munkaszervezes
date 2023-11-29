# Munkaszervezes development guide #

### Configuration ###

- NodeJS https://nodejs.org/en/
- Composer https://getcomposer.org/

1. Clone the project

2. Go to the folder application using cd command on your cmd or terminal

3. Run ```composer install --ignore-platform-reqs``` and ```npm install```

4. Run ```copy .env.example .env``` or ```cp .env.example .env```

5. Open your .env file and change the database name (DB_DATABASE) to whatever you have, username (DB_USERNAME) and password (DB_PASSWORD) field correspond to your configuration.

6. Run ```php artisan key:generate```

7. Run ```php artisan optimize:clear```

8. Run ```php artisan migrate --seed```

9. Run ```php artisan storage:link```

10. Run ```npm run dev```. For production run ```npm run build```.

11. Run ```php artisan serve```

### Admin ###
As long as the application is running local mode the developer account(s) are seeded automatically.

Admin URL: /dashboard

##### Default credentials
##### `email: admin@admin.com`
##### `password: secret`


