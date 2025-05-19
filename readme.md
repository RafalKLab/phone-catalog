# Phone Catalog App

Dockerized web application for managing a phone catalog using Symfony (API Platform), Vue.js, and a Python-based scraper.

---

## Project Structure

- `backend/` - Symfony application
- `frontend/` - Vue.js SPA
- `scraper/` - Python-based scraper
- `nginx` - Nginx server configuration
- `.env` - Global environment variables

---

## Requirements

- [Docker](https://docs.docker.com/get-docker/)
- [Docker Compose](https://docs.docker.com/compose/install/)

---

## Setup Instructions

1. **Clone the repository:**
    ```bash
    git clone https://github.com/RafalKLab/phone-catalog.git
    ```

2. Navigate to root folder
    ```bash
    cd phone-catalog
    ```

3. **Start all services using Docker Compose:**
   ```bash
   docker-compose up -d
   ```

4. **Install PHP dependencies (inside the PHP container):**
   ```bash
   docker exec -it symfony_php composer install
   ```

5. **Run Symfony database migrations:**
   ```bash
   docker exec -it symfony_php composer install
   ```

6. **Run Scraper:**
   ```bash
   docker-compose run scraper scrape
   ```

## Access the Application
- Frontend (Vue.js SPA):
http://localhost:3000

- Backend (Symfony API Platform):
http://localhost:8000/api


