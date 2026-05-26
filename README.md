# 🎾 Padel App — Symfony Edition

Symfony 7.4 rewrite of our padel booking project.



## Setup (first time)

```bash
# 1. Clone the repo
git clone [<repo-url>](https://github.com/alifeki21/PadelAppV2.git)
cd padelappv2

# 2. Install dependencies
composer install

# 3. Copy the env file
cp .env .env.local
```

Then **edit `.env.local`** and update the `DATABASE_URL` line with your local MySQL credentials. If you use XAMPP with the default settings (user `root`, no password, port 3306):

```
DATABASE_URL="mysql://root:@127.0.0.1:3306/padelappv2?serverVersion=10.4.32-MariaDB&charset=utf8mb4"
```

> If your XAMPP runs MySQL on port 3307, change `3306` to `3307`.

```bash
# 4. Start MySQLserver

# 5. Create the database
php bin/console doctrine:database:create

# 6. Run migrations (this creates all the tables)
php bin/console doctrine:migrations:migrate

# 7. Start the Symfony dev server
symfony server:start
```

Open **https://127.0.0.1:8000** — you should see the home page. ✅

---

## Daily workflow

Every time you start working:

```bash
git pull origin main                          # get latest code
composer install                              # in case dependencies changed
php bin/console doctrine:migrations:migrate   # in case someone added entities
symfony server:start                          # start working
```

---




**Branch naming:** `feature/urName-<short-description>`
Example: `feature/ali-reservation-system`

**Never push directly to `main`.** Always open a Pull Request. dont merge yourself , i will accept PRs later.

---

## Useful commands

```bash
# Clear cache if something feels weird
php bin/console cache:clear

# See all routes
php bin/console debug:router

# Drop and recreate the DB (DEV ONLY — destroys all data)
php bin/console doctrine:database:drop --force
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

---

## Project structure (the important parts)

```
src/Controller/   ← your page controllers
src/Entity/       ← database models (already created)
src/Form/         ← form classes (you create these)
templates/        ← Twig templates (HTML versions of pages)
public/           ← CSS, JS, images
migrations/       ← DB migration files (already created)
.env.local        ← YOUR local config (not committed)
```

---

