
--- Simplecatalog ---

http://psnet.lookformp3.net/
http://livestreet.ru/profile/PSNet/
https://catalog.livestreetcms.com/profile/PSNet/
http://livestreetguide.com/developer/PSNet/


--- Адаптация шаблонов ---

Если шаблон указан в файле config/general.php в параметре:

  /*
   * Коды шаблонов, для которых нужно дополнительно подгружать специальные ксс файлы
   */
  $config['general']['assets']['load_special_assets_for_skins'] = array(
    'developer-kit',
  );


То для такого шаблона нужно добавить папку (в этой директории) с именем шаблона и все ксс файлы из неё будут автоматически подключены плагином.
Если папки с именем шаблона не будет, то будут подключены файлы из папки "default".
