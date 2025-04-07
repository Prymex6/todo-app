# To-Do List Application

Aplikacja zarządzania zadaniami z pełną funkcjonalnością CRUD, powiadomieniami i integracją z Google Calendar.

## Wymagania

- PHP 8.2+
- Composer
- MySQL 5.7+ lub SQLite
- Node.js (opcjonalnie dla assetów)

## Instalacja

1. Sklonuj repozytorium:
git clone https://github.com/twoje-repo/todo-app.git
cd todo-app
Zainstaluj zależności:

composer install
npm install
Skonfiguruj środowisko:

cp .env.example .env
php artisan key:generate

Edytuj .env i ustaw:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=todo_app
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=

Wykonaj migracje:

php artisan migrate --seed

Uruchom serwer:

php artisan serve

Uruchomienie workerów
W osobnym terminalu uruchom kolejkowanie:

php artisan queue:work

Uruchom scheduler (dla powiadomień):

php artisan schedule:work

Pełny CRUD zadań

Filtrowanie zadań

Powiadomienia mailowe

Udostępnianie zadań przez link

Historia zmian

Integracja z Google Calendar