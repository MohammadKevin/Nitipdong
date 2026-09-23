<div align="center">

# Nitipdong

<p>Jasa Titip (Jastip) Delivery & Shopping Request Platform</p>

![Status](https://img.shields.io/badge/Status-Active-success?style=flat-square)
![License](https://img.shields.io/badge/License-MIT-green?style=flat-square)
![Laravel](https://img.shields.io/badge/Laravel-blue?style=flat-square) ![Blade](https://img.shields.io/badge/Blade-blue?style=flat-square) ![PHP](https://img.shields.io/badge/PHP-blue?style=flat-square) ![Android APK CI/CD](https://img.shields.io/badge/Android%20APK%20CI%2FCD-blue?style=flat-square) ![MySQL](https://img.shields.io/badge/MySQL-blue?style=flat-square)

</div>

---

## Overview
A cross-region shopping assistance (jastip) web platform built with Laravel Blade and Android APK build integration to handle item requests and delivery tracking.

---

## Key Features
- Customer shopping request posting with budget estimations
- Traveler trip registration and accepted item list
- Automated Android APK build workflow via GitHub Actions

---

## Tech Stack
- **Framework**: Laravel (PHP)
- **View**: Blade Templating, Tailwind CSS
- **Database**: MySQL
- **CI/CD**: GitHub Actions (APK build)

---

## Project Structure
```text
Nitipdong/
├── app/                # Laravel Controllers & Models
├── resources/views/    # Blade templates
├── routes/             # Web & API routes
└── .github/workflows/  # Build APK workflow
```

---

## Getting Started

### Prerequisites
Make sure you have the required runtimes and tools installed on your machine:
- Node.js (v18+ recommended) / Appropriate runtime
- Git

### Installation & Local Setup
```bash
git clone https://github.com/MohammadKevin/Nitipdong.git
cd Nitipdong
composer install
cp .env.example .env && php artisan key:generate
php artisan migrate
php artisan serve
```

---

## Author
**Mohammad Kevin Arif Rudianto**
- **GitHub:** [@MohammadKevin](https://github.com/MohammadKevin)
- **Portfolio:** [portfolio-mohammadkevin.vercel.app](https://portfolio-mohammadkevin.vercel.app)
- **LinkedIn:** [Mohammad Kevin](https://www.linkedin.com/in/mohammad-kevin-arif-rudianto-945733347)
- **Email:** [kvn4.200581@gmail.com](mailto:kvn4.200581@gmail.com)

---

## License
This project is licensed under the [MIT License](https://opensource.org/licenses/MIT).

<div align="center">
If you found this repository useful, please consider giving it a star!
</div>
