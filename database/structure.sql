CREATE SEQUENCE systemuser_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
CREATE TABLE systemuser (
    id bigint DEFAULT nextval('systemuser_id_seq'::regclass) NOT NULL,
    name character varying(256) NOT NULL,
    email character varying(256) NOT NULL,
    password character varying(1024) NOT NULL,
    sessiontoken character varying(1024),
    confirmemailtoken character varying(1024),
    forgottoken character varying(1024),
    changeemailtoken character varying(1024),
    newemail character varying(256),
    usercountry integer,
    userlang integer
);
CREATE TABLE systemuserlog (
    id bigint NOT NULL,
    systemuser bigint,
    servertime bigint,
    remoteaddress character varying,
    remotehost character varying,
    remoteport character varying,
    httpreferer character varying,
    httplang character varying,
    httpcharset character varying,
    httphost character varying,
    httpuseragent character varying,
    https character varying,
    querystring character varying,
    uselatitude character varying,
    uselongitude character varying,
    usedevicemodel character varying,
    usedeviceos character varying,
    usedeviceversion character varying,
    usecountry character varying,
    uselanguage character varying,
    usebatterylevel character varying,
    payload character varying
);
CREATE SEQUENCE systemuserlog_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
ALTER TABLE ONLY systemuserlog ALTER COLUMN id SET DEFAULT nextval('systemuserlog_id_seq'::regclass);
ALTER TABLE ONLY systemuser
    ADD CONSTRAINT systemuser_pk PRIMARY KEY (id);
ALTER TABLE ONLY systemuserlog
    ADD CONSTRAINT systemuserlog_pk PRIMARY KEY (id);
CREATE INDEX systemuser_changeemailtoken_index ON systemuser USING btree (changeemailtoken);
CREATE INDEX systemuser_changepasswordtoken_index ON systemuser USING btree (newemail);
CREATE INDEX systemuser_confirmemailtoken_index ON systemuser USING btree (confirmemailtoken);
CREATE INDEX systemuser_email_index ON systemuser USING btree (email);
CREATE INDEX systemuser_forgottoken_index ON systemuser USING btree (forgottoken);
CREATE INDEX systemuser_password_index ON systemuser USING btree (password);
CREATE INDEX systemuser_usercountry_index ON systemuser USING btree (usercountry);
CREATE INDEX systemuser_userlang_index ON systemuser USING btree (userlang);
CREATE INDEX systemuserlog_systemuser_index ON systemuserlog USING btree (systemuser);
ALTER TABLE ONLY systemuserlog
    ADD CONSTRAINT systemuserlog_systemuser_fkey FOREIGN KEY (systemuser) REFERENCES systemuser(id) MATCH FULL ON DELETE CASCADE;