#Bicubic Framework PHP
A simple and efficient MVC framework written in PHP for PostgreSQL, or similar DB.


##Directory Structure
- app: Application classes.
- assets: javascript, css, and images files.
- beans: DataObject classes.
- data: TransationManager classes.
- lang: language files.
- lib: library files.
- nav: Navigation classes.
- templates: templates files for applications.
- views: views files for navigations.
- config.php: editable configuration file.
- factory.php: editable file with application mappings.
- index.php: non editable init file.

##Beans params
- name: name of field.
- lang: lang key for display name.
- type: type of field, a value from PropertyTypes.
- require: indicates if the field is required for complete purpuses.
- default: indicates a default value in case of null.
- serializable: indicates that the property is going to be stored.
- index: indicates that a btree index needs to be created for this field.
- reference: indicates the name of another bean this property is referencing to.
- updatenull: if true, a null value on the field should override any existing value, if false, the stored value should not be changed.
- hidden: if true, this field should not be shown in forms.
- private: if true, this field should not be present in forms nor manipulated by clients.
- unique: if true, the value of the field must be unique.
- table: if true the field is shown in automatic tables.
