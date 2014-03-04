<?php

/**
 * Bicubic PHP Framework
 *
 * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
 * @copyright  2011-2014 Bicubic Technology - http://www.bicubic.cl
 * @license    MIT
 * @version 3.0.0
 */
class PropertyTypes {

    public static $_INT = "int";
    public static $_LIST = "list";
    public static $_SHORTLIST = "shortlist";
    public static $_LONG = "long";
    public static $_DOUBLE = "double";
    public static $_EMAIL = "email";
    public static $_PASSWORD = "password";
    public static $_STRING = "string";
    public static $_STRING2048 = "string2048";
    public static $_STRING1024 = "string1024";
    public static $_STRING512 = "string512";
    public static $_STRING256 = "string256";
    public static $_STRING128 = "string128";
    public static $_STRING64 = "string64";
    public static $_STRING32 = "string32";
    public static $_STRING24 = "string24";
    public static $_STRING16 = "string16";
    public static $_STRING8 = "string8";
    public static $_STRING4 = "string4";
    public static $_STRING2 = "string2";
    public static $_STRING1 = "string1";
    public static $_FLAT = "flat";
    public static $_LETTERS = "letters";
    public static $_ALPHANUMERIC = "alphanumeric";
    public static $_DATE = "date";
    public static $_BOOLEAN = "boolean";
    public static $_INTARRAY = "int-array";
    public static $_DOUBLEARRAY = "double-array";
    public static $_STRINGARRAY = "string-array";
    public static $_JSON = "json";
    public static $_ENUM = array(
        1 => "lang_item1",
        2 => "lang_item2",
        3 => "lang_item3",
    );

}

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