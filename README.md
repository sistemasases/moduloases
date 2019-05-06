# Módulo Ases

Es un plugin de moodle de tipo bloque, que está diseñado para sistematizar el proceso de seguimiento estudiantil que se realiza dentro de la estrategia ASES. Es una aplicación web que sirve para gestionar la información de la estrategia ASES.
* Moodle: Moodle es un acrónimo de Ambiente de aprendizaje dinámico modular orientado a objetos.

El Módulo ASES permite:
* Gestión de usuarios pertenecientes a la estrategia, cada uno con sus respectivos roles.
* Registrar en detalle todo el proceso socioeducativo que se le hace a los beneficiarios del programa.
* Realizar reportes con el fin de mostrar información relevante respecto a los seguimientos socioeducativos.

# Instalación
*Es necesario que la instalación de el plugin se haga una vez restauradas
las tablas con la información que se tenga hasta el momento, tanto
en las tablas talentospilos\* como en la tabla mdl_cohort*  
Existen dos formas de instalar el plugin, usando la interfaz gráfica de 
administración de Moodle o por medio de la consola.
## Instalación por medio de la interfaz gráfica
Para la instalación debe primero descargar la ultima versión de el plugin
ASES
```bash
curl -s https://api.github.com/repos/sistemasases/moduloases/releases/latest \
| grep "zipball_url" | head -1 \
| cut -d : -f 2,3 \
| tr -d \", \
| wget  --output-document ases.zip -qi - && unzip ases.zip  && rm ases.zip \
| mv sistemasases-moduloases-* ases \
| zip -r ases.zip ases && rm -rf ases
```
Esto creara un archivo `ases.zip` el cual tendrá la información de el plugin
y luego debe dirigirse a la página {{$CFG->wwwroot}}/admin/tool/installaddon/index.php
y proceder con la instalación.

## Instalación por consola en el servidor 
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
