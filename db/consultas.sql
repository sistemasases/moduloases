----ARCHIVO CON CONSULTAS SQL AUXILIARES *NO EJECUTABLE*
-----------------------------------------------------------------------------------------------------------------
--- Info de todos los cursos donde hay talentos
SELECT DISTINCT curso.id,
                curso.fullname,
                curso.shortname,

  (SELECT concat_ws(' ',firstname,lastname) AS fullname
   FROM
     (SELECT usuario.firstname,
             usuario.lastname,
             userenrol.timecreated
      FROM mdl_course cursoP
      INNER JOIN mdl_context cont ON cont.instanceid = cursoP.id
      INNER JOIN mdl_role_assignments rol ON cont.id = rol.contextid
      INNER JOIN mdl_user usuario ON rol.userid = usuario.id
      INNER JOIN mdl_enrol enrole ON cursoP.id = enrole.courseid
      INNER JOIN mdl_user_enrolments userenrol ON (enrole.id = userenrol.enrolid
                                                   AND usuario.id = userenrol.userid)
      WHERE cont.contextlevel = 50
        AND rol.roleid = 3
        AND cursoP.id = curso.id
      ORDER BY userenrol.timecreated ASC
      LIMIT 1) AS subc) AS nombre_Profesor
FROM mdl_course curso
INNER JOIN mdl_enrol ROLE ON curso.id = role.courseid
INNER JOIN mdl_user_enrolments enrols ON enrols.enrolid = role.id
WHERE enrols.userid IN
    (SELECT user_m.id
     FROM  mdl_user user_m
     INNER JOIN mdl_user_info_data data ON data.userid = user_m.id
     INNER JOIN mdl_user_info_field field ON data.fieldid = field.id
     INNER JOIN mdl_talentospilos_usuario user_t ON data.data = CAST(user_t.id AS VARCHAR)
     INNER JOIN mdl_talentospilos_est_estadoases estado_u ON user_t.id = estado_u.id_estudiante 
     INNER JOIN mdl_talentospilos_estados_ases estados ON estados.id = estado_u.id_estado_ases
     WHERE estados.nombre = 'ACTIVO/SEGUIMIENTO' AND field.shortname = 'idtalentos'

    INTERSECT

    SELECT user_m.id
    FROM mdl_user user_m INNER JOIN mdl_cohort_members memb ON user_m.id = memb.userid INNER JOIN mdl_cohort cohorte ON memb.cohortid = cohorte.id 
    WHERE SUBSTRING(cohorte.idnumber FROM 1 FOR 2) = 'SP'

      )
;

---misma para modelo anterior

SELECT DISTINCT curso.id,
                curso.fullname,
                curso.shortname,

  (SELECT concat_ws(' ',firstname,lastname) AS fullname
   FROM
     (SELECT usuario.firstname,
             usuario.lastname,
             userenrol.timecreated
      FROM mdl_course cursoP
      INNER JOIN mdl_context cont ON cont.instanceid = cursoP.id
      INNER JOIN mdl_role_assignments rol ON cont.id = rol.contextid
      INNER JOIN mdl_user usuario ON rol.userid = usuario.id
      INNER JOIN mdl_enrol enrole ON cursoP.id = enrole.courseid
      INNER JOIN mdl_user_enrolments userenrol ON (enrole.id = userenrol.enrolid
                                                   AND usuario.id = userenrol.userid)
      WHERE cont.contextlevel = 50
        AND rol.roleid = 3
        AND cursoP.id = curso.id
      ORDER BY userenrol.timecreated ASC
      LIMIT 1) AS subc) AS nombre_Profesor
FROM mdl_course curso
INNER JOIN mdl_enrol ROLE ON curso.id = role.courseid
INNER JOIN mdl_user_enrolments enrols ON enrols.enrolid = role.id
WHERE enrols.userid IN
    (SELECT user_m.id
    FROM  mdl_user user_m
    INNER JOIN mdl_user_info_data data ON data.userid = user_m.id
    INNER JOIN mdl_user_info_field field ON data.fieldid = field.id
    INNER JOIN mdl_talentospilos_usuario user_t ON data.data = CAST(user_t.id AS VARCHAR)
    WHERE user_t.estado = 'ACTIVO' AND field.shortname = 'idtalentos'

    INTERSECT

    SELECT user_m.id
    FROM mdl_user user_m 
    INNER JOIN mdl_cohort_members memb ON user_m.id = memb.userid 
    INNER JOIN mdl_cohort cohorte ON memb.cohortid = cohorte.id 
    INNER JOIN mdl_inst_cohorte as inst_coh ON cohorte.id = inst_coh.id_cohorte
    WHERE inst_coh.id_instancia = $instanceid
      )
;



--------------------------------------------------------------------------------------
--Sacar nombre profesor de un curso.
SELECT concat_ws(' ',firstname,lastname) as fullname
FROM (SELECT usuario.firstname, usuario.lastname, userenrol.timecreated
FROM mdl_course cursoP INNER JOIN mdl_context cont ON cont.instanceid = cursoP.id 
    INNER JOIN mdl_role_assignments rol ON cont.id = rol.contextid INNER JOIN mdl_user usuario ON rol.userid = usuario.id
    INNER JOIN mdl_enrol enrole ON cursoP.id = enrole.courseid INNER JOIN mdl_user_enrolments userenrol ON (enrole.id = userenrol.enrolid AND usuario.id = userenrol.userid) 
WHERE cont.contextlevel = 50 AND rol.roleid = 3 AND cursoP.id = 2
ORDER BY userenrol.timecreated ASC LIMIT 1) as subc;


SELECT *
FROM mdl_course curso INNER JOIN mdl_enrol ON curso.id = mdl_enrol.courseid INNER JOIN mdl_user_enrolments ON (mdl_user_enrolments.enrolid =mdl_enrol.id) 
WHERE curso.id=3;



--------------------------------------------------------------------------------------

--IDs de moodle de los usuarios de una instancia

SELECT pgr.cod_univalle as cod
FROM mdl_talentospilos_instancia inst INNER JOIN mdl_talentospilos_programa pgr ON inst.id_programa = pgr.id
WHERE inst.id_instancia= 19


