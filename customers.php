<?php
include('config/config.php');
include('lib/bdd.lib.php');


$vue='customers.phtml';
$title = 'Tous les clients';
$activeMenu='clients';

try
{
    /** 1 : connexion au serveur de BDD - SGBDR */
    $dbh = connexion();

    /**2 : Prépare ma requête SQL */
    $sth = $dbh->prepare('SELECT * FROM '.DB_PREFIXE.'customers c INNER JOIN employees e on (c.salesRepEmployeeNumber = e.employeeNumber) ORDER BY customerNumber');

    /** 3 : executer la requête */
    $sth->execute();

    /** 4 : recupérer les résultats 
     * On utilise FETCH car un seul résultat attendu
    */
    $customers = $sth->fetchAll(PDO::FETCH_ASSOC);

}
catch(PDOException $e)
{
    $vue = 'erreur.phtml';
    //Si une exception est envoyée par PDO (exemple : serveur de BDD innaccessible) on arrive ici
    $messageErreur =  'Une erreur de connexion a eu lieu :'.$e->getMessage();
}

include('tpl/layout.phtml');
