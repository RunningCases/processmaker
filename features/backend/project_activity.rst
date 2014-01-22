PROJECT ACTIVITY RESOURCES

1 SOLICITUDES

1.1 GET: Get properties & definition of a project activity.

/api/1.0/{workspace}/project/{uid}/activity/{uid}

Función para la llamada: doGetProjectActivity (ver DICCIONARIO DE 

FUNCIONES).

Solicitud: No es necesario enviar datos en la solicitud.

Retorno: Ejecutado el llamado, el JSON de respuesta es el siguiente:

{

 "definition": {},

 "properties": 

 {

 "tas_title": "Tarea Inicial",

 "tas_description": "La descripcion de esta tarea",

 "tas_priority_variable": "@@VAR_PRIORITY",

 "tas_derivation_screen_tpl": "template.html",

 "tas_start": "TRUE",

 "tas_assign_type" : "SELF_SERVICE_EVALUATE",

 "tas_assign_variable": "@@USER_LOGGED",

 "tas_group_variable": "@@GROUP_UID",

 "tas_selfservice_timeout": 1,

 "tas_selfservice_time": "2",

 "tas_selfservice_time_unit" : "DAYS",

 "tas_selfservice_trigger_uid" : "3229227245298e1c5191f95009451434",

 "tas_transfer_fly": "FALSE",

 "tas_duration" : "2",

 "tas_timeunit" : "DAYS",

 "tas_type_day": "2",

 "tas_calendar": "00000000000000000000000000000001",

 "tas_type": "ADHOC",

 "tas_def_title": "Case Title",

 "tas_def_description": "Case Descripction",

 "tas_send_last_email": "FALSE",

 "tas_def_subject_message": "Titulo de notifiacion",

 "tas_def_message_type": "template",

 "tas_def_message": "Esta es una notificacion",

 "tas_def_message_template": "template.html"

 }

}

Para conocer detalles de los campos json devueltos, ver CAMPOS PARA GET 

Y PUT EN ACTIVITY 

1.2 GET: Get definition of a project activity.

/api/1.0/{workspace}/project/{uid}/activity/{uid}?filter=definition

Función para la llamada: doGetProjectActivity (ver DICCIONARIO DE 

FUNCIONES) realiza la petición para solo obtener los datos de “definition”.

Solicitud: No es necesario enviar datos en la solicitud.

Retorno: Ejecutado el llamado, el JSON de respuesta es el siguiente:

{

 "definition": {}

}

1.3 GET: Get properties of a project activity.

/api/1.0/{workspace}/project/{uid}/activity/{uid}?filter=properties

Función para la llamada: doGetProjectActivity (ver DICCIONARIO DE

FUNCIONES) realiza la petición para solo obtener los datos de “properties”.

Solicitud: No es necesario enviar datos en la solicitud.

Retorno: Ejecutado el llamado, el JSON de respuesta es el siguiente:

{

 "properties": 

 {

 "tas_title": "Tarea Inicial",

 "tas_description": "La descripcion de esta tarea",

 "tas_priority_variable": "@@VAR_PRIORITY",

 "tas_derivation_screen_tpl": "template.html",

 "tas_start": "TRUE",

 "tas_assign_type" : "SELF_SERVICE_EVALUATE",

 "tas_assign_variable": "@@USER_LOGGED",

 "tas_group_variable": "@@GROUP_UID",

 "tas_selfservice_timeout": 1,

 "tas_selfservice_time": "2",

 "tas_selfservice_time_unit" : "DAYS",

 "tas_selfservice_trigger_uid" : "3229227245298e1c5191f95009451434",

 "tas_transfer_fly": "FALSE",

 "tas_duration" : "2",

 "tas_timeunit" : "DAYS",

 "tas_type_day": "2",

 "tas_calendar": "00000000000000000000000000000001",

 "tas_type": "ADHOC",

 "tas_def_title": "Case Title",

 "tas_def_description": "Case Descripction",

 "tas_send_last_email": "FALSE",

 "tas_def_subject_message": "Titulo de notifiacion",

 "tas_def_message_type": "template",

 "tas_def_message": "Esta es una notificacion",

 "tas_def_message_template": "template.html"

 }

}

1.4 PUT: Update a project activity.

/api/1.0/{workspace}/project/{uid}/activity/{uid}

Función para la llamada: doPutProjectActivity (ver DICCIONARIO DE 

FUNCIONES).

