<?php
require('config/config.php');
require('lib/bdd.lib.php');


$vue='product/list';
$title = 'Tous les produits';
$activeMenu='products';

try
{
    /** 1 : connexion au serveur de BDD - SGBDR */
    $dbh = connexion();

    /**2 : Prépare ma requête SQL */
    $sth = $dbh->prepare('SELECT *,(MSRP - buyPrice) as marge FROM '.DB_PREFIXE.'products');

    /** 3 : executer la requête */
    $sth->execute();

    /** 4 : recupérer les résultats 
     * On utilise FETCHALL car il y a plusieurs éléments à récupérer
    */
    $products = $sth->fetchAll(PDO::FETCH_ASSOC);

}
catch(PDOException $e)
{
    $vue = 'erreur';
    //Si une exception est envoyée par PDO (exemple : serveur de BDD innaccessible) on arrive ici
    $messageErreur =  'Une erreur de connexion a eu lieu :'.$e->getMessage();
}

require('tpl/' . LAYOUT . '.phtml');
