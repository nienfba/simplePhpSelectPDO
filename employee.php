<?php
require('config/config.php');
require('lib/bdd.lib.php');


$vue='employee/detail';
$title = 'Fiche employé';
$activeMenu='employes';

try
{
    /** On envoi une exception si l'id n'est pas pasé dans la chaine de requête
     * Le reste des ligne du bloc try ne sera pas executé
     * On va directement au bloc catch
     */
    if(!array_key_exists('id',$_GET))
        throw new Exception('Tu fais quoi ici ?');

    $employeeNumber = $_GET['id'];

    $dbh = connexion();
    $sth = $dbh->prepare('SELECT e.*,e2.firstName as firstNameReportsTo,e2.lastName as lastNameReportsTo, e2.jobTitle as jobTitleReportsTo FROM '.DB_PREFIXE.'employees e LEFT JOIN employees e2 ON (e.reportsTo = e2.employeeNumber) WHERE e.employeeNumber = :id');
    $sth->bindValue('id',$employeeNumber);
    $sth->execute();
    $employee = $sth->fetch(PDO::FETCH_ASSOC);



    /** On va maintenant récupérer tous les cliens de
     * l'employé en faisant une nouvelle requête 
     * On est déjà connecté donc inutile de se reconnecter au serveur
     * On commence à l'étape 2 
    */
    $sth = $dbh->prepare('SELECT * FROM '.DB_PREFIXE.'customers WHERE salesRepEmployeeNumber = :id');
    $sth->bindValue('id', $employeeNumber);
    $sth->execute();
    $customers = $sth->fetchAll(PDO::FETCH_ASSOC);


    /** On va maintenant récupérer tous les employés sous la responsabilité hiérarchique
     * 
     */
    $sth = $dbh->prepare('SELECT e.* 
    FROM '.DB_PREFIXE.'employees e
    WHERE e.reportsTo = :id
    ORDER BY employeeNumber');
    $sth->bindValue('id', $employeeNumber);
    $sth->execute();
    $employees = $sth->fetchAll(PDO::FETCH_ASSOC);

}
catch(PDOException $e)
{
    $vue = 'erreur';
    //Si une exception est envoyée par PDO (exemple : serveur de BDD innaccessible) on arrive ici
    $messageErreur = 'Une erreur de connexion a eu lieu :'.$e->getMessage();
}
catch(Exception $e)
{
    $vue = 'erreur';
    //Si une exception est envoyée
    $messageErreur =  'Erreur dans la page :'.$e->getMessage();
}



require('tpl/' . LAYOUT . '.phtml');

