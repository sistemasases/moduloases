# Historial de cambios en el modelo de datos


# Vistas añadidas 

massive_upload.php

- Se añade la vista para hacer cargas masivas, además de toda la logica y librerias para realizarlas.
- Se añade la carga masiva de usuarios ases
- Se añade la carga masiva de actualizar historial academico
- Se añade la carga masiva de actualizar condición de excepción

# Vistas modificadas

Reporte backup: 

- Se agrega visualización de logs de formularios dinámicos en lenguaje natural.
- Se agrega comparación de estados (estado previo, enviado y almacenado) de logs de formularios dinámicos en lenguaje natural.
- Se agrega restaurado lógico de formularios dinámicos borrados.

Ficha estudiante: 

 - Se agrega visualización de sede actual del programa en curso de un estudiante.

# Otros

- Se crea gestor de modal estándar.
- Add: Se crea gestor de modal estándar.
- Add: Se adiciona validación a los campos tipo DATE con los atributos min|max= fecha|today()
- Add: Se mejora la gestión de errores en los formularios dinámicos.
- Add: Se adiciona soporte al casteo de columnas en xQuery de formularios dinámicos.
- Add: Se adiciona el soporte a funciones anónimas en xQuery de formularios dinámicos.
- Add: Se crea nuevo renderizador para los formularios con soporte a valores iniciales y múltiples botones. 
- Fix: Se soluciona el envío de observaciones.
- Fix: Se corrige la carga del campo 'envío de observaciones' para los practicantes.
- Fix: Se corrige el conteo en el reporte de seguimientos.
