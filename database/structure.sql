/***

/**
 * Bicubic PHP Framework
 *
 * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
 * @copyright  2011 Bicubic Technology - http://www.bicubic.cl
 * @license    MIT
 * @framework  2.2
 */

*/

CREATE TABLE SystemUser (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  username varchar(16) NOT NULL,
  password varchar(16) NOT NULL,
  email varchar(320) NOT NULL,
  token varchar(64) NOT NULL
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;