-- /**
--  * Bicubic PHP Framework
--  *
--  * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
--  * @copyright  2011-2014 Bicubic Technology - http://www.bicubic.cl
--  * @license    MIT
--  * @version 3.0.0
--  */

CREATE TABLE "systemadmin" (
    "id" BIGSERIAL NOT NULL, 
    "name" VARCHAR(256) NOT NULL, 
    "email" VARCHAR(256) NOT NULL, 
    "password" VARCHAR(1024) NOT NULL, 
    "sessiontoken" VARCHAR(1024), 
    CONSTRAINT "systemadmin_pk" PRIMARY KEY ("id")
);
CREATE INDEX systemadmin_email_index ON systemadmin USING btree (email);
CREATE INDEX systemadmin_password_index ON systemadmin USING btree (password);
INSERT INTO "systemadmin"("name", "email", "password") VALUES ('Administrator', 'admin@localhost', '$2a$10$s2/s7.yPO2WZWy0fSqWiH.YfEao1o1BC47hcyoiKK6m5Ph8V/zLWu');

CREATE TABLE "systemuser" (
    "id" BIGSERIAL NOT NULL, 
    "name" VARCHAR(256) NOT NULL, 
    "email" VARCHAR(256) NOT NULL, 
    "password" VARCHAR(1024) NOT NULL, 
    "sessiontoken" VARCHAR(1024), 
    "confirmemailtoken" VARCHAR(1024), 
    "forgottoken" VARCHAR(1024), 
    "changeemailtoken" VARCHAR(1024), 
    "changepasswordtoken" VARCHAR(1024), 
    "usercountry" VARCHAR(2),
    "userlang" VARCHAR(2), 
    CONSTRAINT "systemuser_pk" PRIMARY KEY ("id")
);
CREATE INDEX systemuser_email_index ON systemuser USING btree (email);
CREATE INDEX systemuser_password_index ON systemuser USING btree (password);
CREATE INDEX systemuser_confirmemailtoken_index ON systemuser USING btree (confirmemailtoken);
CREATE INDEX systemuser_forgottoken_index ON systemuser USING btree (forgottoken);
CREATE INDEX systemuser_changeemailtoken_index ON systemuser USING btree (changeemailtoken);
CREATE INDEX systemuser_changepasswordtoken_index ON systemuser USING btree (changepasswordtoken);
CREATE INDEX systemuser_usercountry_index ON systemuser USING btree (usercountry);
CREATE INDEX systemuser_userlang_index ON systemuser USING btree (userlang);

CREATE TABLE "systememail" (
    "id" BIGSERIAL NOT NULL, 
    "broadcast" INT NOT NULL, 
    "broadcastcountry" VARCHAR(2), 
    "broadcastlang" VARCHAR(2), 
    "to" VARCHAR(256), 
    "from" VARCHAR(256) NOT NULL, 
    "subject" VARCHAR(1024) NOT NULL, 
    "body" TEXT NOT NULL, 
    "sent" INT NOT NULL,
    CONSTRAINT "systememail_pk" PRIMARY KEY ("id")
);
CREATE INDEX systememail_sent_index ON systememail USING btree (sent);

CREATE TABLE "systemuserlog" (
    "id" BIGSERIAL NOT NULL, 
    "systemuser" BIGINT, 
    "servertime" BIGINT, 
    "remoteaddress" VARCHAR, 
    "remotehost" VARCHAR, 
    "remoteport" VARCHAR, 
    "httpreferer" VARCHAR, 
    "httplang" VARCHAR, 
    "httpcharset" VARCHAR, 
    "httphost" VARCHAR, 
    "httpuseragent" VARCHAR,
    "https" VARCHAR, 
    "querystring" VARCHAR, 
    "userlatitude" VARCHAR, 
    "uselongitude" VARCHAR, 
    "usedevicemodel" VARCHAR, 
    "usedeviceos" VARCHAR,
    "usedeviceversion" VARCHAR,
    "usecountry" VARCHAR,
    "uselanguage" VARCHAR,
    "usebatterylevel" VARCHAR,
    "payload" VARCHAR, 
    CONSTRAINT "systemuserlog_pk" PRIMARY KEY ("id")
);
CREATE INDEX systemuserlog_systemuser_index ON systemuserlog USING btree (systemuser);
ALTER TABLE systemuserlog ADD CONSTRAINT systemuserlog_systemuser_fkey FOREIGN KEY (systemuser) REFERENCES systemuser (id) MATCH FULL ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;