SELECT user_m.id
FROM mdl_user user_m INNER JOIN mdl_cohort_members memb ON user_m.id = memb.userid INNER JOIN mdl_cohort cohorte ON memb.cohortid = cohorte.id 
WHERE SUBSTRING(cohorte.idnumber FROM 1 FOR 2) = 'SP'

--------------------------------------------------------------------------------------

---sacar peso usado en una categoria
SELECT sum(peso)
FROM (SELECT id,SUM(aggregationcoef) as peso 
FROM mdl_grade_items
WHERE categoryid = 23
GROUP by id

UNION 
SELECT item.id, SUM(item.aggregationcoef) as peso
FROM mdl_grade_items item INNER JOIN mdl_grade_categories cat ON item.iteminstance=cat.id
WHERE cat.parent = 23
GROUP by item.id)as pesos


--------------------------------------------------------------------------------------

--prueba cursos donde hay pilos

SELECT DISTINCT curso.id,
                curso.fullname,
                curso.shortname,

  (SELECT concat_ws(' ',firstname,lastname) AS fullname
   FROM
     (SELECT usuario.firstname,
             usuario.lastname,
             userenrol.timecreated
      FROM mdl_course cursoP
      INNER JOIN mdl_context cont ON cont.instanceid = cursoP.id
      INNER JOIN mdl_role_assignments rol ON cont.id = rol.contextid
      INNER JOIN mdl_user usuario ON rol.userid = usuario.id
      INNER JOIN mdl_enrol enrole ON cursoP.id = enrole.courseid
      INNER JOIN mdl_user_enrolments userenrol ON (enrole.id = userenrol.enrolid
                                                   AND usuario.id = userenrol.userid)
      WHERE cont.contextlevel = 50
        AND rol.roleid = 3
        AND cursoP.id = curso.id
      ORDER BY userenrol.timecreated ASC
      LIMIT 1) AS subc) AS nombre_Profesor
FROM mdl_course curso
INNER JOIN mdl_enrol ROLE ON curso.id = role.courseid
INNER JOIN mdl_user_enrolments enrols ON enrols.enrolid = role.id
WHERE enrols.userid IN
    (SELECT moodle_user.id
     FROM mdl_user moodle_user
     INNER JOIN mdl_user_info_data DATA ON moodle_user.id = data.userid
     INNER JOIN mdl_user_info_field field ON field.id = data.fieldid
     WHERE field.shortname = 'idtalentos' AND moodle_user.id IN (SELECT user_m.id
FROM mdl_user user_m INNER JOIN mdl_cohort_members memb ON user_m.id = memb.userid INNER JOIN mdl_cohort cohorte ON memb.cohortid = cohorte.id 
WHERE SUBSTRING(cohorte.idnumber FROM 1 FOR 2) = 'SP')
       AND data.data IN
         (SELECT CAST(id AS VARCHAR)
          FROM mdl_talentospilos_usuario) )
          
          
  --------------------------------------------------------------------------------------        
          
--ESTUDIANTES PILOS EN UN CURSO
SELECT usuario.firstname, usuario.lastname
FROM mdl_user usuario INNER JOIN mdl_user_enrolments enrols ON usuario.id = enrols.userid 
INNER JOIN mdl_enrol enr ON enr.id = enrols.enrolid 
INNER JOIN mdl_course curso ON enr.courseid = curso.id  
WHERE curso.id= 3q AND usuario.id IN (SELECT user_m.id
FROM mdl_user user_m INNER JOIN mdl_cohort_members memb ON user_m.id = memb.userid INNER JOIN mdl_cohort cohorte ON memb.cohortid = cohorte.id 
WHERE SUBSTRING(cohorte.idnumber FROM 1 FOR 2) = 'SP')


--------------------------------------------------------------------------------------
---CONSULTA PARA grade_item isASES()

SELECT id
FROM (SELECT user_m.id
      FROM  {user} user_m
      INNER JOIN {user_info_data} data ON data.userid = user_m.id
      INNER JOIN {user_info_field} field ON data.fieldid = field.id
      INNER JOIN {talentospilos_usuario} user_t ON data.data = CAST(user_t.id AS VARCHAR)
      WHERE user_t.estado = 'ACTIVO' AND field.shortname = 'idtalentos'

      INTERSECT

      SELECT user_m.id
      FROM {user} user_m INNER JOIN {cohort_members} memb ON user_m.id = memb.userid INNER JOIN {cohort} cohorte ON memb.cohortid = cohorte.id 
      WHERE SUBSTRING(cohorte.idnumber FROM 1 FOR 2) = 'SP') estudiantes_ases
WHERE estudiantes_ases.id = $userid

SELECT id
FROM (SELECT user_m.id
      FROM  mdl_user user_m
      INNER JOIN mdl_user_info_data data ON data.userid = user_m.id
      INNER JOIN mdl_user_info_field field ON data.fieldid = field.id
      INNER JOIN mdl_talentospilos_usuario user_t ON data.data = CAST(user_t.id AS VARCHAR)
      WHERE user_t.estado = 'ACTIVO' AND field.shortname = 'idtalentos'

      INTERSECT

      SELECT user_m.id
      FROM mdl_user user_m INNER JOIN mdl_cohort_members memb ON user_m.id = memb.userid INNER JOIN mdl_cohort cohorte ON memb.cohortid = cohorte.id 
      WHERE SUBSTRING(cohorte.idnumber FROM 1 FOR 2) = 'SP') estudiantes_ases
WHERE estudiantes_ases.id = $userid

--------------------------------------------------------------------------------------
--Estudiantes a los que se les hace seguimiento

SELECT user_m.id
FROM  {user} user_m
INNER JOIN {user_info_data} data ON data.userid = user_m.id
INNER JOIN {user_info_field} field ON data.fieldid = field.id
INNER JOIN {talentospilos_usuario} user_t ON data.data = CAST(user_t.id AS VARCHAR)
INNER JOIN {talentospilos_estudiante_estado_ases} estado_u ON user_t.id = estado_u.id_estudiante 
INNER JOIN {talentospilos_estados_ases} estados ON estados.id = estado_u.estado_ases
WHERE estados.nombre = 'ACTIVO/SEGUIMIENTO' AND field.shortname = 'idtalentos'

INTERSECT

