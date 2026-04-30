@echo off
set PHPRC=%~dp0tools\php84
"%~dp0tools\php84\php.exe" artisan serve --host=127.0.0.1 --port=8086