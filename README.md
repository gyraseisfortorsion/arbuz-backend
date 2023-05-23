## О задание

### Framework: 
Laravel

### Database: 
MySQL (Hosted on AWS RDS)

### Пояснения
Я так же использвал сидеры и фабрики, поэтому в таблицах автоматически генерируются юзеры, продукты, и подписки, однако сгенерированный подписки создаются засчет queries и не проходят через мои апи контроллеры поэтому могут создаваться подписки которые не следуют моим критериям в апи. Чтобы проверить это лучше прописывать кастомные post реквесты

Модели:

- User
- Product
- Subscription
- ProductSubscription (Концепция выбранных продуктов в индивидуальной подписке, однако не создает отдельную таблицу продуктов для каждой подписке а просто фильтрует общую таблицу по FK)

Все важные файлы:
- app/Http/Contollers
- app/Models
- routes/api.php
- database/factories
- database/migrations
- database/seeders
- .env (скрытый)

Остальные это автоматически сгенерированный бойлерплейт ларавел, я там ничего не менял, проверяйте только указанные пути
## Установка


- Склонируйте репо
- Пропишите внутри папки проекта: 
```
$ php artisan serve
```
- В случае если захотите ресетнуть бд, пропишите, после этого вам возможно придется поменять запросы в пост коллекции так как продукты будут заново сгенерированы:

```
$ php artisan  migrate:fresh --seed
```

- В случае если хостинг бд по какой то причине накроется, убедительная просьба захостить бд локально и поменять параметры в .env файле.

## POSTMAN 

Ссылка:
https://winter-water-675810.postman.co/workspace/Team-Workspace~a7be05f9-cd54-48bf-8215-00430f710a2e/collection/27334327-5f021837-6d8e-49af-9cf6-c6825a890502?action=share&creator=27334327 

Кнопка(может не работать):

[![Run in Postman](https://run.pstmn.io/button.svg)](https://app.getpostman.com/run-collection/27334327-5f021837-6d8e-49af-9cf6-c6825a890502?action=collection%2Ffork&source=rip_markdown&collection-url=entityId%3D27334327-5f021837-6d8e-49af-9cf6-c6825a890502%26entityType%3Dcollection%26workspaceId%3Da7be05f9-cd54-48bf-8215-00430f710a2e#?env%5BNew%20Environment%5D=W10=)


