# Hostinger Deployment — Riches Corsos

## Folder structure on the server

```
public_html/
├── index.php          ← copy from deployment/public_html/index.php
├── .htaccess          ← copy from deployment/public_html/.htaccess
└── riches-corsos/     ← upload the full Laravel project here
    ├── app/
    ├── bootstrap/
    ├── config/
    ├── database/
    ├── public/
    │   ├── index.php
    │   ├── .htaccess
    │   └── build/     ← run `npm run build` locally first
    ├── resources/
    ├── routes/
    ├── storage/
    ├── vendor/        ← run `composer install --no-dev` locally first
    └── .env           ← create on server (do NOT upload your local .env)
```

## Step-by-step

### 1. Build assets locally (on your machine)
```bash
npm run build
```

### 2. Install PHP dependencies locally for production
```bash
composer install --optimize-autoloader --no-dev
```

### 3. Upload files to Hostinger
- Upload the two files from `deployment/public_html/` into the root of `public_html/`
  - `public_html/index.php`
  - `public_html/.htaccess`
- Upload the entire Laravel project folder into `public_html/riches-corsos/`
  (you can skip: `node_modules/`, `.git/`, `deployment/`, `.env`)

### 4. Create .env on the server
Via Hostinger File Manager, create `public_html/riches-corsos/.env` with:
```
APP_NAME="Riches Corsos"
APP_ENV=production
APP_KEY=           ← generate below
APP_DEBUG=false
APP_URL=https://yourdomain.com

APP_TIMEZONE=America/Chicago

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_db_name
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=465
MAIL_USERNAME=your@email.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=your@email.com
MAIL_FROM_NAME="Riches Corsos"

GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=https://yourdomain.com/auth/google/callback
```

### 5. Run setup commands via Hostinger SSH (or Terminal in hPanel)
```bash
cd public_html/riches-corsos

# Generate app key
php artisan key:generate

# Run migrations
php artisan migrate --force

# Create storage symlink so uploaded images are web-accessible
php artisan storage:link

# Cache config/routes/views for performance
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 6. Create your admin user
```bash
php artisan tinker --execute 'App\Models\User::create(["name"=>"Admin","email"=>"you@email.com","password"=>bcrypt("yourpassword"),"role"=>"admin"]);'
```
Then log in at `https://yourdomain.com/admin`

### 7. Set storage folder permissions (if images won't save)
```bash
chmod -R 775 storage bootstrap/cache
```

## Replacing the old PHP/HTML site

Since your domain already points to `public_html/`, you are just replacing what's inside it.

- Delete (or back up) the old HTML/PHP/CSS/JS files from `public_html/`
- Upload the two new files (`index.php`, `.htaccess`) and the `riches-corsos/` folder
- The old site is gone, the Laravel app takes over immediately

## Notes

- If Hostinger gives you SSH access (Business plan or higher), all the `php artisan` commands above can be run directly. If not, use Hostinger's hPanel Terminal.
- The `deployment/` folder is local-only — do not upload it to the server.
- Never upload your local `.env` — always create a fresh one on the server.
