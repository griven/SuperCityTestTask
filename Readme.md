# Тестовое задание на проект SuperCity

конфиг настроен на докер, но его можно поменять в StorageTest.php метод storageProvider

## Работа через docker
### запуск докер контейнеров
```
docker-compose up -d
```

### запуск тестов (если при клонировании не меняли папку SuperCity-PHP-test-task)
```
docker exec -it supercityphptesttask_php_1 php ./vendor/phpunit/phpunit/phpunit --configuration phpunit.xml
```

### запуск редис клиента
```
docker exec -it supercityphptesttask_redis_1 redis-cli
```

### запуск монго клиента
```
docker exec -it supercityphptesttask_mongo_1 mongo
```
