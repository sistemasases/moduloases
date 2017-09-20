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
    FROM mdl_user user_m INNER JOIN mdl_cohort_members memb ON user_m.id = memb.userid INNER JOIN mdl_cohort cohorte ON memb.cohortid = cohorte.id 
    WHERE SUBSTRING(cohorte.idnumber FROM 1 FOR 2) = 'SP'

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

--prueba

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