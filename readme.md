# A technical test about a web crawler written in Lumen.

## Set up
1. `git clone git@github.com:geobas/web-crawler.git crawler`
2. Run `composer install`
3. Run `cp .env.example .env`
4. Create a database named 'crawler' in your development environment.
5. Run migrations.
6. Run `./artisan crawl:site https://example.com` from application's root folder.
