
# CV Nexis - AI-Powered HR Management System

CV Nexis is an intelligent Human Resource Management (HRM) application designed to streamline the candidate evaluation process through advanced AI-powered CV analysis. The platform empowers HR professionals and recruiters to efficiently manage candidate data by automating the extraction and categorization of key information from resumes.

## Contact details & History

It started when I saw the software my wife’s company was using. I knew I could do better

— so I did. Meet a faster, smarter way to work.

If you have a commercial request, you can contact me via e-mail remdex@gmail.com, my other profiles

* Telegram: https://t.me/remdex
* Discord: https://discord.com/users/711446833066541066
* Microsoft Teams: https://teams.microsoft.com/l/chat/0/0?users=remdex@gmail.com
* WhatsApp: https://wa.me/37065272274

## Demo

* https://hrm.qu.lt/
* Login: remdex@gmail.com
* Pass: admin

## Youtube demo

* English language demo - https://youtu.be/uUdog-MFqyM
* Lithuanian language demo - https://youtu.be/NXm7gltWOW0

### Additional information

* Demo is reset every hour
* You can try to download few sample CV from https://www.resumeviking.com/templates/ It's the ones I tried.
* AI Processing document takes from 5-15 seconds. So wait till a circle stop spinning.

Demo video under way...

## Key Features

### 🤖 AI-Powered CV Processing
- **Automated CV Import**: Seamlessly upload and process candidate CVs in various formats
- **Intelligent Attribute Extraction**: AI algorithms automatically identify and extract relevant candidate information
- **Smart Categorization**: Candidates are automatically assigned appropriate specialities and competencies based on their CV content

### 👥 Comprehensive Candidate Management
- **Candidate Profiles**: Detailed candidate records with extracted skills, experience, and qualifications
- **Competence Mapping**: Systematic tracking of candidate competencies and skill levels
- **Speciality Classification**: Organized categorization of candidates by their professional domains
- **Comment System**: Collaborative notes and feedback on candidate evaluations

### 📊 Advanced Analytics & Insights
- **Candidate Analytics**: Data-driven insights into candidate pool characteristics
- **Competence Analytics**: Track and analyze skill distributions across your candidate database
- **Reporting Dashboard**: Comprehensive reporting tools for recruitment metrics

### 🏢 Multi-Company Support
- **Company Management**: Support for multiple companies and organizations
- **Document Management**: Organized storage and retrieval of candidate documents
- **User Role Management**: Granular permissions and access control

## Technology Stack

- **Backend**: Laravel PHP Framework
- **Frontend**: Modern web interface with Vite build system
- **Admin Panel**: Laravel Orchid for administrative interface
- **Database**: Robust database layer with comprehensive migrations
- **AI Integration**: Advanced machine learning algorithms for CV processing
- **Testing**: Comprehensive PHPUnit test suite

## Getting Started

### Prerequisites
- PHP 8.1 or higher
- Composer
- Node.js and npm
- MySQL/PostgreSQL database

### Installation

1. Clone the repository
```bash
git clone https://github.com/remdex/CV-Nexis.git
cd civis-hrm
```

2. Install PHP dependencies
```bash
composer install
```

3. Install Node.js dependencies
```bash
npm install
```

4. Set up environment configuration
```bash
cp .env.example .env
php artisan key:generate
```

5. Configure your environment settings in `.env`

   After copying from `.env.example`, update the following key configurations:

   **AI Configuration (Required for CV Processing):**
   ```bash
   GEMINI_MODEL=gemini-3-flash-preview     # Add your preferred Gemini model
   GEMINI_API_KEY=your_gemini_api_key      # Add your Google Gemini API key
   ```

   **Application Settings:**
   ```bash
   APP_NAME=CIVIS                    # Change from "Laravel" to "CIVIS"
   APP_ENV=local                     # Keep as "local" for development
   APP_URL=http://localhost:8000     # Update to match your local server URL
   ```

   **Database Configuration:**
   ```bash

   # SQL for creating database `CREATE DATABASE `hrm_fresh` COLLATE 'utf8mb4_unicode_ci'`;
   # For MySQL/PostgreSQL, uncomment and configure:
   DB_CONNECTION=mysql               # or "pgsql" for PostgreSQL
   DB_HOST=127.0.0.1
   DB_PORT=3306                     # 5432 for PostgreSQL
   DB_DATABASE=civis_hrm            # Change from "laravel" to your database name
   DB_USERNAME=your_username        # Change from "root" to your DB username
   DB_PASSWORD=your_password        # Add your database password
   ```

   **Mail Configuration (for notifications):**
   ```bash
   MAIL_MAILER=smtp                 # Change from "log" for production
   MAIL_HOST=your.smtp.host
   MAIL_PORT=587
   MAIL_USERNAME=your@email.com
   MAIL_PASSWORD=your_email_password
   MAIL_FROM_ADDRESS=noreply@yourcompany.com  # Change from "hello@example.com"
   MAIL_FROM_NAME="CIVIS HRM"       # Change from "${APP_NAME}"
   ```

6. Run database migrations

```bash
php artisan migrate
```

Prefill basic Competences and Specialities

```bash
php artisan db:seed
```

7. Change storage folder permissions to be able to write

8. Build frontend assets
```bash
npm run build
# Or via docker
docker run --rm -v "$(pwd)":/app -w /app node:22 npm install
docker run --rm -v "$(pwd)":/app -w /app node:22 npm run build
```

9. Create an admin user

```bash
php artisan orchid:admin
```

9. Start the development server or just setup virtual host.

```bash
php artisan serve
```

## Usefull commands

Import companies activities from JSON file. This file is dedicated for Lithuanian statistic department.

> php artisan import:activities <path/to/file.json>

> Source https://get.data.gov.lt/datasets/gov/lsd/cl/evrk/EkonominesVeiklosRusis I downloaded JSON

Import companies. This file is dedicated to the Lithuanian statistics department.

> php artisan import:companies <path/to/file.csv>

> Source https://get.data.gov.lt/datasets/gov/vmi/mm_registras/MokesciuMoketojas I downloaded as CSV

Cleanup orphan files via daily cronjob

>  php artisan attachment:clear

## Usage

### Importing CVs
1. Navigate to the candidate management section
2. Use the CV import feature to upload candidate resumes
3. The AI system will automatically process the documents
4. Review and verify the extracted candidate attributes
5. Save the processed candidate profile to your database

### Managing Candidates
- View and edit candidate profiles
- Add comments and evaluation notes
- Assign competencies and specialities
- Track candidate progress through your recruitment pipeline

### Analytics & Reporting
- Access the dashboard for recruitment insights
- Generate reports on candidate statistics
- Analyze competency distributions
- Track recruitment metrics and KPIs

## Contributing

We welcome contributions to CIVIS! Please read our contributing guidelines and submit pull requests for any improvements.

## License

This project is licensed under the [MIT License](LICENSE).

## Support

For support and questions, please contact our development team or create an issue in the repository.

## How to prepare your own Specialities and Skills

Here is a query you can feed to https://aistudio.google.com/ or any other ai also attach like ten or more CV to extract those details.

```
Based on those resumes prepare me list of skills/Competences, and Specialities/Positions. Max two words per single item. I'm building application where to CV should be assigend `Competences` and Specialities (Positions) where person has worked.

Skill E.g MS Word, Driving License
Specialities E.g Driver-Expediter

I need SQL for those table structure. Prepare 4 inserts, one for english language and one for lithuanian language

CREATE TABLE `competences` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci

CREATE TABLE `specialities` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```




