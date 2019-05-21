<?php
include('config/config.php');
include('lib/bdd.lib.php');


$vue='employees.phtml';
$title = 'Tous les employés';
$activeMenu='employes';

try
{
    /** 1 : connexion au serveur de BDD - SGBDR */
    $dbh = connexion();

    /**2 : Prépare ma requête SQL */
    $sth = $dbh->prepare('SELECT e.*,e2.firstName as firstNameReportsTo,e2.lastName as lastNameReportsTo, e2.jobTitle as jobTitleReportsTo FROM '.DB_PREFIXE.'employees e LEFT JOIN employees e2 ON (e.reportsTo = e2.employeeNumber) ORDER BY employeeNumber');

    /** 3 : executer la requête */
    $sth->execute();

    /** 4 : recupérer les résultats 
     * On utilise FETCH car un seul résultat attendu
    */
    $employees = $sth->fetchAll(PDO::FETCH_ASSOC);

   // var_dump($employes);exit();

}
catch(PDOException $e)
{
    $vue = 'erreur.phtml';
    //Si une exception est envoyée par PDO (exemple : serveur de BDD innaccessible) on arrive ici
    $messageErreur =  'Une erreur de connexion a eu lieu :'.$e->getMessage();
}

include('tpl/layout.phtml');