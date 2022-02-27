# BASE_BOULANGERIE
Site eboutique de base avec Symfony

1/ cloner le dépôt
2/ créer fichier .env
3/ définir les valeurs des variables suivantes:
   - NBRE_DE_JOUR_DECALAGE_RESERVATION=1   (si on est lundi, affichage rdv du mardi)
   - TAILLE_DE_RESERVATION_DANS_PERIODE=15   (proposition de rdv toutes les x min)
   - NBRE_DE_RESERVATION_MAX_PAR_PERIODE=15   (nbre de réservations par période)
   - STRIPE_KEY=  sk_  (votre clé stripe API)
   - STRIPE_NAME_BOUTIQUE_IN_CHECKOUTPAGE="Commande à la boulangerie"  (nom afficher sur la page de paiement de stripe)

4/ définir la connexion à la bdd
5/ faire un composer install
6/ inscription du super admin
7/ mettre ROLE_SUPER_ADMIN dans bdd
8/ définir les horaires de la Eboutique
9/ définir catégories et produits...