Solicitud: La solicitud es un array de datos los cuales se actualizarán, el

JSON de envio es el siguiente: (para mas detalle ver CAMPOS PARA GET Y 

PUT EN ACTIVITY).

Content-Type: application/json

{

 "definition": {},

 "properties": 

 {

 "tas_title": "Tarea Inicial",

 "tas_description": "La descripcion de esta tarea",

 "tas_priority_variable": "@@VAR_PRIORITY",

 "tas_derivation_screen_tpl": "template.html",

 "tas_start": "TRUE",

 "tas_assign_type" : "SELF_SERVICE_EVALUATE",

 "tas_assign_variable": "@@USER_LOGGED",

 "tas_group_variable": "@@GROUP_UID",

 "tas_selfservice_timeout": 1,

 "tas_selfservice_time": "2",

 "tas_selfservice_time_unit" : "DAYS",

 "tas_selfservice_trigger_uid" : "3229227245298e1c5191f95009451434",

 "tas_transfer_fly": "FALSE",

 "tas_duration" : "2",

 "tas_timeunit" : "DAYS",

 "tas_type_day": "2",

 "tas_calendar": "00000000000000000000000000000001",

 "tas_type": "ADHOC",

 "tas_def_title": "Case Title",

 "tas_def_description": "Case Descripction",

 "tas_send_last_email": "FALSE",

 "tas_def_subject_message": "Titulo de notifiacion",

 "tas_def_message_type": "template",

 "tas_def_message": "Esta es una notificacion",

 "tas_def_message_template": "template.html"

 }

}

Retorno: El llamado no retornara datos (Solo el estado código 200 de OK).

1.5 DELETE: Delete a project activity.

/api/1.0/{workspace}/project/{uid}/activity/{uid}

Función para la llamada: doDeleteProjectActivity (ver DICCIONARIO DE 

FUNCIONES).

Solicitud: No es necesario enviar datos en la solicitud.

Retorno: El llamado no retornara datos (Solo el estado código 200 de OK).

2 DICCIONARIO DE FUNCIONES

2.1 doGetProjectActivity

ARCHIVO ../src/Services/Api/ProcessMaker/Project/Activity.php

CLASE Activity

MÉTODO doGetProjectActivity

PARÁMETRO 1 (Obligatorio) projectUid: ID del proceso.

PARÁMETRO 2 (Obligatorio) activityUid: ID de la tarea.

PARÁMETRO 3 (Opcional)

RETORNO

filter: Campo para determinar que valores de la 

activity se desea obtener. Posibles valores “definition” 

(para obtener los valores de definición), “properties” 

(para obtener los valores de propiedades) o “” 

(cadena vacía, para obtener los valores de definición 

y propiedades).

Array: El retorno de esta función es un arreglo con 2 

niveles, definition y properties, ambos son arreglos 

con los valores de sus datos.

2.2 doPutProjectActivity

ARCHIVO ../src/Services/Api/ProcessMaker/Project/Activity.php

CLASE Activity

MÉTODO doPutProjectActivity

PARÁMETRO 1 (Obligatorio) projectUid: ID del proceso.

PARÁMETRO 2 (Obligatorio) activityUid: ID de la tarea.

PARÁMETRO 3 (Opcional) request_data: Arreglo de datos de valores de la 

RETORNO Esta función no retorna ningún parámetro.

activity se desea actualizar.

2.3 doDeleteProjectActivity

ARCHIVO ../src/Services/Api/ProcessMaker/Project/Activity.php

CLASE Activity

MÉTODO doDeleteProjectActivity

PARÁMETRO 1 (Obligatorio) projectUid: ID del proceso.

PARÁMETRO 2 (Obligatorio) activityUid: ID de la tarea.

RETORNO Esta función no retorna ningún parámetro.

3 CAMPOS PARA GET Y PUT EN ACTIVITY

NOMBRE DESCRIPCIÓN REQUERIDO TIPO VALOR

tas_title Nombre de la tarea NO String

tas_description Descripción de la 

tas_priority_variable Variable para la 

tas_derivation_screen_tpl Plantilla de la pantalla 

tas_start Es una tarea inicial NO String

tarea NO String

prioridad del caso NO String

de derivación NO String

tas_assign_type Tipo de asignación en 

la tarea NO String

tas_assign_variable

“Cadena”

(Cadena 

alfanumérica)

“Descripción”

(Cadena 

alfanumérica)

