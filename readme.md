Step 1:
Clone Project
Step 2: Run this command: composer install
Step 3:
+ Add file .env by editing file .env.example
+ Generate APP_KEY by run this command: php artisan key:generate
+ Edit file config in .env such as: Database(DB_DATABASE, DB_USERNAME, DB_PASSWORD,...), Mail(MAIL_USERNAME, MAIL_PASSWORD, MAIL_ENCRYPTION)
+ Generate JWT_SECRET Key by run this command: php artisan jwt:secret
Step 4: Migration & seed
+ Run this command: php artisan migrate --seed

Error: 404 Not Found when you Execute Api Swagger, if you see host was wrong. Open app/swagger-template/swagger-v1.php and add this text: 
            host="localhost/test/maxdino_backend/public"
below: schemes={"http", "https"},

Link API SWAGGER: host + /api/documentation