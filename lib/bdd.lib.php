<?php

function connexion()
{
    $dbh = new PDO(DB_DSN,DB_USER,DB_PASS);
    //On dit à PDO de nous envoyer une exception s'il n'arrive pas à se connecter ou s'il rencontre une erreur
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    return $dbh;
}

function getColumnName($table,$dbh)
{
    $q = $dbh->prepare("DESCRIBE ".$table);
    $q->execute();
    $table_fields = $q->fetchAll(PDO::FETCH_COLUMN);
    $stringFields = '';
    foreach($table_fields as $field)
        $stringFields .= $field.',';

    return $stringFields;
}
