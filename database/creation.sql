CREATE SCHEMA IF NOT EXISTS lbaw25145;
SET search_path TO lbaw25145;

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

DROP TRIGGER IF EXISTS restaurant_search_update ON restaurant CASCADE;
DROP TRIGGER IF EXISTS user_archive_trigger ON "user" CASCADE;
DROP TRIGGER IF EXISTS check_reservation_before_review_trigger ON review CASCADE;
DROP TRIGGER IF EXISTS check_capacity_before_reservation ON reservation CASCADE;
DROP TRIGGER IF EXISTS auto_complete_reservations ON reservation CASCADE;
DROP TRIGGER IF EXISTS update_restaurant_modified_date ON restaurant CASCADE;
DROP TRIGGER IF EXISTS cascade_review_deletion_trigger ON review CASCADE;
DROP TRIGGER IF EXISTS notify_reservation_creation ON reservation CASCADE;
DROP TRIGGER IF EXISTS validate_reservation_changes ON reservation CASCADE;
DROP TRIGGER IF EXISTS update_review_edit_time ON review CASCADE;
DROP TRIGGER IF EXISTS update_reply_edit_time ON reply CASCADE;
DROP TRIGGER IF EXISTS update_reservation_edit_time ON reservation CASCADE;
DROP TRIGGER IF EXISTS cascade_restaurant_archive_trigger ON restaurant CASCADE;
DROP TRIGGER IF EXISTS validate_opening_hours ON reservation CASCADE;

DROP FUNCTION IF EXISTS restaurant_search_update() CASCADE;
DROP FUNCTION IF EXISTS archive_user_data() CASCADE;
DROP FUNCTION IF EXISTS check_completed_reservation_before_review() CASCADE;
DROP FUNCTION IF EXISTS check_restaurant_capacity() CASCADE;
DROP FUNCTION IF EXISTS auto_complete_past_reservations() CASCADE;
DROP FUNCTION IF EXISTS update_restaurant_timestamp() CASCADE;
DROP FUNCTION IF EXISTS cascade_review_deletion() CASCADE;
DROP FUNCTION IF EXISTS notify_new_reservation() CASCADE;
DROP FUNCTION IF EXISTS validate_reservation_modification() CASCADE;
DROP FUNCTION IF EXISTS update_edit_timestamps() CASCADE;
DROP FUNCTION IF EXISTS cascade_restaurant_archive() CASCADE;
DROP FUNCTION IF EXISTS can_reserve(INT, DATE, TIME) CASCADE;
DROP FUNCTION IF EXISTS check_opening_hours() CASCADE;

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
    profile_description TEXT,
    deleted_at TIMESTAMP
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
    opening_hours JSONB NOT NULL,
    capacity INTEGER CHECK (capacity > 0),
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP,
    closed_at TIMESTAMP
);

CREATE TABLE "favourite" (
    user_id INTEGER REFERENCES "user"(id) ON DELETE CASCADE,
    restaurant_id INTEGER REFERENCES "restaurant"(id) ON DELETE CASCADE,
    PRIMARY KEY (user_id, restaurant_id)
);

CREATE TABLE "review" (
    id INTEGER PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
    user_id INTEGER REFERENCES "user"(id) ON DELETE CASCADE NULL,
    restaurant_id INTEGER REFERENCES "restaurant"(id) ON DELETE CASCADE,
    content TEXT NOT NULL,
    rating INTEGER NOT NULL CHECK (rating >= 1 AND rating <= 5),
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    edited_at TIMESTAMP,
    deleted_at TIMESTAMP
);

CREATE TABLE "reply" (
    id INTEGER PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
    user_id INTEGER REFERENCES "user"(id) ON DELETE CASCADE NULL,
    review_id INTEGER REFERENCES "review"(id) ON DELETE CASCADE,
    content TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    edited_at TIMESTAMP,
    deleted_at TIMESTAMP
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
    user_id INTEGER REFERENCES "user"(id) ON DELETE CASCADE NULL,
    restaurant_id INTEGER REFERENCES "restaurant"(id) ON DELETE CASCADE,
    title TEXT DEFAULT 'Reservation',
    description TEXT,
    number_of_people INTEGER NOT NULL CHECK (number_of_people > 0),
    date_of_visit DATE NOT NULL,
    time_of_visit TIME NOT NULL,
    is_confirmed BOOLEAN NOT NULL DEFAULT false,
    is_completed BOOLEAN NOT NULL DEFAULT false,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    edited_at TIMESTAMP,
    deleted_at TIMESTAMP
);

CREATE TABLE "waitlist" (
    id INTEGER PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
    user_id INTEGER REFERENCES "user"(id) ON DELETE CASCADE NULL,
    reservation_id INTEGER REFERENCES "reservation"(id) ON DELETE CASCADE,
    position INTEGER NOT NULL CHECK (position > 0)
);