SELECT user_m.id
FROM mdl_user user_m INNER JOIN mdl_cohort_members memb ON user_m.id = memb.userid INNER JOIN mdl_cohort cohorte ON memb.cohortid = cohorte.id 
WHERE SUBSTRING(cohorte.idnumber FROM 1 FOR 2) = 'SP'

----misma para modelo anterior

SELECT user_m.id
FROM  {user} user_m
INNER JOIN {user_info_data} data ON data.userid = user_m.id
INNER JOIN {user_info_field} field ON data.fieldid = field.id
INNER JOIN {talentospilos_usuario} user_t ON data.data = CAST(user_t.id AS VARCHAR)
WHERE user_t.estado = 'ACTIVO' AND field.shortname = 'idtalentos'

INTERSECT

SELECT user_m.id
FROM mdl_user user_m INNER JOIN mdl_cohort_members memb ON user_m.id = memb.userid INNER JOIN mdl_cohort cohorte ON memb.cohortid = cohorte.id 
WHERE SUBSTRING(cohorte.idnumber FROM 1 FOR 2) = 'SP'




----------------------------------------------------------------------------------
--Estudiantes asignados a un monitor en el ultimo semestre
SELECT muser.id 
FROM {user} muser INNER JOIN {user_info_data} data ON muser.id = data.userid 
WHERE data.data IN (SELECT CAST(mon_estud.id_estudiante as text) 
                    FROM {talentospilos_monitor_estud} mon_estud 
                    WHERE id_monitor = $id AND id_semestre = (SELECT id FROM {talentospilos_semestre} WHERE fecha_inicio = (SELECT max(fecha_inicio) from {talentospilos_semestre}))) 
     AND data.fieldid = (SELECT id 
                         FROM  {user_info_field} 
                         WHERE shortname ='idtalentos')




SELECT muser.id 
FROM mdl_user muser INNER JOIN mdl_user_info_data data ON muser.id = data.userid 
WHERE data.data IN (SELECT CAST(mon_estud.id_estudiante as text) 
                    FROM mdl_talentospilos_monitor_estud mon_estud 
                    WHERE id_monitor = $id AND id_semestre = (SELECT id FROM mdl_talentospilos_semestre WHERE fecha_inicio = (SELECT max(fecha_inicio) from mdl_talentospilos_semestre))) 
     AND data.fieldid = (SELECT id 
                         FROM  mdl_user_info_field 
                         WHERE shortname ='idtalentos')

--Estudiantes asignados a un practicante en el ultimo semestre

SELECT muser.id 
FROM {user} muser INNER JOIN {user_info_data} data ON muser.id = data.userid 
WHERE data.data IN (SELECT CAST(mon_estud.id_estudiante as text) 
                    FROM {talentospilos_monitor_estud} mon_estud  
                    WHERE id_monitor IN (SELECT urol.id_usuario
                                        FROM {talentospilos_user_rol} urol 
                                        WHERE id_jefe = $id)
                    AND id_semestre = (SELECT id FROM {talentospilos_semestre} WHERE fecha_inicio = (SELECT max(fecha_inicio) from {talentospilos_semestre})))
    AND data.fieldid = (SELECT id 
                         FROM  mdl_user_info_field 
                         WHERE shortname ='idtalentos')


SELECT muser.id 
FROM mdl_user muser INNER JOIN mdl_user_info_data data ON muser.id = data.userid 
WHERE data.data IN (SELECT CAST(mon_estud.id_estudiante as text) 
                    FROM mdl_talentospilos_monitor_estud mon_estud  
                    WHERE id_monitor IN (SELECT urol.id_usuario
                                        FROM mdl_talentospilos_user_rol urol 
                                        WHERE id_jefe = 121)
                    AND id_semestre = (SELECT id FROM mdl_talentospilos_semestre WHERE fecha_inicio = (SELECT max(fecha_inicio) from mdl_talentospilos_semestre)))
    AND data.fieldid = (SELECT id 
                         FROM  mdl_user_info_field 
                         WHERE shortname ='idtalentos')    


------------------------------------------------------------------------------


---CONSULTA ESPECIFICA DESARROLLADA DE ASES_REPORT

 SELECT username,firstname,lastname,num_doc FROM {cohort} AS pc 
                INNER JOIN (
                    SELECT * FROM {cohort_members} AS pcm 
                    INNER JOIN (
                        SELECT * FROM (
                            SELECT id AS id_1, * FROM (SELECT *, id AS id_user FROM {user}) AS userm 
                            INNER JOIN (
                                SELECT userid, CAST(d.data as int) as data 
                                FROM {user_info_data} d 
                                WHERE d.data <> '' 
                                AND fieldid = (SELECT id FROM  {user_info_field} as f WHERE f.shortname ='idtalentos')
                            ) AS field 
                            ON userm. id_user = field.userid ) AS usermoodle 
                        INNER JOIN {talentospilos_usuario} as usuario 
                        ON usermoodle.data = usuario.id 
                        WHERE usermoodle.id_user in (
                            SELECT umood.id
                        FROM {user} umood INNER JOIN {user_info_data} udata ON umood.id = udata.userid 
                        INNER JOIN {talentospilos_est_estadoases} estado_ases ON udata.data = CAST(estado_ases.id_estudiante as TEXT)
                        WHERE udata.fieldid = (SELECT id FROM  {user_info_field} as f WHERE f.shortname ='idtalentos')
                         AND estado_ases.fecha = (SELECT MAX(fecha) FROM {talentospilos_est_estadoases} WHERE id_estudiante = estado_ases.id_estudiante) ) AND usermoodle.id_user in ( SELECT muser.id 
                  FROM {user} muser INNER JOIN {user_info_data} data ON muser.id = data.userid 
                  WHERE data.data IN (SELECT CAST(mon_estud.id_estudiante as text) 
                                      FROM {talentospilos_monitor_estud} mon_estud  
                                      WHERE id_monitor IN (SELECT urol.id_usuario
                                                          FROM {talentospilos_user_rol} urol 
                                                          WHERE id_jefe = 121)
                                      AND id_semestre = (SELECT id FROM {talentospilos_semestre} WHERE fecha_inicio = (SELECT max(fecha_inicio) from {talentospilos_semestre})))
                      AND data.fieldid = (SELECT id 
                                           FROM  mdl_user_info_field 
                                           WHERE shortname ='idtalentos') )
                        ) as usertm 
                    ON usertm.id_user = pcm.userid) as pcmuser 
                ON pc.id = pcmuser.cohortid WHERE pc.idnumber like '1008%' OR pc.idnumber LIKE 'SP%';


