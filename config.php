<?php
SQL::setDB('');

Dir::addAlias('css', 'views/css/');
Dir::addAlias('js', 'views/js/');
Dir::addAlias('html', 'views/html/');
Dir::addAlias('templates', 'templates/');

View::setProjectName('Novo projeto');
View::setBaseHref('http://');
?>