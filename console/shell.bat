@echo.
@echo off

SET app=%0
SET lib=%~dp0

php -q "%lib%shell.php" -working "%CD% " %*

echo.

exit /B %ERRORLEVEL%