----------------------------------------------------------------
--Consulta de todos los cursos donde hay pilos. Con la info de los estudiantes.--

SELECT DISTINCT SUBSTRING(curso.shortname FROM 4 FOR 7) as "Cod Asignatura",
				curso.fullname as "Asignatura",
                SUBSTRING(curso.shortname FROM 12 FOR 2) as "Grupo",
                SUBSTRING(estud.username FROM 1 FOR 7) as "Codigo",
                concat_ws(' ',estud.firstname,estud.lastname) AS "Estudiante",
                SUBSTRING(estud.username FROM 9 FOR 4) as "Programa Academico",
          (SELECT concat_ws(' ',firstname,lastname) AS fullname
           FROM
             (SELECT usuario.firstname,
                     usuario.lastname,
                     userenrol.timecreated
              FROM {course} cursoP
              INNER JOIN {context} cont ON cont.instanceid = cursoP.id
              INNER JOIN {role_assignments} rol ON cont.id = rol.contextid
              INNER JOIN {user} usuario ON rol.userid = usuario.id
              INNER JOIN {enrol} enrole ON cursoP.id = enrole.courseid
              INNER JOIN {user_enrolments} userenrol ON (enrole.id = userenrol.enrolid
                                                           AND usuario.id = userenrol.userid)
              WHERE cont.contextlevel = 50
                AND rol.roleid = 3
                AND cursoP.id = curso.id
              ORDER BY userenrol.timecreated ASC
              LIMIT 1) AS subc) AS "Docente"
        FROM {course} curso
        INNER JOIN {enrol} ROLE ON curso.id = role.courseid
        INNER JOIN {user_enrolments} enrols ON enrols.enrolid = role.id
        INNER JOIN {user} estud ON enrols.userid = estud.id
        WHERE SUBSTRING(curso.shortname FROM 15 FOR 6) = '201708' AND enrols.userid IN
            (SELECT user_m.id
     FROM  {user} user_m
     INNER JOIN {user_info_data} data ON data.userid = user_m.id
     INNER JOIN {user_info_field} field ON data.fieldid = field.id
     INNER JOIN {talentospilos_usuario} user_t ON data.data = CAST(user_t.id AS VARCHAR)
     INNER JOIN {talentospilos_est_estadoases} estado_u ON user_t.id = estado_u.id_estudiante
     INNER JOIN {talentospilos_estados_ases} estados ON estados.id = estado_u.id_estado_ases
     WHERE estados.nombre = 'ACTIVO/SEGUIMIENTO' AND field.shortname = 'idtalentos'

    INTERSECT

    SELECT user_m.id
    FROM {user} user_m INNER JOIN {cohort_members} memb ON user_m.id = memb.userid INNER JOIN {cohort} cohorte ON memb.cohortid = cohorte.id
    WHERE cohorte.idnumber LIKE = 'SP%')


-----CONSULTA DE ESTUDIANTES ASES CON EL NUMERO DE ITEMS PERDIDOS



SELECT estudiantes.*, COUNT(grades.id)
FROM (SELECT user_m.id,SUBSTRING(user_m.username FROM 1 FOR 7) as codigo, user_m.firstname, user_m.lastname
     FROM  {user} user_m
     INNER JOIN {user_info_data} data ON data.userid = user_m.id
     INNER JOIN {user_info_field} field ON data.fieldid = field.id
     INNER JOIN {talentospilos_usuario} user_t ON data.data = CAST(user_t.id AS VARCHAR)
     INNER JOIN {talentospilos_est_estadoases} estado_u ON user_t.id = estado_u.id_estudiante
     INNER JOIN {talentospilos_estados_ases} estados ON estados.id = estado_u.id_estado_ases
     WHERE estados.nombre = 'ACTIVO/SEGUIMIENTO' AND field.shortname = 'idtalentos'

    INTERSECT

    SELECT user_m.id, SUBSTRING(user_m.username FROM 1 FOR 7) as codigo, user_m.firstname, user_m.lastname
    FROM {user} user_m INNER JOIN {cohort_members} memb ON user_m.id = memb.userid INNER JOIN {cohort} cohorte ON memb.cohortid = cohorte.id
    WHERE cohorte.idnumber LIKE 'SP%') estudiantes INNER JOIN {grade_grades} grades ON estudiantes.id = grades.userid
    INNER JOIN {grade_items} items ON grades.itemid = items.id 
    INNER JOIN {course} curso ON curso.id = items.courseid
WHERE SUBSTRING(curso.shortname FROM 15 FOR 6) = '201708' AND
      grades.finalgrade < 3 
GROUP BY estudiantes.id, estudiantes.codigo, estudiantes.firstname, estudiantes.lastname
-------------------

SELECT estudiantes.*, COUNT(grades.id)
FROM (SELECT user_m.id,SUBSTRING(user_m.username FROM 1 FOR 7) as codigo, user_m.firstname, user_m.lastname
     FROM  mdl_user user_m
     INNER JOIN mdl_user_info_data data ON data.userid = user_m.id
     INNER JOIN mdl_user_info_field field ON data.fieldid = field.id
     INNER JOIN mdl_talentospilos_usuario user_t ON data.data = CAST(user_t.id AS VARCHAR)
     INNER JOIN mdl_talentospilos_est_estadoases estado_u ON user_t.id = estado_u.id_estudiante
     INNER JOIN mdl_talentospilos_estados_ases estados ON estados.id = estado_u.id_estado_ases
     WHERE estados.nombre = 'ACTIVO/SEGUIMIENTO' AND field.shortname = 'idtalentos'

    INTERSECT

    SELECT user_m.id, SUBSTRING(user_m.username FROM 1 FOR 7) as codigo, user_m.firstname, user_m.lastname
    FROM mdl_user user_m INNER JOIN mdl_cohort_members memb ON user_m.id = memb.userid INNER JOIN mdl_cohort cohorte ON memb.cohortid = cohorte.id
    WHERE cohorte.idnumber LIKE 'SP%') estudiantes INNER JOIN mdl_grade_grades grades ON estudiantes.id = grades.userid
    INNER JOIN mdl_grade_items items ON grades.itemid = items.id 
    INNER JOIN mdl_course curso ON curso.id = items.courseid
