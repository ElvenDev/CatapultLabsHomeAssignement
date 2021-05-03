# Requirements

For the application to work, you need to have installed [docker](https://www.docker.com/). That's it. Everything else is done for you.

# Installation

1. Install docker.
2. Clone repository/files to your preferred location.
3. Run `docker-compose up` in application root (INSTALL-DIR) folder.

## Locations:
- Project itself: http://localhost:8070/ test:Test123
- Adminer: http://localhost:8071/ username:password:database
- Reporting API endpoint: http://localhost:8070/report
    - POST only, 
    - Example request: [{"timestamp": 1619973003, "value": 33, "company": 1}]
    
## Notes:
- All the example data (fixtures) will be generated for you when you run `docker-compose up`

## In case of not working:

There is a chance that docker web will set up faster than database. In that case, migrations and fixtures will fail. You can run manually these commands inside container:
- `composer install -n`
- `bin/console doc:mig:mig --no-interaction`
- `bin/console doctrine:fixtures:load --no-interaction`
Other option is rerunning the container by this command:
- `docker-compose build web ; docker-compose up -d`