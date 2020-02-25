<?php
require('config/config.php');
require('lib/bdd.lib.php');

$vue='search';
$title="Résultat de la recherche";
$activeMenu='';
    
try {
    /** On envoi une exception si l'id n'est pas passé dans la chaine de requête
     * Le reste des ligne du bloc try ne sera pas executé
     * On va directement au bloc catch
     */
    if (!array_key_exists('search', $_POST)) {
        throw new Exception('Tu fais quoi ici ?');
    }

    $search = $_POST['search'];

    $title.= ' : '.$search;

    $bind = array('search'=>'%'.$search.'%');

    /** 1 : connexion au serveur de BDD - SGBDR */
    $dbh = connexion();


    /** PREMIERE REQUETE ON RECUPERE LES CLIENTS **/
    /**2 : Prépare ma requête SQL */
    $sth = $dbh->prepare('SELECT * FROM  '.DB_PREFIXE. 'customers c
     INNER JOIN employees e on (c.salesRepEmployeeNumber = e.employeeNumber) WHERE customerName LIKE :search OR contactLastName LIKE :search OR contactFirstName LIKE :search');
    /** 3 : executer la requête
    */
    $sth->execute($bind);
    /** 4 : recupérer les résultats
     * On utilise FETCH car un seul résultat attendu
    */
    $customers = $sth->fetchAll(PDO::FETCH_ASSOC);

    //echo getColumnName('cm_customers',$dbh);

    /** DEUXIEME REQUETE LES PRODUITS */
    $sth = $dbh->prepare('SELECT *, (MSRP-buyPrice) as marge 
        FROM '.DB_PREFIXE.'products
        WHERE productName LIKE :search OR productLine LIKE :search OR productVendor LIKE :search OR productDescription LIKE :search');
    $sth->execute($bind);
    $products = $sth->fetchAll(PDO::FETCH_ASSOC);


    /** TROISIEME REQUETE LES EMPLOYES */
    $sth = $dbh->prepare('SELECT e.*,e2.firstName as firstNameReportsTo,e2.lastName as lastNameReportsTo, e2.jobTitle as jobTitleReportsTo 
        FROM ' . DB_PREFIXE . 'employees e 
        LEFT JOIN employees e2 ON (e.reportsTo = e2.employeeNumber)
        WHERE e.firstName LIKE :search OR e.lastName LIKE :search OR e.jobTitle LIKE :search
        ORDER BY employeeNumber');
    $sth->execute($bind);
    $employees = $sth->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $vue = 'erreur';
    //Si une exception est envoyée par PDO (exemple : serveur de BDD innaccessible) on arrive ici
    $messageErreur = 'Une erreur de connexion a eu lieu :'.$e->getMessage();
} catch (Exception $e) {
    $vue = 'erreur';
    //Si une exception est envoyée
    $messageErreur =  'Erreur dans la page :'.$e->getMessage();
}



require('tpl/' . LAYOUT . '.phtml');
