# Historial de cambios en el modelo de datos


# Permisos
> ## Funcionalidades creadas
- massive_upload
> ## Acciones creadas 
- massive_upload Acceso a todas las cargas masivas que utiliza datatables para visualizar el resultado de la carga

>> Funcionalidad padre: massive_upload
>> Roles con permiso de acceder: sistemas   
# Vistas añadidas 

massive_upload.php

> - Se añade la vista para hacer cargas masivas, además de toda la logica y librerias para realizarlas.
> - Se añade la carga masiva de usuarios ases
> - Se añade la carga masiva de actualizar historial academico
> - Se añade la carga masiva de actualizar condición de excepción

# Vistas modificadas

Reporte backup: 

- Se agrega visualización de logs de formularios dinámicos en lenguaje natural.
- Se agrega comparación de estados (estado previo, enviado y almacenado) de logs de formularios dinámicos en lenguaje natural.
- Se agrega restaurado lógico de formularios dinámicos borrados.

Ficha estudiante: 

 - Se agrega visualización de sede actual del programa en curso de un estudiante.

# Otros

- Se crea gestor de modal estándar.
- <Luis Manrique><luis.manrique@correounivalle.edu.co> Se han borrado metodos repetidos de query.php, los archivos que requerian dichos metodos de query.php se han actualizado a requerir los archivos donde estan contenidas las funciones borradas de query.php
- <Luis Manrique><luis.manrique@correounivalle.edu.co> Se ha borrado codigo comentado no relevante de query.php
<!--

Se han agregado los archivos update siguientes:
insert_cond_excepcion.php
insert_materias_json_schema.php

-->
