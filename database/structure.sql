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
    "username" VARCHAR(256) NOT NULL,
    "password" VARCHAR(1024) NOT NULL,
    "email" VARCHAR(256) NOT NULL,
    "token" VARCHAR(1024) NOT NULL,
    CONSTRAINT "systemuser_pk" PRIMARY KEY ("id")
);

INSERT INTO "systemuser"(
            "name", "username", "password", "email", "token")
    VALUES ('administrator', 'admin@localhost', '$2a$10$s2/s7.yPO2WZWy0fSqWiH.YfEao1o1BC47hcyoiKK6m5Ph8V/zLWu', 'admin@localhost', '');