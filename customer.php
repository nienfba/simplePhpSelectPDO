<?php
require('config/config.php');
require('lib/bdd.lib.php');


$vue='customer/detail';
$title = 'Fiche client';
$activeMenu='clients';

try
{
    /** On envoi une exception si l'id n'est pas passée dans la chaîne de requête
     * Le reste des lignes du bloc try ne sera pas executé
     * On va directement au bloc catch
     */
    if (!array_key_exists('id', $_GET) || !is_numeric($_GET['id'])) {
        throw new Exception('Tu fais quoi ici ?');
    }
    
    $customerNumber = $_GET['id'];

    /** 1 : connexion au serveur de BDD - SGBDR */
    $dbh = connexion();

    /**2 : Prépare ma requête SQL */
    $sth = $dbh->prepare('SELECT * FROM ' . DB_PREFIXE . 'customers c INNER JOIN employees e on (c.salesRepEmployeeNumber = e.employeeNumber) WHERE customerNumber = ? ORDER BY customerNumber');

    /** 3 : executer la requête et bindage en une ligne
     * Attention : ici je fais confiance à PDO pour binder correctement la valeur.
     * J'utilise donc un ? dans la préparation de la requête et je passe un tableau indéxé à execute.
     * Dans un projet sérieux on préfèrera utiliser bindValue ou bindParam comme je vous l'ai montré. 
     * Mais dans la réalité vous pourrez aussi être confronté à des requête avec des ?. C'est pour 
     * cette raison que je vous présente cela dans cette correction et pour toutes les requêtes ! 
    */
    $sth->execute(array($customerNumber));

    /** 4 : recupérer les résultats 
     * On utilise FETCH car un seul résultat attendu
    */
    $customer = $sth->fetch(PDO::FETCH_ASSOC);


    /** On va maintenant récupérer toutes les commandes du 
     * client en faisant une nouvelle requête 
     * On est déjà connecté donc inutile de se reconnecter au serveur
     * On commence à l'étape 2 
    */
    /**2 : Prépare ma requête SQL */
    $sth = $dbh->prepare('SELECT * FROM '.DB_PREFIXE.'orders WHERE customerNumber = ?');
    /** 3 : executer la requête */
    $sth->execute(array($customerNumber));
    /** 4 : recupérer les résultats */
    $orders = $sth->fetchAll(PDO::FETCH_ASSOC);

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

