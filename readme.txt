=== Instasport Calendar ===
Contributors: Instasport
Tags:
Requires at least: 4.8
Tested up to: 5.6.1
Stable tag:  2.1.10
Requires PHP: 5.6.20
License: GPLv2 or later
License URI: https://gnu.org/licenses/gpl-2.0.html

== Description ==
The Instasport Calendar plugin allows you to display the training schedule on the sport club website

== Installation ==
Для установки плагина на сайт

1. Загрузитие .zip файл на сайт
2. Активируйте плагин - InstasportCalendar
3. В настройках плагина добавьте ключи

slug: club slug
key: XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX

Если клубов несколько, то добавьте еще slug и key

На странице, где нужно разместить календарь добавьте код
Для первого клуба

[instasport-calendar]

Для второго клуба (если есть)

[instasport-calendar id=2]

4. Добавьте кнопку авторизации

Шорткод кнопки авторизации/профиля

[instasport-button-profile]

5. Добавьте кнопку записи на пробную тренировку

Шорткод кнопки записи на пробную тренировку

[instasport-button-pilot]

Если на сайте несколько клубов можно указать id клуба к которому относится кнопка

6. Переопределите шаблоны интерфейса

В плагине есть возможность переопределить шаблоны интерфейса, для этого нужно копировать небходимый шаблон с каталога wp-content/plugins/instasport-calendar/templates в каталог instasport вашей темы.

Например wp-content/plugins/instasport-calendar/templates/button_profile.php => wp-content/themes/ваша_тема/instasport/button_profile.php

button_pilot.php - Кнопка записи на пробную тренировку
button_profile.php - Кнопка авторизации/профиля
Шаблоны поддерживают интерполяцию, подробнее можно посмотреть здесь https://codex.wordpress.org/Javascript_Reference/wp.template

== Changelog ==
= v2.1.0 =
* Initial release.
= v2.1.1 =
* Bugs fixed.
= v2.1.2 =
* Installation updated.
= v2.1.3 =
* Zone support added.
= v2.1.4 =
* Bugs fixed.
= v2.1.5 =
* Mobile schedule redesigned
= v2.1.6 =
* Bugs fixed.
= v2.1.7 =
* Bugs fixed.
= v2.1.8 =
* Bugs fixed.
= v2.1.9 =
* Bugs fixed.
= v2.1.10 =
* API url.
