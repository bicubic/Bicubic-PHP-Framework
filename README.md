Bicubic-PHP-Framework
=====================

A simple and efficient MVC framework written in PHP for PostgreSQL, or similar DB.

Arquitecture
-------
![MVC](/doc/framework.jpeg)

Directory Structure
-------
- app: Application classes
- assets: javascript, css, and images files
- beans: DataObject classes
- config: config files
- data: TransationManager classes
- int: interoperability classes
- lang: language files
- lib: library files
- nav: Navigation classes
- templates: templates files for applications
- views: template files for navigations

Beans params
-------
- name: name of field
- type: type of field, a value from PropertyTypes
- require: indicates if the field is required for complete purpuses
- default: indicates a default value in case of null
- serializable: indicates that the property is going to be stored
- index: indicates that a btree index needs to be created for this field
- reference: indicates the name of another bean this property is referencing to.
- updatenull: if true, a null value on the field should override any existing value, if false, the stored value should not be changed.
- hidden: if true, this field should not be shown in forms.
- private: if true, this field should not be present in forms nor manipulated by clients.

