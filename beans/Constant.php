<?php 



class CurrencyKeys {

    public static $_ENUM = array(
        1 => "CLP",
        2 => "NZD",
        3 => "USD",
        4 => "AUD",
        5 => "BRL",
        6 => "MXN",
        7 => "ARS",
        8 => "COP",
        9 => "PEN",
    );
}

class CurrencyCountry {

    public static $_ENUM = array(
        1 => "Chile",
        2 => "New Zealand",
        3 => "United States",
        4 => "Australia",
        5 => "Brazil",
        6 => "Mexico",
        7 => "Argentina",
        8 => "Colombia",
        9 => "Perú",
    );

}

class CurrencySimbols {

    public static $_ENUM = array(
        "CLP" => "$",
        "NZD" => "$",
        "USD" => "$",
        "AUD" => "$",
        "BRL" => "R$",
        "MXN" => "$",
        "ARS" => "$",
        "COP" => "$",
        "PEN" => "$",
    );
}

class CurrencyLang {

    public static $_ENUM = array(
        "CLP" => "es",
        "NZD" => "en",
        "USD" => "en",
        "AUD" => "en",
        "BRL" => "en",
        "MXN" => "es",
        "ARS" => "es",
        "COP" => "es",
        "PEN" => "es",
    );

}

class Lang {

    public static $_ENUM = array(
        "es" => "Español",
        "en" => "English",
    );
    
    public static $_DEFAULT = "en";

}



class Gender {

    public static $_NONE = 1;
    public static $_MALE = 2;
    public static $_FEMALE = 3;
    public static $_ENUM = array(
        1 => "lang_none",
        2 => "lang_male",
        3 => "lang_female"
    );

}


class ObjectBoolean {

    public static $_NO = 0;
    public static $_YES = 1;
    public static $_ENUM = array(
        0 => "lang_no",
        1 => "lang_yes",
    );

}

class ExampleList {

    public static $_ENUM = array(
        1 => "lang_item1",
        2 => "lang_item2",
        3 => "lang_item3",
    );

}



?>
