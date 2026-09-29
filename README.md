# NDA EMPIRE by Niomba

Site du salon (perruques, poses, tresses, cils) à Kigali : réservations en ligne, catalogue de perruques avec cabine d'essayage IA, comptes clientes, notifications, espace de gestion.

Laravel 13, Blade, CSS pur (`public/css/app.css`), sans étape de build JS. Hébergé sur Railway, déployé à chaque push sur `main`.

## Ce que fait le site

- **Clientes** : compte (nom, téléphone, email), réservation d'un créneau libre (une seule chaise, horaires dans `config/salon.php`), annulation, commandes de perruques payées au retrait, historique, notifications (cloche dans le site + email).
- **Cabine d'essayage** (`/perruques`) : la cliente ajoute une photo d'elle (privée), touche « Essayer » sur une perruque, et Google Gemini génère la photo d'elle avec la perruque (`app/Services/WigTryOn.php`). Limite de `TRYONS_PER_DAY` essais par jour et par cliente, car chaque essai est facturé par Google.
- **Gestion** (`/admin`, réservée à Niomba) : demandes à confirmer, agenda des 14 prochains jours, clôture des rendez-vous (venue ou absente, ce qui compte les visites), commandes, fiches clientes (date d'inscription, nombre de visites, dernière visite, commandes, essais), prestations et prix, perruques (vidéo et photo ; la photo peut être prise automatiquement dans la vidéo), réalisations.

## Déploiement Railway

Services dans un même projet Railway, tous branchés sur ce dépôt :

| Service | Rôle | Réglages |
|---|---|---|
| `web` | le site | volume monté sur `/app/storage/app` (photos, vidéos, selfies) ; commande de pré-déploiement `php artisan migrate --force && php artisan app:admin` |
| `scheduler` | rappels la veille des rendez-vous | même dépôt et mêmes variables, commande de démarrage `php artisan schedule:work` |
| `MySQL` | base de données | modèle MySQL de Railway |

Variables (service `web` et `scheduler`) :

```
APP_NAME="NDA EMPIRE"
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:...          # php artisan key:generate --show
APP_URL=https://<domaine>
APP_LOCALE=fr
DB_CONNECTION=mysql
DB_URL=${{MySQL.MYSQL_URL}}
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=sync
LOG_CHANNEL=stderr
ADMIN_EMAIL=...             # compte de Niomba, créé au premier déploiement
ADMIN_PASSWORD=...          # 10 caractères minimum ; ne sert qu'à la création
GEMINI_API_KEY=...          # https://aistudio.google.com/apikey
MAIL_MAILER=smtp            # sinon les emails ne partent pas (les notifications dans le site marchent quand même)
MAIL_HOST=... MAIL_PORT=... MAIL_USERNAME=... MAIL_PASSWORD=... MAIL_FROM_ADDRESS=...
SALON_INSTAGRAM=https://instagram.com/...   # facultatif
SALON_TIKTOK=https://tiktok.com/@...        # facultatif
```

## En local

```bash
composer install && cp .env.example .env && php artisan key:generate
php artisan migrate && php artisan storage:link
php artisan serve
php artisan test          # base MySQL `nda_empire_test`
```
