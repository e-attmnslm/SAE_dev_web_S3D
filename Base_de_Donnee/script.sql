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




-------- A REVOIR--------
--une liste de touites qu’il a publiée 
select t.id_touite, t.TEXTE from UTILISATEUR u 
    inner join touite t on t.id_uti = u.ID_UTI 
    Where u.id_uti = 1;

--une liste de touites des utilisateurs suivis
SELECT t.id_touite, t.texte 
    FROM suivre s 
    INNER JOIN touite t ON t.id_uti = s.uti_suivi
    WHERE s.uti_suiveur = 1;

--une liste de touites par tag donné
SELECT t.id_touite, t.texte 
    FROM appartienttag ats
    INNER JOIN touite t ON t.id_touite = ats.id_touite
    WHERE ats.id_tag = 2;
-------- A REVOIR--------


-- Mettre à jour le nombre de touites d’un utilisateur lorsqu’un touite est créé.
CREATE OR REPLACE PROCEDURE updateTouite(uti NUMBER) IS
    nbt NUMBER;
    trouver NUMBER;
BEGIN

    SELECT COUNT(*) INTO trouver 
    FROM utilisateur 
    WHERE id_uti = uti;

    IF trouver = 0 THEN
        -- Si l'utilisateur n'existe pas 
        RAISE NO_DATA_FOUND;
    END IF;


    SELECT COUNT(*) INTO nbt 
    FROM touite 
    WHERE id_uti = uti;


    UPDATE utilisateur 
    SET nbtouite = nbt 
    WHERE id_uti = uti; 

    DBMS_OUTPUT.PUT_LINE('Le nouveau nombre de touites de l''utilisateur ' || uti || ' : ' || nbt);

EXCEPTION
    WHEN NO_DATA_FOUND THEN
        DBMS_OUTPUT.PUT_LINE('L''utilisateur '|| uti|| ' n''existe pas' );
END updateTouite;
/

select * from touite;

declare
begin
UPDATETOUITE(3);
end;


--Un utilisateur ne peut pas se suivre lui-même ou suivre un utilisateur ayant le même email.

CREATE OR REPLACE FUNCTION SuivreUti( suiveur number, suivi number) RETURN boolean
    is
    nbt number ;
    mail_suiveur varchar(50);
    mail_suivi varchar(50);
    possible number;

    begin
    if suiveur = suivi then 
        dbms_output.put_line('l''utilisateur ne pas se suivre lui meme');
        return false;
    end if;

    select mail into mail_suiveur from utilisateur where ID_UTI = suiveur;
    select mail into mail_suivi from utilisateur where ID_UTI = suivi;

    if mail_suiveur = mail_suivi then 
        dbms_output.put_line('l''utilisateur ne peut pas se suivre un utilisateur avec le meme mail');
        return false;
    end if;

    select count(*) into possible from suivre where UTI_SUIVEUR=suiveur and uti_suivi= suivi;

    if possible !=0 then 
        dbms_output.put_line('il suit deja l''utilisateur');
        return false;
    else 
        insert into SUIVRE(UTI_SUIVEUR,UTI_SUIVI) values (suiveur,suivi);
        dbms_output.put_line('il a bien pu suivre l''utilisateur');
    end if;

    return true;


exception
when NO_DATA_FOUND then
dbms_output.put_line('un des utilisateur n''existe pas');
return false;

END SuivreUti;





--Empêcher la modification de l’auteur d’un touite : une fois publié, un touite conserve
--son auteur initial. Il est interdit de transférer une publication à un autre utilisateur.

CREATE OR REPLACE TRIGGER modif_auteur 
    BEFORE update on touite 
    for each row
    begin 
        if :new.id_uti!=:old.id_uti then 
            Raise_application_error(-20001, 'l''auteur d''un touite ne peut pas etre modifier') ;
        end if;
    end;


--Un utilisateur est limité à 50 touites par jour, pour éviter que des bots ne saturent la plateforme.

CREATE OR REPLACE TRIGGER trg_limite_50_touites
    BEFORE INSERT ON touite
    FOR EACH ROW
    DECLARE
        v_compteur NUMBER;
    BEGIN
        -- 1. Compter les touites de l'utilisateur pour la journée en cours
        SELECT COUNT(*) 
        INTO v_compteur 
        FROM touite 
        WHERE id_uti = :new.id_uti 
        AND TRUNC(date_public) =  TRUNC(SYSDATE);

        -- 2. Si la limite de 50 est atteinte ou dépassée, on bloque
        IF v_compteur >= 50 THEN
            RAISE_APPLICATION_ERROR(-20002, ' Vous ne pouvez pas publier plus de 50 touites par jour.');
        END IF;
    END;
