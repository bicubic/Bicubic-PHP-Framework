/***
 * Bicubic PHP Framework
 *
 * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
 * @copyright  2011 Bicubic Technology - http://www.bicubic.cl
 * @license    MIT
 * @framework  2.2
*/

CREATE SEQUENCE "systemuser_id_seq";

CREATE TABLE "systemuser" (
    "id" BIGINT NOT NULL DEFAULT nextval('systemuser_id_seq'), 
    "name" VARCHAR(256) NOT NULL, 
    "email" VARCHAR(256) NOT NULL, 
    "password" VARCHAR(1024) NOT NULL, 
    "sessiontoken" VARCHAR(1024), 
    "confirmemailtoken" VARCHAR(1024), 
    "forgottoken" VARCHAR(1024), 
    "changeemailtoken" VARCHAR(1024), 
    "changepasswordtoken" VARCHAR(1024), 
    "usercountry" INT,
    "userlang" VARCHAR(2), 
    CONSTRAINT "systemuser_pk" PRIMARY KEY ("id")
);

CREATE SEQUENCE "systememail_id_seq";

CREATE TABLE "systememail" (
    "id" BIGINT NOT NULL DEFAULT nextval('systemuser_id_seq'), 
    "broadcast" INT, 
    "broadcastlang" INT, 
    "broadcastcountry" VARCHAR(2), 
    "to" VARCHAR(256), 
    "from" VARCHAR(256), 
    "subject" VARCHAR(1024), 
    "body" TEXT, 
    CONSTRAINT "systememail_pk" PRIMARY KEY ("id")
);