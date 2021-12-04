create table admin
(
    id         int auto_increment primary key      not null,
    correo     varchar(100)                        not null,
    password   varchar(100)                        not null,
    created_at timestamp default current_timestamp not null,
    updated_at timestamp default current_timestamp not null
);

insert into admin (id, correo, password)
    value (1, 'prueba@gmail.com', '12345678');

create table apoderado
(
    id         int auto_increment primary key      not null,
    name       varchar(100)                        not null,
    lastname   varchar(100)                        not null,
    correo     varchar(100)                        not null,
    password   varchar(100)                        not null,
    created_at timestamp default current_timestamp not null,
    updated_at timestamp default current_timestamp not null
);

insert into apoderado (id, name, lastname, correo, password)
VALUES (0, '', '', '', '');

insert into apoderado (id, name, lastname, correo, password)
VALUES (1, 'Katia', 'Garcia', 'katia@gmail.com', '12345678');

insert into apoderado (id, name, lastname, correo, password)
VALUES (2, 'Apoderado Prueba', 'Apoderado Prueba', 'prueba_apoderado@gmail.com', '12345678');

insert into apoderado (id, name, lastname, correo, password)
VALUES (3, 'Alejandra', 'Gonzales', 'alejandra@gmail.com', '12345678');

create table tokensFCM
(
    idUser     int                                 not null,
    token      text                                not null,
    role       varchar(20)                         not null,
    created_at timestamp default current_timestamp not null,
    updated_at timestamp default current_timestamp not null
);

create table tokenSession
(
    idUser     int                                 not null,
    token      text                                not null,
    role       varchar(20)                         not null,
    created_at timestamp default current_timestamp not null,
    updated_at timestamp default current_timestamp not null
);

create table niveles
(
    id         int primary key                     not null,
    name       varchar(100)                        not null,
    created_at timestamp default current_timestamp not null,
    updated_at timestamp default current_timestamp not null
);
insert into niveles (id, name)
values (1, 'Preescolar'),
       (2, 'Primaria'),
       (3, 'Secundaria'),
       (4, 'Preparatoria'),
       (5, 'Licenciatura');

create table sub_nivel
(
    id         int auto_increment primary key      not null,
    idNivel    int                                 not null,
    name       varchar(100)                        not null,
    created_at timestamp default current_timestamp not null,
    updated_at timestamp default current_timestamp not null
);

insert into sub_nivel (id, idNivel, name)
values (1, 1, 'Primero'),
       (2, 1, 'Segundo'),
       (3, 1, 'Tercero'),
       (4, 2, 'Primero'),
       (5, 2, 'Segundo'),
       (6, 2, 'Tercero'),
       (7, 2, 'Cuarto'),
       (8, 2, 'Quinto'),
       (9, 2, 'Sexto'),
       (10, 3, 'Primero'),
       (11, 3, 'Segundo'),
       (12, 3, 'Tercero'),
       (13, 4, 'Primer Semestre'),
       (14, 4, 'Segundo Semestre'),
       (15, 4, 'Tercer Semestre'),
       (16, 4, 'Cuarto Semestre'),
       (17, 4, 'Quinto Semestre'),
       (18, 4, 'Sexto Semestre'),
       (19, 5, 'Primer Cuatrimestre'),
       (20, 5, 'Segundo Cuatrimestre'),
       (21, 5, 'Tercer Cuatrimestre'),
       (22, 5, 'Cuarto Cuatrimestre'),
       (23, 5, 'Quinto Cuatrimestre'),
       (24, 5, 'Sexto Cuatrimestre'),
       (25, 5, 'Setimo Cuatrimestre'),
       (26, 5, 'Octavo Cuatrimestre'),
       (27, 5, 'Noveno Cuatrimestre');

create table estudiantes
(
    id          int auto_increment primary key      not null,
    idapoderado int       default 0                 not null,
    name        varchar(100)                        not null,
    lastname    varchar(100)                        not null,
    correo      varchar(100)                        not null,
    password    varchar(100)                        not null,
    idSubNivel  int                                 not null,
    created_at  timestamp default current_timestamp not null,
    updated_at  timestamp default current_timestamp not null
);

create table notificaciones
(
    id           int auto_increment primary key      not null,
    idapoderado  int                                 not null,
    idEstudiante int                                 not null,
    titulo       varchar(100)                        not null,
    mensaje      text                                not null,
    date_limit   timestamp default current_timestamp not null,
    created_at   timestamp default current_timestamp not null,
    updated_at   timestamp default current_timestamp not null
);

alter table sub_nivel
    add constraint idNivel_Niveles foreign key (idNivel) references niveles (id) on update restrict on delete restrict;


alter table estudiantes
    add constraint idapoderado_apoderado foreign key (idapoderado) references apoderado (id) on update restrict on delete restrict,
    add constraint idSubNivel_subNivel foreign key (idSubNivel) references sub_nivel (id) on update restrict on delete restrict;

alter table notificaciones
    add constraint idapoderado_apoderadoN foreign key (idapoderado) references apoderado (id) on update restrict on delete restrict,
    add constraint idEstudiante_Estudiantes foreign key (idEstudiante) references estudiantes (id)
        on update restrict on delete restrict;

select *
from tokenSession;
/*select *
from estudiantes
where correo = ?
  and password = ?
  and idSubNivel > 19;*/
