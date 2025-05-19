# Phone Catalog App

Dockerized web application for managing a phone catalog using Symfony (API Platform), Vue.js, and a Python-based scraper.

---

## Project Structure



---

## Requirements

- [Docker](https://docs.docker.com/get-docker/)
- [Docker Compose](https://docs.docker.com/compose/install/)

---

## Setup Instructions

1. **Start all services using Docker Compose:**
   ```bash
   docker-compose up -d
   ```

2. **Install PHP dependencies (inside the PHP container):**
   ```bash
   docker exec -it symfony_php composer install
   ```

3. **Run Symfony database migrations:**
   ```bash
   docker exec -it symfony_php composer install
   ```

4. **Run Scraper:**
   ```bash
   docker-compose run scraper scrape
   ```

## Access the Application
- Frontend (Vue.js SPA):
http://localhost:3000

- Backend (Symfony API Platform):
http://localhost:8000/api


