
--- Simplecatalog ---

http://psnet.lookformp3.net/
http://livestreet.ru/profile/PSNet/
https://catalog.livestreetcms.com/profile/PSNet/
http://livestreetguide.com/developer/PSNet/


--- Доработка фронтенда плагина ---

В этот каталог можно добавить свои кастомные ксс и жс файлы, которые не будут затираться при обновлении плагина:

  /templates/skin/_custom/style.css
  /templates/skin/_custom/script.js

Чтобы включить поддержку кастомных ксс и жс файлов нужно в конфиге плагина /config/general.php включить параметр:

  $config['general']['assets']['enable_custom_css_and_js_files'] = true;



--- Адаптация шаблонов ---

Также для шаблонов можно делать адаптации - см. файл:

  /templates/skin/_adaptations/readme.txt

для подробностей.
