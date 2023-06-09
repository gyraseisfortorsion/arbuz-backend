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
* Модель юзера и его контроллер особо не трогал, насколько я понимаю это не входит в задание(?)
### Сложности

- Когда пытался подключить апи к айос приложению (для выполнения айос задания), JSONDecoder не мог нормально прочитать список продуктов в джсоне потому что поле weight было указано типа decimal, а он автоматом в джсоне конвертируется в стринг, чтобы решить это надо было просто заменить тип на float, его sql не конвертирует в стринг.
- Когда генерировал продукты и подписки через фабрики, хотел чтобы продукты генерировались из списка настоящих продуктов на русском языке, а не просто рандомный набор букв (его возвращает рандомазайр из библиотеки factory), однако это изначально не представлялось возможным поскольку концепция фабрики генерирует новый энтри независимо от прошлых энтри, иными словами если рандомизировать выбор из массива продуктов то продукты в таблице могут повторять, а некоторые даже не появляться. Чтобы решить это проблему я сначала создал 10 ранддомных энтрис за счет фабрики, а потом внутри сидера поменял имя каждого продукта в таблице на имена из массива (см файл ProductTableSeeder.php)
- Очень много думал об edge кейсах, учел прайс лимит для каждой индивидуальной подписки, учел что продукты могут измеряться в весе и количестве, учел конвертацию между весом и количеством, учел стоимость каждого продукта в корзине за счет умножения цены/кг или же цены/штука на вес/количество, учел что продукта может не существовать (возвращает ошибку 400 и сообщение о том что продукт кончился или его не существует), учел ситуацию когда общая стоимость корзины на подписку превышает ценовой лимит подписки, сделал так что при создании корзины на индивидуальную подписку, вес/колво продуктов в этой корзине отнимается от общего веса/количества в каждом продукте. Для этого у меня существует две таблицы, общая учетная таблица всех продуктов, так сказать их наличие на складе и вся информация о них, и таблица products_subsctiptions где каждый энтри привязан к какой то подписке через foreign key и указано колво/вес продукта в этой подписке.
- put запрос к подписке работает таким образом что обновляются только те поля что были предоставлены в реквесте, он не будет ругаться если все поля не будут предоставлены
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