“@@VAR”

(Variable de 

caso)

“temp.html”

(Template 

valido)

“TRUE” o 

“FALSE”

(Únicos 

Valores)

“BALANCED” 

o “MANUAL” o 

“EVALUATE” o 

“REPORT_TO” 

o 

“SELF_SE

RVICE” o 

“SELF_SERVIC

E_EVALUATE”

(Únicos 

Valores)

“@@VALOR”

(Variable de 

caso)

tas_group_variable

tas_selfservice_timeout

tas_selfservice_time

Variable para 

Asignación por 

valor habilitado 

cuando el campo 

“tas_assign_type” 

tiene el valor de 

“EVALUATE”

Variable de Asignación 

Self Services por 

valor habilitado 

cuando el campo 

“tas_assign_type” 

tiene el valor de 

“SELF_SERVICE_EVALUA

TE”

Configurar tiempo 

de espera habilitado 

cuando el campo 

“tas_assign_type” 

tiene el valor de 

“SELF_SERVICE” y 

“SELF_SERVICE_EVALUA

TE”

Tiempo para la 

configuración 

habilitado 

cuando el campo 

“tas_selfservice_timeo

ut” tiene el valor de 1

NO String

NO String

“@@VALOR” 

(Variable de 

caso)

NO Integer

1 o 0

(Únicos 

Valores)

NO Integer 2

(Valor Entero)

tas_selfservice_time_unit

Unidad de tiempo 

para la configuración 

habilitado 

cuando el campo 

“tas_selfservice_timeo

ut” tiene el valor de 1

Trigger a ejecutarse 

para la configuración 

habilitado 

cuando el campo 

“tas_selfservice_timeo

ut” tiene el valor de 1

Permitir que el control 

de tiempo lo defina el 

usuario

Duración de la 

tarea habilitado 

cuando el campo 

“tas_transfer_fly” 

tiene el valor de 0

Unidad de tiempo 

para la duración 

habilitado 

cuando el campo 

“tas_transfer_fly” 

tiene el valor de 0

Contar días laborables 

o calendario habilitado

cuando el campo 

“tas_transfer_fly” 

tiene el valor de 0

Calendario de la 

tarea habilitado 

cuando el campo 

“tas_transfer_fly” 

tiene el valor de 0

Permitir la 

transferencia 

arbitraria (Ad hoc)

NO String

“DAYS” o 

“HOURS”

(Únicos 

Valores)

tas_selfservice_trigger_uid

NO String “a32hnj2…”

tas_transfer_fly

NO String

(Id de trigger)

“TRUE” o 

“FALSE”

(Únicos 

Valores)

tas_duration

NO Integer 2

(Valor Entero)

tas_timeunit

NO String

“DAYS” o 

“HOURS”

(Únicos 

Valores)

tas_type_day

NO Integer

1 o 2

(Únicos 

Valores)

tas_calendar

NO String

tas_type

NO String

tas_def_title Case Title de la tarea NO String

tas_def_description Case Description de la 

tarea NO String

tas_send_last_email Notificar al usuario al 

“h3kj231…”

(Id de 

calendario)

“NORMAL” o 

“ADHOC”

(Únicos 

Valores)

“@@Title” 

(Cadena 

alfanumérica 

incluido Campo 

Variable de 

caso)

“@@Desc”

(Cadena 

alfanumérica 

incluido Campo 

Variable de 

caso)

“TRUE” o 

“FALSE”

(Únicos 

Valores)

tas_def_subject_message

derivar un caso NO String

Titulo de la 

notificación de 

derivación habilitado 

cuando el campo 

“tas_send_last_email” 

tiene el valor de 

“TRUE”

NO String

“Mi Titulo”

(Cadena 

alfanumérica)

tas_def_message_type 

tas_def_message

tas_def_message_template

Tipo del contenido 

de la notificación 

habilitado 

cuando el campo 

“tas_send_last_email” 

tiene el valor de 

“TRUE”

Contenido plano de la 

notificación habilitado 

cuando el campo 

“tas_def_message_t

ype” tiene el valor de 

“text”

Plantilla para la 

notificación habilitado 

cuando el campo 

“tas_def_message_t

ype” tiene el valor de 

“template”

NO String

“template” o 

“text”

(Únicos 

Valores)

NO String

“Contenido…”

(Cadena 

alfanumérica)

NO String

“temp.html” 

(Template 

valido)
