# Установка плагина на сайт

1. Загрузитие .zip файл на сайт
2. Активируйте плагин - InstasportCalendar
3. В настройках плагина добавьте ключи

slug: club slug

key: XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX

Если клубов несколько, то добавьте еще slug и key

4. На странице, где нужно разместить календарь добавьте код

Для первого клуба

[instasport-calendar]

Для второго клуба (если есть)

[instasport-calendar id=2]

# Кнопки

Шорткод кнопки авторизации/профиля

[instasport-button-profile]

Шорткод кнопки записи на пробную тренировку

[instasport-button-pilot]

Если на сайте несколько клубов можно указать id клуба к которому относится кнопка 

# Шаблоны

В плагине есть возможность переопределить шаблоны интерфейса, для этого нужно копировать небходимый шаблон с каталога wp-content/plugins/instasport-calendar/templates в каталог instasport вашей темы.

Например wp-content/plugins/instasport-calendar/templates/button_profile.php => wp-content/themes/ваша_тема/instasport/button_profile.php

- button_pilot.php - Кнопка записи на пробную тренировку
- button_profile.php - Кнопка авторизации/профиля

Шаблоны поддерживают интерполяцию, подробнее можно посмотреть здесь https://codex.wordpress.org/Javascript_Reference/wp.template