WHERE SUBSTRING(curso.shortname FROM 15 FOR 6) = '201708' AND
      grades.finalgrade < 3 
GROUP BY estudiantes.id, estudiantes.codigo, estudiantes.firstname, estudiantes.lastname


--------- CONSULTA POR DOCENTES DE ITEMS CALIFICADOS
SELECT DISTINCT
  (SELECT concat_ws(' ',firstname,lastname) AS fullname
   FROM
     (SELECT usuario.firstname,
             usuario.lastname,
             userenrol.timecreated
      FROM {course} cursoP
      INNER JOIN {context} cont ON cont.instanceid = cursoP.id
      INNER JOIN {role_assignments} rol ON cont.id = rol.contextid
      INNER JOIN {user} usuario ON rol.userid = usuario.id
      INNER JOIN {enrol} enrole ON cursoP.id = enrole.courseid
      INNER JOIN {user_enrolments} userenrol ON (enrole.id = userenrol.enrolid
                                                 AND usuario.id = userenrol.userid)
      WHERE cont.contextlevel = 50
        AND rol.roleid = 3
        AND cursoP.id = curso.id
      ORDER BY userenrol.timecreated ASC
      LIMIT 1) AS subc) AS "DOCENTE",
                curso.fullname AS "CURSO",
                curso.shortname AS "CÓDIGO",

  (SELECT COUNT(id) AS cant
   FROM {grade_items}
   WHERE courseid = curso.id) AS "ITEMS CREADOS",

   (SELECT COUNT(notas.id)
   FROM {grade_items} items INNER JOIN {grade_grades} notas ON items.id = notas.itemid
   WHERE items.courseid = curso.id
    AND notas.finalgrade IS NOT NULL) AS "N° NOTAS CALIFICADAS",

    (SELECT COUNT(notas.id)
   FROM {grade_items} items INNER JOIN {grade_grades} notas ON items.id = notas.itemid
   WHERE items.courseid = curso.id
    AND notas.finalgrade < 3 
    AND notas.userid IN
    (SELECT user_m.id
     FROM {user} user_m
     INNER JOIN {user_info_data} DATA ON data.userid = user_m.id
     INNER JOIN {user_info_field} field ON data.fieldid = field.id
     INNER JOIN {talentospilos_usuario} user_t ON data.data = CAST(user_t.id AS VARCHAR)
     INNER JOIN {talentospilos_est_estadoases} estado_u ON user_t.id = estado_u.id_estudiante
     INNER JOIN {talentospilos_estados_ases} estados ON estados.id = estado_u.id_estado_ases
     WHERE estados.nombre = 'ACTIVO/SEGUIMIENTO'
       AND field.shortname = 'idtalentos')) AS "N° NOTAS PERDIDAS"

FROM {course} curso
INNER JOIN {enrol} ROLE ON curso.id = role.courseid
INNER JOIN {user_enrolments} enrols ON enrols.enrolid = role.id
WHERE SUBSTRING(curso.shortname
                FROM 15
                FOR 6) = '201708'
  AND enrols.userid IN
    (SELECT user_m.id
     FROM {user} user_m
     INNER JOIN {user_info_data} DATA ON data.userid = user_m.id
     INNER JOIN {user_info_field} field ON data.fieldid = field.id
     INNER JOIN {talentospilos_usuario} user_t ON data.data = CAST(user_t.id AS VARCHAR)
     INNER JOIN {talentospilos_est_estadoases} estado_u ON user_t.id = estado_u.id_estudiante
     INNER JOIN {talentospilos_estados_ases} estados ON estados.id = estado_u.id_estado_ases
     WHERE estados.nombre = 'ACTIVO/SEGUIMIENTO'
       AND field.shortname = 'idtalentos')
