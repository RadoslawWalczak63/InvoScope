# InvoScope

A Dockerized Laravel environment with MySQL, Vite, and Traefik.

## Access

* **Localhost:** [http://invoscope.localhost](http://invoscope.localhost)
* **Production:** [https://invoscope.tech](http://invoscope.tech)

## Prerequisites

* Docker & Docker Compose.
* **Traefik** must be running and connected to an external network named `proxy`.

```bash
docker network create proxy
```

## Quick Setup

**1. Configure Environment**
Copy the example files and update `.docker/.env` with your current `UID`/`USERNAME` to avoid permission issues. In
`.env`, set `COPILOT_API_KEY`.

```bash
cp .docker/.env.example .docker/.env
cp .env.example .env
```

**2. Start Services**
Build and start the containers from the docker directory.

```bash
cd .docker && docker compose up -d
```

**3. Initialize Application**
Enter the web container to install dependencies and build assets.

```bash
docker compose exec web bash
```

Run the following commands inside the container:

```bash
composer install
npm install && npm run build
php artisan key:generate
php artisan migrate
```
