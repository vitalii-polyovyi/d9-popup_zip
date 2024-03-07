dependecies:
 https://www.drupal.org/project/phone_international   
composer require oomphinc/composer-installers-extender
hack for working
in file phone_international.module
70: function _phone_international_get_path() {
    +return 'web/libraries/intl-tel-input/build';

--------------------------------------------------
telegram
https://www.drupal.org/project/drupal_telegram_sdk/releases/1.0.0-alpha3
$ composer require 'drupal/drupal_telegram_sdk:^1.0@alpha'