---------------
SELECT DISTINCT         
          (SELECT concat_ws(' ',firstname,lastname) AS fullname
           FROM
             (SELECT usuario.firstname,
                     usuario.lastname,
                     userenrol.timecreated
              FROM mdl_course cursoP
              INNER JOIN mdl_context cont ON cont.instanceid = cursoP.id
              INNER JOIN mdl_role_assignments rol ON cont.id = rol.contextid
              INNER JOIN mdl_user usuario ON rol.userid = usuario.id
              INNER JOIN mdl_enrol enrole ON cursoP.id = enrole.courseid
              INNER JOIN mdl_user_enrolments userenrol ON (enrole.id = userenrol.enrolid
                                                           AND usuario.id = userenrol.userid)
              WHERE cont.contextlevel = 50
                AND rol.roleid = 3
                AND cursoP.id = curso.id
              ORDER BY userenrol.timecreated ASC
              LIMIT 1) AS subc) AS DOCENTE,
			  curso.fullname AS CURSO,
			  curso.shortname AS CODIGO,
              (SELECT COUNT(id) as cant
                    FROM mdl_grade_items
                    WHERE courseid = curso.id)  AS "ITEMS CREADOS",

   (SELECT COUNT(notas.id)
   FROM mdl_grade_items items INNER JOIN mdl_grade_grades notas ON items.id = notas.itemid
   WHERE items.courseid = curso.id
    AND notas.finalgrade IS NOT NULL 
    AND notas.userid IN
            (SELECT user_m.id
     FROM  mdl_user user_m
     INNER JOIN mdl_user_info_data data ON data.userid = user_m.id
     INNER JOIN mdl_user_info_field field ON data.fieldid = field.id
     INNER JOIN mdl_talentospilos_usuario user_t ON data.data = CAST(user_t.id AS VARCHAR)
     INNER JOIN mdl_talentospilos_est_estadoases estado_u ON user_t.id = estado_u.id_estudiante
     INNER JOIN mdl_talentospilos_estados_ases estados ON estados.id = estado_u.id_estado_ases
     WHERE estados.nombre = 'ACTIVO/SEGUIMIENTO' AND field.shortname = 'idtalentos')) AS "N° NOTAS CALIFICADAS",

    (SELECT COUNT(notas.id)
   FROM mdl_grade_items items INNER JOIN mdl_grade_grades notas ON items.id = notas.itemid
   WHERE items.courseid = curso.id
    AND notas.finalgrade < 3
    AND notas.userid IN
            (SELECT user_m.id
     FROM  mdl_user user_m
     INNER JOIN mdl_user_info_data data ON data.userid = user_m.id
     INNER JOIN mdl_user_info_field field ON data.fieldid = field.id
     INNER JOIN mdl_talentospilos_usuario user_t ON data.data = CAST(user_t.id AS VARCHAR)
     INNER JOIN mdl_talentospilos_est_estadoases estado_u ON user_t.id = estado_u.id_estudiante
     INNER JOIN mdl_talentospilos_estados_ases estados ON estados.id = estado_u.id_estado_ases
     WHERE estados.nombre = 'ACTIVO/SEGUIMIENTO' AND field.shortname = 'idtalentos')) AS "N° NOTAS PERDIDAS"

        FROM mdl_course curso
        INNER JOIN mdl_enrol ROLE ON curso.id = role.courseid
        INNER JOIN mdl_user_enrolments enrols ON enrols.enrolid = role.id
        WHERE SUBSTRING(curso.shortname FROM 15 FOR 6) = '201708' AND enrols.userid IN
            (SELECT user_m.id
     FROM  mdl_user user_m
     INNER JOIN mdl_user_info_data data ON data.userid = user_m.id
     INNER JOIN mdl_user_info_field field ON data.fieldid = field.id
     INNER JOIN mdl_talentospilos_usuario user_t ON data.data = CAST(user_t.id AS VARCHAR)
     INNER JOIN mdl_talentospilos_est_estadoases estado_u ON user_t.id = estado_u.id_estudiante
     INNER JOIN mdl_talentospilos_estados_ases estados ON estados.id = estado_u.id_estado_ases
     WHERE estados.nombre = 'ACTIVO/SEGUIMIENTO' AND field.shortname = 'idtalentos')
     

     --------------------------------------------------------------------------------------------

     SELECT user_m.id
     FROM {user} user_m
     INNER JOIN {talentospilos_user_extended} extended ON user_m.id = extended.id_moodle_user
     INNER JOIN {talentospilos_usuario} user_t ON extended.id_ases_user = user_t.id
     INNER JOIN {talentospilos_est_estadoases} estado_u ON user_t.id = estado_u.id_estudiante
     INNER JOIN {talentospilos_estados_ases} estados ON estados.id = estado_u.id_estado_ases
     WHERE estados.nombre = 'ACTIVO/SEGUIMIENTO'


     SELECT id_moodle_user 
     FROM {talentospilos_user_extended} extended
     WHERE id_ases_user = $id


     -------------------------------------------------------------------------------------------------
     --Consultas historic_academic_reports

     SELECT num_doc, SUBSTRING(username FROM 1 FOR 7) as username, firstname, lastname, semestre.nombre as semestre, promedio_semestre as promSem, promedio_acumulado as promAcum, programa.nombre as programa, cohorte.name as cohorte, json_materias
     FROM {talentospilos_history_academ} historic INNER JOIN {talentospilos_usuario} usuario ON historic.id_estudiante = usuario.id 
     INNER JOIN {talentospilos_semestre} semestre ON historic.id_semestre = semestre.id
     INNER JOIN {talentospilos_programa} programa ON historic.id_programa = programa.id
     INNER JOIN {talentospilos_user_extended} user_ext ON historic.id_estudiante = user_ext.id_ases_user
     INNER JOIN {user} user_moodle ON user_ext.id_moodle_user = user_moodle.id



------------------------------------------------------------

--Consulta para saber por cohortes
SELECT cohorte.name AS nombre_cohorte,
         COUNT(usuario.id) AS "TOTAL",
         
    (SELECT COUNT(*)
    FROM 
        (SELECT DISTINCT id_moodle_user
        FROM {cohort_members} membic
        INNER JOIN {talentospilos_user_extended} extic
            ON membic.userid = extic.id_moodle_user
        INNER JOIN {talentospilos_est_est_icetex} est_icetex
            ON est_icetex.id_estudiante = extic.id_ases_user
        INNER JOIN {talentospilos_estados_icetex} estados_icetex
            ON est_icetex.id_estado_icetex = estados_icetex.id
        WHERE membic.cohortid=cohorte.id
                AND estados_icetex.nombre = 'ACTIVO'
                AND est_icetex.fecha = 
            (SELECT MAX(fecha)
            FROM {talentospilos_est_est_icetex}
            WHERE id_estudiante = extic.id_ases_user)) activos_icetex ) AS activos_icetex, 
            (SELECT COUNT(*)
            FROM 
                (SELECT DISTINCT id_moodle_user
                FROM {cohort_members} membases
                INNER JOIN {talentospilos_user_extended} extases
                    ON membases.userid = extases.id_moodle_user
                INNER JOIN {talentospilos_est_estadoases} est_ases
                    ON est_ases.id_estudiante = extases.id_ases_user
                INNER JOIN {talentospilos_estados_ases} estados_ases
                    ON est_ases.id_estado_ases = estados_ases.id
                WHERE membases.cohortid = cohorte.id
                        AND estados_ases.nombre = 'seguimiento'
                        AND est_ases.fecha = 
                    (SELECT max(fecha)
                    FROM {talentospilos_est_estadoases}
                    WHERE id_estudiante = extases.id_ases_user)) activos_ases ) AS activos_ases, 
                    (SELECT COUNT(*)
                    FROM 
                        (SELECT DISTINCT id_moodle_user
                        FROM {cohort_members} membprog
                        INNER JOIN {talentospilos_user_extended} extprog
                            ON membprog.userid = extprog.id_moodle_user
                        WHERE membprog.cohortid = cohorte.id
                                AND (extprog.program_status = 'ACTIVO'
                                OR extprog.program_status = '1')) activos_sra) AS activos_sra
                    FROM {cohort} cohorte
                INNER JOIN {cohort_members} memb
                ON cohorte.id = memb.cohortid
        INNER JOIN {user} usuario
        ON usuario.id = memb.userid
WHERE cohorte.idnumber = 'SPT12016A'
        OR cohorte.idnumber = 'SPP42018A'
        OR cohorte.idnumber = 'SPP32017A'
        OR cohorte.idnumber = 'SPP22016A'
        OR cohorte.idnumber = 'SPP12015A'
