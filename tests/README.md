#Instalar y ejecutar composer
Desde la raíz de moodle ejecutar los comandos que aparecen en: https://getcomposer.org/download/
Para ejecutarlo: `php composer.phar install`

#Inicializar el ambiente de pruebas
```
php admin/tool/phpunit/cli/init.php
```

# Ejecución
Con el ambiente de pruebas inicializado y desde la carpeta raíz de moodle, ejecutar `vendor/bin/phpunit --group block_ases`

##Más información
https://docs.moodle.org/dev/PHPUnit
