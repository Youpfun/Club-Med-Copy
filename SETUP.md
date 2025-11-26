# 🚀 Guide de Configuration Laravel - Développement Local

## 📋 Prérequis

Avant de commencer, assure-toi d'avoir installé :

- **PHP** (version 8.1 ou supérieure) - [Télécharger](https://www.php.net/downloads)
- **Composer** - [Télécharger](https://getcomposer.org/download/)
- **Node.js** et npm - [Télécharger](https://nodejs.org/)
- **MySQL** (optionnel) ou utilise SQLite

---

## ⚙️ Configuration initiale (À faire une seule fois)

### 1️⃣ Installation des dépendances

```powershell
# Installer les dépendances PHP
composer install

# Installer les dépendances JavaScript
npm install
```

### 2️⃣ Configuration du fichier .env

Si le fichier `.env` n'existe pas, copie le fichier exemple :

```powershell
Copy-Item .env.example .env
```

#### Option A : Utiliser SQLite (Recommandé pour débuter)

Modifie le fichier `.env` :
```env
DB_CONNECTION=sqlite
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=laravel
# DB_USERNAME=root
# DB_PASSWORD=
```

Puis crée le fichier de base de données :
```powershell
New-Item -Path "database\database.sqlite" -ItemType File
```

#### Option B : Utiliser MySQL

Modifie le fichier `.env` :
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nom_de_ta_base
DB_USERNAME=root
DB_PASSWORD=ton_mot_de_passe
```

### 3️⃣ Génération de la clé d'application

```powershell
php artisan key:generate
```

### 4️⃣ Création du lien de stockage

```powershell
php artisan storage:link
```

### 5️⃣ Migration de la base de données

```powershell
php artisan migrate
```

---

## 🎯 Lancer le serveur de développement

### Option 1 : Deux terminaux séparés (Recommandé)

**Terminal 1 - Serveur Laravel :**
```powershell
php artisan serve
```
✅ Accessible sur : `http://localhost:8000`

**Terminal 2 - Vite (assets CSS/JS) :**
```powershell
npm run dev
```
✅ Dev Server Vite : `http://localhost:5173`

### Option 2 : Un seul terminal avec jobs en arrière-plan

```powershell
# Lancer Laravel en arrière-plan
Start-Job -ScriptBlock { Set-Location $PWD; php artisan serve } -Name "Laravel"

# Lancer Vite en arrière-plan
Start-Job -ScriptBlock { Set-Location $PWD; npm run dev } -Name "Vite"

# Voir les logs
Receive-Job -Name "Laravel" -Keep
Receive-Job -Name "Vite" -Keep

# Arrêter les serveurs
Stop-Job -Name "Laravel","Vite"
Remove-Job -Name "Laravel","Vite"
```

---

## 📝 Commandes utiles

### Gestion de la base de données
```powershell
# Créer les tables
php artisan migrate

# Réinitialiser et recréer toutes les tables
php artisan migrate:fresh

# Réinitialiser + remplir avec des données de test
php artisan migrate:fresh --seed
```

### Cache et optimisation
```powershell
# Vider tous les caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimiser pour la production
php artisan optimize
```

### Création de composants
```powershell
# Créer un contrôleur
php artisan make:controller NomController

# Créer un modèle
php artisan make:model NomModele

# Créer une migration
php artisan make:migration create_nom_table

# Créer un modèle + migration + contrôleur
php artisan make:model NomModele -mc
```

---

## 🌐 Structure Laravel - Comment ça marche ?

### Routes (`routes/web.php`)
Définit les URLs de ton application
```php
Route::get('/', function () {
    return view('welcome');
});
```

### Contrôleurs (`app/Http/Controllers/`)
Contient la logique métier
```php
public function index() {
    return view('home');
}
```

### Vues (`resources/views/`)
Templates Blade (HTML + PHP)
```blade
<h1>{{ $titre }}</h1>
```

### Modèles (`app/Models/`)
Interagit avec la base de données
```php
$users = User::all();
```

---

## 🛠️ Résolution de problèmes

### Erreur : "No application encryption key has been specified"
```powershell
php artisan key:generate
```

### Erreur : "Class 'X' not found"
```powershell
composer dump-autoload
```

### Les CSS/JS ne se chargent pas
Vérifie que Vite est lancé :
```powershell
npm run dev
```

### Port 8000 déjà utilisé
Lance sur un autre port :
```powershell
php artisan serve --port=8080
```

---

## 📚 Ressources

- [Documentation Laravel](https://laravel.com/docs)
- [Laracasts - Tutoriels vidéo](https://laracasts.com)
- [Laravel Daily - Tips & Tricks](https://laraveldaily.com)

---

## ✅ Checklist de démarrage rapide

- [ ] `composer install`
- [ ] `npm install`
- [ ] Copier `.env.example` vers `.env`
- [ ] Configurer la base de données dans `.env`
- [ ] `php artisan key:generate`
- [ ] `php artisan migrate`
- [ ] `php artisan serve` (terminal 1)
- [ ] `npm run dev` (terminal 2)
- [ ] Ouvrir `http://localhost:8000`

**Bon développement ! 🎉**
