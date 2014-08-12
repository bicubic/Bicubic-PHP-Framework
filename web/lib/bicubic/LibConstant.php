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
    public static $_STRINGLIST = "stringlist";
    public static $_SHORTLIST = "shortlist";
    public static $_LONG = "long";
    public static $_DOUBLE = "double";
    public static $_URL = "url";
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
    public static $_TIME = "time";
    public static $_BOOLEAN = "boolean";
    public static $_INTARRAY = "int-array";
    public static $_DOUBLEARRAY = "double-array";
    public static $_STRINGARRAY = "string-array";
    public static $_JSON = "json";
    public static $_RUT = "rut";
    public static $_FILE = "file";
    public static $_IMAGE256 = "image256";
	public static $_IMAGE512 = "image512";
	public static $_IMAGE1024 = "image1024";
    public static $_POSTGRESQLTYPES = array(
        "int"=>'integer',
        "list"=>'integer',
        "stringlist"=>'character varying(2)',
        "shortlist"=>'integer',
        "long"=>'bigint',
        "double"=>'double precision',
        "url"=>'character varying(1024)',
        "email"=>'character varying(256)',
        "password"=>'character varying(1024)',
        "string"=>'character varying',
        "string2048"=>'character varying(2048)',
        "string1024"=>'character varying(1024)',
        "string512"=>'character varying(512)',
        "string256"=>'character varying(256)',
        "string128"=>'character varying(128)',
        "string64"=>'character varying(64)',
        "string32"=>'character varying(32)',
        "string16"=>'character varying(16)',
        "string8"=>'character varying(8)',
        "string4"=>'character varying(4)',
        "string2"=>'character varying(2)',
        "string1"=>'character varying(1)',
        "flat"=>'character varying',
        "letters"=>'character varying',
        "alphanumeric"=>'character varying',
        "date"=>'bigint',
        "time"=>'bigint',
        "boolean"=>'integer',
        "int-array"=>'character varying',
        "double-array"=>'character varying',
        "string-array"=>'character varying',
        "json"=>'character varying',
        "rut"=>'character varying',
        "file"=>'character varying(1024)',
        "image256"=>'character varying(1024)',
		"image512"=>'character varying(1024)',
		"image1024"=>'character varying(1024)',
    );

}

class Gender {

    public static $_NONE = 1;
    public static $_MALE = 2;
    public static $_FEMALE = 3;
    public static $_ENUM = array(
        1=>'lang_none',
        2=>'lang_male',
        3=>'lang_female'
    );

}

class ObjectBoolean {

    public static $_NO = 0;
    public static $_YES = 1;
    public static $_ENUM = array(
        0=>'lang_no',
        1=>'lang_yes',
    );

}

class ObjectOrder {

    public static $_ASC = 1;
    public static $_DESC = 2;
    public static $_ENUM = array(
        1=>'lang_asc',
        2=>'lang_desc',
    );
    public static $_VALUE = array(
        1=>'ASC',
        2=>'DESC',
    );
    public static $_OPOSITE = array(
        1=>2,
        2=>1,
    );

}



