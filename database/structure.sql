-- /**
--  * Bicubic PHP Framework
--  *
--  * @author     Juan Rodríguez-Covili <juan@bicubic.cl>
--  * @copyright  2011-2014 Bicubic Technology - http://www.bicubic.cl
--  * @license    MIT
--  * @version 3.0.0
--  */


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
    "usercountry" INTEGER,
    "userlang" INTEGER, 
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