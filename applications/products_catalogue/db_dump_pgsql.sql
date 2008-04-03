--
-- PostgreSQL database dump
--

SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

--
-- Name: SCHEMA public; Type: COMMENT; Schema: -; Owner: postgres
--

COMMENT ON SCHEMA public IS 'Standard public schema';


SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: brands; Type: TABLE; Schema: public; Owner: p4a; Tablespace: 
--

CREATE TABLE brands (
    brand_id integer NOT NULL,
    description text NOT NULL,
    visible boolean NOT NULL
);


ALTER TABLE public.brands OWNER TO p4a;

--
-- Name: brands_brand_id_seq; Type: SEQUENCE; Schema: public; Owner: p4a
--

CREATE SEQUENCE brands_brand_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.brands_brand_id_seq OWNER TO p4a;

--
-- Name: brands_brand_id_seq; Type: SEQUENCE SET; Schema: public; Owner: p4a
--

SELECT pg_catalog.setval('brands_brand_id_seq', 3, true);


--
-- Name: categories; Type: TABLE; Schema: public; Owner: p4a; Tablespace: 
--

CREATE TABLE categories (
    category_id integer NOT NULL,
    description text NOT NULL,
    visible boolean NOT NULL
);


ALTER TABLE public.categories OWNER TO p4a;

--
-- Name: categories_category_id_seq; Type: SEQUENCE; Schema: public; Owner: p4a
--

CREATE SEQUENCE categories_category_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.categories_category_id_seq OWNER TO p4a;

--
-- Name: categories_category_id_seq; Type: SEQUENCE SET; Schema: public; Owner: p4a
--

SELECT pg_catalog.setval('categories_category_id_seq', 3, true);


--
-- Name: products; Type: TABLE; Schema: public; Owner: p4a; Tablespace: 
--

CREATE TABLE products (
    product_id integer NOT NULL,
    brand_id integer NOT NULL,
    category_id integer NOT NULL,
    model text NOT NULL,
    date_arrival date,
    price numeric(10,2) NOT NULL,
    discount integer NOT NULL,
    picture text,
    is_new boolean NOT NULL,
    visible boolean NOT NULL,
    description text NOT NULL
);


ALTER TABLE public.products OWNER TO p4a;

--
-- Name: products_product_id_seq; Type: SEQUENCE; Schema: public; Owner: p4a
--

CREATE SEQUENCE products_product_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.products_product_id_seq OWNER TO p4a;

--
-- Name: products_product_id_seq; Type: SEQUENCE SET; Schema: public; Owner: p4a
--

SELECT pg_catalog.setval('products_product_id_seq', 1, true);


--
-- Data for Name: brands; Type: TABLE DATA; Schema: public; Owner: p4a
--

INSERT INTO brands VALUES (1, 'Brand 1', true);
INSERT INTO brands VALUES (2, 'Brand 2', true);
INSERT INTO brands VALUES (3, 'Brand 3', true);


--
-- Data for Name: categories; Type: TABLE DATA; Schema: public; Owner: p4a
--

INSERT INTO categories VALUES (1, 'Category 1', true);
INSERT INTO categories VALUES (2, 'Category 2', true);
INSERT INTO categories VALUES (3, 'Category 3', true);


--
-- Data for Name: products; Type: TABLE DATA; Schema: public; Owner: p4a
--

INSERT INTO products VALUES (1, 1, 1, 'Model 1', '2008-04-03', 10.00, 2, NULL, false, true, '<p>This is the first product</p>');


--
-- Name: brands_pkey; Type: CONSTRAINT; Schema: public; Owner: p4a; Tablespace: 
--

ALTER TABLE ONLY brands
    ADD CONSTRAINT brands_pkey PRIMARY KEY (brand_id);


--
-- Name: categories_pkey; Type: CONSTRAINT; Schema: public; Owner: p4a; Tablespace: 
--

ALTER TABLE ONLY categories
    ADD CONSTRAINT categories_pkey PRIMARY KEY (category_id);


--
-- Name: products_pkey; Type: CONSTRAINT; Schema: public; Owner: p4a; Tablespace: 
--

ALTER TABLE ONLY products
    ADD CONSTRAINT products_pkey PRIMARY KEY (product_id);


--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- PostgreSQL database dump complete
--

