<?php
include('config/config.php');
include('lib/bdd.lib.php');

$vue='bc.phtml';
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

    $bind = array('orderNum'=>$orderNumber);

    /** 1 : connexion au serveur de BDD - SGBDR */
    $dbh = connexion();


    /** PREMIERE REQUETE ON RECUPERE LES INFOS DU CLIENT **/
    /**2 : Prépare ma requête SQL */
    $sth = $dbh->prepare('SELECT * FROM  '.DB_PREFIXE.'customers c
     INNER JOIN  '.DB_PREFIXE.'orders o ON o.customerNumber = c.customerNumber
     WHERE orderNumber = :orderNum');
    /** 3 : executer la requête 
     * Cette fois si on passe à execute un tableau associatif et on utilise un paramètre :orderNum dans la requête préparée.
     * Le tableau $bind est défini ligne 21 !
     * C'est encore une autre façon de faire ;)
    */
    //$sth->bindValue('orderNum',$orderNumber,PDO::PARAM_INT);

    $sth->execute($bind);
    /** 4 : recupérer les résultats 
     * On utilise FETCH car un seul résultat attendu
    */
    $customer = $sth->fetch(PDO::FETCH_ASSOC);

   //echo getColumnName('cm_customers',$dbh);

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
    /** 3 : executer la requête */
    $sth->execute($bind);
    /** 4 : recupérer les résultats */
    $orderDetails = $sth->fetchAll(PDO::FETCH_ASSOC);

    $orderDate = new DateTime($orderDetails['0']['orderDate']);

    /** TROISIEME REQUETE ON CALCULE LE MONTANT TOTAL */
    $sth = $dbh->prepare('SELECT SUM(priceEach * quantityOrdered) AS totalAmount
    FROM '.DB_PREFIXE.'orderdetails
    WHERE orderNumber = :orderNum');
    /** 3 : executer la requête */
    $sth->execute($bind);
    /** 4 : recupérer les résultats */
    $res = $sth->fetch(PDO::FETCH_ASSOC);
    $orderTotalAmount = $res['totalAmount']; //variable contenant directement le montant total !
}
catch(PDOException $e)
{
    $vue = 'erreur.phtml';
    //Si une exception est envoyée par PDO (exemple : serveur de BDD innaccessible) on arrive ici
    $messageErreur = 'Une erreur de connexion a eu lieu :'.$e->getMessage();
}
catch(Exception $e)
{
    $vue = 'erreur.phtml';
    //Si une exception est envoyée
    $messageErreur =  'Erreur dans la page :'.$e->getMessage();
}



include('tpl/layout.phtml');
