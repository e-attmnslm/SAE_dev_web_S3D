create table utilisateur (
    id_uti number primary key,
    nom varchar(50),
    prenom varchar(50),
    mail varchar(50),
    nbtouite number 

);

create table touite (
    id_uti number,
    id_touite number primary key,
    id_img number,
    texte varchar(235),
    date_public date 

);

create table tags (
    id_tag number primary key,
    libelle varchar(200),
    desc_tag varchar(200)

);

create table image (
    id_img number primary key,
    desc_img varchar(200),
    chemin varchar(200)


);

create table Suivre (
    uti_suiveur number,
    uti_suivi number,
    PRIMARY KEY (uti_suiveur, uti_suivi)

);

create table AbonneTag (
    id_uti number,
    id_tag number,
    PRIMARY KEY (id_uti, id_tag)


);

create table AppartientTag (
    id_touite number,
    id_tag number,
    PRIMARY KEY (id_touite, id_tag)


);

--Table touite
alter table touite add ( foreign
key (id_img)
references image(id_img));

alter table touite add ( foreign
key (id_uti)
references utilisateur(id_uti));

--Table suivre

alter table suivre add ( foreign key
(uti_suiveur)
references utilisateur(id_uti));

alter table suivre add ( foreign key
(uti_suivi)
references utilisateur(id_uti));

--Table AbonneTag

alter table AbonneTag add ( foreign key
(id_uti)
references utilisateur(id_uti));

alter table AbonneTag add ( foreign key
(id_tag)
references tags(id_tag));

--Table AppartienTag

alter table AppartientTag add ( foreign key
(id_touite)
references touite(id_touite));

alter table AppartientTag add ( foreign key
(id_tag)
references tags(id_tag));