GROUP BY  nombre_cohorte, cohorte.id 








--activos en icetex en una cohorte determinada (11 = SPP3)

SELECT COUNT(*) AS activos_icetex
FROM 
    (SELECT DISTINCT id_moodle_user
    FROM {cohort_members} membic
    INNER JOIN {talentospilos_user_extended} extic
        ON membic.userid = extic.id_moodle_user
    INNER JOIN {talentospilos_est_est_icetex} est_icetex
        ON est_icetex.id_estudiante = extic.id_ases_user
    INNER JOIN {talentospilos_estados_icetex} estados_icetex
        ON est_icetex.id_estado_icetex = estados_icetex.id
    WHERE membic.cohortid=11
            AND estados_icetex.nombre = 'ACTIVO'
            AND est_icetex.fecha = 
        (SELECT MAX(fecha)
        FROM {talentospilos_est_est_icetex}
        WHERE id_estudiante = extic.id_ases_user)) activos_icetex 


--activos en ases en una cohorte determinada (11 = SPP3)


SELECT COUNT(*)
FROM 
    (SELECT DISTINCT id_moodle_user
    FROM {cohort_members} membases
    INNER JOIN {talentospilos_user_extended} extases
        ON membases.userid = extases.id_moodle_user
    INNER JOIN {talentospilos_est_estadoases} est_ases
        ON est_ases.id_estudiante = extases.id_ases_user
    INNER JOIN {talentospilos_estados_ases} estados_ases
        ON est_ases.id_estado_ases = estados_ases.id
    WHERE membases.cohortid = 11
            AND estados_ases.nombre = 'seguimiento'
            AND est_ases.fecha = 
        (SELECT max(fecha)
        FROM {talentospilos_est_estadoases}
        WHERE id_estudiante = extases.id_ases_user)) activos_ases


--activos en programa
SELECT COUNT(*)
FROM 
    (SELECT DISTINCT id_moodle_user
    FROM {cohort_members} membprog
    INNER JOIN {talentospilos_user_extended} extprog
        ON membprog.userid = extprog.id_moodle_user
    WHERE membprog.cohortid = 11
            AND (extprog.program_status = 'ACTIVO'
            OR extprog.program_status = '1')) activos_sra

-- Cursos ases sin profesor


SELECT DISTINCT
    On (
       ases_course.curso_id) *
from            (
                select distinct ON (mdl_course.id)
                       mdl_course.id AS curso_id,
                       mdl_course.fullname,
                       mdl_course.shortname
                FROM            mdl_user
                                  INNER JOIN      mdl_talentospilos_user_extended
                                    ON              mdl_user.id = mdl_talentospilos_user_extended.id_moodle_user
                                  INNER JOIN      mdl_cohort_members
                                    ON              mdl_cohort_members.userid = mdl_user.id
                                  INNER JOIN      mdl_talentospilos_inst_cohorte
                                    ON              mdl_talentospilos_inst_cohorte.id_cohorte = mdl_cohort_members.cohortid
                                  INNER JOIN      mdl_role_assignments
                                    ON              mdl_role_assignments.userid = mdl_user.id
                                  INNER JOIN      mdl_context
                                    ON              mdl_role_assignments.contextid = mdl_context.id
                                  INNER JOIN      mdl_course
                                    ON              mdl_course.id = mdl_context.instanceid
                                  INNER JOIN      mdl_talentospilos_est_estadoases
                                    ON              mdl_talentospilos_user_extended.id_ases_user = mdl_talentospilos_est_estadoases.id_estudiante
                                  INNER JOIN      mdl_talentospilos_estados_ases
                                    ON              mdl_talentospilos_est_estadoases.id_estado_ases = mdl_talentospilos_estados_ases.id
                WHERE           mdl_talentospilos_estados_ases.nombre='seguimiento'
                  AND             mdl_talentospilos_inst_cohorte.id_instancia = 450299
                  AND             mdl_talentospilos_user_extended.tracking_status = 1
                  AND             mdl_role_assignments.roleid = 5
                  AND             SUBSTRING(mdl_course.shortname FROM 15 FOR 6) = '201808' ) AS ases_course
                  INNER JOIN      mdl_context
                    ON              mdl_context.instanceid = ases_course.curso_id
                  INNER JOIN      mdl_role_assignments
                    ON              mdl_role_assignments.contextid = mdl_context.id
                  INNER JOIN      mdl_user
                    ON              mdl_role_assignments.userid = mdl_user.id
                                      AND             ases_course.curso_id NOT IN
                                                      (
                                                      SELECT DISTINCT
                                                          ON (
                                                             ases_course.curso_id) ases_course.curso_id
                                                      FROM            (
                                                                      SELECT DISTINCT
                                                                          ON (
                                                                             mdl_course.id) mdl_course.id AS curso_id,
                                                                             mdl_course.fullname,
                                                                             mdl_course.shortname
                                                                      FROM            mdl_user
                                                                                        INNER JOIN      mdl_talentospilos_user_extended
                                                                                          ON              mdl_user.id = mdl_talentospilos_user_extended.id_moodle_user
                                                                                        INNER JOIN      mdl_cohort_members
                                                                                          ON              mdl_cohort_members.userid = mdl_user.id
                                                                                        INNER JOIN      mdl_talentospilos_inst_cohorte
                                                                                          ON              mdl_talentospilos_inst_cohorte.id_cohorte = mdl_cohort_members.cohortid
                                                                                        INNER JOIN      mdl_role_assignments
                                                                                          ON              mdl_role_assignments.userid = mdl_user.id
                                                                                        INNER JOIN      mdl_context
                                                                                          ON              mdl_role_assignments.contextid = mdl_context.id
                                                                                        INNER JOIN      mdl_course
                                                                                          ON              mdl_course.id = mdl_context.instanceid
                                                                                        INNER JOIN      mdl_talentospilos_est_estadoases
                                                                                          ON              mdl_talentospilos_user_extended.id_ases_user = mdl_talentospilos_est_estadoases.id_estudiante
                                                                                        INNER JOIN      mdl_talentospilos_estados_ases
                                                                                          ON              mdl_talentospilos_est_estadoases.id_estado_ases = mdl_talentospilos_estados_ases.id
                                                                      WHERE           mdl_talentospilos_estados_ases.nombre='seguimiento'
                                                                        AND             mdl_talentospilos_inst_cohorte.id_instancia = 450299
                                                                        AND             mdl_talentospilos_user_extended.tracking_status = 1
                                                                        AND             mdl_role_assignments.roleid = 5
                                                                        AND             SUBSTRING(mdl_course.shortname FROM 15 FOR 6) = '201808' ) AS ases_course
                                                                        INNER JOIN      mdl_context
                                                                          ON              mdl_context.instanceid = ases_course.curso_id
                                                                        INNER JOIN      mdl_role_assignments
                                                                          ON              mdl_role_assignments.contextid = mdl_context.id
                                                                        INNER JOIN      mdl_user
                                                                          ON              mdl_role_assignments.userid = mdl_user.id
                                                      WHERE           mdl_role_assignments.roleid = 3)

