CREATE SCHEMA IF NOT EXISTS lbaw25145;
SET search_path TO lbaw25145;


-- Drop existing tables, domains, and functions
DROP TABLE IF EXISTS "user" CASCADE;
DROP TABLE IF EXISTS "administrator" CASCADE;
DROP TABLE IF EXISTS "customer" CASCADE;
DROP TABLE IF EXISTS "owner" CASCADE;
DROP TABLE IF EXISTS "favourite" CASCADE;
DROP TABLE IF EXISTS "waitlist" CASCADE;
DROP TABLE IF EXISTS "review" CASCADE;
DROP TABLE IF EXISTS "reply" CASCADE;
DROP TABLE IF EXISTS "restaurant" CASCADE;
DROP TABLE IF EXISTS "restaurant_photo" CASCADE;
DROP TABLE IF EXISTS "reservation" CASCADE;
DROP TABLE IF EXISTS "offer" CASCADE;
DROP TABLE IF EXISTS "notification" CASCADE;
DROP TABLE IF EXISTS "review_notification" CASCADE;
DROP TABLE IF EXISTS "reservation_notification" CASCADE;
DROP TABLE IF EXISTS "offer_notification" CASCADE;

DROP DOMAIN IF EXISTS types_of_reservation_notification;
DROP DOMAIN IF EXISTS types_of_review_notification;
DROP DOMAIN IF EXISTS types_of_offer_notification;

DROP FUNCTION IF EXISTS delete_user_data();
DROP TRIGGER IF EXISTS reply_on_review_deletion ON "review";

-- Create custom domains
CREATE DOMAIN types_of_reservation_notification AS TEXT
CHECK(
    VALUE IN ('new_reservation', 'reservation_cancelled', 'reservation_modified', 'reservation_reminder')
);

CREATE DOMAIN types_of_review_notification AS TEXT
CHECK(
    VALUE IN ('review_posted', 'review_replied_to')
);

CREATE DOMAIN types_of_offer_notification AS TEXT
CHECK(
    VALUE IN ('general_offer', 'personalized_offer')
);

-- Create tables
CREATE TABLE "user" (
    id INTEGER PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
    username TEXT NOT NULL UNIQUE,
    email TEXT NOT NULL UNIQUE,
    name TEXT NOT NULL,
    surname TEXT NOT NULL,
    password TEXT NOT NULL,
    joined_at DATE NOT NULL DEFAULT CURRENT_DATE,
    is_blocked BOOLEAN NOT NULL DEFAULT false,
    profile_picture TEXT,
    profile_description TEXT
);

CREATE TABLE "administrator" (
    id INTEGER PRIMARY KEY REFERENCES "user"(id) ON DELETE CASCADE
);

CREATE TABLE "customer" (
    id INTEGER PRIMARY KEY REFERENCES "user"(id) ON DELETE CASCADE
);

CREATE TABLE "owner" (
    id INTEGER PRIMARY KEY REFERENCES "user"(id) ON DELETE CASCADE
);

CREATE TABLE "restaurant" (
    id INTEGER PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
    owner_id INTEGER REFERENCES "owner"(id) ON DELETE SET NULL,
    name TEXT NOT NULL,
    description TEXT NOT NULL,
    email TEXT NOT NULL,
    phone_number TEXT,
    address TEXT NOT NULL,
    opening_hours TEXT NOT NULL,
    capacity INTEGER CHECK (capacity > 0),
    created_at DATE NOT NULL DEFAULT CURRENT_DATE,
    updated_at DATE,
    closed_at DATE
);

CREATE TABLE "favourite" (
    user_id INTEGER REFERENCES "user"(id) ON DELETE CASCADE,
    restaurant_id INTEGER REFERENCES "restaurant"(id) ON DELETE CASCADE,
    PRIMARY KEY (user_id, restaurant_id)
);

CREATE TABLE "review" (
    id INTEGER PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
    user_id INTEGER REFERENCES "user"(id) ON DELETE CASCADE,
    restaurant_id INTEGER REFERENCES "restaurant"(id) ON DELETE CASCADE,
    content TEXT NOT NULL,
    rating INTEGER NOT NULL CHECK (rating >= 1 AND rating <= 5),
    created_at DATE NOT NULL DEFAULT CURRENT_DATE,
    edited_at DATE,
    deleted_at DATE
);

CREATE TABLE "reply" (
    id INTEGER PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
    user_id INTEGER REFERENCES "user"(id) ON DELETE CASCADE,
    review_id INTEGER REFERENCES "review"(id) ON DELETE CASCADE,
    content TEXT NOT NULL,
    created_at DATE NOT NULL DEFAULT CURRENT_DATE,
    edited_at DATE,
    deleted_at DATE
);

CREATE TABLE "restaurant_photo" (
    id INTEGER PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
    restaurant_id INTEGER REFERENCES "restaurant"(id) ON DELETE CASCADE,
    link TEXT NOT NULL,
    display_order INTEGER CHECK (display_order > 0),
    title TEXT,
    price INTEGER CHECK (price > 0)
);

