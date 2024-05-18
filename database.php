<?php

$setup = require_once 'setup.php';

try {
    
    $db = new PDO("mysql:host={$setup['host']};dbname={$setup['database']};charset=utf8",
        $setup['user'], $setup['password'], [
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                  ]);
    
} catch (PDOException $error) {
    
    echo $error->getMessage();
    exit('Database error');
    
    }
?>
    
    // linijka 3 - "zwróciliśmy" do tablicy o nazwie config nasze cztery poprzednio przygotowane, asocjasyjnie nazwane szufladki (localhost, root, '', newsletter)
    // linijka 7 - tworzymy nowy obiekt klasy PDO
    // linijka 9 - ustawienia atrybutów, zwiększa odporność na wstrzykiwanie sql
    // linijka 10 - wyjątek przerywa wykonanie skryptu i daje nam informacje
    // linjka 13 - wyjątek naszej bazy danych
