@echo off
setlocal enabledelayedexpansion
:menu
cls
echo Select an command:
echo 1. Create Model
echo 2. Create Controller
echo 3. Exit

set /p choice=Enter the number of your choice:

if "%choice%"=="1" (
    goto createEntity
) else if "%choice%"=="2" (
    goto createController
) else if "%choice%"=="3" (
    exit /b 0
) else (
    goto menu
)
:createEntity
cls
set /p class=Enter a class name:
set /p table=Enter a table name:

REM Set your source file name and relative path
set "sourceFileName=model_template.txt"
set "sourceRelativePath=Core\Templates"

REM Set your destination relative path
set "destinationRelativePath=src\Model"

REM Set the variable values
set "variable1=%class%"
set "variable2=%table%"

REM Build the full paths
set "sourceFilePath=%cd%\%sourceRelativePath%\%sourceFileName%"
set "destinationPath=%cd%\%destinationRelativePath%"
set "destinationFilePath=%destinationPath%\%class%.php"

REM Copy the file
copy "%sourceFilePath%" "%destinationFilePath%"

REM Replace {var1} and {var2} with the batch variable values in the new file
set "search1={className}"
set "replace1=!variable1!"
set "search2={table}"
set "replace2=!variable2!"

REM Process the file with delayed expansion
(for /f "delims=" %%a in ('findstr /n "^" "%destinationFilePath%"') do (
    set "line=%%a"
    set "line=!line:*:=!"
    if not defined line (
        echo.
    ) else (
        set "line=!line:%search1%=%replace1%!"
        set "line=!line:%search2%=%replace2%!"
        echo !line!
    )
)) > "%destinationFilePath%.temp"

move /y "%destinationFilePath%.temp" "%destinationFilePath%"

echo File copied and variables replaced successfully!

endlocal
goto end

@REM -----------------------------------------------------------------Controller

:createController
cls
set /p controllerName=Enter a controller name:

REM Set your source file name and relative path
set "sourceFileName=controller_template.txt"
set "sourceRelativePath=Core\Templates"

REM Set your destination relative path
set "destinationRelativePath=src\Controller"

REM Set the variable values
set "variable1=%controllerName%"

REM Build the full paths
set "sourceFilePath=%cd%\%sourceRelativePath%\%sourceFileName%"
set "destinationPath=%cd%\%destinationRelativePath%"
set "destinationFilePath=%destinationPath%\%controllerName%Controller.php"

REM Copy the file
copy "%sourceFilePath%" "%destinationFilePath%"

REM Replace {var1} and {var2} with the batch variable values in the new file
set "search1={controllerName}"
set "replace1=!variable1!"

REM Process the file with delayed expansion
(for /f "delims=" %%a in ('findstr /n "^" "%destinationFilePath%"') do (
    set "line=%%a"
    set "line=!line:*:=!"
    if not defined line (
        echo.
    ) else (
        set "line=!line:%search1%=%replace1%!"
        echo !line!
    )
)) > "%destinationFilePath%.temp"

move /y "%destinationFilePath%.temp" "%destinationFilePath%"

echo File copied and variables replaced successfully!

endlocal
:end