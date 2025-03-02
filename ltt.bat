@echo off

rem -------------------------------------------------------------
rem  LTT command line bootstrap script for Windows.
rem -------------------------------------------------------------

@setlocal

set LTT=%~dp0

if "%PHP_COMMAND%" == "" set PHP_COMMAND=php.exe

"%PHP_COMMAND%" "%LTT%ltt" %*

@endlocal