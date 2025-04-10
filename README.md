This is a Laravel test to show waterfall comments that can be replied to without reloading the page and the backend was supposed to be Laravel or Zend Framework. With Laravel, the decision was to use services and repositories and not straight model use to reference the database. This keeps the code more maintainable and testable as well as keeping database queries structured.

## Comments frontend & CMS
Simple frontend to add comments and CMS to delete.

## Installation instructions
Run:
composer install

## Database
Currently using SQLite

Run the following migration once:
php artisan migrate

## Starting
Run:
php artisan serve