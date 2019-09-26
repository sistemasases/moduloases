# Managers

Una parte importante de implementación del plugin está ubicada y debidamente seccionada en el directorio de managers, cuyo objetivo es mantener las funcionalidades agrupadas por similitud para dar contexto amplio a lo que ahí se implementa. Un manager puede requerir a otros managers, con el fin de evitar funcionalidades duplicadas o de dificil interpretación.

## Estructura
En la carpeta *managers* se maneja la siguiente estructura para los archivos:
* Managers: 
  - nombre_manager:
      - lib
      - api
