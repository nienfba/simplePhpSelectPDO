
# ClassicModel - Lecture d'une base de données en PHP
**Correction de Fabien**

Vous y trouverez l'accès aux commandes, clients, employées, bon de commandes et fiche client, produits et un formulaire de recherche.

Il y a aussi une gestion minimaliste d'une thème (tpl/layout.phtml et tpl/layout2.phtml)

# Pour vous entrainer vous pouvez faire différent accès :
L'affichage des bureaux, et/ou l'affichage des bureaux avec les employés associés.
Une balance des paiements sur la fiche client.

# Ajout d'un élément dans la base

Si vous voulez vous pouvez vous entrainer à l'ajout d'un élément par exemple un client.
Pour se faire il faut créer une page addclient.php.
Cette page affiche un formulaire de saisie pour tous les champs.
Le fomrmuliare est posté sur la même page, dont il y a un test sur cette page, si le formuliare est posté on enregitre sinon on affiche le formulaire.
L'enregistrement s'effectue avec une requête INSERT INTO customers (n1,n2...) VALUES (v1,v2,...)
Ou n1, n2, sont les noms des champs et v1,v2 sont les valeurs à y insérer.
Vous pouvez voir une requête INSERT complète dans phpMyAdmin en vous positionnant sur la table customers puis en cliquant sur Insérez. Remplissez les champs puis validez, la requête est alors affichée !

Algorithme de la page :

    Si le formulaire est posté
    {
    	Récupération des données du formulaire ($_POST)
    	Vérification des données (non vide)
    	Connexion au serveur de bdd
    	Préparation de la requête d'ajout
    	Execution de la requête
    	Redirection sur la page des la liste des clients header('Location:clients.php');
    }
    sinon
    {
    	Connexion au serveur de bdd
    	Préparation de la requête pour récupérer les employés
    	Exécution de la requête
    	Récupération de tous les employés 
    		(on pourra construire une liste déroulante SELECT>OPTION !)
    	vue <- 'formulaireAjout'
    	affichage layout
    }

# Prochaine étape et réflexion :
Comment gérer l'application de façon plus optimisée.
Ici on a une gestion de la connexion sur chaque page PHP par exemple avec la gestion des exceptions.
Ne peut-on pas réfléchir à une structure qui nous permet de mieux gérer tout ça ?
Et l'affichage des données qui est parfois redondante dans les vues ? On peut voir un exemple de simplification dans le page des résultats de recherche ?
A vos propositions. On en reparle !
