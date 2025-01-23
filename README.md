<div align="center">
    <h1>PHP Blog [study]</h1>
    <strong><i>ウェブプログラミングⅢの最終課題です</i></strong>
</div>

## Live Service / Demo

### Web

Check out the live service / demo and see for yourself [PHP-BLOG](https://blog.swqh.online)

### Docker

Docker is the easiest way. 

```yaml
# docker-compose.yml

version: "3.8"

services:
  apache:
    image: php:8.2-apache
    container_name: apache_server
    ports:
      - "80:80"
    volumes:
      - ./www:/var/www/html
      - ./apache-config:/etc/apache2/sites-enabled
    depends_on:
      - mysql
    restart: always

  mysql:
    image: mysql:8.0
    container_name: mysql_server
    ports:
      - "3306:3306"
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: test
      MYSQL_USER: test
      MYSQL_PASSWORD: test
    volumes:
      - db_data:/var/lib/mysql
      - ./init.sql:/docker-entrypoint-initdb.d/init.sql
    restart: always

  phpmyadmin:
    image: phpmyadmin/phpmyadmin
    container_name: phpmyadmin
    ports:
      - "8081:80"
    environment:
      PMA_HOST: mysql
      PMA_PORT: 3306
    depends_on:
      - mysql
    restart: always

volumes:
  db_data:

```
