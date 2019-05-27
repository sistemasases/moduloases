# Módulo Ases

Es un plugin de moodle de tipo bloque, que está diseñado para sistematizar el proceso de seguimiento estudiantil que se realiza dentro de la estrategia ASES. Es una aplicación web que sirve para gestionar la información de la estrategia ASES.
* Moodle: Moodle es un acrónimo de Ambiente de aprendizaje dinámico modular orientado a objetos.

El Módulo ASES permite:
* Gestión de usuarios pertenecientes a la estrategia, cada uno con sus respectivos roles.
* Registrar en detalle todo el proceso socioeducativo que se le hace a los beneficiarios del programa.
* Realizar reportes con el fin de mostrar información relevante respecto a los seguimientos socioeducativos.

## Construido con (Herramientas usadas para el desarrollo)

* PostgreSQL 9.6 (https://www.postgresql.org/) - Base de datos
* Apache 2 (https://httpd.apache.org/) - Servidor 
* PHP 7.2 (mínimo) (https://www.php.net/) - Lenguaje de programación
* JavaScript (https://www.javascript.com/)
* HTML
* CSS

## Estructura

En el módulo ASES se maneja la siguiente estructura:

* Managers: 
	nombre_manager:  
		- lib  
		- api  
		- classes  

* Core:
	nombre:	
		- interfaz  
		- versión:  
			- implementación	

## Instalación

*Es necesario que la instalación de el plugin se haga una vez restauradas
las tablas con la información que se tenga hasta el momento, tanto
en las tablas talentospilos\* como en la tabla mdl_cohort*  

Existen dos formas de instalar el plugin, usando la interfaz gráfica de 
administración de Moodle o por medio de la consola.

  ## *Instalación por medio de la interfaz gráfica*
Para la instalación debe primero descargar la ultima versión del plugin ASES

```bash
curl -s https://api.github.com/repos/sistemasases/moduloases/releases/latest \
| grep "zipball_url" | head -1 \
| cut -d : -f 2,3 \
| tr -d \", \
| wget  --output-document ases.zip -qi - && unzip ases.zip  && rm ases.zip \
| mv sistemasases-moduloases-* ases \
| zip -r ases.zip ases && rm -rf ases
```
Esto creará un archivo `ases.zip` el cual tendrá la información de el plugin
y luego debe dirigirse a la página {{$CFG->wwwroot}}/admin/tool/installaddon/index.php
y proceder con la instalación.

## *Instalación por consola en el servidor*
Debe abrir la carpeta `blocks` en su instalación moodle,
suponiendo que esta esta en el directorio `/var/www/html/moodle` el
comando que deberá ejecutar es el siguiente:
* Recuerde estar logueado como administrador.

```bash
apache_user=$(ps -ef | egrep '(httpd|apache2|apache)' | grep -v `whoami` | grep -v root | head -n1 | awk '{print $1}')
curl -s https://api.github.com/repos/sistemasases/moduloases/releases/latest \
| grep "zipball_url" | head -1 \
| cut -d : -f 2,3 \
| tr -d \", \
| wget  --output-document ases.zip -qi - && unzip ases.zip  && rm ases.zip \
| mv sistemasases-moduloases-* ases && chown -R $apache_user:$apache_user ases
```
Luego debe dirigirse a la pagina inicial de su sitio, loguearse como
administración y ejecutar la actualización de la base de datos.

## Sistema de versiones 

Usamos la siguiente estructura para el versionado:

	a . b . c

* a = Macro-cambio (Core)/View con cambios en la BD
* b = Actualización/Nueva view sin cambios en la BD/Macro-cambio parcial
* c = Corrección de errores/Refactorización menor

## Autores

* **Jeison Cardona Gómez** - [vvbv](https://github.com/vvbv)
* **Iader E. García Gómez** - [iaderegg](https://github.com/iaderegg)
* **Luis Gerardo Manrique Cardona** - [luchoman08](https://github.com/luchoman08)
* **Juan Pablo Moreno Muñoz** - [juanpamm](https://github.com/juanpamm)
* **Camilo José Cruz Rivera** - [cjcruzrivera](https://github.com/cjcruzrivera)
* **Isabella Serna Ramírez** - [isabella317](https://github.com/isabella317)
* **Juan Pablo Castro** - [jpcv222](https://github.com/jpcv222)
* **Joan Manuel Tovar Guzmán** - [joanmtg](https://github.com/joanmtg)
* **ASES Dev** - [sistemasases](https://github.com/sistemasases)
* **Fabio Andres Castañeda Duarte** - [fabioacd](https://github.com/fabioacd)
* **José Alejandro Libreros** - *Documentación* - [josealejolibreros](https://github.com/josealejolibreros)
* **Diana Melissa Millares** - *Documentación* - [melissamillares](https://github.com/melissamillares)