CREATE TABLE "reservation" (
    id INTEGER PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
    user_id INTEGER REFERENCES "user"(id) ON DELETE CASCADE,
    restaurant_id INTEGER REFERENCES "restaurant"(id) ON DELETE CASCADE,
    title TEXT DEFAULT 'Reservation',
    description TEXT,
    number_of_people INTEGER NOT NULL CHECK (number_of_people > 0),
    date_of_visit DATE NOT NULL CHECK (date_of_visit >= CURRENT_DATE),
    time_of_visit TIME NOT NULL,
    is_confirmed BOOLEAN NOT NULL DEFAULT false,
    is_completed BOOLEAN NOT NULL DEFAULT false,
    created_at DATE NOT NULL DEFAULT CURRENT_DATE,
    edited_at DATE,
    deleted_at DATE
);

CREATE TABLE "waitlist" (
    id INTEGER PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
    user_id INTEGER REFERENCES "user"(id) ON DELETE CASCADE,
    reservation_id INTEGER REFERENCES "reservation"(id) ON DELETE CASCADE,
    position INTEGER NOT NULL CHECK (position > 0)
);

CREATE TABLE "offer" (
    id INTEGER PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
    restaurant_id INTEGER REFERENCES "restaurant"(id) ON DELETE CASCADE,
    title TEXT NOT NULL,
    content TEXT,
    start_date DATE NOT NULL CHECK (start_date >= CURRENT_DATE),
    end_date DATE NOT NULL CHECK (end_date >= start_date)
);

CREATE TABLE "notification" (
    id INTEGER PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
    user_id INTEGER REFERENCES "user"(id) ON DELETE CASCADE,
    title TEXT NOT NULL,
    date DATE NOT NULL DEFAULT CURRENT_DATE,
    content TEXT,
    viewed BOOLEAN NOT NULL DEFAULT false
);

CREATE TABLE "review_notification" (
    notification_id INTEGER PRIMARY KEY REFERENCES "notification"(id) ON DELETE CASCADE,
    review_id INTEGER REFERENCES "review"(id) ON DELETE CASCADE
);

CREATE TABLE "reservation_notification" (
    notification_id INTEGER PRIMARY KEY REFERENCES "notification"(id) ON DELETE CASCADE,
    reservation_id INTEGER REFERENCES "reservation"(id) ON DELETE CASCADE
);

CREATE TABLE "offer_notification" (
    notification_id INTEGER PRIMARY KEY REFERENCES "notification"(id) ON DELETE CASCADE,
    offer_id INTEGER REFERENCES "offer"(id) ON DELETE CASCADE
);

-- Create Indexes (as specified in ebd.md)

-- IDX01: For retrieving all reviews of a restaurant
CREATE INDEX review_restaurant_idx ON review USING btree (restaurant_id);
-- CLUSTER review USING review_restaurant_idx; -- Note: CLUSTER needs to be run manually or as a separate step

-- IDX02: For checking table availability at a restaurant for a specific date
CREATE INDEX reservation_restaurant_date_idx ON reservation USING btree (restaurant_id, date_of_visit);

-- Full-text search index
ALTER TABLE restaurant ADD COLUMN tsvectors TSVECTOR;

CREATE FUNCTION restaurant_search_update() RETURNS TRIGGER AS $$
BEGIN
 IF TG_OP = 'INSERT' THEN
        NEW.tsvectors = (
         setweight(to_tsvector('english', NEW.name), 'A') ||
         setweight(to_tsvector('english', NEW.description), 'B')
        );
 END IF;
 IF TG_OP = 'UPDATE' THEN
         IF (NEW.name <> OLD.name OR NEW.description <> OLD.description) THEN
           NEW.tsvectors = (
             setweight(to_tsvector('english', NEW.name), 'A') ||
             setweight(to_tsvector('english', NEW.description), 'B')
           );
         END IF;
 END IF;
 RETURN NEW;
END $$
LANGUAGE plpgsql;

CREATE TRIGGER restaurant_search_update
 BEFORE INSERT OR UPDATE ON restaurant
 FOR EACH ROW
 EXECUTE PROCEDURE restaurant_search_update();

CREATE INDEX restaurant_search_idx ON restaurant USING GIN (tsvectors);

-- Create Functions and Triggers
CREATE FUNCTION delete_user_data() RETURNS TRIGGER AS $$
BEGIN
    -- Anonymize reviews
    UPDATE "review" SET content = 'Deleted review', user_id = NULL WHERE user_id = OLD.id;
    -- Anonymize replies
    UPDATE "reply" SET content = 'Deleted reply', user_id = NULL WHERE user_id = OLD.id;
    -- Delete reservations
    DELETE FROM "reservation" WHERE user_id = OLD.id;
    -- Delete from waitlist
    DELETE FROM "waitlist" WHERE user_id = OLD.id;
    -- Delete from favourites
    DELETE FROM "favourite" WHERE user_id = OLD.id;
    -- Delete notifications
    DELETE FROM "notification" WHERE user_id = OLD.id;
    RETURN OLD;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER user_delete_trigger
BEFORE DELETE ON "user"
FOR EACH ROW
EXECUTE FUNCTION delete_user_data();

CREATE FUNCTION reply_on_review_deletion() RETURNS TRIGGER AS $$
BEGIN
    -- Delete replies to a review when the review is deleted
    UPDATE "reply" SET content = 'Deleted reply' WHERE review_id = OLD.id;
    RETURN OLD;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER reply_on_review_deletion
BEFORE DELETE ON "review"
FOR EACH ROW
EXECUTE FUNCTION reply_on_review_deletion();