/***
 * Bicubic PHP Framework
 *
 * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
 * @copyright  2011 Bicubic Technology - http://www.bicubic.cl
 * @license    MIT
 * @framework  2.2
*/

CREATE SEQUENCE "systemuser_id_seq";

CREATE TABLE "SystemUser" (
    "id" BIGINT NOT NULL DEFAULT nextval('systemuser_id_seq'),
    "name" VARCHAR(64) NOT NULL,
    "username" VARCHAR(16) NOT NULL,
    "password" VARCHAR(16) NOT NULL,
    "email" VARCHAR(320) NOT NULL,
    "token" VARCHAR(64) NOT NULL,
    CONSTRAINT "systemuser_pk" PRIMARY KEY ("id")
);

INSERT INTO "SystemUser"(
            "name", "username", "password", "email", "token")
    VALUES ('Administrator', 'admin', 'admin', 'admin@localhost', '');