-- estudiantes ases relacionados a items
select distinct on (mdl_talentospilos_usuario.id) * from mdl_talentospilos_usuario
                                                           inner join mdl_talentospilos_user_extended
                                                             on mdl_talentospilos_user_extended.id_ases_user = mdl_talentospilos_usuario.id
                                                           inner join mdl_role_assignments
                                                             on mdl_role_assignments.userid = mdl_talentospilos_user_extended.id_moodle_user
where TO_TIMESTAMP(mdl_role_assignments.timemodified) > to_timestamp('2018-05-01', 'YYYY-MM-d') --current semester role assignments
  and mdl_role_assignments.roleid =  5 ;-- Student role assignemnts


select * from mdl_grade_items
                inner join mdl_grade_grades
                  on mdl_grade_items.id = mdl_grade_grades.itemid
                inner join mdl_user
                  on mdl_grade_grades.userid = mdl_user.id
where  mdl_grade_items.itemtype != 'category'
  AND  mdl_grade_items.itemtype != 'course';


select to_timestamp('2018-05-01', 'YYYY-MM-d');

-- Estudiantes ases y cantidad de items perdidos y ganados en el semestre
select  count(*) filter(where item_ganado = false) as cantidad_items_perdidos, count(*) filter (where item_ganado = true) as cantidad_items_ganados, username, mdl_talentospilos_usuario_id, firstname, lastname , num_doc  from (
                                                                                                                                                                                                                                 select
                                                                                                                                                                                                                                     distinct mdl_user.*, mdl_talentospilos_usuario.num_doc,
                                                                                                                                                                                                                                              case when (finalgrade < grademax * 0.6 or finalgrade is  null) then false else true end as item_ganado,
                                                                                                                                                                                                                                              mdl_talentospilos_usuario.id as mdl_talentospilos_usuario_id, finalgrade, grademax, mdl_grade_items.itemname, mdl_grade_items.id as item_id  ,
                                                                                                                                                                                                                                              (select count(*) from mdl_grade_grades as mdl_grade_grades_inner
                                                                                                                                                                                                                                                                      inner join mdl_grade_items as mdl_grade_items_inner
                                                                                                                                                                                                                                                                        on mdl_grade_grades_inner.itemid = mdl_grade_items_inner.id
                                                                                                                                                                                                                                               where mdl_grade_items_inner.courseid = mdl_course.id
                                                                                                                                                                                                                                                 and mdl_grade_items_inner.id = mdl_grade_items.id
                                                                                                                                                                                                                                                 and mdl_grade_grades.userid = mdl_user.id
                                                                                                                                                                                                                                                 and mdl_grade_grades_inner.finalgrade is not null ) as calificaciones_item_todos_estudiantes
                                                                                                                                                                                                                                 from mdl_talentospilos_usuario
                                                                                                                                                                                                                                        inner join mdl_talentospilos_user_extended
                                                                                                                                                                                                                                          on mdl_talentospilos_user_extended.id_ases_user = mdl_talentospilos_usuario.id
                                                                                                                                                                                                                                        inner join mdl_user
                                                                                                                                                                                                                                          on mdl_user.id = mdl_talentospilos_user_extended.id_moodle_user
                                                                                                                                                                                                                                        inner join mdl_cohort_members
                                                                                                                                                                                                                                          on mdl_cohort_members.userid = mdl_user.id
                                                                                                                                                                                                                                        inner join mdl_talentospilos_est_estadoases
                                                                                                                                                                                                                                          on mdl_talentospilos_user_extended.id_ases_user = mdl_talentospilos_est_estadoases.id_estudiante
                                                                                                                                                                                                                                        inner join mdl_talentospilos_estados_ases
                                                                                                                                                                                                                                          on mdl_talentospilos_est_estadoases.id_estado_ases = mdl_talentospilos_estados_ases.id
                                                                                                                                                                                                                                        inner join mdl_grade_grades
                                                                                                                                                                                                                                          on mdl_grade_grades.userid = mdl_user.id
                                                                                                                                                                                                                                        inner join mdl_grade_items
                                                                                                                                                                                                                                          on mdl_grade_items.id = mdl_grade_grades.itemid
                                                                                                                                                                                                                                        inner join mdl_course
                                                                                                                                                                                                                                          on mdl_grade_items.courseid= mdl_course.id

                                                                                                                                                                                                                                 where substring(mdl_course.shortname from 15 for 6) = '201808'
                                                                                                                                                                                                                                   and mdl_cohort_members.cohortid in (
                                                                                                                                                                                                                                                                      select id from mdl_talentospilos_inst_cohorte where mdl_talentospilos_inst_cohorte.id_instancia = 450299
                                                                                                                                                                                                                                                                      )
                                                                                                                                                                                                                                   and mdl_grade_items.itemtype != 'category'
                                                                                                                                                                                                                                   AND  mdl_grade_items.itemtype != 'course'
                                                                                                                                                                                                                                   and mdl_talentospilos_user_extended.tracking_status = 1
                                                                                                                                                                                                                                   and mdl_talentospilos_estados_ases.nombre = 'seguimiento'
                                                                                                                                                                                                                                 ) as notas_estudiante
where notas_estudiante.calificaciones_item_todos_estudiantes > 0
group by (username, mdl_talentospilos_usuario_id,  firstname, lastname, num_doc)
;