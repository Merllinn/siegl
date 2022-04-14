del /q d:\projects\htdocs\TP\siegl\temp\cache\*
for /d %%x in (d:\projects\htdocs\TP\siegl\temp\cache\*) do @rd /s /q "%%x"

del /q d:\projects\htdocs\TP\siegl\temp\sessions\*

del /q d:\projects\htdocs\TP\siegl\temp\translates\*

del /q d:\projects\htdocs\TP\siegl\log\*.html
del /q d:\projects\htdocs\TP\siegl\log\*.log