CREATE TABLE "offer" (
    id INTEGER PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
    restaurant_id INTEGER REFERENCES "restaurant"(id) ON DELETE CASCADE,
    title TEXT NOT NULL,
    content TEXT,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL CHECK (end_date >= start_date)
);

CREATE TABLE "notification" (
    id INTEGER PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
    user_id INTEGER REFERENCES "user"(id) ON DELETE CASCADE NULL,
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

CREATE INDEX review_restaurant_idx ON review USING btree (restaurant_id);
CLUSTER review USING review_restaurant_idx;

CREATE INDEX reservation_restaurant_date_idx ON reservation USING btree (restaurant_id, date_of_visit);

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
             setweight(to_tsvector('english', NEW.description), 'B') ||
             setweight(to_tsvector('english', NEW.location), 'C')
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

CREATE OR REPLACE FUNCTION check_completed_reservation_before_review()
RETURNS TRIGGER AS $$
BEGIN
    IF NOT EXISTS (
        SELECT 1
        FROM reservation
        WHERE user_id = NEW.user_id
          AND restaurant_id = NEW.restaurant_id
          AND is_completed = TRUE
    ) THEN
        RAISE EXCEPTION 'Users can only review restaurants where they have a completed reservation.';
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER check_reservation_before_review_trigger
BEFORE INSERT ON "review"
FOR EACH ROW
EXECUTE FUNCTION check_completed_reservation_before_review();

CREATE OR REPLACE FUNCTION check_restaurant_capacity()
RETURNS TRIGGER AS $$
DECLARE
    restaurant_capacity INTEGER;
    total_reserved INTEGER;
BEGIN
    SELECT capacity INTO restaurant_capacity 
    FROM restaurant 
    WHERE id = NEW.restaurant_id;

    SELECT COALESCE(SUM(number_of_people), 0) INTO total_reserved
    FROM reservation
    WHERE restaurant_id = NEW.restaurant_id
        AND date_of_visit = NEW.date_of_visit
        AND deleted_at IS NULL
        AND id != COALESCE(NEW.id, -1)
        AND (is_confirmed = TRUE);

    IF (total_reserved + NEW.number_of_people) > restaurant_capacity THEN
        RAISE EXCEPTION 'Restaurant capacity exceeded. Only % seats available for this time slot.', 
                        (restaurant_capacity - total_reserved);
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER check_capacity_before_reservation
    BEFORE INSERT OR UPDATE ON reservation
    FOR EACH ROW
    EXECUTE FUNCTION check_restaurant_capacity();

CREATE OR REPLACE FUNCTION auto_complete_past_reservations()
RETURNS TRIGGER AS $$
BEGIN
    UPDATE reservation 
    SET is_completed = TRUE 
    WHERE (date_of_visit < CURRENT_DATE OR 
          (date_of_visit = CURRENT_DATE AND time_of_visit < CURRENT_TIME))
      AND is_completed = FALSE
      AND deleted_at IS NULL;
    
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER auto_complete_reservations
    AFTER INSERT OR UPDATE ON reservation
    FOR EACH ROW
    EXECUTE FUNCTION auto_complete_past_reservations();

CREATE OR REPLACE FUNCTION update_restaurant_timestamp()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER update_restaurant_modified_date
    BEFORE UPDATE ON restaurant
    FOR EACH ROW
    EXECUTE FUNCTION update_restaurant_timestamp();

CREATE OR REPLACE FUNCTION cascade_review_deletion()
RETURNS TRIGGER AS $$
BEGIN
    IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
        UPDATE reply 
        SET deleted_at = CURRENT_TIMESTAMP
        WHERE review_id = NEW.id 
          AND deleted_at IS NULL;
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER cascade_review_deletion_trigger
    AFTER UPDATE ON review
    FOR EACH ROW
    EXECUTE FUNCTION cascade_review_deletion();

CREATE OR REPLACE FUNCTION notify_new_reservation()
RETURNS TRIGGER AS $$
BEGIN
    INSERT INTO notification (user_id, title, content)
    SELECT 
        r.owner_id,
        'New Reservation Request',
        'New reservation request for ' || NEW.number_of_people || ' people on ' || 
        NEW.date_of_visit || ' at ' || NEW.time_of_visit
    FROM restaurant r
    WHERE r.id = NEW.restaurant_id;

    INSERT INTO notification (user_id, title, content)
    VALUES (
        NEW.user_id,
        'Reservation Request Sent',
        'Your reservation request has been sent to the restaurant'
    );

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER notify_reservation_creation
    AFTER INSERT ON reservation
    FOR EACH ROW
    EXECUTE FUNCTION notify_new_reservation();

CREATE OR REPLACE FUNCTION validate_reservation_modification()
RETURNS TRIGGER AS $$
BEGIN
    IF OLD.user_id IS NOT NULL AND NEW.user_id IS NULL THEN
        RETURN NEW;
    END IF;

    IF TG_OP = 'UPDATE' AND NEW.deleted_at IS DISTINCT FROM OLD.deleted_at THEN
        RETURN NEW;
    END IF;

    IF OLD.is_completed = TRUE THEN
        RAISE EXCEPTION 'Cannot modify completed reservations';
    END IF;

    IF (OLD.date_of_visit < CURRENT_DATE OR 
        (OLD.date_of_visit = CURRENT_DATE AND OLD.time_of_visit < CURRENT_TIME)) THEN
        RAISE EXCEPTION 'Cannot modify past reservations';
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER validate_reservation_changes
    BEFORE UPDATE ON reservation
    FOR EACH ROW
    EXECUTE FUNCTION validate_reservation_modification();

CREATE OR REPLACE FUNCTION update_edit_timestamps()
RETURNS TRIGGER AS $$
BEGIN
    IF TG_TABLE_NAME = 'review' THEN
        IF NEW.content IS DISTINCT FROM OLD.content OR NEW.rating IS DISTINCT FROM OLD.rating THEN
            NEW.edited_at = CURRENT_TIMESTAMP;
        END IF;
    ELSIF TG_TABLE_NAME = 'reply' THEN
        IF NEW.content IS DISTINCT FROM OLD.content THEN
            NEW.edited_at = CURRENT_TIMESTAMP;
        END IF;
    ELSIF TG_TABLE_NAME = 'reservation' THEN
        IF NEW.number_of_people IS DISTINCT FROM OLD.number_of_people OR
           NEW.date_of_visit IS DISTINCT FROM OLD.date_of_visit OR
           NEW.time_of_visit IS DISTINCT FROM OLD.time_of_visit THEN
            NEW.edited_at = CURRENT_TIMESTAMP;
        END IF;
    END IF;
    
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER update_review_edit_time
    BEFORE UPDATE ON review
    FOR EACH ROW
    EXECUTE FUNCTION update_edit_timestamps();

CREATE TRIGGER update_reply_edit_time
    BEFORE UPDATE ON reply
    FOR EACH ROW
    EXECUTE FUNCTION update_edit_timestamps();

CREATE TRIGGER update_reservation_edit_time
    BEFORE UPDATE ON reservation
    FOR EACH ROW
    EXECUTE FUNCTION update_edit_timestamps();

CREATE OR REPLACE FUNCTION cascade_restaurant_archive() 
RETURNS TRIGGER AS $$
BEGIN
    IF OLD.closed_at IS NULL AND NEW.closed_at IS NOT NULL THEN 
        UPDATE reservation 
        SET deleted_at = CURRENT_DATE
        WHERE restaurant_id = NEW.id 
          AND date_of_visit >= CURRENT_DATE
          AND deleted_at IS NULL;
       
        UPDATE offer 
        SET end_date = CURRENT_DATE
        WHERE restaurant_id = NEW.id 
          AND end_date > CURRENT_DATE;
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER cascade_restaurant_archive_trigger
    AFTER UPDATE ON restaurant
    FOR EACH ROW
    EXECUTE FUNCTION cascade_restaurant_archive();



CREATE OR REPLACE FUNCTION can_reserve(
    restaurant_id INT,
    user_id INT,
    res_date DATE,
    res_time TIME
)
RETURNS BOOLEAN AS $$
DECLARE
    day_key TEXT;
    time_slot TEXT;
    start_time TIME;
    end_time TIME;
    hours_json JSONB;
BEGIN
    IF user_id IS NULL THEN
        RETURN TRUE;
    END IF;

    day_key := lower(to_char(res_date, 'Dy'));
    day_key := replace(day_key, '.', '');
    
    SELECT opening_hours INTO hours_json
    FROM restaurant 
    WHERE id = restaurant_id;
    
    IF hours_json IS NULL OR hours_json->day_key IS NULL OR jsonb_array_length(hours_json->day_key) = 0 THEN
        RETURN FALSE;
    END IF;

    FOR time_slot IN
        SELECT jsonb_array_elements_text(hours_json->day_key)
    LOOP
        BEGIN
            start_time := split_part(trim(time_slot), '-', 1)::time;
            end_time := split_part(trim(time_slot), '-', 2)::time;
            
            IF res_time >= start_time AND res_time < end_time THEN
                RETURN TRUE;
            END IF;
        EXCEPTION
            WHEN others THEN
                CONTINUE;
        END;
    END LOOP;

    RETURN FALSE;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION check_opening_hours()
RETURNS TRIGGER AS $$
BEGIN
    IF NOT can_reserve(NEW.restaurant_id, NEW.user_id, NEW.date_of_visit, NEW.time_of_visit) THEN
        RAISE EXCEPTION 'Restaurant is not opened at this time.';
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE TRIGGER validate_opening_hours
    BEFORE INSERT OR UPDATE ON reservation
    FOR EACH ROW
    EXECUTE FUNCTION check_opening_hours();
