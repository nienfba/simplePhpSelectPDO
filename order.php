<?php
require('config/config.php');
require('lib/bdd.lib.php');

$vue='order/detail';
$title="Bon de commande";
$activeMenu='orders';
    
try
{
    /** On envoi une exception si l'id n'est pas passé dans la chaine de requête
     * Le reste des ligne du bloc try ne sera pas executé
     * On va directement au bloc catch
     */
    if(!array_key_exists('id',$_GET))
        throw new Exception('Tu fais quoi ici ?');

    $orderNumber = $_GET['id'];

    /** 1 : connexion au serveur de BDD - SGBDR */
    $dbh = connexion();


    /** PREMIERE REQUETE ON RECUPERE LES INFOS DU CLIENT **/
    /**2 : Prépare ma requête SQL */
    $sth = $dbh->prepare('SELECT * FROM  '.DB_PREFIXE.'customers c
     INNER JOIN  '.DB_PREFIXE.'orders o ON o.customerNumber = c.customerNumber
     WHERE orderNumber = :orderNum');
    /** 3 : executer la requête */
    $sth->bindValue('orderNum',$orderNumber,PDO::PARAM_INT);

    $sth->execute();
    /** 4 : recupérer les résultats -  On utilise FETCH car un seul résultat attendu */
    $customer = $sth->fetch(PDO::FETCH_ASSOC);

    /** Date de la commande au format DateTime */
    $orderDate = new DateTime($customer['orderDate']);


    /** DEUXIEME REQUETE ON RECUPERE LES INFOS DE LA COMMANDE et LES PRODUITS */
    /**2 : Prépare ma requête SQL */
    $sth = $dbh->prepare('SELECT
            p.productName,
            od.priceEach,
            od.quantityOrdered,
            (od.priceEach * od.quantityOrdered) AS totalPrice,
            o.orderDate
        FROM '.DB_PREFIXE.'orders o
        INNER JOIN  '.DB_PREFIXE.'orderdetails od ON o.orderNumber = od.orderNumber
        INNER JOIN  '.DB_PREFIXE.'products p ON p.productCode = od.productCode
        WHERE o.orderNumber = :orderNum
        ORDER BY od.orderLineNumber');
    $sth->bindValue('orderNum', $orderNumber, PDO::PARAM_INT);
    /** 3 : executer la requête */
    $sth->execute();
    /** 4 : recupérer les résultats */
    $orderDetails = $sth->fetchAll(PDO::FETCH_ASSOC);

    

    /** TROISIEME REQUETE ON CALCULE LE MONTANT TOTAL */
    $sth = $dbh->prepare('SELECT SUM(priceEach * quantityOrdered) AS totalAmount
    FROM '.DB_PREFIXE.'orderdetails
    WHERE orderNumber = :orderNum');
    $sth->bindValue('orderNum', $orderNumber, PDO::PARAM_INT);
    /** 3 : executer la requête */
    $sth->execute();
    /** 4 : recupérer les résultats */
    $res = $sth->fetch(PDO::FETCH_ASSOC);
    $orderTotalAmount = $res['totalAmount']; //variable contenant directement le montant total !
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
