# Partes de horas DANNY y tareas pendientes

## Preguntas pendientes

* **Job orders**: En la cración / actualización, se puede asignar más de un empleado a una villa que ya está cogida por otro incluso mismo empleado, fechas coincidentes, etc, osea hay que revisar todas las coincidencias?

## Tareas pendientes

* TODO: Revisar emails duplicados antes de crear/actualizar usuario

## Partes de horas

* DONE: 20250325 (1hrs)
    - Añadido plugin de js **FullCalendar** para empezar a crear el calendario de partes de trabajo para admins y employers del dashboard y empezado a investigar su funcionamiento. Creado un callback de ejemplo mostrando datos fake
    - Cambiados índices de bbdd para hacer el mail único y además faltaba un campo en job_orders para el empleador y el empleado, renombrados los campos asimismo.

* DONE: 20250319 (1hrs)
    - Añadidos helpers en base de datos para corregir problemas con parametros de queries estilo :campo, que no fucnionaba bien. También comprobación de emails duplicados antes de crear/insertar usuarios
    - Actualización de usuarios: Se tiene en cuenta que no se pueda repetir el email a la hora de crear/insertar

* DONE: 20250317 (1hrs)
    - Creado repositorio remoto en github y compartido con Yanina
    - Descargado en local Yanina y testado todo ok
    - Reactualizada BBDD

* DONE: 20250312 (1hrs)
    - Subido favicon de app
    - Estandarizados algunos includes para eliminar codigo duplicado
    - Iniciada sección de creación de **job order** con nuevos métodos necesarios en `database.php` y helpers en `globals.php` para trabajos con fechas
    - **FALTA**: Procesar datos de envío y creación de job order

# Diseño técnico del proyecto

## Rol employer y admin

Hace un login y accede la pagina `dashboard.php`.

Debería aparecer un calendario del mes actual y marcados los días donde hay un parte. En su defecto, que sea una lista con los próximos partes, por ejemplo 10 próximos con su fecha, nombre de villa. Y se pueda seleccionar para ver los detalles del parte.

Acciones disponibles:
* **Nuevo parte**: Lleva a un formulario de crear un nuevo parte:
    - Lleva los campos de la tabla `job_orders` del 2 al 7, para los campos iduser, idvilla y idservice son listas desplegables y el comentario opcional textarea
* **Nueva villa**: Lleva a un formulario de crear una vila nueva:
    - Lleva los campos de la tabla `villas` del 2 al 7 y todos obligatorios, iduser es lista desplegable. Si puedes ser una lista con buscador mejor (api externa).
* **Nuevo usuario**: Hecho, es ir directamente a `empl
* **Nuevo servicio**: _Falta por hacer_: Lleva a un formulario que genera un `services.name`.
* **Ver totales**: _Falta por hacer_: Lleva a un tabla con estadísticas, se puede elegir en un formulario un selector de empleado (solo employee), otro de villa y un rango de fechas (ira a buscar en `job_orders.check_in_employee` y `job_orders.check_out_employee`) y mostrará como resultado el total de horas del empleado (si no selecciona villa será excluyendo la villa y si no selecciona el empleado, excluirá el empleado).

Menú superior:
* **Usuarios**: Ya está hecho
* **Villas**: Vista CRUD y acciones sobre `villas`.
* **Partes de trabajo**: Vista CRUD y acciones sobre `job_orders`.

## Rol empleado

* Menú con salir y "ver mis tareas realizadas" y cada fila sea un parte de `job_orders`. Debe tener un buscador con lista de villas que ha participado para poder ver los totales de horas de dicha villa y si no elije ninguna lo verá acumulado por cada una de las villas que ha participado. También que pueda filtar por rango de tiempo.
* En el dashboard debe salir una lista de todos sus partes activos que si el campo `job_orders.check_in_employee` esta a null, establecerá la hora en este, luego solo podrá generar un botón de salida que afectaría para el mismo parte a `job_orders.check_out_employee`. Además, el campo `comment_time` deberá poder editarlo siempre y cuando haya hecho al menos el `check_in_employee` con información del estilo que hubo una incidencia para fichar o enfermedad o incidente.

