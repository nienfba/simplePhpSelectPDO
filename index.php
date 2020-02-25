<?php
require('config/config.php');
require('lib/bdd.lib.php');


/** On prépare les variables qui sont nécessaire au layout.phtml
 * 
 * $vue : La vue qui sera incluse dans le layout pour cette page
 * $title : le titre de la page (balise title et balise h1)
 * $activeMenu : le menu qui sera actif, donc avec une classe 'active' pour mettre en surbrillance
 */
$vue='dashboard';
$title = 'Dashboard';
$activeMenu = 'home';

/** On essaie de se connecter et de faire notre requête
 * Principe des exception en programmation
 * Je vous expliquerai tout ça mais vous pouvez déjà lire ceci :
 * https://www.pierre-giraud.com/php-mysql/cours-complet/php-exceptions.php
 * http://php.net/manual/fr/language.exceptions.php
 * 
 */

try
{
    /** 1 : connexion au serveur de BDD - SGBDR 
     * Ici j'appelle une fonction pour éviter de répéter la connexion dans toutes mes pages
    */
    $dbh = connexion();

    /** 2 : Prépare ma requête SQL - 10 dernière commandes */
    $sth = $dbh->prepare('SELECT * FROM '.DB_PREFIXE.'orders o INNER JOIN '.DB_PREFIXE.'customers c ON (c.customerNumber = o.customerNumber) ORDER BY orderDate DESC LIMIT 1,10');
    $sth->execute();
    $orders = $sth->fetchAll(PDO::FETCH_ASSOC);

    /** 2 : Prépare ma requête SQL - Nombre de commande 
     * Je ne récupère ici que seule ligne de donnée (aggregation count sur toute la table). Donc on utilise fetch au lieu de fetchAll
    */
    $sth = $dbh->prepare('SELECT count(orderNumber) as nbOrders FROM '.DB_PREFIXE.'orders');
    $sth->execute();
    $nbOrders = $sth->fetch(PDO::FETCH_ASSOC);

    /** 2 : Prépare ma requête SQL - Nombre de clients */
    $sth = $dbh->prepare('SELECT count(customerNumber) as nbCustomers FROM '.DB_PREFIXE.'customers');
    $sth->execute();
    $nbCustomers = $sth->fetch(PDO::FETCH_ASSOC);

    /** 2 : Prépare ma requête SQL - 10 derniers clients */
    $sth = $dbh->prepare('SELECT * FROM '.DB_PREFIXE.'customers c INNER JOIN '.DB_PREFIXE.'employees e ON (e.employeeNumber = c.salesRepEmployeeNumber) ORDER BY customerNumber DESC LIMIT 1,10');
    $sth->execute();
    $customers = $sth->fetchAll(PDO::FETCH_ASSOC);
}
catch (PDOException $e)
{
    /** Si on a une erreur de connexion ou de requête PDO on modifie la vue pour la vue erreur.phtml
     * On définie une variable $messageErreur qui sera affichée dans la vue !
     */
    $vue = 'erreur';
    //Si une exception est envoyée par PDO (exemple : serveur de BDD innaccessible) on arrive ici
    $messageErreur =  'Une erreur de connexion a eu lieu :'.$e->getMessage();
}

/** On inclu le layout pour afficher les résultats 
 * Le layout est commun à toutes les pages. On y inclu dedans la vue, ce qui évite de découper le layout en header/footer 
 * et d'inclure à chaque fois ces éléments dans chaque vue. Beaucoup plus simple. En plus on peut changer de layout facilement (donc de mise en forme).
 * Essayez de remplacer layout par layout2 fans le fichier config ! Magique ;)
*/
require('tpl/'. LAYOUT .'.phtml');




